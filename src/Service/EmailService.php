<?php
namespace App\Service;

use App\Helper\Email\AbstractEmail;
use Psr\Log\LoggerInterface;
use Swift_Mailer;
use Swift_Message;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class EmailService
{
    private Environment $twig;
    private Swift_Mailer $mailer;
    private LoggerInterface $logger;

    public function __construct(Environment $twig, Swift_Mailer $mailer, LoggerInterface $logger)
    {
        $this->twig = $twig;
        $this->mailer = $mailer;
        $this->logger = $logger;
    }

    public function send(AbstractEmail $email): void
    {
        $from = str_replace(' ', '__', $email->getFrom());
        $to = str_replace(' ', '__', $email->getTo());
        $message = (new Swift_Message())
            ->setSubject($email->getSubject())
            ->setFrom($from, $email->getFromName())
            ->setTo($to, $email->getToName())
            ->setBody($email->getHtmlBody($this->twig), 'text/html')
            ->addPart($email->getTextBody(), 'text/plain');

        $to = array_keys($message->getTo())[0];

        $failures = [];
        $this->logger->info('Sending email of type ' .get_class($email). " to $to");
        if (!$this->mailer->send($message, $failures)) {
            $this->logger->error("Can't send e-mail '$email': failed with ".print_r($failures, true));
        }

        if ($email->getTo() !== $_ENV['SMTP_USERNAME']) {
            $message->setSubject("[Sent to $to] {$message->getSubject()}");
            $message->setTo($_ENV['SMTP_USERNAME']);
            $this->logger->info('Sending email of type ' .get_class($email). ' to ' .$_ENV['SMTP_USERNAME']);
            if (!$this->mailer->send($message, $failures)) {
                $this->logger->error("Can't send e-mail '$email': failed with ".print_r($failures, true));
            }
        }
    }
}
