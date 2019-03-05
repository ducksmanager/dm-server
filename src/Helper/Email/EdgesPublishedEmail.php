<?php
namespace App\Helper\Email;


use App\Entity\Dm\Users;
use App\Helper\EmailHelper;
use Swift_Mailer;
use Symfony\Contracts\Translation\TranslatorInterface;

class EdgesPublishedEmail extends EmailHelper {

    private $extraEdges;
    private $extraPhotographerPoints;
    private $translator;
    private $newMedalLevel;

    public function __construct(Swift_Mailer $mailer, TranslatorInterface $translator, Users $user, int $extraEdges, int $extraPhotographerPoints, $newMedalLevel = null) {
        parent::__construct($mailer, $user);
        $this->translator = $translator;
        $this->extraEdges = $extraEdges;
        $this->extraPhotographerPoints = $extraPhotographerPoints;
        $this->newMedalLevel = $newMedalLevel;
    }

    protected function getFrom() : string {
        return $_ENV['SMTP_USERNAME'];
    }

    protected function getFromName() : string {
        return $_ENV['SMTP_FRIENDLYNAME'];
    }

    protected function getTo() : string {
        return $this->user->getEmail();
    }

    protected function getToName() : string {
        return $this->user->getUsername();
    }

    protected function getSubject() : string {
        return $this->extraEdges > 1
            ? $this->translator->trans('EMAIL_EDGES_PUBLISHED_SUBJECT')
            : $this->translator->trans('EMAIL_ONE_EDGE_PUBLISHED_SUBJECT');
    }

    protected function getTextBody() : string {
        return '';
    }

    protected function getHtmlBody() : string {
        return implode('<br />', [
            $this->translator->trans('EMAIL_HELLO', ['%userName%' => $this->user->getUsername()]),

            $this->extraEdges > 1
                ? $this->translator->trans('EMAIL_EDGES_PUBLISHED_INTRO', ['%edgeNumber%' => $this->extraEdges])
                : $this->translator->trans('EMAIL_ONE_EDGE_PUBLISHED_INTRO'),

            !is_null($this->newMedalLevel)
                ? ('<p style="text-align: center"><img width="100" src="'.$_ENV['ASSETS_MEDALS_PICTURES_ROOT']."Photographe_{$this->newMedalLevel}_{$this->translator->getLocale()}.png".'" /><br />'
                    .$this->translator->trans('EMAIL_EDGES_PUBLISHED_MEDAL', [
                        '%medalLevel%' => $this->translator->trans("MEDAL_{$this->newMedalLevel}")
                    ]). '</p>')
                : '',

            $this->translator->trans('EMAIL_EDGES_PUBLISHED_POINTS', ['%extraPhotographerPoints%' => $this->extraPhotographerPoints]),

            '<br />',

            $this->translator->trans('EMAIL_SIGNATURE'),

            '<img width="400" src="'.$_ENV['WEBSITE_ROOT'].'logo_petit.png" />'
        ]);
    }

    public function __toString() : string {
        return "user {$this->user->getUsername()}'s edge(s) got published";
    }
}
