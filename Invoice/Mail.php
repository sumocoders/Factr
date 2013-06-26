<?php
namespace TijsVerkoyen\Factr\Invoice;

class Mail
{
    /**
     * @var array
     */
    protected $bcc, $cc, $to;

    /**
     * @var string
     */
    protected $subject, $text;

    /**
     * @param array $bcc
     */
    public function setBcc($bcc)
    {
        $this->bcc = $bcc;
    }

    /**
     * @return array
     */
    public function getBcc()
    {
        return $this->bcc;
    }

    /**
     * @param array $cc
     */
    public function setCc($cc)
    {
        $this->cc = $cc;
    }

    /**
     * @return array
     */
    public function getCc()
    {
        return $this->cc;
    }

    /**
     * @param string $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param string $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param array $to
     */
    public function setTo($to)
    {
        $this->to = $to;
    }

    /**
     * @return array
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * Initialize the object with raw data
     *
     * @param $data
     * @return Mail
     */
    public static function initializeWithRawData($data)
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
