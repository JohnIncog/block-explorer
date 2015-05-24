<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 5/24/15
 * Time: 4:05 PM
 */

namespace PP;


class MysqlString {

	public $sql;

	public function __construct($sql) {
		$this->sql = $sql;
		return $this;
	}
} 