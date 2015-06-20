<?php
/**
 * @author John <john@paycoin.com>
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */
namespace lib;
use DebugBar\DataCollector\PDO\PDOCollector;
use DebugBar\DataCollector\PDO\TraceablePDO;
use \PDO;

/**
 * Class Mysql
 * @package lib
 */
class Mysql {

	protected static $instance;

	protected $mysql;

	protected $trace;

	protected $debug = true;

	/** @var \PDO  */
	protected $pdo;

	private function __construct() {
		/** @var $config array */
		include(__DIR__ . '/../conf/config.php');

		$dsn = 'mysql:host=localhost;dbname=' . $config['mysql']['database'];
		$username = $config['mysql']['user'];
		$password = $config['mysql']['password'];
		$options = array(
			PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
		);

		$pdo = new \PDO($dsn, $username, $password, $options);
		if (DEBUG_BAR) {
			$this->pdo = new TraceablePDO($pdo);
			Bootstrap::getInstance()->debugbar->addCollector(new PDOCollector($this->pdo));
		} else {
			$this->pdo = new \PDO($dsn, $username, $password, $options);
		}

		$this->cache = Cache::getInstance();

	}

	public static function getInstance() {
		if (!isset(self::$instance)) {
			self::$instance = new Mysql();
		}
		return self::$instance;
	}

	public function trace($trace) {
		if ($this->debug) {
			$this->trace .= $trace . "\n";
		}
	}
	public function query($sql) {

		$this->trace($sql);

		$result = $this->pdo->query($sql);
		return $result;
	}

	public function startTransaction() {
		$this->pdo->beginTransaction();
	}
	public function completeTransaction() {
		$this->pdo->commit();
	}

	public function select($sql, $cacheTime = false) {
		if ($cacheTime > 0) {
			$key = 'SQL:' . md5($sql);
			$result = $this->cache->get($key);

			if ($this->cache->wasResultFound()) {
				if (DEBUG_BAR) {
					Bootstrap::getInstance()->debugbar['messages']->addMessage("Cached SQL: " . $sql);
				}
				return $result;
			}
		}
		try {
			$result = $this->pdo->query($sql, \PDO::FETCH_ASSOC);
		} catch (\PDOException $e) {
			trigger_error($e->getMessage(), E_USER_ERROR);
			$result = array();
		}

		$rows = array();
		foreach ($result as $row) {
			$rows[] = $row;
		}

		if ($cacheTime > 0) {
			$this->cache->set($key, $rows, $cacheTime);
		}


		return $rows;
	}

	public function selectRow($sql, $cacheTime = false) {

		if ($cacheTime > 0) {
			$key = 'SQL:' . md5($sql);
			$result = $this->cache->get($key);

			if ($this->cache->wasResultFound()) {
				if (DEBUG_BAR) {
					Bootstrap::getInstance()->debugbar['messages']->addMessage("Cached SQL: " . $sql);
				}
				return $result;
			}
		}

		try {
			$rows = $this->pdo->query($sql)->fetch(PDO::FETCH_ASSOC);
		} catch (\PDOException $e) {
			trigger_error($e->getMessage(), E_USER_ERROR);
		}

		if ($cacheTime > 0) {
			$this->cache->set($key, $rows, $cacheTime);
		}
		return $rows;
	}

	public function escape($value) {
		if (is_null($value)) {
			$escaped = "NULL";
		} elseif (is_numeric($value)) {
			if (is_int($value) || is_float($value)) {
				$escaped = $value;
			} else {
				$escaped = "'$value'";
			}

		} else {
			$escaped = $this->pdo->quote($value);
		}
		return $escaped;
	}

	public function insert($table, array $insert, $ignore = false, array $update = array()) {


		$sql = "INSERT ";
		if ($ignore) {
			$sql .= " IGNORE ";
		}
		$sql .= "INTO {$table} (";

		$fields = array_keys($insert);

		foreach ($fields as $field) {
			$sql .= "`{$field}`, ";
		}
		$sql = substr($sql, 0, -2);
		$sql .= ") VALUES (";
		foreach ($insert as $value) {
			$sql .= $this->escape($value) .', ';
		}
		$sql = substr($sql, 0, -2);
		$sql .= ")";
		$return = false;
		if (count($update) > 0) {
			$sql .= ' ON DUPLICATE KEY UPDATE ';
			foreach ($update as $field => $value) {
				$sql .= "{$field} = " . $this->escape($value) .", ";
			}
			$sql = substr($sql, 0, -2);

		}
		try {
			$return = $this->pdo->exec($sql);
		} catch (\PDOException $e) {
			if ($e->getCode() == 23000) { //duplicate
				$return = false;
			}
		}

		return $return;

	}

	public function insertMultiple($table, array $values, $ignore = false) {

		$fields = array_keys(current($values));

		$sql = "INSERT ";
		if ($ignore) {
			$sql .= " IGNORE ";
		}
		$sql .= "INTO {$table} (";

		foreach ($fields as $field) {
			$sql .= "`{$field}`, ";
		}
		$sql = substr($sql, 0, -2);
		$sql .= ") VALUES ";
		foreach ($values as $insert) {
			$sql .= "(";
			foreach ($insert as $value) {
				$sql .= $this->escape($value) .', ';
			}
			$sql = substr($sql, 0, -2);
			$sql .= "), ";
		}
		$sql = substr($sql, 0, -2);
		$this->pdo->exec($sql);

	}

	public function getInClause(array $ins) {
		$sql = ' IN (';
		foreach ($ins as $in) {
			$sql .= $this->pdo->quote($in) . ', ';
		}
		$sql = substr($sql, 0, -2);
		$sql .= ' )';
		return $sql;
	}

} 