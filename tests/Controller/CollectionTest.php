<?php
namespace App\Tests\Controller;

use App\Entity\Dm\Achats;
use App\Entity\Dm\BibliothequeOrdreMagazines;
use App\Entity\Dm\Numeros;
use App\Tests\TestCommon;
use Symfony\Component\HttpFoundation\Response;

class CollectionTest extends TestCommon
{
    protected function getEmNamesToCreate(): array
    {
        return ['dm', 'coa'];
    }

    public function testAddIssue(): void
    {
        $this->createUserCollection('dm_test_user');

        $response = $this->buildAuthenticatedServiceWithTestUser('/collection/issues', self::$dmUser, 'POST', [
            'country' => 'fr',
            'publication' => 'DDD',
            'issuenumbers' => ['3'],
            'condition' => 'bon'
        ])->call();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertCount(4, $this->getUserIssues(self::$defaultTestDmUserName));

        $userIssues = $this->getUserIssues(self::$defaultTestDmUserName);
        /** @var Numeros $lastIssue */
        $lastIssue = $userIssues[count($userIssues) -1];
        $this->assertEquals('fr', $lastIssue->getPays());
        $this->assertEquals('DDD', $lastIssue->getMagazine());
        $this->assertEquals('3', $lastIssue->getNumero());
        $this->assertEquals('bon', $lastIssue->getEtat());
        $this->assertEquals(-2, $lastIssue->getIdAcquisition());
        $this->assertEquals(false, $lastIssue->getAv());
        $this->assertEquals($this->getUser(self::$defaultTestDmUserName)->getId(), $lastIssue->getIdUtilisateur());
        $this->assertEquals(date('Y-m-d'), $lastIssue->getDateajout()->format('Y-m-d'));
    }

    public function testUpdateCollectionCreateIssueWithOptions(): void
    {
        $this->createUserCollection('dm_test_user');

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

        $userIssues = $this->getUserIssues(self::$defaultTestDmUserName);
        $this->assertCount(3, $userIssues);

        /** @var Numeros $updatedIssue */
        $updatedIssue = $this->getEm('dm')->getRepository(Numeros::class)->findOneBy(
            ['idUtilisateur' => $this->getUser('dm_test_user')->getId(), 'pays' => $country, 'magazine' => $publication, 'numero' => $issueToUpdate]
        );
        $this->assertEquals('fr', $updatedIssue->getPays());
        $this->assertEquals('DDD', $updatedIssue->getMagazine());
        $this->assertEquals('1', $updatedIssue->getNumero());
        $this->assertEquals('bon', $updatedIssue->getEtat());
        $this->assertEquals(2, $updatedIssue->getIdAcquisition());
        $this->assertEquals(true, $updatedIssue->getAv());
        $this->assertEquals($this->getUser('dm_test_user')->getId(), $updatedIssue->getIdUtilisateur());
        $this->assertEquals(date('Y-m-d'), $updatedIssue->getDateajout()->format('Y-m-d'));
    }

