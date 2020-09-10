<?php
namespace App\Helper\Email;

use App\Entity\Dm\Users;
use Swift_Message;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

abstract class AbstractEmail extends Swift_Message{
    protected TranslatorInterface $translator;
    protected Users $user;

    public function __construct($translator, Users $user)
    {
        parent::__construct();
        $this->translator = $translator;
        $this->user = $user;
    }

    abstract public function getTextBody() : string;
    abstract public function getHtmlBody(Environment $twig) : string;
}
