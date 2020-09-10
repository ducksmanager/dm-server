<?php
namespace App\Tests\Controller;

use App\Entity\Dm\Users;
use App\Entity\Dm\UsersSuggestionsNotifications;
use App\Service\NotificationService;
use App\Tests\Fixtures\CoaFixture;
use App\Tests\Fixtures\DmCollectionFixture;
use App\Tests\Fixtures\DmStatsFixture;
use App\Tests\TestCommon;
use DateTime;

class NotificationTest extends TestCommon
{
    protected function getEmNamesToCreate(): array
    {
        return ['dm', 'dm_stats', 'coa'];
    }

    public function setUp()
    {
        parent::setUp();
        DmCollectionFixture::$username = self::$defaultTestDmUserName;
        $this->loadFixtures([DmCollectionFixture::class], true, 'dm');

        DmStatsFixture::$userId = 1;
        $this->loadFixtures([DmStatsFixture::class], true, 'dm_stats');

        $this->loadFixtures([CoaFixture::class], true, 'coa');
    }

    public function testSendNotification(): void
    {
        NotificationService::$mockResultsStack = [
            'OK'
        ];

        $this->buildAuthenticatedServiceWithTestUser('/notification/send', self::$rawSqlUser, 'POST')
            ->call();

        $notificationsSentToUser = $this->getEm('dm')->getRepository(UsersSuggestionsNotifications::class)->findBy([
            'user' => $this->getEm('dm')->getRepository(Users::class)->findOneBy(['username' => DmCollectionFixture::$username])
        ]);

        $this->assertCount(2, $notificationsSentToUser);
        $this->assertEquals('fr/PM 315', $notificationsSentToUser[0]->getIssuecode());
        $this->assertEquals('Picsou Magazine 315', $notificationsSentToUser[0]->getText());
        $this->assertEquals('fr/DDD 1', $notificationsSentToUser[1]->getIssuecode());
        $this->assertEquals('Dynastie 1', $notificationsSentToUser[1]->getText());
        foreach($notificationsSentToUser as $notificationSentToUser) {
            $notificationDate = $notificationSentToUser->getDate();
            $this->assertEquals((new DateTime())->format('Y-m-d'), $notificationDate->format('Y-m-d'));
        }
    }

    public function testSendNotificationAlreadySent(): void
    {
        $currentUser = $this->getEm('dm')->getRepository(Users::class)->findOneBy(['username' => DmCollectionFixture::$username]);

        $existingNotification = (new UsersSuggestionsNotifications())
            ->setIssuecode('fr/DDD 1')
            ->setUser($currentUser)
            ->setDate(new DateTime('yesterday'))
            ->setText('Notification body');
        $this->getEm('dm')->persist($existingNotification);
        $this->getEm('dm')->flush();

        NotificationService::$mockResultsStack = [
            'OK'
        ];
        $this->buildAuthenticatedServiceWithTestUser('/notification/send', self::$rawSqlUser, 'POST')
            ->call();
$notificationsSentToUser = $this->getEm('dm')->getRepository(UsersSuggestionsNotifications::class)->findBy([
            'user' => $currentUser
        ]);

        $this->assertCount(2, $notificationsSentToUser);

        $newNotifications = array_values(array_filter($notificationsSentToUser, function(UsersSuggestionsNotifications $notification) {
            return $notification->getDate()->format('Y-m-d') === (new DateTime())->format('Y-m-d');
        }));
        $this->assertCount(1, $newNotifications);
        $this->assertEquals('fr/PM 315', $newNotifications[0]->getIssuecode());
        $this->assertEquals('Picsou Magazine 315', $newNotifications[0]->getText());
    }
}
