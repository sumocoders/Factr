<?php
namespace SumoCoders\Factr\Invoice;

/**
 * Class History
 *
 * @package SumoCoders\Factr\Invoice
 */
class History
{
    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var string
     */
    protected $message;

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
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Initialize the object with raw data
     *
     * @param $data
     * @return History
     */
    public static function initializeWithRawData($data)
    {
        $item = new History();

        if(isset($data['created_at'])) $item->setCreatedAt(new \DateTime('@' . strtotime($data['created_at'])));
        if(isset($data['message'])) $item->setMessage($data['message']);

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
        $data['created_at'] = $this->getCreatedAt()->getTimestamp();
        $data['message'] = $this->getMessage();

        return $data;
    }
}
