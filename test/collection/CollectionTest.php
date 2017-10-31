<?php
namespace DmServer\Test;

use Dm\Models\Achats;
use Dm\Models\BibliothequeAccesExternes;
use Dm\Models\BibliothequeOrdreMagazines;
use Dm\Models\Numeros;

use DmServer\Controllers\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use DmServer\DmServer;

class CollectionTest extends TestCommon
{
    public function testAddIssue() {
        $this->assertCount(0, $this->getCurrentUserIssues());

        self::createTestCollection(self::$defaultTestDmUserName); // Creates a collection with 3 issues

        $response = $this->buildAuthenticatedServiceWithTestUser('/collection/issues', TestCommon::$dmUser, 'POST', [
            'country' => 'fr',
            'publication' => 'DDD',
            'issuenumbers' => ['3'],
            'condition' => 'bon'
        ])->call();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertCount(4, $this->getCurrentUserIssues());

        $userIssues = $this->getCurrentUserIssues();
        /** @var Numeros $lastIssue */
        $lastIssue = $userIssues[count($userIssues) -1];
        $this->assertEquals('fr', $lastIssue->getPays());
        $this->assertEquals('DDD', $lastIssue->getMagazine());
        $this->assertEquals('3', $lastIssue->getNumero());
        $this->assertEquals('bon', $lastIssue->getEtat());
        $this->assertEquals(-2, $lastIssue->getIdAcquisition());
        $this->assertEquals(false, $lastIssue->getAv());
        $this->assertEquals(AbstractController::getSessionUser($this->app)['id'], $lastIssue->getIdUtilisateur());
        $this->assertEquals(date('Y-m-d'), $lastIssue->getDateajout()->format('Y-m-d'));
    }

    public function testUpdateCollectionCreateIssueWithOptions() {
        self::createTestCollection(self::$defaultTestDmUserName);

        $country = 'fr';
        $publication = 'DDD';
        $issueToUpdate = '1';

        $response = $this->buildAuthenticatedServiceWithTestUser('/collection/issues', TestCommon::$dmUser, 'POST', [
            'country' => $country,
            'publication' => $publication,
            'issuenumbers' => [$issueToUpdate],
            'condition' => 'bon',
            'istosell' => '1',
            'purchaseid' => '2'
        ])->call();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $responseObject = json_decode($response->getContent());
        $this->assertNotNull($responseObject);

        $this->assertEquals('UPDATE', $responseObject[0]->action);
        $this->assertEquals(1, $responseObject[0]->numberOfIssues);

        $userIssues = $this->getCurrentUserIssues();
        $this->assertCount(3, $userIssues);

        /** @var Numeros $updatedIssue */
        $updatedIssue = DmServer::getEntityManager(DmServer::CONFIG_DB_KEY_DM)->getRepository(Numeros::class)->findOneBy(
            ['idUtilisateur' => AbstractController::getSessionUser($this->app)['id'], 'pays' => $country, 'magazine' => $publication, 'numero' => $issueToUpdate]
        );
        $this->assertEquals('fr', $updatedIssue->getPays());
        $this->assertEquals('DDD', $updatedIssue->getMagazine());
        $this->assertEquals('1', $updatedIssue->getNumero());
        $this->assertEquals('bon', $updatedIssue->getEtat());
        $this->assertEquals(2, $updatedIssue->getIdAcquisition());
        $this->assertEquals(true, $updatedIssue->getAv());
        $this->assertEquals(AbstractController::getSessionUser($this->app)['id'], $updatedIssue->getIdUtilisateur());
        $this->assertEquals(date('Y-m-d'), $updatedIssue->getDateajout()->format('Y-m-d'));
    }

    public function testDeleteFromCollection() {
        $collectionUserInfo = self::createTestCollection();
        self::setSessionUser($this->app, $collectionUserInfo);

        $response = $this->buildAuthenticatedServiceWithTestUser('/collection/issues', TestCommon::$dmUser, 'POST', [
            'country' => 'fr',
            'publication' => 'DDD',
            'issuenumbers' => ['1'],
            'condition' => 'non_possede',
        ])->call();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $responseObject = json_decode($response->getContent());
        $this->assertNotNull($responseObject);

        $this->assertEquals('DELETE', $responseObject[0]->action);
        $this->assertEquals(1, $responseObject[0]->numberOfIssues);
    }

