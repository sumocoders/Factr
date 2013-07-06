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
    protected $street, $number, $zip, $city, $country, $countryName;

    /**
     * @param string $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

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
     * @param string $number
     */
    public function setNumber($number)
    {
        $this->number = $number;
    }

    /**
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @param string $street
     */
    public function setStreet($street)
    {
        $this->street = $street;
    }

    /**
     * @return string
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * @param string $zip
     */
    public function setZip($zip)
    {
        $this->zip = $zip;
    }

    /**
     * @return string
     */
    public function getZip()
    {
        return $this->zip;
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

        if(isset($data['street'])) $item->setStreet($data['street']);
        if(isset($data['number'])) $item->setNumber($data['number']);
        if(isset($data['zip'])) $item->setZip($data['zip']);
        if(isset($data['city'])) $item->setCity($data['city']);
        if(isset($data['country'])) $item->setCountry($data['country']);
        if(isset($data['country_name'])) $item->setCountryName($data['country_name']);

        return $item;
    }

    /**
     * Converts the object into an array
     *
     * @return array
     */
    public function toArray()
    {
        $data = array();

        $data['street'] = $this->getStreet();
        $data['number'] = $this->getNumber();
        $data['zip'] = $this->getZip();
        $data['city'] = $this->getCity();
        $data['country'] = $this->getCountry();

        return $data;
    }
}