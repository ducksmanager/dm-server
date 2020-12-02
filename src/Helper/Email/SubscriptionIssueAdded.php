<?php
namespace App\Helper\Email;

use App\Entity\Dm\Users;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class SubscriptionIssueAdded extends AbstractEmail {
    private string $publicationName;
    private string $issueNumber;

    public function __construct(TranslatorInterface $translator, Users $user, string $publicationName, string $issueNumber) {
        parent::__construct($translator, $user);
        $this->publicationName = $publicationName;
        $this->issueNumber = $issueNumber;
    }

    public function getFrom() : string {
        return $_ENV['SMTP_USERNAME'];
    }

    public function getFromName() : string {
        return $_ENV['SMTP_FRIENDLYNAME'];
    }

    public function getTo() : string {
        return $this->user->getEmail();
    }

    public function getToName() : string {
        return $this->user->getUsername();
    }

    public function getSubject() : string {
        return $this->translator->trans('EMAIL_SUBSCRIPTION_ISSUE_ADDED', [
            '%publicationName%' => $this->publicationName,
            '%issueNumber%' => $this->issueNumber,
        ]);
    }

    public function getTextBody() : string {
        return '';
    }

    public function getHtmlBody(Environment $twig) : string {
        return $twig->render('emails/subscription-issue-added.twig', [
            'user' => $this->user,
            'publicationName' => $this->publicationName,
            'issueNumber' => $this->issueNumber,
        ] + $_ENV);
    }

    public function __toString() : string {
        return "user {$this->user->getUsername()} received issue {$this->publicationName} {$this->issueNumber} from a subscription";
    }
}
