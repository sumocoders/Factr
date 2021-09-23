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
    protected string $country;

    protected string $countryName;

    protected string $fullAddress;

    public function setCountry(string $country)
    {
        $this->country = $country;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    private function setCountryName(string $countryName)
    {
        $this->countryName = $countryName;
    }

    public function getCountryName(): string
    {
        return $this->countryName;
    }

    public function setFullAddress(string $fullAddress)
    {
        $this->fullAddress = $fullAddress;
    }

    public function getFullAddress(): string
    {
        return $this->fullAddress;
    }

    /**
     * Initialize the object with raw data
     */
    public static function initializeWithRawData(array $data): Address
    {
        $item = new Address();

        if(isset($data['full_address'])) $item->setFullAddress($data['full_address']);
        if(isset($data['country'])) $item->setCountry($data['country']);
        if(isset($data['country_name'])) $item->setCountryName($data['country_name']);

        return $item;
    }

    /**
     * Converts the object into an array
     */
    public function toArray(bool $forApi = false): array
    {
        $data = array();

        $data['address'] = $this->getFullAddress();
        $data['country'] = $this->getCountry();

        return $data;
    }
}
