<?php
namespace App\Helper\Email;

use App\Entity\Dm\Users;
use Psr\Log\LoggerInterface;
use Swift_Mailer;
use Swift_Message;

abstract class AbstractEmail {

    /** @var Swift_Mailer $mailer */
    private $mailer;

    /** @var Users $user */
    protected $user;

    /** @var LoggerInterface $logger */
    protected $logger;

    public function __construct(Swift_Mailer $mailer, Users $user, LoggerInterface $logger)
    {
        $this->mailer = $mailer;
        $this->user = $user;
        $this->logger = $logger;
    }

    abstract protected function getFrom() : string;
    abstract protected function getFromName() : string;
    abstract public function getTo() : string;
    abstract protected function getToName() : string;
    abstract public function getSubject() : string;
    abstract protected function getTextBody() : string;
    abstract protected function getHtmlBody() : string;
    abstract public function __toString() : string;

    public function send(): void
    {
        $message = new Swift_Message();
        $message
            ->setSubject($this->getSubject())
            ->setFrom($this->getFrom(), $this->getFromName())
            ->setTo($this->getTo(), $this->getToName())
            ->setBody($this->getHtmlBody(), 'text/html')
            ->addPart($this->getTextBody(), 'text/plain');

        $failures = [];
        $this->logger->info('Sending email of type ' .get_class($this). ' to ' .$this->getTo());
        if (!$this->mailer->send($message, $failures)) {
            $this->logger->error("Can't send e-mail '". $this ."': failed with ".print_r($failures, true));
        }

        $message->setSubject('[Sent to '. array_keys($message->getTo())[0] ."] {$message->getSubject()}");
        $message->setTo($_ENV['SMTP_USERNAME']);
        $this->logger->info('Sending email of type ' .get_class($this). ' to ' .$_ENV['SMTP_USERNAME']);
        if (!$this->mailer->send($message, $failures)) {
            $this->logger->error("Can't send e-mail '". $this ."': failed with ".print_r($failures, true));
        }
    }
}
