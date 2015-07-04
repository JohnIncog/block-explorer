<?php
/**
 * @author John <john@paycoin.com>
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */
namespace lib;

class IpInfoDb {

	protected $url;
	protected $key;
	public function __construct() {
		/** @var $config array */
		include(__DIR__ . '/../conf/config.php');
		$this->url = $config['ipinfodb']['apiurl'];
		$this->key = $config['ipinfodb']['apikey'];

	}

	public function getGeoInfo($ip) {

		$client = new \GuzzleHttp\Client();
		$res = $client->get($this->url . '?key=' . $this->key . '&ip=' . $ip);

		$response = explode(';', $res->getBody());

		$return = array(
			'countryCode' => $response[3],
			'countryName' => $response[4],
			'state' => $response[5],
			'city' => $response[6],
		);


		return $return;
	}

}