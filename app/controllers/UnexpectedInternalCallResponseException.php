<?php

namespace DmServer\Controllers;

class UnexpectedInternalCallResponseException extends \Exception
{
    private $content;
    private $statusCode;

    /**
     * UnexpectedInternalCallResponseException constructor.
     * @param string $content
     * @param string $statusCode
     */
    public function __construct($content, $statusCode)
    {
        parent::__construct($content, $statusCode);
        $this->content = $content;
        $this->statusCode = $statusCode;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @return string
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }
}
