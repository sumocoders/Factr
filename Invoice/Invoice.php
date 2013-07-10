<?php
namespace SumoCoders\Factr\Invoice;

use SumoCoders\Factr\Client\Client;
use SumoCoders\Factr\Factr;

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
    protected $iid, $state, $description, $shownRemark;

    /**
     * @var \DateTime
     */
    protected $generated, $dueDate;

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
        if(isset($data['due_date'])) $item->setDueDate(new \DateTime('@' . strtotime($data['due_date'])));
        if (isset($data['history'])) {
            foreach ($data['history'] as $row) {
                $item->addHistory(History::initializeWithRawData($row));
            }
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
        $data['description'] = $this->getDescription();
        $data['shown_remark'] = $this->getShownRemark();
        $data['items'] = array();
        foreach ($this->getItems() as $item) {
            $data['items'][] = $item->toArray($forApi);
        }

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
        }

        return $data;
    }
}
