<?php
namespace SumoCoders\Factr\Invoice;

/**
 * Class Item
 *
 * @package SumoCoders\Factr\Invoice
 */
class Item
{
    /**
     * @var string
     */
    protected $description;

    /**
     * @var float
     */
    protected $amount, $price, $totalWithoutVat, $totalVat, $totalWithVat;

    /**
     * @var int
     */
    protected $vat, $referenceId;

    /**
     * @param float $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
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
     * @param int $referenceId
     */
    private function setReferenceId($referenceId)
    {
        $this->referenceId = $referenceId;
    }

    /**
     * @return int
     */
    public function getReferenceId()
    {
        return $this->referenceId;
    }

    /**
     * @param float $totalVat
     */
    private function setTotalVat($totalVat)
    {
        $this->totalVat = (float) $totalVat;
    }

    /**
     * @return float
     */
    public function getTotalVat()
    {
        return $this->totalVat;
    }

    /**
     * @param float $totalWithVat
     */
    private function setTotalWithVat($totalWithVat)
    {
        $this->totalWithVat = (float) $totalWithVat;
    }

    /**
     * @return float
     */
    public function getTotalWithVat()
    {
        return $this->totalWithVat;
    }

    /**
     * @param float $totalWithoutVat
     */
    private function setTotalWithoutVat($totalWithoutVat)
    {
        $this->totalWithoutVat = (float) $totalWithoutVat;
    }

    /**
     * @return float
     */
    public function getTotalWithoutVat()
    {
        return $this->totalWithoutVat;
    }

    /**
     * @param int $vat
     */
    public function setVat($vat)
    {
        $this->vat = (int) $vat;
    }

    /**
     * @return int
     */
    public function getVat()
    {
        return $this->vat;
    }

    /**
     * Initialize the object with raw data
     *
     * @param $data
     * @return Item
     */
    public static function initializeWithRawData($data)
    {
        $item = new Item();

        if(isset($data['description'])) $item->setDescription($data['description']);
        if(isset($data['amount'])) $item->setAmount($data['amount']);
        if(isset($data['price'])) $item->setPrice($data['price']);
        if(isset($data['vat'])) $item->setVat($data['vat']);
        if(isset($data['reference_id'])) $item->setReferenceId($data['reference_id']);
        if(isset($data['total_without_vat'])) $item->setTotalWithoutVat($data['total_without_vat']);
        if(isset($data['total_vat'])) $item->setTotalVat($data['total_vat']);
        if(isset($data['total_with_vat'])) $item->setTotalWithVat($data['total_with_vat']);

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
        $data['description'] = $this->getDescription();
        $data['price'] = $this->getPrice();
        $data['amount'] = $this->getAmount();
        $data['vat'] = $this->getVat();

        if (!$forApi) {
            $data['total_without_vat'] = $this->getTotalWithoutVat();
            $data['total_vat'] = $this->getTotalVat();
            $data['total_with_vat'] = $this->getTotalWithVat();
            $data['reference_id'] = $this->getReferenceId();
        }

        return $data;
    }
}
