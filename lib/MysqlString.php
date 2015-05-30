<?php

namespace PP;


class MysqlString {

	public $sql;

	public function __construct($sql) {
		$this->sql = $sql;
		return $this;
	}
} 