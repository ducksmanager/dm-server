<?php
namespace App\Helper\Email;

use App\Entity\Dm\Users;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class EdgesPublishedWithCreator extends AbstractEmail {

    private int $extraEdges;
    private int $extraCreatorPoints;
    private string $locale;
    private ?int $newMedalLevel;

    public function __construct(TranslatorInterface $translator, string $locale, Users $user, int $extraEdges, int $extraCreatorPoints, ?int $newMedalLevel = null) {
        parent::__construct($translator, $user);
        $this->locale = $locale;
        $this->extraEdges = $extraEdges;
        $this->extraCreatorPoints = $extraCreatorPoints;
        $this->newMedalLevel = $newMedalLevel;
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
        return $this->extraEdges > 1
            ? $this->translator->trans('EMAIL_EDGES_PUBLISHED_WITH_CREATOR_SUBJECT')
            : $this->translator->trans('EMAIL_EDGE_PUBLISHED_WITH_CREATOR_SUBJECT');
    }

    public function getTextBody() : string {
        return '';
    }

    public function getHtmlBody(Environment $twig) : string {
        return $twig->render('emails/edges-published-with-creator.html.twig', [
            'user' => $this->user,
            'newMedalLevel' => $this->newMedalLevel,
            'extraEdges' => $this->extraEdges,
            'extraCreatorPoints' => $this->extraCreatorPoints,
            'locale' => $this->locale,
        ] + $_ENV);
    }

    public function __toString() : string {
        return "user {$this->user->getUsername()}'s edge(s) got published";
    }
}
