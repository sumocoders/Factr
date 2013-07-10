<?php

namespace SumoCoders\Factr\Client;

use SumoCoders\Factr\Client\Address;

/**
 * Client class
 *
 * @author		Tijs Verkoyen <php-factr@verkoyen.eu>
 * @version		2.0.0
 * @copyright	Copyright (c), Tijs Verkoyen. All rights reserved.
 * @license		BSD License
 */
class Client
{
    /**
     * @var int
     */
    protected $id, $paymentDays;

    /**
     * @var string
     */
    protected $cid,
                $company, $vat,
                $firstName, $lastName,
                $phone, $fax, $cellphone,
                $website,
                $remarks;

    /**
     * @var Address
     */
    protected $billingAddress, $companyAddress;

    /**
     * @var array
     */
    protected $email;

    /**
     * @var bool
     */
    protected $invoiceableByEmail, $invoiceableBySnailMail, $invoiceableByFactr;

    /**
     * @param \SumoCoders\Factr\Client\Address $billingAddress
     */
    public function setBillingAddress($billingAddress)
    {
        $this->billingAddress = $billingAddress;
    }

    /**
     * @return \SumoCoders\Factr\Client\Address
     */
    public function getBillingAddress()
    {
        return $this->billingAddress;
    }

    /**
     * @param string $cellphone
     */
    public function setCellphone($cellphone)
    {
        $this->cellphone = (string) $cellphone;
    }

    /**
     * @return string
     */
    public function getCellphone()
    {
        return $this->cellphone;
    }

    /**
     * @param string $cid
     */
    public function setCid($cid)
    {
        $this->cid = (string) $cid;
    }

    /**
     * @return string
     */
    public function getCid()
    {
        return $this->cid;
    }

    /**
     * @param string $company
     */
    public function setCompany($company)
    {
        $this->company = (string) $company;
    }

    /**
     * @return string
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param \SumoCoders\Factr\Client\Address $companyAddress
     */
    public function setCompanyAddress($companyAddress)
    {
        $this->companyAddress = $companyAddress;
    }

    /**
     * @return \SumoCoders\Factr\Client\Address
     */
    public function getCompanyAddress()
    {
        return $this->companyAddress;
    }

    /**
     * @param $email
     */
    public function addEmail($email)
    {
        $this->email[] = (string) $email;
    }

    /**
     * @param array $email
     */
    public function setEmail(array $email)
    {
        $this->email = $email;
    }

    /**
     * @return array
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $fax
     */
    public function setFax($fax)
    {
        $this->fax = (string) $fax;
    }

