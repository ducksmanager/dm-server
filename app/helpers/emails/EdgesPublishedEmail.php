<?php
namespace DmServer\Emails;

use Dm\Models\Users;
use DmServer\DmServer;
use Silex\Application\TranslationTrait;
use Swift_Mailer;

class EdgesPublishedEmail extends EmailHelper {

    private $extraEdges;
    private $extraPhotographerPoints;
    private $translator;
    private $newMedalLevel;

    public function __construct(Swift_Mailer $mailer, TranslationTrait $translator, Users $user, int $extraEdges, int $extraPhotographerPoints, $newMedalLevel = null)
    {
        parent::__construct($mailer, $user);
        $this->translator = $translator;
        $this->extraEdges = $extraEdges;
        $this->extraPhotographerPoints = $extraPhotographerPoints;
        $this->newMedalLevel = $newMedalLevel;
    }

    function getFrom()
    {
        return [DmServer::$settings['smtp_username']];
    }

    function getTo()
    {
        return [$this->user->getEmail()];
    }

    function getSubject()
    {
        return $this->extraEdges > 1
            ? $this->translator->trans('EMAIL_EDGES_PUBLISHED_SUBJECT')
            : $this->translator->trans('EMAIL_ONE_EDGE_PUBLISHED_SUBJECT');
    }

    function getTextBody()
    {
    }

    function getHtmlBody()
    {
        $body = $this->translator->trans('EMAIL_HELLO', ['%userName%', $this->user->getUsername()]);

        $body.= $this->extraEdges > 1
            ? $this->translator->trans('EMAIL_EDGES_PUBLISHED_INTRO', ['%edgeNumber%' => $this->extraEdges])
            : $this->translator->trans('EMAIL_ONE_EDGE_PUBLISHED_INTRO');

        if (!is_null($this->newMedalLevel)) {
            $body.= $this->translator->trans('EMAIL_EDGES_PUBLISHED_MEDAL', ['%medalLevel%' => $this->newMedalLevel]);
        }

        $body.= $this->translator->trans('EMAIL_EDGES_PUBLISHED_POINTS', ['%extraPhotographerPoints%' => $this->extraPhotographerPoints]);

        $body.= $this->translator->trans('EMAIL_CONFIRMATION_SIGNATURE');

        return $body;
    }

    public function __toString()
    {
        return "user {$this->user->getUsername()} suggested a bookcase";
    }


}
