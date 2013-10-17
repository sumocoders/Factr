<?php

namespace SumoCoders\Factr\Client;

/**
 * Address class
 *
 * @author		Tijs Verkoyen <php-factr@verkoyen.eu>
 * @version		2.0.0
 * @copyright	Copyright (c), Tijs Verkoyen. All rights reserved.
 * @license		BSD License
 */
class Address
{
    /**
     * @var string
     */
    protected $country, $countryName, $fullAddress;

    /**
     * @param string $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param string $countryName
     */
    private function setCountryName($countryName)
    {
        $this->countryName = $countryName;
    }

    /**
     * @return string
     */
    public function getCountryName()
    {
        return $this->countryName;
    }

    /**
     * @param string $fullAddress
     */
    public function setFullAddress($fullAddress)
    {
        $this->fullAddress = $fullAddress;
    }

    /**
     * @return string
     */
    public function getFullAddress()
    {
        return $this->fullAddress;
    }

    /**
     * Initialize the object with raw data
     *
     * @param $data
     * @return Address
     */
    public static function initializeWithRawData($data)
    {
        $item = new Address();

        if(isset($data['full_address'])) $item->setFullAddress($data['full_address']);
        if(isset($data['country'])) $item->setCountry($data['country']);
        if(isset($data['country_name'])) $item->setCountryName($data['country_name']);

        return $item;
    }

    /**
     * Converts the object into an array
     *
     * @param  bool[optional] $forApi Will the result be used in the API?
     * @return array
     */
    public function toArray($forApi = false)
    {
        $data = array();

        $data['address'] = $this->getFullAddress();
        $data['country'] = $this->getCountry();

        return $data;
    }
}
