<?php
/**
 * @author John <john@paycoin.com>
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */
namespace lib;

class MaxMind {

	protected $url;
	protected $user;
	protected $password;

	public function __construct() {
		/** @var $config array */
		include(__DIR__ . '/../conf/config.php');
		$this->url =  $config['maxmind']['url'];;
		$this->user = $config['maxmind']['user'];
		$this->password = $config['maxmind']['password'];

	}

	public function getGeoInfo($ip) {

		$cache = Cache::getInstance();
		$return = $cache->get('geo:' . $ip);
		if ($cache->wasResultFound()) {
			if (DEBUG_BAR) {
				Bootstrap::getInstance()->debugbar['messages']->addMessage("Cached GeoInfo: $ip");
			}
			return $return;
		}

		$client = new \GuzzleHttp\Client();
		//'https://geoip.maxmind.com/geoip/v2.1/city/me
		$res = $client->get($this->url . $ip, array('auth' => array($this->user, $this->password)));


		$body = $res->getBody(true);
		$json = json_decode($body);

		$return = array(
			'countryCode' => $json->country->iso_code,
			'countryName' => $json->country->names->en,
			'state' => $json->subdivisions[0]->names->en,
			'city' => $json->city->names->en,
		);
		if (empty($return['city'])) {
			$return['city'] = 'Unknown';
		}
		if (empty($return['state'])) {
			$return['state'] = 'Unknown';
		}
		$cache->set('geo:' . $ip, $return, 3600);
		return $return;
	}

}