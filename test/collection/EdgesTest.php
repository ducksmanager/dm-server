<?php
namespace DmServer\Test;

use Dm\Models\TranchesDoublons;
use DmServer\SimilarImagesHelper;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;

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
}