    public function testUpdateCollectionCreateAndUpdateIssue() {
        $collectionUserInfo = self::createTestCollection();
        self::setSessionUser($this->app, $collectionUserInfo);

        $country = 'fr';
        $publication = 'DDD';
        $issueToUpdate = '1';
        $issueToCreate = '3';

        $response = $this->buildAuthenticatedServiceWithTestUser('/collection/issues', TestCommon::$dmUser, 'POST', [
            'country' => $country,
            'publication' => $publication,
            'issuenumbers' => [$issueToUpdate, $issueToCreate],
            'condition' => 'bon',
        ])->call();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $responseObject = json_decode($response->getContent());
        $this->assertNotNull($responseObject);

        $this->assertEquals('UPDATE', $responseObject[0]->action);
        $this->assertEquals(1, $responseObject[0]->numberOfIssues);

        /** @var Numeros $updatedIssue */
        $updatedIssue = DmServer::getEntityManager(DmServer::CONFIG_DB_KEY_DM)->getRepository(Numeros::class)->findOneBy(
            ['idUtilisateur' => AbstractController::getSessionUser($this->app)['id'], 'pays' => $country, 'magazine' => $publication, 'numero' => $issueToUpdate]
        );
        $this->assertNotNull($updatedIssue);
        $this->assertEquals('bon', $updatedIssue->getEtat());
        $this->assertEquals('-2', $updatedIssue->getIdAcquisition());
        $this->assertFalse($updatedIssue->getAv());

        $this->assertEquals('CREATE', $responseObject[1]->action);
        $this->assertEquals(1, $responseObject[1]->numberOfIssues);

        /** @var Numeros $createdIssue */
        $createdIssue = DmServer::getEntityManager(DmServer::CONFIG_DB_KEY_DM)->getRepository(Numeros::class)->findOneBy(
            ['idUtilisateur' => AbstractController::getSessionUser($this->app)['id'], 'pays' => $country, 'magazine' => $publication, 'numero' => $issueToCreate]
        );
        $this->assertNotNull($createdIssue);
        $this->assertEquals('bon', $createdIssue->getEtat());
        $this->assertEquals('-2', $createdIssue->getIdAcquisition());
        $this->assertFalse($createdIssue->getAv());
    }

    public function testFetchCollection() {
        $collectionUserInfo = self::createTestCollection('dm_test_user');
        self::setSessionUser($this->app, $collectionUserInfo);

        self::createCoaData();

        $response = $this->buildAuthenticatedServiceWithTestUser('/collection/issues', TestCommon::$dmUser)->call();

        $objectResponse = json_decode($response->getContent());

        $this->assertInternalType('object', $objectResponse);

        $this->assertInternalType('object', $objectResponse->static);
        $this->assertInternalType('object', $objectResponse->static->pays);
        $this->assertEquals('France', $objectResponse->static->pays->fr);

        $this->assertInternalType('object', $objectResponse->static->magazines);
        $this->assertEquals('Dynastie', $objectResponse->static->magazines->{'fr/DDD'});
        $this->assertEquals('Parade', $objectResponse->static->magazines->{'fr/MP'});

        $this->assertInternalType('object', $objectResponse->numeros);
        $this->assertInternalType('array', $objectResponse->numeros->{'fr/DDD'});
        $this->assertEquals('1', $objectResponse->numeros->{'fr/DDD'}[0]->numero);
        $this->assertEquals('indefini', $objectResponse->numeros->{'fr/DDD'}[0]->etat);

        $this->assertInternalType('array', $objectResponse->numeros->{'fr/MP'});
        $this->assertEquals('300', $objectResponse->numeros->{'fr/MP'}[0]->numero);
        $this->assertEquals('bon', $objectResponse->numeros->{'fr/MP'}[0]->etat);
        $this->assertEquals('301', $objectResponse->numeros->{'fr/MP'}[1]->numero);
        $this->assertEquals('mauvais', $objectResponse->numeros->{'fr/MP'}[1]->etat);
    }

