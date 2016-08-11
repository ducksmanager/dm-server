<?php
namespace Wtd\Test;

use Symfony\Component\HttpFoundation\Response;
use Wtd\Models\Numeros;
use Wtd\Models\Users;

class FetchCollectionTest extends TestCommon
{
    public function setUp()
    {
        parent::setUp();
        self::createTestCollection('dm_user');
        self::createCoaData();
    }


    public function testFetchCollection() {
        $service = $this->buildAuthenticatedServiceWithTestUser('/collection/fetch', 'GET');
        $response = $service->call();

        $objectResponse = json_decode($response->getContent());

        $this->assertInternalType('object', $objectResponse);
    }
}
