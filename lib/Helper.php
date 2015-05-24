<?php
namespace PP;

class Helper {

	public static function formatXPY($xpy) {

		return self::formatCoin($xpy, 'XPY');

	}

	public static function formatCoin($amount, $symbol) {
		$number = number_format($amount, 8);
		$number = rtrim($number, 0);

		if (substr($number, -1) == '.') {
			$number = $number . 0;
		}
		if ($number < 0) {
			return str_replace('-', '- ', $number) . ' ' . $symbol;
		}
		return $number . ' ' . $symbol;
	}

	public static function getAddressLink($address) {
		$link = '<a href="' . self::getUrl('address', array('address' => $address))
			. '" class="">' . $address . '</a>';
		return $link;
	}

	public static function getTxHashLink($transaction) {
		$link = '<div class="hash"><a href="' . self::getUrl('transaction', array('transaction' => $transaction))
			. '" class="">' . $transaction . '</a></div>';
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
