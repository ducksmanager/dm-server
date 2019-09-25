<?php
namespace App\Tests;

use App\Entity\Dm\Users;
use App\Entity\Dm\UsersSuggestionsNotifications;
use App\Service\NotificationService;
use App\Tests\Fixtures\DmCollectionFixture;
use App\Tests\Fixtures\DmStatsFixture;

class NotificationTest extends TestCommon
{
    protected function getEmNamesToCreate(): array
    {
        return ['dm', 'dm_stats'];
    }

    public function setUp()
    {
        parent::setUp();
        DmCollectionFixture::$username = self::$defaultTestDmUserName;
        $this->loadFixtures([DmCollectionFixture::class], true, 'dm');

        DmStatsFixture::$userId = 1;
        $this->loadFixtures([DmStatsFixture::class], true, 'dm_stats');
    }

    public function testSendNotification(): void
    {
        NotificationService::$mockResultsStack = [
            'OK'
        ];

        $this->buildAuthenticatedServiceWithTestUser('/notification/send', self::$rawSqlUser, 'POST')
            ->call();

        $notificationsSentForUser = $this->getEm('dm')->getRepository(UsersSuggestionsNotifications::class)->findBy([
            'user' => $this->getEm('dm')->getRepository(Users::class)->findOneBy(['username' => DmCollectionFixture::$username])
        ]);

        $this->assertCount(1, $notificationsSentForUser);
        $this->assertEquals(true, $notificationsSentForUser[0]->getNotified());
    }
}
