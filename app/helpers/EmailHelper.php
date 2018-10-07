<?php
namespace DmServer\Emails;

use Dm\Models\Users;
use RuntimeException;
use Swift_Mailer;

abstract class EmailHelper {

    /** @var Swift_Mailer $mailer */
    private $mailer;

    /** @var Users $user */
    protected $user;

    /**
     * EmailHelper constructor.
     * @param Swift_Mailer $mailer
     * @param Users $user
     */
    public function __construct($mailer, $user)
    {
        $this->mailer = $mailer;
        $this->user = $user;
    }

    abstract function getFrom();
    abstract function getTo();
    abstract function getSubject();
    abstract function getTextBody();
    abstract function getHtmlBody();
    abstract function __toString();

    /**
     * @throws RuntimeException
     */
    public function send() {
        $message = new \Swift_Message();
        $message
            ->setSubject($this->getSubject())
            ->setFrom($this->getFrom())
            ->setTo($this->getTo())
            ->setBody($this->getHtmlBody(), 'text/html')
            ->addPart($this->getTextBody(), 'text/plain');

        $failures = [];
        if (!$this->mailer->send($message, $failures)) {
            throw new RuntimeException('Can\'t send e-mail \''.$this->__toString().'\': failed with '.print_r($failures, true));
        }
    }
}