    public function testDeleteFromCollection(): void
    {
        $this->createUserCollection('dm_test_user');

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

    public function testUpdateCollectionCreateAndUpdateIssue(): void
    {
        $this->createUserCollection('dm_test_user');

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
        $updatedIssue = $this->getEm('dm')->getRepository(Numeros::class)->findOneBy(
            ['idUtilisateur' => $this->getUser('dm_test_user')->getId(), 'pays' => $country, 'magazine' => $publication, 'numero' => $issueToUpdate]
        );
        $this->assertNotNull($updatedIssue);
        $this->assertEquals('bon', $updatedIssue->getEtat());
        $this->assertEquals('-2', $updatedIssue->getIdAcquisition());
        $this->assertFalse($updatedIssue->getAv());

        $this->assertEquals('CREATE', $responseObject[1]->action);
        $this->assertEquals(1, $responseObject[1]->numberOfIssues);

        /** @var Numeros $createdIssue */
        $createdIssue = $this->getEm('dm')->getRepository(Numeros::class)->findOneBy(
            ['idUtilisateur' => $this->getUser('dm_test_user')->getId(), 'pays' => $country, 'magazine' => $publication, 'numero' => $issueToCreate]
        );
        $this->assertNotNull($createdIssue);
        $this->assertEquals('bon', $createdIssue->getEtat());
        $this->assertEquals('-2', $createdIssue->getIdAcquisition());
        $this->assertFalse($createdIssue->getAv());
    }

    public function testFetchCollection(): void
    {
        self::runCommand('doctrine:fixtures:load -q -n --em=coa --group=coa');
        $this->createUserCollection('dm_test_user');

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

    public function testUpdatePurchase(): void
    {
        $this->createUserCollection('dm_test_user');

        /** @var Achats $purchaseToUpdate */
        $purchaseToUpdate = $this->getEm('dm')->getRepository(Achats::class)->findBy([
            'idUser' => $this->getUser('dm_test_user')->getId()
        ])[0];

        $this->buildAuthenticatedServiceWithTestUser(
            "/collection/purchases/{$purchaseToUpdate->getIdAcquisition()}",
            self::$dmUser,
            'POST', [
                'date' => '2017-01-01',
                'description' => 'New description'
            ])->call();

        /** @var Achats $updatedPurchase */
        $updatedPurchase = $this->getEm('dm')->getRepository(Achats::class)->find($purchaseToUpdate->getIdAcquisition());

        $this->assertEquals(\DateTime::createFromFormat('Y-m-d H:i:s', '2017-01-01 00:00:00'), $updatedPurchase->getDate());
        $this->assertEquals('New description', $updatedPurchase->getDescription());
    }

    public function testUpdatePurchaseOfOtherUser(): void
    {
        $this->createUserCollection('dm_test_user');

        $response = $this->buildAuthenticatedServiceWithTestUser('/collection/purchases/3', self::$dmUser, 'POST', [
            'date' => '2017-01-01',
            'description' => 'New description'
        ])->call();

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testCallOptionsService(): void
    {
        $response = $this->buildAuthenticatedServiceWithTestUser('/collection/purchases/3', self::$dmUser, 'OPTIONS')->call();

        $this->assertEquals(Response::HTTP_METHOD_NOT_ALLOWED, $response->getStatusCode());
    }

    public function testSetBookcaseSorts(): void
    {
        $this->createUserCollection('dm_test_user');

        $newSorts = [
            'fr/SPG', 'fr/DDD', 'se/KAP'
        ];

        $getResponse = $this->buildAuthenticatedServiceWithTestUser('/collection/bookcase/sort', self::$dmUser, 'POST', ['sorts' => $newSorts])->call();
        $objectResponse = json_decode($getResponse->getContent());
        $this->assertEquals(2, $objectResponse->max);

        /** @var BibliothequeOrdreMagazines[] $updatedSorts */
        $updatedSorts = $this->getEm('dm')->getRepository(BibliothequeOrdreMagazines::class)->findBy([
            'idUtilisateur' => $this->getUser('dm_test_user')->getId()
        ], ['ordre' => 'ASC']);

        $this->assertCount(3, $updatedSorts);
        $this->assertEquals('fr/SPG', $updatedSorts[0]->getPublicationcode());
        $this->assertEquals('fr/DDD', $updatedSorts[1]->getPublicationcode());
        $this->assertEquals('se/KAP', $updatedSorts[2]->getPublicationcode());
    }

    public function testImportFromInducksInit(): void
    {
        self::runCommand('doctrine:fixtures:load -q -n --em=coa --group=coa');
        $this->createUserCollection('dm_test_user');

        $response = $this->buildAuthenticatedServiceWithTestUser('/collection/inducks/import/init', self::$dmUser, 'POST', ['rawData' => implode("\n", [
            'country^entrycode^collectiontype^comment',
            'fr^PM 315^^',
            'us^CBL 7^^'
        ])])->call();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $objectResponse = json_decode($this->getResponseContent($response));
        $this->assertCount(2, (array)$objectResponse->issues);
        $this->assertEquals([], $objectResponse->nonFoundIssues);
        $this->assertEquals(0, $objectResponse->existingIssuesCount);
    }

    public function testImportFromInducksInitExistingIssues(): void
    {
        self::runCommand('doctrine:fixtures:load -q -n --em=coa --group=coa');
        $this->createUserCollection('dm_test_user');

        $response = $this->buildAuthenticatedServiceWithTestUser('/collection/inducks/import/init', self::$dmUser, 'POST', ['rawData' => implode("\n", [
            'country^entrycode^collectiontype^comment',
            'fr^PM 315^^',
            'fr^DDD 1^^', // Already existing
            'us^CBL 7^^',
            'us^MAD  15^^' // Not referenced
        ])])->call();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $objectResponse = json_decode($this->getResponseContent($response));
        $this->assertCount(2, $objectResponse->issues);
        $this->assertEquals(['us/MAD  15'], $objectResponse->nonFoundIssues);
        $this->assertEquals(1, $objectResponse->existingIssuesCount);
    }

    public function testImportFromInducksInitStrangeIssueNumbers(): void
    {
        self::runCommand('doctrine:fixtures:load -q -n --em=coa --group=coa');
        $this->createUserCollection('dm_test_user');

        $response = $this->buildAuthenticatedServiceWithTestUser('/collection/inducks/import/init', self::$dmUser, 'POST', ['rawData' => implode("\n", [
            'country^entrycode^collectiontype^comment',
            'de^MM1951-00^^',
            'fr^CB PN  1^^'
        ])])->call();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $objectResponse = json_decode($this->getResponseContent($response));
        $this->assertEquals([
            (object) [ 'issuecode' => 'de/MM1951-00', 'publicationcode' => 'de/MM', 'issuenumber' => '1951-00'],
            (object) [ 'issuecode' => 'fr/CB PN  1',  'publicationcode' => 'fr/CB', 'issuenumber' => 'PN  1'],
        ], $objectResponse->issues);
        $this->assertEquals([], $objectResponse->nonFoundIssues);
        $this->assertEquals(0, $objectResponse->existingIssuesCount);
    }

    public function testImportFromInducks(): void
    {
        $this->createUserCollection('dm_test_user');

        $response = $this->buildAuthenticatedServiceWithTestUser('/collection/inducks/import', self::$dmUser, 'POST', ['issues' => [
            ['publicationcode' => 'fr/AJM', 'issuenumber' => '58'],
            ['publicationcode' => 'fr/D', 'issuenumber' => '28'],
            ['publicationcode' => 'fr/JM', 'issuenumber' => '56'],
            ['publicationcode' => 'us/MAD', 'issuenumber' => '15']
        ], 'defaultCondition' => 'mauvais'
        ])->call();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $objectResponse = json_decode($this->getResponseContent($response));
        $this->assertEquals(4, $objectResponse->importedIssuesCount);
        $this->assertEquals(0, $objectResponse->existingIssuesCount);

        /** @var Numeros $singleCreatedIssue */
        $singleCreatedIssue = $this->getEm('dm')->getRepository(Numeros::class)->findOneBy([
            'idUtilisateur' => $this->getUser('dm_test_user')->getId(),
            'magazine' => 'MAD'
        ]);
        $this->assertNotNull($singleCreatedIssue->getDateajout());
    }

    public function testImportFromInducksWithExistingIssues(): void
    {
        $this->createUserCollection('dm_test_user');

        $response = $this->buildAuthenticatedServiceWithTestUser('/collection/inducks/import', self::$dmUser, 'POST', ['issues' => [
            ['publicationcode' => 'fr/AJM', 'issuenumber' => '58'],
            ['publicationcode' => 'fr/DDD', 'issuenumber' => '1'],
            ['publicationcode' => 'fr/D', 'issuenumber' => '28'],
            ['publicationcode' => 'fr/JM', 'issuenumber' => '56'],
            ['publicationcode' => 'us/MAD', 'issuenumber' => '15']
        ], 'defaultCondition' => 'mauvais'
        ])->call();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $objectResponse = json_decode($this->getResponseContent($response));
        $this->assertEquals(4, $objectResponse->importedIssuesCount);
        $this->assertEquals(1, $objectResponse->existingIssuesCount);
    }

    public function testGetPrivileges(): void
    {
        $this->createUserCollection('demo', ['EdgeCreator' => 'Affichage']);
        $sha1Password = sha1('password');
        $response = $this->buildAuthenticatedService('/collection/privileges', self::$dmUser, [
            'username' => 'demo',
            'password' => $sha1Password
        ], [], 'GET')->call();
        $objectResponse = json_decode($this->getResponseContent($response));
        $this->assertEquals('Affichage', $objectResponse->EdgeCreator);
    }
}
