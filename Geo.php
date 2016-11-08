<?php
/**
 * Geo class
 *
 * Luki framework
 *
 * @author Peter Alaxin, <peter@lavien.sk>
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Geo
 * @filesource
 */

namespace Luki;

class Geo
{
	private $street;
	private $city;
	private $state;
	private $longitude;
	private $latitude;
	private $coordinates = array();
	private $address = array();
	private $url = 'https://maps.google.com/maps/api/geocode/json?sensor=false&';
	private $key;

	public function __construct($key = '')
	{
		$this->setKey($key);
	}

	public function __destruct()
	{
		foreach ($this as &$value) {
			$value = null;
		}
	}

	public function setKey($key)
	{
		$this->key = (string) $key;
	}

	public function setStreet($street)
	{
		$this->street = (string) $street;
		$this->reset();

		return $this;
	}

	public function setCity($city)
	{
		$this->city = (string) $city;
		$this->reset();

		return $this;
	}

	public function setState($state)
	{
		$this->state = (string) $state;
		$this->reset();

		return $this;
	}

	public function setLongitude($longitude)
	{
		$this->longitude = str_replace(',', '.', $longitude);
		$this->reset(false);

		return $this;
	}

	public function setLatitude($latitude)
	{
		$this->latitude = str_replace(',', '.', $latitude);
		$this->reset(false);

		return $this;
	}

	public function getCoordinates()
	{
		if (empty($this->coordinates)) {
			if (!empty($this->latitude) and ! empty($this->longitude)) {
				$this->fillCoordinates();
			} else {
				$this->findCoordinates();
			}
		}

		return $this->coordinates;
	}

	public function reset($all = true)
	{
		$this->address = array();
		$this->coordinates = array();

		if ($all) {
			$this->longitude = null;
			$this->latitude = null;
		}

		return $this;
	}

	private function findCoordinates()
	{
		$url = $this->url.'address='.$this->makeAddress();

		if (!empty($this->key)) {
			$url .= '&key='.$this->key;
		}

		$json = file_get_contents($url);
		$jsondata = json_decode($json);

		if ($jsondata->status == 'OK') {
			$this->latitude = str_replace(',', '.', $jsondata->results[0]->geometry->location->lat);
			$this->longitude = str_replace(',', '.', $jsondata->results[0]->geometry->location->lng);
			$this->fillCoordinates();
		} else {
			$this->reset();
		}
	}

	private function fillCoordinates()
	{
		$this->coordinates = array('longitude' => $this->longitude, 'latitude' => $this->latitude);
	}

	public function getFullAddress()
	{
		if (empty($this->address)) {
			$this->findAddress();
		}

		return $this->address;
	}

	private function findAddress()
	{
		$url = $this->url.'latlng='.$this->latitude.",".$this->longitude;

		if (!empty($this->key)) {
			$url .= '&key='.$this->key;
		}

		$json = file_get_contents($url);
		$jsondata = json_decode($json, true);

		if ($jsondata["status"] == "OK") {
			$this->address = array(
				'country' => $this->getCountry($jsondata),
				'province' => $this->getProvince($jsondata),
				'city' => $this->getCity($jsondata),
				'street' => $this->getStreet($jsondata),
				'postal_code' => $this->getPostalCode($jsondata),
				'country_code' => $this->getCountryCode($jsondata),
				'formatted_address' => $this->getAddress($jsondata),
			);
		} else {
			$this->reset(false);
		}
	}

	private function makeAddress()
	{
		$address = '';
		if (!empty($this->street)) {
			$address .= $this->street;
		}

		if (!empty($this->city)) {
			if (!empty($address)) {
				$address .= ', ';
			}
			$address .= $this->city;
		}

		if (!empty($this->state)) {
			if (!empty($address)) {
				$address .= ', ';
			}
			$address .= $this->state;
		}

		$address = urlencode($address);

		return $address;
	}

	public function getDistance($param1, $param2 = null)
	{
		if (is_a($param1, 'Luki\Geo')) {
			$coordinates = $param1->getCoordinates();
			$longitude = $coordinates['longitude'];
			$latitude = $coordinates['latitude'];
		} elseif (is_array($param1)) {
			$longitude = $param1['longitude'];
			$latitude = $param1['latitude'];
		} else {
			$longitude = $param1;
			$latitude = $param2;
		}

		$distance = $this->computeDistance($longitude, $latitude);

		return $distance;
	}

	private function computeDistance($longitude, $latitude)
	{
		if (empty($this->coordinates)) {
			$this->getCoordinates();
		}

		$theta = $this->longitude - $longitude;
		$computing = sin(deg2rad($this->latitude)) * sin(deg2rad($latitude)) +
			cos(deg2rad($this->latitude)) * cos(deg2rad($latitude)) * cos(deg2rad($theta));
		$computing = rad2deg(acos($computing));
		$distance = $computing * 60 * 1.1515 * 1.609344;

		return $distance;
	}

	private function getCountry($jsondata)
	{
		return $this->findValueByType("country", $jsondata["results"][0]["address_components"]);
	}

	private function getProvince($jsondata)
	{
		return $this->findValueByType("administrative_area_level_1", $jsondata["results"][0]["address_components"], true);
	}

	private function getCity($jsondata)
	{
		return $this->findValueByType("locality", $jsondata["results"][0]["address_components"]);
	}

	private function getStreet($jsondata)
	{
		return $this->findValueByType("street_number", $jsondata["results"][0]["address_components"]).' '.$this->findValueByType("route", $jsondata["results"][0]["address_components"]);
	}

	private function getPostalCode($jsondata)
	{
		return $this->findValueByType("postal_code", $jsondata["results"][0]["address_components"]);
	}

	private function getCountryCode($jsondata)
	{
		return $this->findValueByType("country", $jsondata["results"][0]["address_components"], true);
	}

	private function getAddress($jsondata)
	{
		return $jsondata["results"][0]["formatted_address"];
	}

	private function findValueByType($type, $array, $short_name = false)
	{
		foreach ($array as $value) {
			if (in_array($type, $value["types"])) {
				if ($short_name) {
					return $value["short_name"];
				}
				return $value["long_name"];
			}
		}
	}
}
