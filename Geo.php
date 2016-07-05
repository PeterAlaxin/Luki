<?php

/**
 * Geo class
 *
 * Luki framework
 * Date 24.9.2012
 *
 * @version 3.0.0
 *
 * @author Peter Alaxin, <peter@lavien.sk>
 * @copyright (c) 2009, Almex spol. s r.o.
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Class
 * @filesource
 */

namespace Luki;

/**
 * Geo class
 *
 * Geo data manipulation
 *
 * @package Luki
 */
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

        unset($key);
    }

    public function setKey($key)
    {
        $this->key = (string) $key;

        unset($key);
    }

    public function setStreet($street)
    {
        $this->street = (string) $street;
        $this->reset();

        unset($street);
        return $this;
    }

    public function setCity($city)
    {
        $this->city = (string) $city;
        $this->reset();

        unset($city);
        return $this;
    }

    public function setState($state)
    {
        $this->state = (string) $state;
        $this->reset();

        unset($state);
        return $this;
    }

    public function setLongitude($longitude)
    {
        $this->longitude = str_replace(',', '.', $longitude);
        $this->reset(FALSE);

        unset($longitude);
        return $this;
    }

    public function setLatitude($latitude)
    {
        $this->latitude = str_replace(',', '.', $latitude);
        $this->reset(FALSE);

        unset($latitude);
        return $this;
    }

    public function getCoordinates()
    {
        if ( empty($this->coordinates) ) {
            if ( !empty($this->latitude) and ! empty($this->longitude) ) {
                $this->fillCoordinates();
            } else {
                $this->findCoordinates();
            }
        }

        return $this->coordinates;
    }

    private function reset($all = TRUE)
    {
        $this->address = array();
        $this->coordinates = array();

        if ( $all ) {
            $this->longitude = NULL;
            $this->latitude = NULL;
        }

        unset($all);
    }

    private function findCoordinates()
    {
        $url = $this->url . 'address=' . $this->makeAddress();

        if ( !empty($this->key) ) {
            $url .= '&key=' . $this->key;
        }

        $json = file_get_contents($url);
        $jsondata = json_decode($json);

        if ( $jsondata->status == 'OK' ) {
            $this->latitude = str_replace(',', '.', $jsondata->results[0]->geometry->location->lat);
            $this->longitude = str_replace(',', '.', $jsondata->results[0]->geometry->location->lng);
            $this->fillCoordinates();
        } else {
            $this->reset();
        }

        unset($url, $json, $jsondata);
    }

    private function fillCoordinates()
    {
        $this->coordinates = array(
          'longitude' => $this->longitude,
          'latitude' => $this->latitude,
        );
    }

    public function getAddress()
    {
        if ( empty($this->address) ) {
            $this->findAddress();
        }

        return $this->address;
    }

    private function findAddress()
    {
        $url = $this->url . 'latlng=' . $this->latitude . "," . $this->longitude;

        if ( !empty($this->key) ) {
            $url .= '&key=' . $this->key;
        }

        $json = file_get_contents($url);
        $jsondata = json_decode($json, TRUE);

        if ( $jsondata["status"] == "OK" ) {
            $this->address = array(
              'country' => $this->_getCountry($jsondata),
              'province' => $this->_getProvince($jsondata),
              'city' => $this->_getCity($jsondata),
              'street' => $this->_getStreet($jsondata),
              'postal_code' => $this->_getPostalCode($jsondata),
              'country_code' => $this->_getCountryCode($jsondata),
              'formatted_address' => $this->_getAddress($jsondata),
            );
        } else {
            $this->reset(FALSE);
        }

        unset($url, $json, $jsondata);
    }

    private function makeAddress()
    {
        $address = '';
        if ( !empty($this->street) ) {
            $address .= $this->street;
        }

        if ( !empty($this->city) ) {
            if ( !empty($address) ) {
                $address .= ', ';
            }
            $address .= $this->city;
        }

        if ( !empty($this->state) ) {
            if ( !empty($address) ) {
                $address .= ', ';
            }
            $address .= $this->state;
        }

        $address = urlencode($address);
        return $address;
    }

    public function getDistance($param1, $param2 = NULL)
    {
        if ( is_a($param1, 'Luki\Geo') ) {
            $coordinates = $param1->getCoordinates();
            $longitude = $coordinates['longitude'];
            $latitude = $coordinates['latitude'];
        } elseif ( is_array($param1) ) {
            $longitude = $param1['longitude'];
            $latitude = $param1['latitude'];
        } else {
            $longitude = $param1;
            $latitude = $param2;
        }

        $distance = $this->computeDistance($longitude, $latitude);

        unset($param1, $param2, $coordinates, $longitude, $latitude);
        return $distance;
    }

    private function computeDistance($longitude, $latitude)
    {
        if ( empty($this->coordinates) ) {
            $this->getCoordinates();
        }

        $theta = $this->longitude - $longitude;
        $computing = sin(deg2rad($this->latitude)) * sin(deg2rad($latitude)) +
                cos(deg2rad($this->latitude)) * cos(deg2rad($latitude)) * cos(deg2rad($theta));
        $computing = rad2deg(acos($computing));
        $distance = $computing * 60 * 1.1515 * 1.609344;

        unset($longitude, $latitude, $theta, $computing);
        return $distance;
    }

    private function _getCountry($jsondata)
    {
        return $this->findValueByType("country", $jsondata["results"][0]["address_components"]);
    }

    private function _getProvince($jsondata)
    {
        return $this->findValueByType("administrative_area_level_1", $jsondata["results"][0]["address_components"], TRUE);
    }

    private function _getCity($jsondata)
    {
        return $this->findValueByType("locality", $jsondata["results"][0]["address_components"]);
    }

    private function _getStreet($jsondata)
    {
        return $this->findValueByType("street_number", $jsondata["results"][0]["address_components"]) . ' ' . $this->findValueByType("route", $jsondata["results"][0]["address_components"]);
    }

    private function _getPostalCode($jsondata)
    {
        return $this->findValueByType("postal_code", $jsondata["results"][0]["address_components"]);
    }

    private function _getCountryCode($jsondata)
    {
        return $this->findValueByType("country", $jsondata["results"][0]["address_components"], true);
    }

    private function _getAddress($jsondata)
    {
        return $jsondata["results"][0]["formatted_address"];
    }

    private function findValueByType($type, $array, $short_name = FALSE)
    {
        foreach ( $array as $value ) {
            if ( in_array($type, $value["types"]) ) {
                if ( $short_name ) {
                    return $value["short_name"];
                }
                return $value["long_name"];
            }
        }
    }

}
