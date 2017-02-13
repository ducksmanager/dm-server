<?php
namespace DmServer\Test;

use Dm\Models\Numeros;
use DmServer\Controllers\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use DmServer\DmServer;

class CollectionTest extends TestCommon
{
    public function testAddIssue() {
        $this->assertEquals(0, count($this->getCurrentUserIssues()));

        self::createTestCollection(self::$defaultTestDmUserName); // Creates a collection with 3 issues

        $response = $this->buildAuthenticatedServiceWithTestUser('/collection/issues', TestCommon::$dmUser, 'POST', [
            'country' => 'fr',
            'publication' => 'DDD',
            'issuenumbers' => ['3'],
            'condition' => 'bon'
        ])->call();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals(4, count($this->getCurrentUserIssues()));
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

    public function testUpdateCollection() {
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

}
