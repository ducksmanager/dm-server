<?php
namespace DmServer\Test;

use DateTime;
use Dm\Models\Achats;
use Dm\Models\Bouquineries;
use Dm\Models\Numeros;
use Dm\Models\Users;
use DmServer\DmServer;
use Symfony\Component\HttpFoundation\Response;

class DucksManagerTest extends TestCommon
{
    public function testResetDemoDataWrongUser() {
        $response = $this->buildAuthenticatedService('/ducksmanager/resetDemo', self::$dmUser, [])->call();
        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testResetDemoDataNoDemoUser() {
        $response = $this->buildAuthenticatedService('/ducksmanager/resetDemo', self::$adminUser, [])->call();
        $this->assertEquals(Response::HTTP_EXPECTATION_FAILED, $response->getStatusCode());
    }

    public function testResetDemoData() {
        self::createTestCollection('demo');

        $dmEm = DmServer::getEntityManager(DmServer::CONFIG_DB_KEY_DM);

        $demoUser = $dmEm->getRepository(Users::class)->findOneBy([
            'username' => 'demo'
        ]);

        $purchasesOfDemoUser = $dmEm->getRepository(Achats::class)->findBy([
            'idUser' => $demoUser->getId()
        ]);

        $this->assertCount(1, array_filter($purchasesOfDemoUser, function(Achats $purchase) {
            return $purchase->getDate()->format('Y-m-d') === '2010-01-01' && $purchase->getDescription() === 'Purchase';
        }));

        $issuesOfDemoUser = $dmEm->getRepository(Numeros::class)->findBy([
            'idUtilisateur' => $demoUser->getId()
        ]);
        $this->assertCount(1, array_filter($issuesOfDemoUser, function(Numeros $issue) {
            return $issue->getPays() === 'fr' && $issue->getMagazine() === 'MP' && $issue->getNumero() === '300';
        }));

        $demoUser->setBibliothequeTexture1('A');
        $demoUser->setBibliothequeSousTexture1('B');
        $demoUser->setBibliothequeTexture2('C');
        $demoUser->setBibliothequeSousTexture2('D');
        $demoUser->setBibliothequeGrossissement(1);
        $demoUser->setBetauser(true);
        $dmEm->flush($demoUser);

        $response = $this->buildAuthenticatedService('/ducksmanager/resetDemo', self::$adminUser, [])->call();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $demoUser = $dmEm->getRepository(Users::class)->findOneBy([
            'username' => 'demo'
        ]);

        $this->assertEquals('bois', $demoUser->getBibliothequeTexture1());
        $this->assertEquals('HONDURAS MAHOGANY', $demoUser->getBibliothequeSousTexture1());
        $this->assertEquals('bois', $demoUser->getBibliothequeTexture2());
        $this->assertEquals('KNOTTY PINE', $demoUser->getBibliothequeSousTexture2());
        $this->assertEquals(1.5, $demoUser->getBibliothequeGrossissement());

        $this->assertEquals(true, $demoUser->getBetauser()); // This property shouldn't have reset

        $issuesOfDemoUser = $dmEm->getRepository(Numeros::class)->findBy([
            'idUtilisateur' => $demoUser->getId()
        ]);

        $this->assertCount(35, $issuesOfDemoUser);
        $this->assertCount(0, array_filter($issuesOfDemoUser, function(Numeros $issue) {
            return $issue->getPays() === 'fr' && $issue->getMagazine() === 'MP' && $issue->getNumero() === '300';
        })); // Previous issue has been reset

        $purchasesOfDemoUser = $dmEm->getRepository(Achats::class)->findBy([
            'idUser' => $demoUser->getId()
        ]);

        $this->assertCount(4, $purchasesOfDemoUser);
        $this->assertCount(0, array_filter($purchasesOfDemoUser, function(Achats $purchase) {
            return $purchase->getDate()->format('Y-m-d') === '2010-01-01' && $purchase->getDescription() === 'Purchase';
        })); // Previous issue has been reset
    }

    public function testSendBookcaseEmail() {
        $response = $this->buildAuthenticatedService('/ducksmanager/email/bookstore', self::$dmUser, [])->call();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testSendBookcaseEmailWithUser() {
        self::createTestCollection('demo');

        $dmEm = DmServer::getEntityManager(DmServer::CONFIG_DB_KEY_DM);

        $demoUser = $dmEm->getRepository(Users::class)->findOneBy([
            'username' => 'demo'
        ]);

        $response = $this->buildAuthenticatedService('/ducksmanager/email/bookstore', self::$dmUser, [], [
            'userid' => $demoUser->getId()
        ])->call();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testGetEvents() {
        self::createTestCollection(self::$defaultTestDmUserName);
        self::createTestCollection('user2');
        self::createEdgesData(1);

        $dmEm = DmServer::getEntityManager(DmServer::CONFIG_DB_KEY_DM);

        $oldIssue = new Numeros();
        $dmEm->persist(
            $oldIssue
                ->setPays('fr')
                ->setMagazine('DDD')
                ->setNumero('3')
                ->setEtat('mauvais')
                ->setIdUtilisateur(1)
                ->setDateajout(\DateTime::createFromFormat('Y-m-d', '2017-01-01'))
        );

        $recentIssue = new Numeros();
        $dmEm->persist(
            $recentIssue
                ->setPays('fr')
                ->setMagazine('SPG')
                ->setNumero('1')
                ->setEtat('bon')
                ->setIdUtilisateur(1)
                ->setDateajout((new \DateTime('-1 week')))
        );

        $bookstore = new Bouquineries();
        $dmEm->persist(
            $bookstore
                ->setActif(1)
                ->setNom('My bookstore')
                ->setIdUtilisateur(2)
                ->setAdresse('Address')
                ->setAdressecomplete('Full address')
                ->setCodepostal('Postal code')
                ->setVille('City')
                ->setCommentaire('Comment')
                ->setDateajout((new \DateTime('-2 week')))
        );
        $dmEm->flush();

        $response = $this->buildAuthenticatedService('/ducksmanager/recentevents', self::$dmUser, [], [], 'GET')->call();
        $objectResponse = json_decode($response->getContent());

        $this->assertEventData($objectResponse, ['type' => 'collectioninsert', 'userIds' => [1]], 10, [
            "example_issue_publicationcode" => "fr/MP",
            "example_issue_issuenumber" => " 301",
            "count" => "3"
        ]);

        $this->assertEventData($objectResponse, ['type' => 'collectioninsert', 'userIds' => [1]], 1 * 7 * 24 * 3600, [
            "example_issue_publicationcode" => "fr/SPG",
            "example_issue_issuenumber" => " 1",
            "count" => "1"
        ]);

        $this->assertEventData($objectResponse, ['type' => 'collectioninsert', 'userIds' => [2]], 10, [
            "example_issue_publicationcode" => "fr/MP",
            "example_issue_issuenumber" => " 301",
            "count" => "3"
        ]);

        $this->assertEventData($objectResponse, ['type' => 'signup', 'userIds' => [1]], 10, []);
        $this->assertEventData($objectResponse, ['type' => 'signup', 'userIds' => [2]], 10, []);

        $this->assertEventData($objectResponse, ['type' => 'bookstorecreation', 'userIds' => [2]], 2 * 7 * 24 * 3600, [
            "name" => "My bookstore"
        ]);

        $this->assertEventData($objectResponse, ['type' => 'bookstorecreation', 'userIds' => [2]], 2 * 7 * 24 * 3600, [
            "name" => "My bookstore"
        ]);

        $this->assertEventData($objectResponse, ['type' => 'edgecreation', 'userIds' => [1]], 2 * 24 * 3600, [
            "publicationcode" => "fr/DDD",
            "issuenumber" => "1"
        ]);
    }

    function assertEventData(array $objectResponse, array $filters, int $expectedSecondsDiff, array $expectedData) {
        $filteredEvents = array_filter($objectResponse, function($event) use ($filters, $expectedSecondsDiff) {
            if (abs($expectedSecondsDiff - $event->secondsDiff) > 10) {
                return false;
            }
            foreach($filters as $key=>$value) {
                if ($event->$key !== $value) {
                    return false;
                }
            }
            return true;
        });
        $filteredEvents = array_values($filteredEvents);

        $this->assertCount(1, $filteredEvents, 'Exactly one event should remain after the filter');

        $this->assertEquals($expectedData, (array) $filteredEvents[0]->data);
    }
}
