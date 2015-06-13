<?php
/**
 * @author John <john@paycoin.com>
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */
namespace lib;

/**
 * Class User
 * @package lib
 */
class User {

	public function login($username, $password) {

		$mysql = Mysql::getInstance();

		$sql = "SELECT * FROM users WHERE username = " . $mysql->escape($username);
		$result = $mysql->selectRow($sql);

		if ($result !== false) {
			if (password_verify($password, $result['password'])) {
				return true;
			} else {
				return false;
			}
		}

		return false;

	}

	public function addUser($username, $email, $password) {
		$mysql = Mysql::getInstance();

		$insert = array(
			'username' => $username,
			'email' => $email,
			'password' => password_hash($password, PASSWORD_DEFAULT),
		);

		return $mysql->insert('users', $insert);

	}

}