    public function testUpdatePurchase()
    {
        $collectionUserInfo = self::createTestCollection('dm_test_user');
        self::setSessionUser($this->app, $collectionUserInfo);

        $purchaseToUpdate = $collectionUserInfo['purchaseIds'][0];

        $this->buildAuthenticatedServiceWithTestUser("/collection/purchases/$purchaseToUpdate", TestCommon::$dmUser, 'POST', [
            'date' => '2017-01-01',
            'description' => 'New description'
        ])->call();

        /** @var Achats $updatedPurchase */
        $updatedPurchase = DmServer::getEntityManager(DmServer::CONFIG_DB_KEY_DM)->getRepository(Achats::class)->find(
            $purchaseToUpdate
        );

        $this->assertEquals(\DateTime::createFromFormat('Y-m-d H:i:s', '2017-01-01 00:00:00'), $updatedPurchase->getDate());
        $this->assertEquals('New description', $updatedPurchase->getDescription());
    }

    public function testUpdatePurchaseOfOtherUser()
    {
        $collectionUserInfo = self::createTestCollection('dm_test_user');
        self::setSessionUser($this->app, $collectionUserInfo);

        $response = $this->buildAuthenticatedServiceWithTestUser("/collection/purchases/3", TestCommon::$dmUser, 'POST', [
            'date' => '2017-01-01',
            'description' => 'New description'
        ])->call();

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testCreateExternalAccess()
    {
        $collectionUserInfo = self::createTestCollection('dm_test_user');
        self::setSessionUser($this->app, $collectionUserInfo);

        $response = $this->buildAuthenticatedServiceWithTestUser("/collection/externalaccess", TestCommon::$dmUser, 'PUT')->call();
        $objectResponse = json_decode($response->getContent());

        $this->assertObjectHasAttribute('key', $objectResponse);
        $this->assertRegExp('#[a-zA-Z]+#', $objectResponse->key);
    }

    public function testGetExternalAccess()
    {
        $collectionUserInfo = self::createTestCollection('dm_test_user');
        self::setSessionUser($this->app, $collectionUserInfo);

        $creationResponse = $this->buildAuthenticatedServiceWithTestUser("/collection/externalaccess", TestCommon::$dmUser, 'PUT')->call();
        $key = json_decode($creationResponse->getContent())->key;

        $getResponse = $this->buildAuthenticatedServiceWithTestUser("/collection/externalaccess/$key", TestCommon::$dmUser)->call();
        $objectResponse = json_decode($getResponse->getContent());

        $this->assertCount(1, $objectResponse);
        /** @var BibliothequeAccesExternes $access */
        $access = unserialize($objectResponse[0]);
        $this->assertEquals(1, $access->getIdUtilisateur());
    }

    public function testGetExternalAccessNotExisting()
    {
        $collectionUserInfo = self::createTestCollection('dm_test_user');
        self::setSessionUser($this->app, $collectionUserInfo);

        $getResponse = $this->buildAuthenticatedServiceWithTestUser("/collection/externalaccess/123", TestCommon::$dmUser)->call();
        $objectResponse = json_decode($getResponse->getContent());

        $this->assertCount(0, $objectResponse);
    }

    public function testGetBookcaseSorts()
    {
        $collectionUserInfo = self::createTestCollection('dm_test_user');
        self::setSessionUser($this->app, $collectionUserInfo);

        $getResponse = $this->buildAuthenticatedServiceWithTestUser("/collection/bookcase/sort", TestCommon::$dmUser)->call();
        $objectResponse = json_decode($getResponse->getContent());

        $this->assertCount(2, $objectResponse);
        /** @var BibliothequeOrdreMagazines $order */
        $order1 = unserialize($objectResponse[0]);
        $this->assertEquals('fr', $order1->getPays());
        $this->assertEquals('DDD', $order1->getMagazine());
        $this->assertEquals(1, $order1->getOrdre());

        $order2 = unserialize($objectResponse[1]);
        $this->assertEquals('fr', $order2->getPays());
        $this->assertEquals('JM', $order2->getMagazine());
        $this->assertEquals(2, $order2->getOrdre());
    }
}
