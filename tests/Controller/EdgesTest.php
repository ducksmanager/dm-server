<?php
namespace App\Tests\Controller;

use App\Tests\Fixtures\EdgesFixture;
use App\Tests\TestCommon;

class EdgesTest extends TestCommon
{
    protected function getEmNamesToCreate(): array
    {
        return ['dm'];
    }

    public function setUp() : void
    {
        parent::setUp();
        $this->loadFixtures([ EdgesFixture::class ], true, 'dm');
    }

    public function testGetEdges(): void {
        $publicationCode = 'fr/JM';

        $response = $this->buildAuthenticatedServiceWithTestUser(
            "/edges/$publicationCode/3001", self::$dmUser)->call();

        $objectResponse = json_decode($this->getResponseContent($response));
        $edge1 = $objectResponse[0];

        $this->assertEquals(1, $edge1->id);
        $this->assertEquals('fr/JM', $edge1->publicationcode);
        $this->assertEquals('3001', $edge1->issuenumber);
    }

    public function testGetReferenceEdges(): void {
        $publicationCode = 'fr/JM';

        $response = $this->buildAuthenticatedServiceWithTestUser(
            "/edges/references/$publicationCode/3002", self::$dmUser)->call();

        $objectResponse = json_decode($this->getResponseContent($response));
        $edgeReference1 = $objectResponse[0];

        $this->assertEquals('3002', $edgeReference1->issuenumber);
        $this->assertEquals('3001', $edgeReference1->referenceissuenumber);
    }
}
