<?php

namespace SumoCoders\Factr\Invoice;

use DateTime;
use Exception;

/**
 * Class History
 *
 * @package SumoCoders\Factr\Invoice
 */
class History
{
    protected DateTime $createdAt;

    protected string $message;

    public function setCreatedAt(DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * Initialize the object with raw data
     * @throws Exception
     */
    public static function initializeWithRawData(array $data): History
    {
        $item = new History();

        if(isset($data['created_at'])) $item->setCreatedAt(new DateTime('@' . strtotime($data['created_at'])));
        if(isset($data['message'])) $item->setMessage($data['message']);

        return $item;
    }

    /**
     * Converts the object into an array
     */
    public function toArray(bool $forApi = false): array
    {
        $data = array();
        $data['created_at'] = $this->getCreatedAt()->getTimestamp();
        $data['message'] = $this->getMessage();

        return $data;
    }
}
