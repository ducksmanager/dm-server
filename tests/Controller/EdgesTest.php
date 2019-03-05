<?php
namespace App\Tests;

use App\Tests\Fixtures\EdgesFixture;

class EdgesTest extends TestCommon
{
    protected function getEmNamesToCreate(): array
    {
        return ['dm'];
    }

    public function setUp()
    {
        parent::setUp();
        $this->loadFixture('dm', new EdgesFixture());
    }

    public function testGetEdges()
    {
        $publicationCode = 'fr/JM';

        $response = $this->buildAuthenticatedServiceWithTestUser(
            "/edges/$publicationCode/3001", self::$dmUser)->call();

        $objectResponse = json_decode($this->getResponseContent($response));
        $edge1 = $objectResponse[0];

        $this->assertEquals(1, $edge1->id);
        $this->assertEquals('fr/JM', $edge1->publicationcode);
        $this->assertEquals('3001', $edge1->issuenumber);
    }

    public function testGetReferenceEdges()
    {
        $publicationCode = 'fr/JM';

        $response = $this->buildAuthenticatedServiceWithTestUser(
            "/edges/references/$publicationCode/3002", self::$dmUser)->call();

        $objectResponse = json_decode($this->getResponseContent($response));
        $edgeReference1 = $objectResponse[0];

        $this->assertEquals('3002', $edgeReference1->issuenumber);
        $this->assertEquals('3001', $edgeReference1->referenceissuenumber);
    }
}
