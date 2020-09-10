<?php

namespace App\Tests;

use App\Entity\Dm\Numeros;
use App\Entity\Dm\Users;
use App\Tests\Fixtures\DmCollectionFixture;
use App\Tests\Fixtures\EdgesFixture;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

abstract class TestCommon extends WebTestCase {

    use FixturesTrait;

    /** @var KernelBrowser $client  */
    protected static $client;

    protected static string $defaultTestDmUserName = 'dm_test_user';
    public static array $testDmUsers = [
        'dm_test_user' => 'test'
    ];
    protected static string $dmUser = 'ducksmanager';
    protected static string $edgecreatorUser = 'edgecreator';
    protected static string $rawSqlUser = 'rawsql';
    protected static string $adminUser = 'admin';
    protected static string $uploadBase = '/tmp/dm-server';

    public static string $exampleImage = 'cover_example.jpg';

    private static bool $hasLoadedEdgeFixture = false;

    /**
     * @return array
     */
    protected function getEmNamesToCreate() : array  {
        return [];
    }


    protected function setUp() {
        self::$client = self::createClient();
        foreach($this->getEmNamesToCreate() as $emToCreate) {
            $this->loadFixtures([], false, $emToCreate, 'doctrine', ORMPurger::PURGE_MODE_TRUNCATE);
        }
    }

    private static function getSystemCredentials(string $appUser, string $version = '1.3+'): array
    {
        return self::getSystemCredentialsNoVersion($appUser) + [
            'HTTP_X_DM_VERSION' => $version
        ];
    }

    protected static function getSystemCredentialsNoVersion(string $appUser): array
    {
        $rolePassword = $_ENV['ROLE_PASSWORD_'.strtoupper($appUser)];
        return [
            'HTTP_AUTHORIZATION' => 'Basic '.base64_encode(implode(':', [
                $appUser,
                $rolePassword
            ]))
        ];
    }

    protected function buildService(
      string $path, array $userCredentials = [], array $parameters = [], array $systemCredentials = [], string $method = 'POST', array $files = []
    ): TestServiceCallCommon
    {
        self::getClient()->disableReboot();
        $service = new TestServiceCallCommon(self::getClient());
        $service->setPath($path);
        $service->setUserCredentials($userCredentials);
        $service->setParameters($parameters);
        $service->setSystemCredentials($systemCredentials);
        $service->setMethod($method);
        $service->setFiles($files);
        return $service;
    }

    protected function buildAuthenticatedService(string $path, string $appUser, array $userCredentials, array $parameters = [], string $method = 'POST'): TestServiceCallCommon
    {
        return $this->buildService($path, $userCredentials, $parameters, self::getSystemCredentials($appUser), $method);
    }

    protected function buildAuthenticatedServiceWithTestUser(string $path, string $appUser, string $method = 'GET', array $parameters = [], array $files = []): TestServiceCallCommon
    {
        return $this->buildService(
            $path, [
            'username' => self::$defaultTestDmUserName,
            'password' => sha1(self::$testDmUsers[self::$defaultTestDmUserName])
        ], $parameters, self::getSystemCredentials($appUser), $method, $files
        );
    }

    private static function getClient(): KernelBrowser {
        if (!isset(self::$client)) {
            self::$client = static::createClient();
        }
        return self::$client;
    }

    protected static function getPathToFileToUpload(string $fileName) : string {
        return implode(DIRECTORY_SEPARATOR, [__DIR__, 'Fixtures', $fileName]);
    }

    protected function getEm(string $name): EntityManagerInterface
    {
        return $this->getContainer()->get('doctrine')->getManager($name);
    }

    protected function getUser(string $username): Users
    {
        return $this->getEm('dm')->getRepository(Users::class)->findOneBy(compact('username'));
    }

    /**
     * @return Numeros[]
     */
    protected function getUserIssues(string $username): array
    {
        return $this->getEm('dm')
            ->getRepository(Numeros::class)
            ->findBy(['idUtilisateur' => $this->getUser($username)->getId()]);
    }

    protected function loadFixture(string $emName, FixtureInterface $fixture): void
    {
        $loader = new Loader();
        $loader->addFixture($fixture);

        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->getEm($emName), $purger);
        $executor->execute($loader->getFixtures(), true);
    }

    protected function createUserCollection(string $username, array $roles = [], bool $withPublicationSorts = true): void
    {
        if (!self::$hasLoadedEdgeFixture) {
            $this->loadFixtures([ EdgesFixture::class ], true, 'dm');
        }

        DmCollectionFixture::$username = $username;
        DmCollectionFixture::$roles = $roles;
        DmCollectionFixture::$withPublicationSorts = $withPublicationSorts;
        $this->loadFixtures([ DmCollectionFixture::class ], true, 'dm');

        self::$hasLoadedEdgeFixture = true;
    }

    protected function getResponseContent(Response $response): ?string
    {
        if ($response->isSuccessful()) {
            return $response->getContent();
        }

        $this->fail($response->getContent());
        return null;
    }

    protected function assertUnsuccessfulResponse(Response $response, callable $checkCallback): void
    {
        $this->assertFalse($response->isSuccessful());
        $checkCallback($response);
    }
}
