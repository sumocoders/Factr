<?php

namespace SumoCoders\Factr\Invoice;

use DateTime;
use Exception;

/**
 * Class Payment
 *
 * @package SumoCoders\Factr\Invoice
 */
class Payment
{
    protected float $amount;

    protected DateTime $paidAt;

    protected string $identifier;

    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setIdentifier(string $identifier): void
    {
        $this->identifier = $identifier;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function setPaidAt(DateTime $paidAt): void
    {
        $this->paidAt = $paidAt;
    }

    public function getPaidAt(): DateTime
    {
        return $this->paidAt;
    }

    /**
     * Initialize the object with raw data
     *
     * @throws Exception
     */
    public static function initializeWithRawData(array $data): Payment
    {
        $item = new Payment();

        if(isset($data['amount'])) $item->setAmount($data['amount']);
        if(isset($data['paid_at'])) $item->setPaidAt(new DateTime('@' . strtotime($data['paid_at'])));
        if(isset($data['identifier'])) $item->setIdentifier($data['identifier']);

        return $item;
    }

    /**
     * Converts the object into an array
     */
    public function toArray(bool $forApi = false): array
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
