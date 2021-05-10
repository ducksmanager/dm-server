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
    private TranslatorInterface $translator;
    private LoggerInterface $logger;

    public function __construct(Environment $twig, Swift_Mailer $mailer, TranslatorInterface $translator, LoggerInterface $logger)
    {
        $this->twig = $twig;
        $this->mailer = $mailer;
        $this->translator = $translator;
        $this->logger = $logger;
    }

    public function send(AbstractEmail $email): void
    {
        $message = (new Swift_Message())
            ->setSubject($email->getSubject())
            ->setFrom($email->getFrom(), $email->getFromName())
            ->setTo($email->getTo(), $email->getToName())
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
