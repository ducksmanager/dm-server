<?php
namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Client;

class TestServiceCallCommon {

    /** @var Client $client  */
    private $client;

    private $path;
    private $userCredentials;
    private $parameters = [];
    private $systemCredentials = [];
    private $clientVersion;
    private $method;
    private $files = [];

    /**
     * @param Client $client
     */
    public function __construct($client)
    {
        $this->client = $client;
    }

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param mixed $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @return mixed
     */
    public function getUserCredentials()
    {
        return $this->userCredentials;
    }

    /**
     * @param mixed $userCredentials
     */
    public function setUserCredentials($userCredentials)
    {
        $this->userCredentials = $userCredentials;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @param array $parameters
     */
    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * @return mixed
     */
    public function getSystemCredentials()
    {
        return $this->systemCredentials;
    }

    /**
     * @param mixed $systemCredentials
     */
    public function setSystemCredentials($systemCredentials)
    {
        $this->systemCredentials = $systemCredentials;
    }

    /**
     * @return mixed
     */
    public function getClientVersion()
    {
        return $this->clientVersion;
    }

    /**
     * @param mixed $clientVersion
     */
    public function setClientVersion($clientVersion)
    {
        $this->clientVersion = $clientVersion;
    }

    /**
     * @return mixed
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param mixed $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * @return mixed
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * @param mixed $files
     */
    public function setFiles($files)
    {
        $this->files = $files;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function call() {
        $path = $this->path;
        if ($this->method === 'GET' && count($this->parameters) > 0) {
            $path .= '/' . implode('/', array_values($this->parameters));
        }
        $headers = $this->systemCredentials;
        if (count($this->userCredentials) > 0) {
            $headers = array_merge($headers, [
                'HTTP_X_DM_USER' => $this->userCredentials['username'],
                'HTTP_X_DM_PASS' => $this->userCredentials['password']
            ]);
        }
        $this->client->request(
            $this->method,
            $path,
            $this->parameters,
            $this->files,
            $headers
        );
        return $this->client->getResponse();
    }
}