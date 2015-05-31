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
		list($whole, $decimal) = explode('.', $number);

		return '<span class="coin-whole">' . $whole . '</span>.' . '<span class="coin-decimal">' .$decimal . '</span> ' . $symbol;
	}

	public static function getAddressLink($address) {
		$link = '<a href="' . self::getUrl('address', array('address' => $address))
			. '" class="">' . htmlspecialchars($address) . '</a>';
		return $link;
	}

	public static function getTxHashLink($transaction) {
		$link = '<div ><code><a class="hash" href="' . self::getUrl('transaction', array('transaction' => $transaction))
			. '" class="">' . $transaction . '</a></code></div>';
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

	public static function formatTime($timestamp, $timeAgo = false) {
		return static::getLocalDateTime(date('Y-m-d  H:i:s', $timestamp) . ' UTC', $timeAgo);
	}

	public static function getLocalDateTime($utcDateTime, $timeAgo = false) {
		$js = "
		<script>var date = new Date('" . $utcDateTime . "');
		document.write(date.toString().replace(/GMT.*/g,''));";
		if ($timeAgo == true) {
			$js .= "document.write( '(' + jQuery.timeago(date.toString().replace(/GMT.*/g,'')) + ')' );";
		}
		$js .= "</script>";
		return $js;
	}

	public static function getValue($collection, $key, $default = false) {
		$return = $default;

		if (!empty($collection[$key])) {
			$return = $collection[$key];
		}

		return $return;
	}
}
