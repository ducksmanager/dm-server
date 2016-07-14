<?php
namespace Wtd\Test;

use Silex\Application;
use Symfony\Component\HttpFoundation\Response;

require __DIR__ . '/../test_bootstrap.php';

class CollectionTest extends TestCommon
{
    public function testCreateCollection() {
        $response = $this->callAuthenticatedService([]);
        $this->assertTrue($response->isClientError());
    }
}
