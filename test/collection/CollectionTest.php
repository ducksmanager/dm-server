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
    protected function getEm() {
        return parent::getEntityManagerByName(DmServer::CONFIG_DB_KEY_DM);
    }

    public function testAddIssue() {
        $this->assertCount(0, $this->getCurrentUserIssues());

        self::createTestCollection(self::$defaultTestDmUserName); // Creates a collection with 3 issues

        $response = $this->buildAuthenticatedServiceWithTestUser('/collection/issues', self::$dmUser, 'POST', [
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

        $response = $this->buildAuthenticatedServiceWithTestUser('/collection/issues', self::$dmUser, 'POST', [
            'country' => $country,
            'publication' => $publication,
            'issuenumbers' => [$issueToUpdate],
            'condition' => 'bon',
            'istosell' => '1',
            'purchaseid' => '2'
        ])->call();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $responseObject = json_decode($this->getResponseContent($response));
        $this->assertNotNull($responseObject);

        $this->assertEquals('UPDATE', $responseObject[0]->action);
        $this->assertEquals(1, $responseObject[0]->numberOfIssues);

        $userIssues = $this->getCurrentUserIssues();
        $this->assertCount(3, $userIssues);

        /** @var Numeros $updatedIssue */
        $updatedIssue = $this->getEm()->getRepository(Numeros::class)->findOneBy(
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
        $user = self::createTestCollection();
        self::setSessionUser($this->app, $user);

        $response = $this->buildAuthenticatedServiceWithTestUser('/collection/issues', self::$dmUser, 'POST', [
            'country' => 'fr',
            'publication' => 'DDD',
            'issuenumbers' => ['1'],
            'condition' => 'non_possede',
        ])->call();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $responseObject = json_decode($this->getResponseContent($response));
        $this->assertNotNull($responseObject);

        $this->assertEquals('DELETE', $responseObject[0]->action);
        $this->assertEquals(1, $responseObject[0]->numberOfIssues);
    }

    public function testUpdateCollectionCreateAndUpdateIssue() {
        $user = self::createTestCollection();
        self::setSessionUser($this->app, $user);

        $country = 'fr';
        $publication = 'DDD';
        $issueToUpdate = '1';
        $issueToCreate = '3';

        $response = $this->buildAuthenticatedServiceWithTestUser('/collection/issues', self::$dmUser, 'POST', [
            'country' => $country,
            'publication' => $publication,
            'issuenumbers' => [$issueToUpdate, $issueToCreate],
            'condition' => 'bon',
        ])->call();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $responseObject = json_decode($this->getResponseContent($response));
        $this->assertNotNull($responseObject);

        $this->assertEquals('UPDATE', $responseObject[0]->action);
        $this->assertEquals(1, $responseObject[0]->numberOfIssues);

        /** @var Numeros $updatedIssue */
        $updatedIssue = $this->getEm()->getRepository(Numeros::class)->findOneBy(
            ['idUtilisateur' => AbstractController::getSessionUser($this->app)['id'], 'pays' => $country, 'magazine' => $publication, 'numero' => $issueToUpdate]
        );
        $this->assertNotNull($updatedIssue);
        $this->assertEquals('bon', $updatedIssue->getEtat());
        $this->assertEquals('-2', $updatedIssue->getIdAcquisition());
        $this->assertFalse($updatedIssue->getAv());

        $this->assertEquals('CREATE', $responseObject[1]->action);
        $this->assertEquals(1, $responseObject[1]->numberOfIssues);

        /** @var Numeros $createdIssue */
        $createdIssue = $this->getEm()->getRepository(Numeros::class)->findOneBy(
            ['idUtilisateur' => AbstractController::getSessionUser($this->app)['id'], 'pays' => $country, 'magazine' => $publication, 'numero' => $issueToCreate]
        );
        $this->assertNotNull($createdIssue);
        $this->assertEquals('bon', $createdIssue->getEtat());
        $this->assertEquals('-2', $createdIssue->getIdAcquisition());
        $this->assertFalse($createdIssue->getAv());
    }

    public function testFetchCollection() {
        $user = self::createTestCollection('dm_test_user');
        self::setSessionUser($this->app, $user);

        self::createCoaData();

        $response = $this->buildAuthenticatedServiceWithTestUser('/collection/issues', self::$dmUser)->call();

        $objectResponse = json_decode($this->getResponseContent($response));

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
        $user = self::createTestCollection('dm_test_user');
        self::setSessionUser($this->app, $user);

        /** @var Achats $purchaseToUpdate */
        $purchaseToUpdate = $this->getEm()->getRepository(Achats::class)->findBy([
            'idUser' => $user->getId()
        ])[0];

        $this->buildAuthenticatedServiceWithTestUser(
            "/collection/purchases/{$purchaseToUpdate->getIdAcquisition()}",
            self::$dmUser,
            'POST', [
                'date' => '2017-01-01',
                'description' => 'New description'
            ])->call();

        /** @var Achats $updatedPurchase */
        $updatedPurchase = $this->getEm()->getRepository(Achats::class)->find($purchaseToUpdate);

        $this->assertEquals(\DateTime::createFromFormat('Y-m-d H:i:s', '2017-01-01 00:00:00'), $updatedPurchase->getDate());
        $this->assertEquals('New description', $updatedPurchase->getDescription());
    }

    public function testUpdatePurchaseOfOtherUser()
    {
        $user = self::createTestCollection('dm_test_user');
        self::setSessionUser($this->app, $user);

        $response = $this->buildAuthenticatedServiceWithTestUser('/collection/purchases/3', self::$dmUser, 'POST', [
            'date' => '2017-01-01',
            'description' => 'New description'
        ])->call();

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testCallOptionsService()
    {
        $response = $this->buildAuthenticatedServiceWithTestUser('/collection/purchases/3', self::$dmUser, 'OPTIONS')->call();

        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    public function testCreateExternalAccess()
    {
        $user = self::createTestCollection('dm_test_user');
        self::setSessionUser($this->app, $user);

        $response = $this->buildAuthenticatedServiceWithTestUser('/collection/externalaccess', self::$dmUser, 'PUT')->call();
        $objectResponse = json_decode($this->getResponseContent($response));

        $this->assertObjectHasAttribute('key', $objectResponse);
        $this->assertRegExp('#[a-zA-Z]+#', $objectResponse->key);
    }

    public function testGetExternalAccess()
    {
        $user = self::createTestCollection('dm_test_user');
        self::setSessionUser($this->app, $user);

        $creationResponse = $this->buildAuthenticatedServiceWithTestUser('/collection/externalaccess', self::$dmUser, 'PUT')->call();
        $key = json_decode($creationResponse->getContent())->key;

        $getResponse = $this->buildAuthenticatedServiceWithTestUser("/collection/externalaccess/$key", self::$dmUser)->call();
        $objectResponse = json_decode($getResponse->getContent());

        $this->assertCount(1, $objectResponse);
        /** @var BibliothequeAccesExternes $access */
        $access = unserialize($objectResponse[0]);
        $this->assertEquals(1, $access->getIdUtilisateur());
    }

    public function testGetExternalAccessNotExisting()
    {
        $user = self::createTestCollection('dm_test_user');
        self::setSessionUser($this->app, $user);

        $getResponse = $this->buildAuthenticatedServiceWithTestUser('/collection/externalaccess/123', self::$dmUser)->call();
        $objectResponse = json_decode($getResponse->getContent());

        $this->assertCount(0, $objectResponse);
    }

    public function testGetBookcaseSorts()
    {
        $user = self::createTestCollection('dm_test_user');
        self::setSessionUser($this->app, $user);

        $getResponse = $this->buildAuthenticatedServiceWithTestUser('/collection/bookcase/sort', self::$dmUser)->call();
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

    public function testGetLastPublicationPosition() {
        $user = self::createTestCollection('dm_test_user');
        self::setSessionUser($this->app, $user);

        $getResponse = $this->buildAuthenticatedServiceWithTestUser('/collection/bookcase/sort/max', self::$dmUser)->call();
        $objectResponse = json_decode($getResponse->getContent());

        $this->assertInternalType('int', $objectResponse->max);
        $this->assertEquals(2, $objectResponse->max);
    }

    public function testGetLastPublicationPositionNoPublication() {
        $user = self::createTestCollection('dm_test_user', [], false);
        self::setSessionUser($this->app, $user);

        $getResponse = $this->buildAuthenticatedServiceWithTestUser('/collection/bookcase/sort/max', self::$dmUser)->call();
        $this->assertEquals(Response::HTTP_NO_CONTENT, $getResponse->getStatusCode());
    }
}
