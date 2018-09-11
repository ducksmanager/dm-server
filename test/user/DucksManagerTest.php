<?php
namespace DmServer\Test;

use Dm\Models\Achats;
use Dm\Models\Numeros;
use Dm\Models\Users;
use DmServer\DmServer;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Component\HttpFoundation\Response;

class DucksManagerTest extends TestCommon
{
    protected function getEm() {
        return parent::getEntityManagerByName(DmServer::CONFIG_DB_KEY_DM);
    }

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

        $demoUser = $this->getEm()->getRepository(Users::class)->findOneBy([
            'username' => 'demo'
        ]);

        $purchasesOfDemoUser = $this->getEm()->getRepository(Achats::class)->findBy([
            'idUser' => $demoUser->getId()
        ]);

        $this->assertCount(1, array_filter($purchasesOfDemoUser, function(Achats $purchase) {
            return $purchase->getDate()->format('Y-m-d') === '2010-01-01' && $purchase->getDescription() === 'Purchase';
        }));

        $issuesOfDemoUser = $this->getEm()->getRepository(Numeros::class)->findBy([
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
        try {
            $this->getEm()->flush($demoUser);
        } catch (OptimisticLockException|ORMException $e) {
            $this->fail("Failed to save user : {$e->getMessage()}");
        }

        $response = $this->buildAuthenticatedService('/ducksmanager/resetDemo', self::$adminUser, [])->call();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $demoUser = $this->getEm()->getRepository(Users::class)->findOneBy([
            'username' => 'demo'
        ]);

        $this->assertEquals('bois', $demoUser->getBibliothequeTexture1());
        $this->assertEquals('HONDURAS MAHOGANY', $demoUser->getBibliothequeSousTexture1());
        $this->assertEquals('bois', $demoUser->getBibliothequeTexture2());
        $this->assertEquals('KNOTTY PINE', $demoUser->getBibliothequeSousTexture2());
        $this->assertEquals(1.5, $demoUser->getBibliothequeGrossissement());

        $this->assertEquals(true, $demoUser->getBetauser()); // This property shouldn't have reset

        $issuesOfDemoUser = $this->getEm()->getRepository(Numeros::class)->findBy([
            'idUtilisateur' => $demoUser->getId()
        ]);

        $this->assertCount(35, $issuesOfDemoUser);
        $this->assertCount(0, array_filter($issuesOfDemoUser, function(Numeros $issue) {
            return $issue->getPays() === 'fr' && $issue->getMagazine() === 'MP' && $issue->getNumero() === '300';
        })); // Previous issue has been reset

        $purchasesOfDemoUser = $this->getEm()->getRepository(Achats::class)->findBy([
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

        $demoUser = $this->getEm()->getRepository(Users::class)->findOneBy([
            'username' => 'demo'
        ]);

        $response = $this->buildAuthenticatedService('/ducksmanager/email/bookstore', self::$dmUser, [], [
            'userid' => $demoUser->getId()
        ])->call();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testGetUser() {
        self::createTestCollection('demo');
        $sha1Password = sha1('password');
        $userResponse = $this->buildAuthenticatedService("/ducksmanager/user/get/demo/$sha1Password", self::$dmUser, [], [], 'GET')->call();
        $objectResponse = json_decode($userResponse->getContent());
        $this->assertEquals('demo', $objectResponse->username);
    }

    public function testGetPrivileges() {
        self::createTestCollection('demo', ['EdgeCreator' => 'Affichage']);
        $sha1Password = sha1('password');
        $response = $this->buildAuthenticatedService('/user/privileges', self::$dmUser, [
            'username' => 'demo',
            'password' => $sha1Password
        ], [], 'GET')->call();
        $objectResponse = json_decode($this->getResponseContent($response));
        $this->assertEquals('Affichage', $objectResponse->EdgeCreator);
    }
}
