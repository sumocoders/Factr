<?php

namespace SumoCoders\Factr\Invoice;

/**
 * Class Payment
 *
 * @package SumoCoders\Factr\Invoice
 */
class Payment
{
    /**
     * @var float
     */
    protected $amount;

    /**
     * @var \DateTime
     */
    protected $paidAt;

    /**
     * @var string
     */
    protected $identifier;

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
     * @param string $identifier
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
    }

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @param \DateTime $paidAt
     */
    public function setPaidAt($paidAt)
    {
        $this->paidAt = $paidAt;
    }

    /**
     * @return \DateTime
     */
    public function getPaidAt()
    {
        return $this->paidAt;
    }

    /**
     * Initialize the object with raw data
     *
     * @param $data
     * @return Payment
     */
    public static function initializeWithRawData($data)
    {
        $item = new Payment();

        if(isset($data['amount'])) $item->setAmount($data['amount']);
        if(isset($data['paid_at'])) $item->setPaidAt(new \DateTime('@' . strtotime($data['paid_at'])));
        if(isset($data['identifier'])) $item->setIdentifier($data['identifier']);

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
        $data['amount'] = $this->getAmount();
        if ($this->paidAt !== null) {
            if(!$forApi) $data['paid_at'] = $this->getPaidAt()->getTimestamp();
            else $data['paid_at'] = date('Y-m-d\TH:i:s', $this->getPaidAt()->getTimestamp());
        }
        $data['identifier'] = $this->getIdentifier();

        return $data;
    }
}
