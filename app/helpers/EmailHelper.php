<?php
namespace DmServer\Emails;

use Dm\Models\Users;
use DmServer\DmServer;
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

    abstract protected function getFrom();
    abstract protected function getFromName();
    abstract protected function getTo();
    abstract protected function getToName();
    abstract protected function getSubject();
    abstract protected function getTextBody();
    abstract protected function getHtmlBody();
    abstract public function __toString();

    /**
     * @throws RuntimeException
     */
    public function send() {
        $message = new \Swift_Message();
        $message
            ->setSubject($this->getSubject())
            ->setFrom($this->getFrom(), $this->getFromName())
            ->setTo($this->getTo(), $this->getToName())
            ->setBody($this->getHtmlBody(), 'text/html')
            ->addPart($this->getTextBody(), 'text/plain');

        $failures = [];
        if (!$this->mailer->send($message, $failures)) {
            throw new RuntimeException('Can\'t send e-mail \''.$this->__toString().'\': failed with '.print_r($failures, true));
        }

        $message->setSubject('[Sent to '. array_keys($message->getTo())[0] ."] {$message->getSubject()}");
        $message->setTo(DmServer::$settings['smtp_username']);
        $this->mailer->send($message);
    }
}
