<?php
namespace SumoCoders\Factr\Invoice;

use SumoCoders\Factr\Client\Client;

/**
 * Class Invoice
 *
 * @package SumoCoders\Factr\Invoice
 */
class Invoice
{
    /**
     * @var int
     */
    protected $id, $clientId;

    /**
     * @var string
     */
    protected $iid, $state, $paymentMethod, $description, $shownRemark;

    /**
     * @var \DateTime
     */
    protected $generated, $dueDate, $fullyPaidAt;

    /**
     * @var array
     */
    protected $items, $payments, $history;

    /**
     * @var float
     */
    protected $total;

    /**
     * @var \SumoCoders\Factr\Client\Client
     */
    protected $client;

    /**
     * @var string
     */
    protected $language;

    /**
     * @var float
     */
    protected $discount;

    /**
     * @var bool
     */
    protected $discountIsPercentage = false;

    /**
     * @var string
     */
    protected $discountDescription, $vatException, $vatDescription;

    /**
     * @var bool
     */
    protected $prepareForSending = false;

    /**
     * @param \SumoCoders\Factr\Client\Client $client
     */
    public function setClient(Client $client)
    {
        $this->client = $client;
        $this->setClientId($client->getId());
    }

    /**
     * @return \SumoCoders\Factr\Client\Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param int $clientId
     */
    public function setClientId($clientId)
    {
        $this->clientId = $clientId;
    }

    /**
     * @return int
     */
    public function getClientId()
    {
        return $this->clientId;
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
     * @param \DateTime $dueDate
     */
    private function setDueDate($dueDate)
    {
        $this->dueDate = $dueDate;
    }

    /**
     * @return \DateTime
     */
    public function getDueDate()
    {
        return $this->dueDate;
    }

    /**
     * @param \DateTime $fullyPaidAt
     */
    private function setFullyPaidAt(\DateTime $fullyPaidAt)
    {
        $this->fullyPaidAt = $fullyPaidAt;
    }

    /**
     * @return \DateTime
     */
    public function getFullyPaidAt()
    {
        return $this->fullyPaidAt;
    }

    /**
     * @param \DateTime $generated
     */
    private function setGenerated($generated)
    {
        $this->generated = $generated;
    }

    /**
     * @return \DateTime
     */
    public function getGenerated()
    {
        return $this->generated;
    }

    /**
     * @param History $history
     */
    private function addHistory(History $history)
    {
        $this->history[] = $history;
    }

    /**
     * @param array $history
     */
    public function setHistory($history)
    {
        $this->history = $history;
    }

    /**
     * @return array
     */
    public function getHistory()
    {
        return $this->history;
    }

    /**
     * @param int $id
     */
    private function setId($id)
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
     * @param string $iid
     */
    private function setIid($iid)
    {
        $this->iid = $iid;
    }

    /**
     * @return string
     */
    public function getIid()
    {
        return $this->iid;
    }

    /**
     * @param Item $item
     */
    public function addItem(Item $item)
    {
        $this->items[] = $item;
    }

    /**
     * @param array $items
     */
    public function setItems($items)
    {
        $this->items = $items;
    }

    /**
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Add a payment
     *
     * @param Payment $payment
     */
    private function addPayment(Payment $payment)
    {
        $this->payments[] = $payment;
    }

    /**
     * @return array
     */
    public function getPayments()
    {
        return $this->payments;
    }

    /**
     * @param string $shownRemark
     */
    public function setShownRemark($shownRemark)
    {
        $this->shownRemark = $shownRemark;
    }

    /**
     * @return string
     */
    public function getShownRemark()
    {
        return $this->shownRemark;
    }

    /**
     * @param string $state Possible states are: paid, sent, created
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param string $paymentMethod Possible payment methods are:
     *  not_paid, cheque, transfer, bankcontact, cash, direct_debit and paid
     */
    public function setPaymentMethod($paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;
    }

    /**
     * @return string
     */
    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }

    /**
     * @param float $total
     */
    private function setTotal($total)
    {
        $this->total = $total;
    }

