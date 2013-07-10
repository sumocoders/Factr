<?php
namespace SumoCoders\Factr\Invoice;

class Payment
{
    /**
     * @var int
     */
    protected $id, $invoiceId;

    /**
     * @var float
     */
    protected $amount;

    /**
     * @var \DateTime
     */
    protected $paidAt, $createdAt, $updatedAt;

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
     * @param \DateTime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
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
     * @param int $invoiceId
     */
    public function setInvoiceId($invoiceId)
    {
        $this->invoiceId = $invoiceId;
    }

    /**
     * @return int
     */
    public function getInvoiceId()
    {
        return $this->invoiceId;
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
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
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
        if(isset($data['created_at'])) $item->setCreatedAt(new \DateTime('@' . strtotime($data['created_at'])));
        if(isset($data['id'])) $item->setId($data['id']);
        if(isset($data['invoice_id'])) $item->setInvoiceId($data['invoice_id']);
        if(isset($data['updated_at'])) $item->setUpdatedAt(new \DateTime('@' . strtotime($data['updated_at'])));
        if(isset($data['paid_at'])) $item->setPaidAt(new \DateTime('@' . strtotime($data['paid_at'])));
        if(isset($data['identifier'])) $item->setIdentifier($data['identifier']);

        return $item;
    }
}
