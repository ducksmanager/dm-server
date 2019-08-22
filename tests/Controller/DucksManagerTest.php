<?php
namespace App\Tests;

use App\Controller\RequiresDmVersionController;
use App\Entity\Dm\Achats;
use App\Entity\Dm\Bouquineries;
use App\Entity\Dm\Numeros;
use App\Entity\Dm\Users;
use App\Entity\Dm\UsersContributions;
use App\Entity\Dm\UsersPasswordTokens;
use Countable;
use DateTime;
use Swift_Message;
use Symfony\Bundle\SwiftmailerBundle\DataCollector\MessageDataCollector;
use Symfony\Component\HttpFoundation\Response;

class DucksManagerTest extends TestCommon implements RequiresDmVersionController
{
    protected function getEmNamesToCreate(): array
    {
        return ['dm'];
    }

    public function testResetDemoDataWrongUser(): void
    {
        $response = $this->buildAuthenticatedService('/ducksmanager/resetDemo', self::$dmUser, [])->call();
        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    public function testResetDemoDataNoDemoUser(): void
    {
        $response = $this->buildAuthenticatedService('/ducksmanager/resetDemo', self::$adminUser, [])->call();
        $this->assertEquals(Response::HTTP_EXPECTATION_FAILED, $response->getStatusCode());
    }

    public function testResetDemoData(): void
    {
        $this->createUserCollection('demo');

        $demoUser = $this->getEm('dm')->getRepository(Users::class)->findOneBy([
            'username' => 'demo'
        ]);

        $purchasesOfDemoUser = $this->getEm('dm')->getRepository(Achats::class)->findBy([
            'idUser' => $demoUser->getId()
        ]);

        $this->assertCount(1, array_filter($purchasesOfDemoUser, function(Achats $purchase) {
            return $purchase->getDate()->format('Y-m-d') === '2010-01-01' && $purchase->getDescription() === 'Purchase';
        }));

        $issuesOfDemoUser = $this->getEm('dm')->getRepository(Numeros::class)->findBy([
            'idUtilisateur' => $demoUser->getId()
        ]);
        $this->assertCount(1, array_filter($issuesOfDemoUser, function(Numeros $issue) {
            return $issue->getPays() === 'fr' && $issue->getMagazine() === 'MP' && $issue->getNumero() === '300';
        }));

        $demoUser->setBibliothequeTexture1('A');
        $demoUser->setBibliothequeSousTexture1('B');
        $demoUser->setBibliothequeTexture2('C');
        $demoUser->setBibliothequeSousTexture2('D');
        $demoUser->setBetauser(true);
        $this->getEm('dm')->persist($demoUser);
        $this->getEm('dm')->flush();

        $response = $this->buildAuthenticatedService('/ducksmanager/resetDemo', self::$adminUser, [])->call();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $demoUser = $this->getEm('dm')->getRepository(Users::class)->findOneBy([
            'username' => 'demo'
        ]);

        $this->assertEquals('bois', $demoUser->getBibliothequeTexture1());
        $this->assertEquals('HONDURAS MAHOGANY', $demoUser->getBibliothequeSousTexture1());
        $this->assertEquals('bois', $demoUser->getBibliothequeTexture2());
        $this->assertEquals('KNOTTY PINE', $demoUser->getBibliothequeSousTexture2());

        $this->assertEquals(true, $demoUser->getBetauser()); // This property shouldn't have reset

        $issuesOfDemoUser = $this->getEm('dm')->getRepository(Numeros::class)->findBy([
            'idUtilisateur' => $demoUser->getId()
        ]);

        $this->assertCount(35, $issuesOfDemoUser);
        $this->assertCount(0, array_filter($issuesOfDemoUser, function(Numeros $issue) {
            return $issue->getPays() === 'fr' && $issue->getMagazine() === 'MP' && $issue->getNumero() === '300';
        })); // Previous issue has been reset

        $purchasesOfDemoUser = $this->getEm('dm')->getRepository(Achats::class)->findBy([
            'idUser' => $demoUser->getId()
        ]);

        $this->assertCount(4, $purchasesOfDemoUser);
        $this->assertCount(0, array_filter($purchasesOfDemoUser, function(Achats $purchase) {
            return $purchase->getDate()->format('Y-m-d') === '2010-01-01' && $purchase->getDescription() === 'Purchase';
        })); // Previous issue has been reset
    }

    public function testSendBookcaseEmail(): void
    {
        self::$client->enableProfiler();
        $response = $this->buildAuthenticatedService('/ducksmanager/bookstore/suggest', self::$dmUser, [])->call();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        /** @var MessageDataCollector $mailCollector */
        $mailCollector = self::$client->getProfile()->getCollector('swiftmailer');
        /** @var Swift_Message[]|Countable $messages */
        $messages = $mailCollector->getMessages();
        $this->assertCount(2, $messages);
        [$message, $messageCopy] = $messages;

        $this->assertContains('Ajout de bouquinerie', $message->getSubject());
        $this->assertContains('Validation', $message->getBody());

        $this->assertContains('[Sent to '.$_ENV['SMTP_USERNAME'].'] Ajout de bouquinerie', $messageCopy->getSubject());
        $this->assertContains('Validation', $messageCopy->getBody());
    }

    public function testSuggestBookstoreWithUser(): void
    {
        $this->createUserCollection('demo');

        $demoUser = $this->getEm('dm')->getRepository(Users::class)->findOneBy([
            'username' => 'demo'
        ]);

        self::$client->enableProfiler();
        $response = $this->buildAuthenticatedService('/ducksmanager/bookstore/suggest', self::$dmUser, [], [
            'userid' => $demoUser->getId()
        ])->call();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        /** @var MessageDataCollector $mailCollector */
        $mailCollector = self::$client->getProfile()->getCollector('swiftmailer');
        /** @var Swift_Message[]|Countable $messages */
        $messages = $mailCollector->getMessages();
        $this->assertCount(2, $messages);
        [$message, $messageCopy] = $messages;

        $this->assertContains('Ajout de bouquinerie', $message->getSubject());
        $this->assertContains('Validation', $message->getBody());

        $this->assertContains('[Sent to '.$_ENV['SMTP_USERNAME'].'] Ajout de bouquinerie', $messageCopy->getSubject());
        $this->assertContains('Validation', $messageCopy->getBody());
    }

    public function testSendPendingEmails(): void
    {
        $this->createUserCollection('demo');

        $demoUser = $this->getEm('dm')->getRepository(Users::class)->findOneBy([
            'username' => 'demo'
        ]);

        $bookstore = (new Bouquineries())
            ->setActif(true)
            ->setNom('Bookstore')
            ->setCommentaire('Comment')
            ->setCoordx(0)
            ->setCoordy(0)
            ->setAdressecomplete('1 street A')
            ->setIdUtilisateur($demoUser->getId())
            ->setDateajout(new \DateTime());

        $bookstoreContribution = (new UsersContributions())
            ->setBookstore($bookstore)
            ->setIdUser($demoUser->getId())
            ->setPointsNew(1)
            ->setDate(new \DateTime())
            ->setPointsTotal(1)
            ->setContribution('duckhunter');

        $this->getEm('dm')->persist($bookstore);
        $this->getEm('dm')->persist($bookstoreContribution);
        $this->getEm('dm')->flush();

        self::$client->enableProfiler();
        $response = $this->buildAuthenticatedService('/ducksmanager/emails/pending', self::$dmUser, [])->call();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $objectResponse = json_decode($this->getResponseContent($response));

        /** @var MessageDataCollector $mailCollector */
        $mailCollector = self::$client->getProfile()->getCollector('swiftmailer');
        /** @var Swift_Message[]|Countable $messages */
        $messages = $mailCollector->getMessages();
        $this->assertCount(4, $messages);
        [$edgeEmail, $edgeEmailCopy, $bookstoreEmail, $bookstoreEmailCopy] = $messages;

        $expectedEdgeEmailBody = <<<MESSAGE
            Bonjour demo,
            La tranche dont vous nous avez envoyé la photo est maintenant visionnable dans votre bibliothèque DucksManager ainsi que dans les bibliothèques des autres utilisateurs possédant ce magazine.
            <p style="text-align: center"><img width="100" src="http://localhost:8000/images/medailles/Photographe_1_fr.png" />
            Vous avez remporté la médaille "Photographe DucksManager Débutant" grâce à vos contributions !</p>
            <b>Votre contribution vous a rapporté 50 points "Photographe"</b>, bravo à vous et merci pour votre contribution : nous sommes heureux de vous compter parmi la communauté active de DucksManager !
            
            
            A bientôt sur le site !
            L'équipe DucksManager
            <img width="400" src="http://localhost:8000/logo_petit.png" />
            MESSAGE;
        $this->assertEquals(str_replace("\n", '<br />', $expectedEdgeEmailBody), $edgeEmail->getBody());

        $this->assertEquals(str_replace("\n", '<br />', $expectedEdgeEmailBody), $edgeEmailCopy->getBody());
        $this->assertEquals($_ENV['SMTP_USERNAME'], array_keys($edgeEmailCopy->getTo())[0]);

        $expectedBookstoreEmailBody = <<<MESSAGE
            Bonjour demo,
            La bouquinerie que vous avez proposée est maintenant visible par tous les utilisateurs DucksManager.
            <p style="text-align: center"><img width="100" src="http://localhost:8000/images/medailles/Duckhunter_1_fr.png" />
            Vous avez remporté la médaille "Duckhunter Débutant" grâce à vos contributions !</p>
            Bravo à vous et merci pour votre contribution : nous sommes heureux de vous accueillir parmi la communauté active de DucksManager !
            
            
            A bientôt sur le site !
            L'équipe DucksManager
            <img width="400" src="http://localhost:8000/logo_petit.png" />
            MESSAGE;
        $this->assertEquals(str_replace("\n", '<br />', $expectedBookstoreEmailBody), $bookstoreEmail->getBody());

        $this->assertEquals(str_replace("\n", '<br />', $expectedBookstoreEmailBody), $bookstoreEmailCopy->getBody());
        $this->assertEquals($_ENV['SMTP_USERNAME'], array_keys($bookstoreEmailCopy->getTo())[0]);

        $bookstoreContribution = $this->getEm('dm')->getRepository(UsersContributions::class)->findOneBy(['bookstore' => $bookstore]);
        $this->assertEquals(true, $bookstoreContribution->getEmailsSent());
    }

    public function testApprovedBookcase(): void
    {
        $this->createUserCollection('demo');

        $demoUser = $this->getEm('dm')->getRepository(Users::class)->findOneBy([
            'username' => 'demo'
        ]);

        $existingBookstore = (new Bouquineries())
            ->setActif(false)
            ->setNom('Bookstore')
            ->setCommentaire('Comment')
            ->setCoordx(0)
            ->setCoordy(0)
            ->setAdressecomplete('1 street A')
            ->setIdUtilisateur($demoUser->getId())
            ->setDateajout(new \DateTime());

        $bookstore = (clone $existingBookstore)
            ->setNom('Bookstore 2');

        $this->getEm('dm')->persist($existingBookstore);
        $this->getEm('dm')->persist($bookstore);
        $this->getEm('dm')->persist(
            (new UsersContributions())
                ->setBookstore($existingBookstore)
                ->setIdUser($demoUser->getId())
                ->setDate(new DateTime())
                ->setContribution('duckhunter')
                ->setPointsNew(1)
                ->setPointsTotal(1)
        );
        $this->getEm('dm')->flush();

        $response = $this->buildAuthenticatedService('/ducksmanager/bookstore/approve', self::$dmUser, [], [
            'id' => $bookstore->getId(),
            'coordinates' => [1, 2]
        ])->call();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $updatedBookstore = $this->getEm('dm')->getRepository(Bouquineries::class)->find($bookstore->getId());
        $this->assertEquals(1, $updatedBookstore->getCoordx());
        $this->assertEquals(2, $updatedBookstore->getCoordy());
        $this->assertEquals(true, $updatedBookstore->getActif());

        $userContribution = $this->getEm('dm')->getRepository(UsersContributions::class)->findOneBy([
            'bookstore' => $bookstore,
            'contribution' => 'duckhunter'
        ]);
        $this->assertEquals(1, $userContribution->getPointsNew());
        $this->assertEquals(1 + 1, $userContribution->getPointsTotal());
    }

    public function testInitResetPassword(): void
    {
        $this->createUserCollection('dm_test_user');
        /** @var Users $user */
        $user = $this->getEm('dm')->getRepository(Users::class)->findOneBy(['username' => 'dm_test_user']);

        self::$client->enableProfiler();
        $response = $this->buildAuthenticatedService('/ducksmanager/resetpassword/init', self::$dmUser, [], ['email' => $user->getEmail()])->call();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        /** @var MessageDataCollector $mailCollector */
        $mailCollector = self::$client->getProfile()->getCollector('swiftmailer');
        /** @var Swift_Message[]|Countable $messages */
        $messages = $mailCollector->getMessages();
        $this->assertCount(2, $messages);
        [$message,] = $messages;

        /** @var UsersPasswordTokens $generatedToken */
        $generatedToken = $this->getEm('dm')->getRepository(UsersPasswordTokens::class)->findOneBy([
            'idUser' => $user->getId()
        ]);

        $this->assertNotNull($generatedToken);

        $expectedMessageBody = implode('<br />', [
            'Bonjour dm_test_user,',
            'Un visiteur a indiqué avoir oublié le mot de passe associé à l\'adresse e-mail test@ducksmanager.net.',
            'Si c\'est vous qui en êtes à l\'origine, cliquez sur le lien suivant pour indiquer un nouveau mot de passe pour votre compte DucksManager :',
            '<a href="http://localhost:8000/?action=reset_password&token='.$generatedToken->getToken().'">Mettre à jour mon mot de passe</a>',
            '<br />',
            'A bientôt sur le site !',
            'L\'équipe DucksManager',
            '<img width="400" src="http://localhost:8000/logo_petit.png" />'
        ]);
        $this->assertEquals($expectedMessageBody, $message->getBody());
    }

    public function testInitResetPasswordMissingEmail(): void
    {
        self::$client->enableProfiler();
        $response = $this->buildAuthenticatedService('/ducksmanager/resetpassword/init', self::$dmUser, [], ['email' => 'fakeemail@gmail.com'])->call();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        /** @var MessageDataCollector $mailCollector */
        $mailCollector = self::$client->getProfile()->getCollector('swiftmailer');
        /** @var Swift_Message[]|Countable $messages */
        $messages = $mailCollector->getMessages();
        $this->assertCount(0, $messages);
    }

    public function testCheckPasswordToken(): void
    {
        $this->createUserCollection('dm_test_user');
        /** @var Users $user */
        $user = $this->getEm('dm')->getRepository(Users::class)->findOneBy(['username' => 'dm_test_user']);

        $this->buildAuthenticatedService('/ducksmanager/resetpassword/init', self::$dmUser, [], ['email' => $user->getEmail()])->call();

        /** @var UsersPasswordTokens $generatedToken */
        $generatedToken = $this->getEm('dm')->getRepository(UsersPasswordTokens::class)->findOneBy([
            'idUser' => $user->getId()
        ]);

        $response = $this->buildAuthenticatedService("/ducksmanager/resetpassword/checktoken/{$generatedToken->getToken()}", self::$dmUser, [])->call();

        $objectResponse = json_decode($this->getResponseContent($response));
        $this->assertEquals($generatedToken->getToken(), $objectResponse->token);
    }

    public function testResetPasswordToken(): void
    {
        $this->createUserCollection('dm_test_user');
        /** @var Users $user */
        $user = $this->getEm('dm')->getRepository(Users::class)->findOneBy(['username' => 'dm_test_user']);

        $this->buildAuthenticatedService('/ducksmanager/resetpassword/init', self::$dmUser, [], ['email' => $user->getEmail()])->call();

        /** @var UsersPasswordTokens $generatedToken */
        $generatedToken = $this->getEm('dm')->getRepository(UsersPasswordTokens::class)->findOneBy([
            'idUser' => $user->getId()
        ]);

        $response = $this->buildAuthenticatedService('/ducksmanager/resetpassword', self::$dmUser, [], [
            'token' => $generatedToken->getToken(),
            'password' => 'newpassword',
        ])->call();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        /** @var Users $updatedUser */
        $updatedUser = $this->getEm('dm')->getRepository(Users::class)->findOneBy([
            'id' => $user->getId()
        ]);
        $this->assertEquals(sha1('newpassword'), $updatedUser->getPassword());

        $this->assertNull($this->getEm('dm')->getRepository(UsersPasswordTokens::class)->findOneBy([
            'token' => $generatedToken->getToken()
        ]));
    }

    public function testGetLastPublicationPosition(): void
    {
        $this->createUserCollection('dm_test_user');
        /** @var Users $user */
        $user = $this->getEm('dm')->getRepository(Users::class)->findOneBy(['username' => 'dm_test_user']);

        $getResponse = $this->buildAuthenticatedServiceWithTestUser("/ducksmanager/bookcase/{$user->getId()}/sort/max", self::$dmUser)->call();
        $objectResponse = json_decode($getResponse->getContent());

        $this->assertInternalType('int', $objectResponse->max);
        $this->assertEquals(2, $objectResponse->max);
    }

    public function testGetLastPublicationPositionNoPublication(): void
    {
        $this->createUserCollection('dm_test_user', [], false);
        /** @var Users $user */
        $user = $this->getEm('dm')->getRepository(Users::class)->findOneBy(['username' => 'dm_test_user']);

        $getResponse = $this->buildAuthenticatedServiceWithTestUser("/ducksmanager/bookcase/{$user->getId()}/sort/max", self::$dmUser)->call();
        $this->assertEquals(Response::HTTP_NO_CONTENT, $getResponse->getStatusCode());
    }

    public function testGetBookcaseSorts(): void
    {
        $this->createUserCollection('dm_test_user');
        /** @var Users $user */
        $user = $this->getEm('dm')->getRepository(Users::class)->findOneBy(['username' => 'dm_test_user']);

        $getResponse = $this->buildAuthenticatedServiceWithTestUser("/ducksmanager/bookcase/{$user->getId()}/sort", self::$dmUser)->call();
        $objectResponse = json_decode($getResponse->getContent());

        $this->assertEquals(['fr/DDD', 'fr/JM', 'fr/MP'], $objectResponse);
    }
}
