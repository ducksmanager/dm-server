<?php
namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class TestServiceCallCommon {

    private KernelBrowser $client;

    private $path;
    private array $userCredentials;
    private array $parameters = [];
    private array $systemCredentials = [];
    private string $clientVersion;
    private string $method;
    private array $files = [];

    public function __construct(KernelBrowser $client)
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

    public function setPath(string $path): void {
        $this->path = $path;
    }

    public function getUserCredentials() : array
    {
        return $this->userCredentials;
    }

    public function setUserCredentials(array $userCredentials): void {
        $this->userCredentials = $userCredentials;
    }

    public function getParameters(): array {
        return $this->parameters;
    }

    public function setParameters(array $parameters): void {
        $this->parameters = $parameters;
    }

    public function getSystemCredentials() : array
    {
        return $this->systemCredentials;
    }

    public function setSystemCredentials(array $systemCredentials): void {
        $this->systemCredentials = $systemCredentials;
    }

    public function getClientVersion() : string
    {
        return $this->clientVersion;
    }

    public function setClientVersion(string $clientVersion): void {
        $this->clientVersion = $clientVersion;
    }

    public function getMethod() : string
    {
        return $this->method;
    }

    public function setMethod(string $method): void {
        $this->method = $method;
    }

    public function getFiles() : array
    {
        return $this->files;
    }

    public function setFiles(array $files): void {
        $this->files = $files;
    }

    public function call(): object
    {
        $headers = $this->systemCredentials;
        if (count($this->userCredentials) > 0) {
            $headers = array_merge($headers, [
                'HTTP_X_DM_USER' => $this->userCredentials['username'],
                'HTTP_X_DM_PASS' => $this->userCredentials['password']
            ]);
        }
        $this->client->request(
            $this->method,
            $this->path,
            $this->parameters,
            $this->files,
            $headers
        );
        return $this->client->getResponse();
    }
}
