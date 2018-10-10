<?php
namespace DmServer;

use Swift_Mime_SimpleMessage;
use Swift_Spool;

class SpoolStub implements Swift_Spool
{
    private $messages = [];
    public $hasFlushed = false;

    public function getMessages()
    {
        return $this->messages;
    }

    public function start()
    {
    }

    public function stop()
    {
    }

    public function isStarted()
    {
        return count($this->messages) > 0;
    }

    public function queueMessage(Swift_Mime_SimpleMessage $message)
    {
        $this->messages[] = clone $message;
    }

    public function flushQueue(\Swift_Transport $transport, &$failedRecipients = null)
    {
        // Prevent flushing
    }
}