    /**
     * @return string
     */
    public function getFax()
    {
        return $this->fax;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = (string) $firstName;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param int $id
     */
    private function setId($id)
    {
        $this->id = (int) $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param boolean $invoiceableByEmail
     */
    public function setInvoiceableByEmail($invoiceableByEmail)
    {
        $this->invoiceableByEmail = (bool) $invoiceableByEmail;
    }

    /**
     * @return boolean
     */
    public function getInvoiceableByEmail()
    {
        return $this->invoiceableByEmail;
    }

    /**
     * @param boolean $invoiceableByFactr
     */
    public function setInvoiceableByFactr($invoiceableByFactr)
    {
        $this->invoiceableByFactr = (bool) $invoiceableByFactr;
    }

    /**
     * @return boolean
     */
    public function getInvoiceableByFactr()
    {
        return $this->invoiceableByFactr;
    }

    /**
     * @param boolean $invoiceableBySnailMail
     */
    public function setInvoiceableBySnailMail($invoiceableBySnailMail)
    {
        $this->invoiceableBySnailMail = (bool) $invoiceableBySnailMail;
    }

    /**
     * @return boolean
     */
    public function getInvoiceableBySnailMail()
    {
        return $this->invoiceableBySnailMail;
    }

    /**
     * @param string $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = (string) $lastName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param int $paymentDays
     */
    public function setPaymentDays($paymentDays)
    {
        $this->paymentDays = (int) $paymentDays;
    }

    /**
     * @return int
     */
    public function getPaymentDays()
    {
        return $this->paymentDays;
    }

    /**
     * @param string $phone
     */
    public function setPhone($phone)
    {
        $this->phone = (string) $phone;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $remarks
     */
    public function setRemarks($remarks)
    {
        $this->remarks = (string) $remarks;
    }

    /**
     * @return string
     */
    public function getRemarks()
    {
        return $this->remarks;
    }

    /**
     * @param string $vat
     */
    public function setVat($vat)
    {
        $this->vat = (string) $vat;
    }

    /**
     * @return string
     */
    public function getVat()
    {
        return $this->vat;
    }

    /**
     * @param string $website
     */
    public function setWebsite($website)
    {
        $this->website = (string) $website;
    }

    /**
     * @return string
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * Initialize the object with raw data
     *
     * @param $data
     * @return Client
     */
    public static function initializeWithRawData($data)
    {
        $item = new Client();

        if(isset($data['id'])) $item->setId($data['id']);
        if(isset($data['cid'])) $item->setCid($data['cid']);
        if(isset($data['first_name'])) $item->setFirstName($data['first_name']);
        if(isset($data['last_name'])) $item->setLastName($data['last_name']);
        if(isset($data['company'])) $item->setCompany($data['company']);
        if(isset($data['vat'])) $item->setVat($data['vat']);
        if (isset($data['company_address'])) {
            $address = Address::initializeWithRawData($data['company_address']);
            $item->setCompanyAddress($address);
        }
        if (isset($data['billing_address'])) {
            $address = Address::initializeWithRawData($data['billing_address']);
            $item->setBillingAddress($address);
        }
        if(isset($data['email'])) $item->setEmail($data['email']);
        if(isset($data['fax'])) $item->setFax($data['fax']);
        if(isset($data['phone'])) $item->setPhone($data['phone']);
        if(isset($data['cellphone'])) $item->setCellphone($data['cellphone']);
        if(isset($data['website'])) $item->setWebsite($data['website']);
        if(isset($data['invoiceable_by_email'])) $item->setInvoiceableByEmail($data['invoiceable_by_email']);
        if(isset($data['invoiceable_by_snailmail'])) $item->setInvoiceableBySnailMail($data['invoiceable_by_snailmail']);
        if(isset($data['invoiceable_by_factr'])) $item->setInvoiceableByFactr($data['invoiceable_by_factr']);
        if(isset($data['payment_days'])) $item->setPaymentDays($data['payment_days']);
        if(isset($data['remarks'])) $item->setRemarks($data['remarks']);

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

        $data['id'] = $this->getId();
        $data['cid'] = $this->getCid();
        $data['first_name'] = $this->getFirstName();
        $data['last_name'] = $this->getLastName();
        $data['company'] = $this->getCompany();
        $data['vat'] = $this->getVat();
        $address = $this->getCompanyAddress();
        if($address !== null) $data['company_address'] = $address->toArray();
        $address = $this->getBillingAddress();
        if($address !== null) $data['billing_address'] = $address->toArray();
        // @todo this is a bug in the API, so for now we just send the first email address
        $addresses = $this->getEmail();
        if(!empty($addresses)) $data['email'] = $addresses[0];
        $data['email'] = $addresses;
        $data['fax'] = $this->getFax();
        $data['phone'] = $this->getPhone();
        $data['cellphone'] = $this->getCellphone();
        $data['website'] = $this->getWebsite();
        $data['invoiceable_by_email'] = $this->getInvoiceableByEmail();
        $data['invoiceable_by_snailmail'] = $this->getInvoiceableBySnailMail();
        $data['invoiceable_by_factr'] = $this->getInvoiceableByFactr();
        $data['payment_days'] = $this->getPaymentDays();
        $data['remarks'] = $this->getRemarks();

        return $data;
    }
}
