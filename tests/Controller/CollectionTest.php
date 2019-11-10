<?php
namespace App\Tests\Controller;

use App\Entity\Dm\Achats;
use App\Entity\Dm\BibliothequeOrdreMagazines;
use App\Entity\Dm\Numeros;
use App\Entity\Dm\Users;
use App\Entity\Dm\UsersOptions;
use App\Tests\Fixtures\CoaEntryFixture;
use App\Tests\Fixtures\CoaFixture;
use App\Tests\TestCommon;
use DateTime;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class CollectionTest extends TestCommon
{
    protected function getEmNamesToCreate(): array
    {
        return ['dm', 'coa'];
    }

    protected function setUp(){
        parent::setUp();
        $this->createUserCollection(self::$defaultTestDmUserName);
    }

    public function lastVisitProvider()
    {
        return [
            'should create last visit' => [
                'existingPreviousVisit' => null,
                'existingLastVisit' => null,
                'newPreviousVisit' => null,
                'newLastVisit' => (new DateTime())->format('Y-m-d'),
                'expectedStatus' => Response::HTTP_ACCEPTED
            ],
            'should update previous and last visit from the previous day' => [
                'existingPreviousVisit' => null,
                'existingLastVisit' => new DateTime('yesterday'),
                'newPreviousVisit' => (new DateTime('yesterday'))->format('Y-m-d'),
                'newLastVisit' => (new DateTime())->format('Y-m-d'),
                'expectedStatus' => Response::HTTP_ACCEPTED
            ],
            'should not update last visit on the same day' => [
                'existingPreviousVisit' => null,
                'existingLastVisit' => new DateTime(),
                'newPreviousVisit' => null,
                'newLastVisit' => (new DateTime())->format('Y-m-d'),
                'expectedStatus' => Response::HTTP_NO_CONTENT
            ],
            'should update previous visit from the previous day' => [
                'existingPreviousVisit' => new DateTime('-2 days'),
                'existingLastVisit' => new DateTime('yesterday'),
                'newPreviousVisit' => (new DateTime('yesterday'))->format('Y-m-d'),
                'newLastVisit' => (new DateTime())->format('Y-m-d'),
                'expectedStatus' => Response::HTTP_ACCEPTED
            ],
        ];
    }

    /**
     * @dataProvider lastVisitProvider
     * @throws Exception
     */
    public function testPostLastVisit(?DateTime $existingPreviousVisit, ?DateTime $existingLastVisit, ?string $newPreviousVisit, ?string $newLastVisit, int $expectedStatus): void
    {
        if (!is_null($existingPreviousVisit)) {
            /** @var Users $user */
            $user = $this->getEm('dm')->getRepository(Users::class)->findOneBy(['username'=>self::$defaultTestDmUserName]);
            $user->setPrecedentacces($existingPreviousVisit);
            $this->getEm('dm')->persist($user);
            $this->getEm('dm')->flush();
        }

        if (!is_null($existingLastVisit)) {
            /** @var Users $user */
            $user = $this->getEm('dm')->getRepository(Users::class)->findOneBy(['username'=>self::$defaultTestDmUserName]);
            $user->setDernieracces($existingLastVisit);
            $this->getEm('dm')->persist($user);
            $this->getEm('dm')->flush();
        }

        $userResponse = $this->buildAuthenticatedServiceWithTestUser('/collection/lastvisit', self::$dmUser, 'POST')->call();
        $this->assertEquals($expectedStatus, $userResponse->getStatusCode());

        $this->getEm('dm')->clear();
        /** @var Users $user */
        $user = $this->getEm('dm')->getRepository(Users::class)->findOneBy(['username'=>self::$defaultTestDmUserName]);
        if (is_null($user->getPrecedentacces())) {
            $this->assertEquals($newPreviousVisit, $user->getPrecedentacces());
        }
        else {
            $this->assertEquals($newPreviousVisit, $user->getPrecedentacces()->format('Y-m-d'));
        }
        if (is_null($user->getDernieracces())) {
            $this->assertEquals($newLastVisit, $user->getDernieracces());
        }
        else {
            $this->assertEquals($newLastVisit, $user->getDernieracces()->format('Y-m-d'));
        }
    }

    public function testGetUser(): void
    {
        $userResponse = $this->buildAuthenticatedServiceWithTestUser('/collection/user', self::$dmUser)->call();
        $objectResponse = json_decode($userResponse->getContent());
        $this->assertEquals(self::$defaultTestDmUserName, $objectResponse->username);
    }

    public function testGetIssues(): void
    {
        $userResponse = $this->buildAuthenticatedServiceWithTestUser('/collection/issues', self::$dmUser)->call();
        $objectResponse = json_decode($userResponse->getContent());
        $this->assertEquals([
            (object) [
                'id' => 1,
                'country' => 'fr',
                'magazine' => 'DDD',
                'issueNumber' => '1',
                'condition' => 'indefini',
                'purchaseId' => 1,
            ],
            (object) [
                'id' => 2,
                'country' => 'fr',
                'magazine' => 'MP',
                'issueNumber' => '300',
                'condition' => 'bon',
                'purchaseId' => -1,
            ],
            (object) [
                'id' => 3,
                'country' => 'fr',
                'magazine' => 'MP',
                'issueNumber' => '301',
                'condition' => 'mauvais',
                'purchaseId' => -1,
            ],
        ], $objectResponse);
    }

    public function testGetPurchases(): void
    {
        $userResponse = $this->buildAuthenticatedServiceWithTestUser('/collection/purchases', self::$dmUser)->call();
        $objectResponse = json_decode($userResponse->getContent());
        $this->assertEquals([
            (object) [
                'id' => 1,
                'description' => 'Purchase',
                'date' => '2010-01-01',
            ],
        ], $objectResponse);
    }

    public function testAddIssue(): void
    {
        $response = $this->buildAuthenticatedServiceWithTestUser('/collection/issues', self::$dmUser, 'POST', [
            'publicationCode' => 'fr/DDD',
            'issueNumbers' => ['3'],
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
        $publicationCode = 'fr/DDD';
        $issuesToUpdate = ['1'];

        $response = $this->buildAuthenticatedServiceWithTestUser('/collection/issues', self::$dmUser, 'POST', [
            'publicationCode' => $publicationCode,
            'issueNumbers' => $issuesToUpdate,
            'condition' => 'bon',
            'istosell' => '1',
            'purchaseId' => '2'
        ])->call();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $responseObject = json_decode($this->getResponseContent($response));
        $this->assertNotNull($responseObject);

        $this->assertEquals('UPDATE', $responseObject[0]->action);
        $this->assertEquals(1, $responseObject[0]->numberOfIssues);

        $userIssues = $this->getUserIssues(self::$defaultTestDmUserName);
        $this->assertCount(3, $userIssues);

        [$countryCode, $magazine] = explode('/', $publicationCode);
        /** @var Numeros $updatedIssue */
        $updatedIssue = $this->getEm('dm')->getRepository(Numeros::class)->findOneBy(
            ['idUtilisateur' => $this->getUser(self::$defaultTestDmUserName)->getId(), 'pays' => $countryCode, 'magazine' => $magazine, 'numero' => $issuesToUpdate[0]]
        );
        $this->assertEquals('fr', $updatedIssue->getPays());
        $this->assertEquals('DDD', $updatedIssue->getMagazine());
        $this->assertEquals('1', $updatedIssue->getNumero());
        $this->assertEquals('bon', $updatedIssue->getEtat());
        $this->assertEquals(1, $updatedIssue->getIdAcquisition());
        $this->assertEquals(true, $updatedIssue->getAv());
        $this->assertEquals($this->getUser(self::$defaultTestDmUserName)->getId(), $updatedIssue->getIdUtilisateur());
        $this->assertEquals(date('Y-m-d'), $updatedIssue->getDateajout()->format('Y-m-d'));
    }

    public function testDeleteFromCollection(): void
    {
        $response = $this->buildAuthenticatedServiceWithTestUser('/collection/issues', self::$dmUser, 'POST', [
            'publicationCode' => 'fr/DDD',
            'issueNumbers' => ['1'],
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
        $publicationCode = 'fr/DDD';
        $issueToUpdate = '1';
        $issueToCreate = '3';

        $response = $this->buildAuthenticatedServiceWithTestUser('/collection/issues', self::$dmUser, 'POST', [
            'publicationCode' => $publicationCode,
            'issueNumbers' => [$issueToUpdate, $issueToCreate],
            'condition' => 'bon',
        ])->call();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $responseObject = json_decode($this->getResponseContent($response));
        $this->assertNotNull($responseObject);

        $this->assertEquals('UPDATE', $responseObject[0]->action);
        $this->assertEquals(1, $responseObject[0]->numberOfIssues);

        [$country, $publication] = explode('/', $publicationCode);
        /** @var Numeros $updatedIssue */
        $updatedIssue = $this->getEm('dm')->getRepository(Numeros::class)->findOneBy(
            ['idUtilisateur' => $this->getUser(self::$defaultTestDmUserName)->getId(), 'pays' => $country, 'magazine' => $publication, 'numero' => $issueToUpdate]
        );
        $this->assertNotNull($updatedIssue);
        $this->assertEquals('bon', $updatedIssue->getEtat());
        $this->assertEquals(1, $updatedIssue->getIdAcquisition());
        $this->assertFalse($updatedIssue->getAv());

        $this->assertEquals('CREATE', $responseObject[1]->action);
        $this->assertEquals(1, $responseObject[1]->numberOfIssues);

        /** @var Numeros $createdIssue */
        $createdIssue = $this->getEm('dm')->getRepository(Numeros::class)->findOneBy([
            'idUtilisateur' => $this->getUser(self::$defaultTestDmUserName)->getId(),
            'pays' => $country,
            'magazine' => $publication,
            'numero' => $issueToCreate
        ]);
        $this->assertNotNull($createdIssue);
        $this->assertEquals('bon', $createdIssue->getEtat());
        $this->assertEquals('-2', $createdIssue->getIdAcquisition());
        $this->assertFalse($createdIssue->getAv());
    }

    public function testFetchCollection(): void
    {
        $this->loadFixtures([ CoaFixture::class, CoaEntryFixture::class ], false, 'coa');
        $response = $this->buildAuthenticatedServiceWithTestUser('/collection/issues', self::$dmUser)->call();

        $this->assertJsonStringEqualsJsonString($response->getContent(), json_encode([
            ['id' => 1,
                'country' => 'fr',
                'magazine' => 'DDD',
                'issueNumber' => '1',
                'condition' => 'indefini',
                'purchaseId' => 1
            ],
            ['id' => 2,
                'country' => 'fr',
                'magazine' => 'MP',
                'issueNumber' => '300',
                'condition' => 'bon',
                'purchaseId' => -1
            ],
            ['id' => 3,
                'country' => 'fr',
                'magazine' => 'MP',
                'issueNumber' => '301',
                'condition' => 'mauvais',
                'purchaseId' => -1
            ]
        ]));
    }

    public function testUpdatePurchase(): void
    {
        /** @var Achats $purchaseToUpdate */
        $purchaseToUpdate = $this->getEm('dm')->getRepository(Achats::class)->findOneBy([
            'idUser' => $this->getUser(self::$defaultTestDmUserName)->getId()
        ]);

        $this->buildAuthenticatedServiceWithTestUser(
            "/collection/purchases/{$purchaseToUpdate->getIdAcquisition()}",
            self::$dmUser,
            'POST', [
                'date' => '2017-01-01',
                'description' => 'New description'
            ])->call();

        $this->getEm('dm')->clear();
        /** @var Achats $updatedPurchase */
        $updatedPurchase = $this->getEm('dm')->getRepository(Achats::class)->find($purchaseToUpdate->getIdAcquisition());

        $this->assertEquals(DateTime::createFromFormat('Y-m-d H:i:s', '2017-01-01 00:00:00'), $updatedPurchase->getDate());
        $this->assertEquals('New description', $updatedPurchase->getDescription());
    }

    public function testUpdatePurchaseOfOtherUser(): void
    {
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
        $newSorts = [
            'fr/SPG', 'fr/DDD', 'se/KAP'
        ];

        $getResponse = $this->buildAuthenticatedServiceWithTestUser('/collection/bookcase/sort', self::$dmUser, 'POST', ['sorts' => $newSorts])->call();
        $objectResponse = json_decode($getResponse->getContent());
        $this->assertEquals(2, $objectResponse->max);

        /** @var BibliothequeOrdreMagazines[] $updatedSorts */
        $updatedSorts = $this->getEm('dm')->getRepository(BibliothequeOrdreMagazines::class)->findBy([
            'idUtilisateur' => $this->getUser(self::$defaultTestDmUserName)->getId()
        ], ['ordre' => 'ASC']);

        $this->assertCount(3, $updatedSorts);
        $this->assertEquals('fr/SPG', $updatedSorts[0]->getPublicationcode());
        $this->assertEquals('fr/DDD', $updatedSorts[1]->getPublicationcode());
        $this->assertEquals('se/KAP', $updatedSorts[2]->getPublicationcode());
    }

    public function testImportFromInducksInit(): void
    {
        $this->loadFixtures([ CoaFixture::class, CoaEntryFixture::class ], false, 'coa');
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
        $this->loadFixtures([ CoaFixture::class, CoaEntryFixture::class ], false, 'coa');
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
        $this->loadFixtures([ CoaFixture::class, CoaEntryFixture::class ], false, 'coa');
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
            'idUtilisateur' => $this->getUser(self::$defaultTestDmUserName)->getId(),
            'magazine' => 'MAD'
        ]);
        $this->assertNotNull($singleCreatedIssue->getDateajout());
    }

    public function testImportFromInducksWithExistingIssues(): void
    {
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

    public function testGetCountriesToNotify() : void
    {
        $this->createUserCollection('demo');
        $response = $this->buildAuthenticatedServiceWithTestUser('/collection/notifications/countries', self::$dmUser)->call();
        $objectResponse = json_decode($this->getResponseContent($response));
        $this->assertCount(1, $objectResponse);
        $this->assertEquals('fr', $objectResponse[0]->optionValeur);
    }

    public function testUpdateCountriesToNotify() : void
    {
        $user = $this->getEm('dm')->getRepository(Users::class)->findOneBy(['username'=>self::$defaultTestDmUserName]);
        $response = $this->buildAuthenticatedServiceWithTestUser('/collection/notifications/countries', self::$dmUser, 'POST', [
            'countries' => [
                'fr',
                'se',
                'us'
            ]
        ])->call();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $updatedCountriesToNotify = $this->getEm('dm')->getRepository(UsersOptions::class)->findBy([
            'optionNom' => 'suggestion_notification_country',
            'user' => $user
        ]);
        $this->assertCount(3, $updatedCountriesToNotify);
        $this->assertEquals(['fr', 'se', 'us'], array_map(function(UsersOptions $option) {
            return $option->getOptionValeur();
        }, $updatedCountriesToNotify));
    }
}
