<?php

namespace SumoCoders\Factr\Invoice;

/**
 * Class Mail
 *
 * @package SumoCoders\Factr\Invoice
 */
class Mail
{
    protected array $to;

    protected array $cc;

    protected array $bcc;

    protected string $subject;

    protected string $text;

    public function setBcc(array $bcc): void
    {
        $this->bcc = $bcc;
    }

    public function getBcc(): array
    {
        return $this->bcc;
    }

    public function setCc(array $cc): void
    {
        $this->cc = $cc;
    }

    public function getCc(): array
    {
        return $this->cc;
    }

    public function setSubject(string $subject): void
    {
        $this->subject = $subject;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function setText(string $text): void
    {
        $this->text = $text;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setTo(array $to): void
    {
        $this->to = $to;
    }

    public function getTo(): array
    {
        return $this->to;
    }

    /**
     * Initialize the object with raw data
     */
    public static function initializeWithRawData(array $data): Mail
    {
        $item = new Mail();

        if(isset($data['bcc'])) $item->setBcc($data['bcc']);
        if(isset($data['cc'])) $item->setCc($data['cc']);
        if(isset($data['subject'])) $item->setSubject($data['subject']);
        if(isset($data['text'])) $item->setText($data['text']);
        if(isset($data['to'])) $item->setTo($data['to']);

        return $item;
    }
}