    /**
     * @return float
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @param string $language
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @return float
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * @param float $discount
     * @return self
     */
    public function setDiscount($discount)
    {
        $this->discount = $discount;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isDiscountAPercentage()
    {
        return $this->discountIsPercentage;
    }

    /**
     * @param boolean $discountIsPercentage
     * @return self
     */
    public function setDiscountIsPercentage($discountIsPercentage)
    {
        $this->discountIsPercentage = $discountIsPercentage;

        return $this;
    }

    /**
     * @return string
     */
    public function getDiscountDescription()
    {
        return $this->discountDescription;
    }

    /**
     * @param string $discountDescription
     * @return self
     */
    public function setDiscountDescription($discountDescription)
    {
        $this->discountDescription = $discountDescription;

        return $this;
    }

    public function prepareForSending()
    {
        $this->prepareForSending = true;
    }

    /**
     * @return string
     */
    public function getVatException()
    {
        return $this->vatException;
    }

    /**
     * @param string $vatException
     * @return $this
     */
    public function setVatException($vatException)
    {
        $this->vatException = $vatException;

        return $this;
    }

    /**
     * @return string
     */
    public function getVatDescription()
    {
        return $this->vatDescription;
    }

    /**
     * @param string $vatDescription
     * @return $this
     */
    public function setVatDescription($vatDescription)
    {
        $this->vatDescription = $vatDescription;

        return $this;
    }

    /**
     * Initialize the object with raw data
     *
     * @param $data
     * @return Invoice
     */
    public static function initializeWithRawData($data)
    {
        $item = new Invoice();

        if(isset($data['id'])) $item->setId($data['id']);
        if(isset($data['client_id'])) $item->setClientId($data['client_id']);
        if(isset($data['iid'])) $item->setIid($data['iid']);
        if(isset($data['state'])) $item->setState($data['state']);
        if(isset($data['payment_method'])) $item->setPaymentMethod($data['payment_method']);
        if(isset($data['generated'])) $item->setGenerated(new \DateTime('@' . strtotime($data['generated'])));
        if(isset($data['description'])) $item->setDescription($data['description']);
        if(isset($data['shown_remark'])) $item->setShownRemark($data['shown_remark']);
        if (isset($data['items'])) {
            foreach ($data['items'] as $row) {
                $item->addItem(Item::initializeWithRawData($row));
            }
        }
        if (isset($data['payments'])) {
            foreach ($data['payments'] as $row) {
                $item->addPayment(Payment::initializeWithRawData($row));
            }
        }
        if(isset($data['total'])) $item->setTotal($data['total']);
        if(isset($data['discount'])) $item->setDiscount($data['discount']);
        if(isset($data['percentage'])) $item->setDiscountIsPercentage($data['percentage']);
        if(isset($data['discount_description'])) $item->setDiscountDescription($data['discount_description']);
        if(isset($data['due_date'])) $item->setDueDate(new \DateTime('@' . strtotime($data['due_date'])));
        if(isset($data['fully_paid_at'])) $item->setFullyPaidAt(new \DateTime('@' . strtotime($data['fully_paid_at'])));
        if (isset($data['history'])) {
            foreach ($data['history'] as $row) {
                $item->addHistory(History::initializeWithRawData($row));
            }
        }
        if (isset($data['language'])) {
            $item->setLanguage($data['language']);
        }

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
        $data['client_id'] = $this->getClientId();
        $data['state'] = $this->getState();
        $data['payment_method'] = $this->getPaymentMethod();
        $data['description'] = $this->getDescription();
        $data['shown_remark'] = $this->getShownRemark();
        $discount = $this->getDiscount();
        if (!empty($discount)) {
            $data['discount'] = $this->getDiscount();
            $data['percentage'] = $this->isDiscountAPercentage();
            $data['discount_description'] = $this->getDiscountDescription();
        }
        if ($this->getVatException()) {
            $data['vat_exception'] = $this->getVatException();
        }
        if ($this->getVatDescription()) {
            $data['vat_description'] = $this->getVatDescription();
        }

        $data['items'] = array();
        foreach ($this->getItems() as $item) {
            $data['items'][] = $item->toArray($forApi);
        }
        $data['language'] = $this->getLanguage();

        if (!$forApi) {
            $data['id'] = $this->getId();
            $data['idd'] = $this->getIid();
            $data['generated'] = $this->getGenerated()->getTimestamp();
            $data['due_date'] = $this->getDueDate()->getTimestamp();
            $data['payments'] = array();
            foreach ($this->getPayments() as $payment) {
                $data['payments'][] = $payment->toArray($forApi);
            }
            $data['total'] = $this->getTotal();
            $data['client'] = $this->getClient()->toArray($forApi);
            $data['history'] = array();
            foreach ($this->getHistory() as $history) {
                $data['history'][] = $history->toArray($forApi);
            }
        } else {
            $data['prepare_for_sending'] = $this->prepareForSending;
        }

        return $data;
    }
}
