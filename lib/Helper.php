<?php
namespace lib;

use JsonSchema\Uri\UriRetriever;

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

	/**
	 * @var array Used for local caching.
	 */
	public static $addressTags = array();

	public static function getAddressLink($address, $addressTagMap = null) {


		$link = '<a href="' . self::getUrl('address', array('address' => $address))
			. '" class="">' . htmlspecialchars($address) . '</a>';

		$tag = null;
		if (!empty($addressTagMap[$address])) {
			$tag = $addressTagMap[$address];
		}
		if ($addressTagMap === null) {
			//local caching..
			if ($tag == null) {
				if (isset(self::$addressTags[$address])) {
					$tag = self::$addressTags[$address];
				} else {
					$paycoinDb = new PaycoinDb();
					$tag = $paycoinDb->getAddressTag($address);
					if ($tag == null) {
						self::$addressTags[$address] = false;
					} else {
						self::$addressTags[$address] = $tag;
					}

				}

			}
		}

		if (!empty($tag) && $tag['verified'] != 3) {
			$class = 'label-primary';
			if ($tag['verified'] == 1) {
				$class = 'label-success';
			}

			$link = '<a href="' . self::getUrl('address', array('address' => $address))
				. '"><span class="tagged-address pull-left">' . htmlspecialchars($address) . '</span>';
			if (!empty($tag['url']) && $tag['verified'] == 1) {
				$link .= '</a>';
				$link .= '<h4 class="pull-left" style="margin-top: 0; margin-bottom: 0;">';
				$link .= "<a target=\"_blank\" href=\"{$tag['url']}\" title=\"{$tag['url']}\" class='label " . $class . " tagged-tag'>"
					. htmlspecialchars($tag['tag']). '&nbsp;&nbsp;<span class="glyphicon glyphicon-new-window" aria-hidden="true">
					</span></a></h4>';

			} else {
				$link .= '<h4 class="pull-left" style="margin-top: 0; margin-bottom: 0;">
				<span class="label ' . $class . ' tagged-tag">' . htmlspecialchars($tag['tag']) . '</span></h4>'
					. '</a>';
			}

		}
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
		$iso = date('c', strtotime($utcDateTime));
		$js = "
		<script>var date = new Date('" . $iso . "');
		document.write(date.toString().replace(/GMT.*/g,''));";
		if (false && $timeAgo == true) {
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
