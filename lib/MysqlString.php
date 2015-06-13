<?php
/**
 * @author John <john@paycoin.com>
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */
namespace lib;

/**
 * Class MysqlString
 * @package lib
 */
class MysqlString {

	public $sql;

	public function __construct($sql) {
		$this->sql = $sql;
		return $this;
	}
} 