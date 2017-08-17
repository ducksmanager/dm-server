<?php
namespace DmServer\Test;

class EdgesTest extends TestCommon
{

    public function setUp()
    {
        parent::setUp();
        self::createEdgesData();
    }

    public function testGetEdges()
    {
        $publicationcode = 'fr/JM';

        $response = $this->buildAuthenticatedServiceWithTestUser(
            "/edges/$publicationcode/3001", TestCommon::$dmUser, 'GET')->call();

        $objectResponse = json_decode($response->getContent());
        $edge1 = unserialize($objectResponse[0]);

        $this->assertEquals(1, $edge1->getId());
        $this->assertEquals('fr/JM', $edge1->getPublicationcode());
        $this->assertEquals('3001', $edge1->getIssuenumber());
    }

    public function testGetReferenceEdges()
    {
        $publicationcode = 'fr/JM';

        $response = $this->buildAuthenticatedServiceWithTestUser(
            "/edges/references/$publicationcode/3002", TestCommon::$dmUser, 'GET')->call();

        $objectResponse = json_decode($response->getContent());
        $edgeReference1 = unserialize($objectResponse[0]);

        $this->assertEquals('3002', $edgeReference1['issuenumber']);
        $this->assertEquals('3001', $edgeReference1['referenceissuenumber']);
    }
}
