<?php

namespace SumoCoders\Factr\Product;

final class Product
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $description;

    /**
     * @var float
     */
    private $price;

    /**
     * @var int
     */
    private $vat;

    /**
     * @var string $name
     * @var string $decription
     * @var float $price
     * @var int $vat
     * @var int|null $id
     */
    public function __construct($name, $description, $price, $vat, $id = null)
    {
        $this->name = $name;
        $this->description = $description;
        $this->price = $price;
        $this->vat = $vat;
        if ($id !== null) {
            $this->id = $id;
        }
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param float $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param int $vat
     */
    public function setVat($vat)
    {
        $this->vat = $vat;
    }

    /**
     * @return int
     */
    public function getVat()
    {
        return $this->vat;
    }

    /**
     * @param array $data
     *
     * @return self
     */
    public static function initializeWithRawData($data)
    {
        return new self(
            $data['name'],
            $data['description'],
            $data['price'],
            $data['vat'],
            $data['id']
        );
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $data = array();

        $data['id'] = $this->getId();
        $data['name'] = $this->getName();
        $data['description'] = $this->getDescription();
        $data['price'] = $this->getPrice();
        $data['vat'] = $this->getVat();

        return $data;
    }
}
