<?php
namespace App\Helper;

use App\Entity\Dm\Users;
use RuntimeException;
use Swift_Mailer;

abstract class EmailHelper {

    /** @var Swift_Mailer $mailer */
    private $mailer;

    /** @var Users $user */
    protected $user;

    public function __construct(Swift_Mailer $mailer, Users $user)
    {
        $this->mailer = $mailer;
        $this->user = $user;
    }

    abstract protected function getFrom() : string;
    abstract protected function getFromName() : string;
    abstract protected function getTo() : string;
    abstract protected function getToName() : string;
    abstract protected function getSubject() : string;
    abstract protected function getTextBody() : string;
    abstract protected function getHtmlBody() : string;
    abstract public function __toString() : string;

    /**
     * @throws RuntimeException
     */
    public function send(): void
    {
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
        $message->setTo($_ENV['SMTP_USERNAME']);
        $this->mailer->send($message);
    }
}
