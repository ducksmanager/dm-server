<?php
namespace DmServer\Emails;

use Dm\Models\Users;
use DmServer\DmServer;
use Swift_Mailer;
use Symfony\Component\Translation\Translator;

class EdgesPublishedEmail extends EmailHelper {

    private $extraEdges;
    private $extraPhotographerPoints;
    private $translator;
    private $newMedalLevel;

    public function __construct(Swift_Mailer $mailer, Translator $translator, Users $user, int $extraEdges, int $extraPhotographerPoints, $newMedalLevel = null) {
        parent::__construct($mailer, $user);
        $this->translator = $translator;
        $this->extraEdges = $extraEdges;
        $this->extraPhotographerPoints = $extraPhotographerPoints;
        $this->newMedalLevel = $newMedalLevel;
    }

    protected function getFrom() {
        return [DmServer::$settings['smtp_username']];
    }

    protected function getFromName() {
        return DmServer::$settings['smtp_friendlyname'];
    }

    protected function getTo() {
        return [$this->user->getEmail()];
    }

    protected function getToName() {
        return $this->user->getUsername();
    }

    protected function getSubject() {
        return $this->extraEdges > 1
            ? $this->translator->trans('EMAIL_EDGES_PUBLISHED_SUBJECT')
            : $this->translator->trans('EMAIL_ONE_EDGE_PUBLISHED_SUBJECT');
    }

    protected function getTextBody() {
    }

    protected function getHtmlBody() {
        return implode('<br />', [
            $this->translator->trans('EMAIL_HELLO', ['%userName%' => $this->user->getUsername()]),

            $this->extraEdges > 1
                ? $this->translator->trans('EMAIL_EDGES_PUBLISHED_INTRO', ['%edgeNumber%' => $this->extraEdges])
                : $this->translator->trans('EMAIL_ONE_EDGE_PUBLISHED_INTRO'),

            !is_null($this->newMedalLevel)
                ? ('<p style="text-align: center"><img width="100" src="'.DmServer::$settings['assets_medals_pictures_root']."Photographe_{$this->newMedalLevel}_{$this->translator->getLocale()}.png".'" /><br />'
                    .$this->translator->trans('EMAIL_EDGES_PUBLISHED_MEDAL', [
                        '%medalLevel%' => $this->translator->trans("MEDAL_{$this->newMedalLevel}")
                    ]). '</p>')
                : '',

            $this->translator->trans('EMAIL_EDGES_PUBLISHED_POINTS', ['%extraPhotographerPoints%' => $this->extraPhotographerPoints]),

            '<br />',

            $this->translator->trans('EMAIL_SIGNATURE'),

            '<img width="400" src="'.DmServer::$settings['website_root'].'logo_petit.png" />'
        ]);
    }

    public function __toString() {
        return "user {$this->user->getUsername()}'s edge(s) got published";
    }
}
