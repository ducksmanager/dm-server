<?php
namespace DmServer\Test;

use Dm\Models\Numeros;
use DmServer\AppController;
use Symfony\Component\HttpFoundation\Response;
use Dm\Models\Users;
use DmServer\DmServer;

class CollectionTest extends TestCommon
{
    /**
     * @return null|Response
     */
    private function callAddIssue()
    {
        return $this->buildAuthenticatedServiceWithTestUser('/collection/add', TestCommon::$testUser, 'POST', [
            'country' => 'fr',
            'publication' => 'DDD',
            'issuenumber' => '3',
            'condition' => 'bon'
        ])->call();
    }

    public function testCreateCollection() {
        $response = $this->buildAuthenticatedService('/collection/new', TestCommon::$testUser, [], [
            'username' => 'dm_user',
            'password' => 'dm_pass',
            'password2' => 'dm_pass',
            'email' => 'test@ducksmanager.net'
        ])->call();
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());

        $usersWithUsername = DmServer::getEntityManager(DmServer::CONFIG_DB_KEY_DM)->getRepository(Users::class)->findBy(
            array('username' => 'dm_user')
        );

        $this->assertEquals(1, count($usersWithUsername));
        $this->assertEquals(Users::class, get_class($usersWithUsername[0]));
        $this->assertEquals(sha1('dm_pass'), $usersWithUsername[0]->getPassword());
    }

    public function testCreateCollectionErrorDifferentPasswords() {
        $response = $this->buildAuthenticatedService('/collection/new', TestCommon::$testUser, [], [
            'username' => 'dm_user',
            'password' => 'dm_pass',
            'password2' => 'dm_pass_different',
            'email' => 'test@ducksmanager.net'
        ])->call();
        $this->assertEquals(Response::HTTP_PRECONDITION_FAILED, $response->getStatusCode());
    }

    public function testCreateCollectionErrorShortUsername() {
        $response = $this->buildAuthenticatedService('/collection/new', TestCommon::$testUser, [], [
            'username' => 'dm',
            'password' => 'dm_pass',
            'password2' => 'dm_pass',
            'email' => 'test@ducksmanager.net'
        ])->call();
        $this->assertEquals(Response::HTTP_PRECONDITION_FAILED, $response->getStatusCode());
    }

    public function testCreateCollectionErrorShortPassword() {
        $response = $this->buildAuthenticatedService('/collection/new', TestCommon::$testUser, [], [
            'username' => 'dm_user',
            'password' => 'pass',
            'password2' => 'pass',
            'email' => 'test@ducksmanager.net'
        ])->call();
        $this->assertEquals(Response::HTTP_PRECONDITION_FAILED, $response->getStatusCode());
    }

    public function testCreateCollectionErrorExistingUsername() {
        self::createTestCollection();
        $response = $this->buildAuthenticatedService('/collection/new', TestCommon::$testUser, [], [
            'username' => 'dm_user',
            'password' => 'dm_pass',
            'password2' => 'dm_pass',
            'email' => 'test@ducksmanager.net'
        ])->call();
        $this->assertEquals(Response::HTTP_CONFLICT, $response->getStatusCode());
    }

    public function testAddIssue() {
        $this->assertEquals(0, count($this->getCurrentUserIssues()));

        self::createTestCollection('dm_user'); // Creates a collection with 3 issues

        $response = $this->callAddIssue();

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertEquals(4, count($this->getCurrentUserIssues()));
    }

    public function testDeleteCollection() {
        self::createTestCollection();
        $response = $this->buildAuthenticatedServiceWithTestUser('/collection/update', TestCommon::$testUser, 'POST', [
            'country' => 'fr',
            'publication' => 'DDD',
            'issuenumbers' => ['1'],
            'condition' => 'non_possede',
        ])->call();

        $responseObject = json_decode($response->getContent());
        $this->assertNotNull($responseObject);

        $this->assertEquals('DELETE', $responseObject[0]->action);
        $this->assertEquals(1, $responseObject[0]->numberOfIssues);
    }

    public function testUpdateCollection() {
        self::createTestCollection();

        $country = 'fr';
        $publication = 'DDD';
        $issueToUpdate = '1';
        $issueToCreate = '3';

        $response = $this->buildAuthenticatedServiceWithTestUser('/collection/update', TestCommon::$testUser, 'POST', [
            'country' => $country,
            'publication' => $publication,
            'issuenumbers' => [$issueToUpdate, $issueToCreate],
            'condition' => 'bon',
        ])->call();

        $responseObject = json_decode($response->getContent());
        $this->assertNotNull($responseObject);

        $this->assertEquals('UPDATE', $responseObject[0]->action);
        $this->assertEquals(1, $responseObject[0]->numberOfIssues);

        /** @var Numeros $updatedIssue */
        $updatedIssue = DmServer::getEntityManager(DmServer::CONFIG_DB_KEY_DM)->getRepository(Numeros::class)->findOneBy(
            ['idUtilisateur' => AppController::getSessionUser($this->app)['id'], 'pays' => $country, 'magazine' => $publication, 'numero' => $issueToUpdate]
        );
        $this->assertEquals('bon', $updatedIssue->getEtat());
        $this->assertEquals('-2', $updatedIssue->getIdAcquisition());
        $this->assertFalse($updatedIssue->getAv());

        $this->assertEquals('CREATE', $responseObject[1]->action);
        $this->assertEquals(1, $responseObject[1]->numberOfIssues);

        /** @var Numeros $createdIssue */
        $createdIssue = DmServer::getEntityManager(DmServer::CONFIG_DB_KEY_DM)->getRepository(Numeros::class)->findOneBy(
            ['idUtilisateur' => AppController::getSessionUser($this->app)['id'], 'pays' => $country, 'magazine' => $publication, 'numero' => $issueToCreate]
        );
        $this->assertEquals('bon', $createdIssue->getEtat());
        $this->assertEquals('-2', $createdIssue->getIdAcquisition());
        $this->assertFalse($createdIssue->getAv());
    }

}
