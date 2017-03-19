<?php
namespace DmServer\Test;

use DmServer\DmServer;
use EdgeCreator\Models\EdgecreatorIntervalles;
use EdgeCreator\Models\EdgecreatorModeles2;
use EdgeCreator\Models\EdgecreatorValeurs;
use EdgeCreator\Models\ImagesMyfonts;

class EdgeCreatorTest extends TestCommon
{

    public function setUp()
    {
        parent::setUp();
        $collectionUserInfo = self::createTestCollection();
        self::setSessionUser($this->app, $collectionUserInfo);
        self::createEdgeCreatorData();
    }

    public function testCreateStepWithOptionValue() {
        $service = $this->buildAuthenticatedServiceWithTestUser('/edgecreator/step/fr/PM/1', TestCommon::$edgecreatorUser, 'PUT', [
            'functionname' => 'TexteMyFonts',
            'optionname' => 'Chaine',
            'optionvalue' => 'hello',
            'firstissuenumber' => '1',
            'lastissuenumber' => '2'
        ]);
        $response = $service->call();

        $objectResponse = json_decode($response->getContent());

        $createdModel = DmServer::getEntityManager(DmServer::CONFIG_DB_KEY_EDGECREATOR)->getRepository(EdgecreatorModeles2::class)->findOneBy([
            'pays' => 'fr',
            'magazine' => 'PM',
            'ordre' => '1'
        ]);

        $this->assertEquals($createdModel->getId(), $objectResponse->optionid);

        $createdValue = DmServer::getEntityManager(DmServer::CONFIG_DB_KEY_EDGECREATOR)->getRepository(EdgecreatorValeurs::class)->findOneBy([
            'optionValeur' => 'hello'
        ]);

        $this->assertEquals($createdValue->getId(), $objectResponse->valueid);

        $createdInterval = DmServer::getEntityManager(DmServer::CONFIG_DB_KEY_EDGECREATOR)->getRepository(EdgecreatorIntervalles::class)->findOneBy([
            'idValeur' => $createdValue->getId()
        ]);

        $this->assertEquals($createdInterval->getId(), $objectResponse->intervalid);
    }

    public function testCloneStep() {
        $service = $this->buildAuthenticatedServiceWithTestUser('/edgecreator/step/clone/fr/PM/502/1/to/2', TestCommon::$edgecreatorUser, 'POST');
        $response = $service->call();

        $objectResponse = json_decode($response->getContent());
    }

    public function testShiftStep() {
        $service = $this->buildAuthenticatedServiceWithTestUser('/edgecreator/step/shift/fr/PM/502/1/inclusive', TestCommon::$edgecreatorUser, 'POST');
        $response = $service->call();

        $objectResponse = json_decode($response->getContent());

        $this->assertEquals([json_decode(json_encode(['old' => 1, 'new' => 2]))], $objectResponse->shifts);
    }

    public function testCreateMyfontsPreview() {
        $service = $this->buildAuthenticatedServiceWithTestUser('/edgecreator/myfontspreview', TestCommon::$edgecreatorUser, 'PUT', [
            'font' => 'Arial',
            'fgColor' => '#000000',
            'bgColor' => '#FFFFFF',
            'width' => 200,
            'text' => 'Hello preview',
            'precision' => 18,
        ]);
        $response = $service->call();

        $objectResponse = json_decode($response->getContent());

        $createdPreview = DmServer::getEntityManager(DmServer::CONFIG_DB_KEY_EDGECREATOR)->getRepository(ImagesMyfonts::class)->findOneBy([
            'texte' => 'Hello preview'
        ]);

        $this->assertEquals($createdPreview->getId(), $objectResponse->previewid);
    }

    public function testDeleteMyFontsPreview() {
        $em = DmServer::getEntityManager(DmServer::CONFIG_DB_KEY_EDGECREATOR);

        $newPreview = new ImagesMyfonts();
        $em->persist($newPreview);
        $em->flush();

        $newPreviewId = $newPreview->getId();

        $service = $this->buildAuthenticatedServiceWithTestUser('/edgecreator/myfontspreview/'.$newPreviewId, TestCommon::$edgecreatorUser, 'DELETE');
        $response = $service->call();

        $this->assertNull($em->getRepository(ImagesMyfonts::class)->find($newPreviewId));
    }
}
