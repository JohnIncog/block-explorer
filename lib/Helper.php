<?php
namespace PP;

class Helper {

	public static function formatXPY($xpy) {
		$number = number_format($xpy, 8);
		$number = rtrim($number, 0);
		if (substr($number, -1) == '.') {
			$number = $number . 0;
		}
		return $number;

	}

	public static function getAddressLink($address) {
		$link = '<a href="' . self::getUrl('address', array('address' => $address))
			. '" class="">' . $address . '</a>';
		return $link;
	}


	public static function getUrl($pageName, $params = array()) {
		switch ($pageName) {
			case 'block';
				$url = '/block/' . $params['block'];
				break;
			case 'address';
				$url = '/address/' . $params['address'];
				break;
			case 'transaction';
				$url = '/transaction/' . $params['transaction'];
				break;
			case '404':
				$url = '/404.php';
				break;
			default:
				$url = false;
		}
		return $url;
	}

	public static function formatTime($timestamp) {
		return date('Y-m-d  H:i:s', $timestamp);
	}

	public static function getValue($collection, $key, $default = false) {
		$return = $default;

		if (!empty($collection[$key])) {
			$return = $collection[$key];
		}

		return $return;
	}
}
