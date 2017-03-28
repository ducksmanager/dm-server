<?php
namespace DmServer\Test;

use DmServer\DmServer;
use EdgeCreator\Models\EdgecreatorIntervalles;
use EdgeCreator\Models\EdgecreatorModeles2;
use EdgeCreator\Models\EdgecreatorValeurs;
use EdgeCreator\Models\ImagesMyfonts;
use EdgeCreator\Models\TranchesEnCoursModeles;
use EdgeCreator\Models\TranchesEnCoursValeurs;
use Symfony\Component\HttpFoundation\Response;

class EdgeCreatorTest extends TestCommon
{

    public function setUp()
    {
        parent::setUp();
        $collectionUserInfo = self::createTestCollection();
        self::setSessionUser($this->app, $collectionUserInfo);
        self::createEdgeCreatorData();
    }

    public function testLoadV2Model() {

        $modelRepository = DmServer::getEntityManager(DmServer::CONFIG_DB_KEY_EDGECREATOR)->getRepository(TranchesEnCoursModeles::class);

        $modelId = $modelRepository->findOneBy([
            'pays' => 'fr',
            'magazine' => 'PM',
            'numero' => '502'
        ])->getId();

        $response = $this->buildAuthenticatedServiceWithTestUser("/edgecreator/v2/model/$modelId", TestCommon::$edgecreatorUser, 'GET')->call();

        $model = json_decode($response->getContent());

        $this->assertEquals(json_decode(json_encode([
            'id' => 1,
            'pays' => 'fr',
            'magazine' => 'PM',
            'numero' => '502',
            'username' => 'dm_test_user',
            'nomphotoprincipale' => NULL,
            'photographes' => NULL,
            'createurs' => NULL,
            'active' => '1',
            'pretepourpublication' => '0'
        ])), json_decode($model));
    }

    public function testCreateStepWithOptionValue() {
        $response = $this->buildAuthenticatedServiceWithTestUser('/edgecreator/step/fr/PM/1', TestCommon::$edgecreatorUser, 'PUT', [
            'functionname' => 'TexteMyFonts',
            'optionname' => 'Chaine',
            'optionvalue' => 'hello',
            'firstissuenumber' => '1',
            'lastissuenumber' => '2'
        ])->call();

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

    public function testCreateStepWithOptionValueExistingInterval()
    {
        $response = $this->buildAuthenticatedServiceWithTestUser('/edgecreator/step/fr/DDD/1',
            TestCommon::$edgecreatorUser, 'PUT', [
                'functionname' => 'Remplir',
                'optionname' => 'Couleur',
                'optionvalue' => '#FF0000',
                'firstissuenumber' => '1',
                'lastissuenumber' => '3'
            ])->call();

        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }

    public function testCloneStep() {
        $response = $this->buildAuthenticatedServiceWithTestUser('/edgecreator/step/clone/fr/PM/502/1/to/2', TestCommon::$edgecreatorUser, 'POST')->call();

        $objectResponse = json_decode($response->getContent());

        $this->assertEquals([json_decode(json_encode([
            ['old' => 1, 'new' => 2]
        ]))], $objectResponse->newStepNumbers);
    }

    public function testUpdateStep() {
        $em = DmServer::getEntityManager(DmServer::CONFIG_DB_KEY_EDGECREATOR);

        $model = $em->getRepository(TranchesEnCoursModeles::class)->findOneBy([
            'pays' => 'fr',
            'magazine' => 'PM',
            'numero' => '502',
        ]);

        $response = $this->buildAuthenticatedServiceWithTestUser('/edgecreator/v2/step/fr/PM/502/1', TestCommon::$edgecreatorUser, 'POST', [
            'options' => [
                'Couleur' => '#DDDDDD',
                'Pos_x' => '1',
                'Pos_y' => '2'
            ]
        ])->call();

        $objectResponse = json_decode($response->getContent());

        $this->assertEquals($model->getId(),$objectResponse->modelid);

        /** @var TranchesEnCoursValeurs[] $values */
        $values = $em->getRepository(TranchesEnCoursValeurs::class)->findBy([
            'idModele' => $objectResponse->modelid,
        ]);

        // Unchanged
        $this->assertEquals(2, $values[0]->getOrdre());
        $this->assertEquals('Couleur_texte', $values[0]->getOptionNom());
        $this->assertEquals('#000000', $values[0]->getOptionValeur());

        $this->assertEquals(1, $values[1]->getOrdre());
        $this->assertEquals('Couleur', $values[1]->getOptionNom());
        $this->assertEquals('#DDDDDD', $values[1]->getOptionValeur());

        $this->assertEquals(1, $values[2]->getOrdre());
        $this->assertEquals('Pos_x', $values[2]->getOptionNom());
        $this->assertEquals('1', $values[2]->getOptionValeur());

        $this->assertEquals(1, $values[3]->getOrdre());
        $this->assertEquals('Pos_y', $values[3]->getOptionNom());
        $this->assertEquals('2', $values[3]->getOptionValeur());
    }

    public function testShiftStep() {
        $response = $this->buildAuthenticatedServiceWithTestUser('/edgecreator/step/shift/fr/PM/502/1/inclusive', TestCommon::$edgecreatorUser, 'POST')->call();
        $objectResponse = json_decode($response->getContent());

        $this->assertEquals(json_decode(json_encode([
            ['old' => 1, 'new' => 2],
            ['old' => 2, 'new' => 3]
        ])), $objectResponse->shifts);
    }

    public function testCreateMyfontsPreview() {
        $response = $this->buildAuthenticatedServiceWithTestUser('/edgecreator/myfontspreview', TestCommon::$edgecreatorUser, 'PUT', [
            'font' => 'Arial',
            'fgColor' => '#000000',
            'bgColor' => '#FFFFFF',
            'width' => 200,
            'text' => 'Hello preview',
            'precision' => 18,
        ])->call();

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

        $this->buildAuthenticatedServiceWithTestUser("/edgecreator/myfontspreview/$newPreviewId", TestCommon::$edgecreatorUser, 'DELETE')->call();

        $this->assertNull($em->getRepository(ImagesMyfonts::class)->find($newPreviewId));
    }

    public function testDeactivateModel() {
        $modelRepository = DmServer::getEntityManager(DmServer::CONFIG_DB_KEY_EDGECREATOR)->getRepository(TranchesEnCoursModeles::class);

        $modelId = $modelRepository->findOneBy([
            'pays' => 'fr',
            'magazine' => 'PM',
            'numero' => '502'
        ])->getId();

        $response = $this->buildAuthenticatedServiceWithTestUser("/edgecreator/model/v2/$modelId/deactivate", TestCommon::$edgecreatorUser, 'POST')
            ->call();

        $objectResponse = json_decode($response->getContent());

        $this->assertEquals($modelId, $objectResponse->deactivated);

        $newModel = $modelRepository->findOneBy([
            'pays' => 'fr',
            'magazine' => 'PM',
            'numero' => '502'
        ]);

        $this->assertEquals(false, $newModel->getActive());
    }

    public function testSetModelReadyForPublication() {
        $modelRepository = DmServer::getEntityManager(DmServer::CONFIG_DB_KEY_EDGECREATOR)->getRepository(TranchesEnCoursModeles::class);

        $modelId = $modelRepository->findOneBy([
            'pays' => 'fr',
            'magazine' => 'PM',
            'numero' => '502'
        ])->getId();

        $response = $this->buildAuthenticatedServiceWithTestUser("/edgecreator/model/v2/$modelId/readytopublish/1", TestCommon::$edgecreatorUser, 'POST')
            ->call();

        $objectResponse = json_decode($response->getContent());

        $this->assertEquals(['modelid' => $modelId, 'readytopublish' => true], (array) $objectResponse->readytopublish);

        $newModel = $modelRepository->findOneBy([
            'pays' => 'fr',
            'magazine' => 'PM',
            'numero' => '502'
        ]);

        $this->assertEquals(true, $newModel->getPretepourpublication());

        $response = $this->buildAuthenticatedServiceWithTestUser("/edgecreator/model/v2/$modelId/readytopublish/0", TestCommon::$edgecreatorUser, 'POST')
            ->call();

        $objectResponse = json_decode($response->getContent());

        $this->assertEquals(['modelid' => $modelId, 'readytopublish' => false], (array) $objectResponse->readytopublish);

        $newModel = $modelRepository->findOneBy([
            'pays' => 'fr',
            'magazine' => 'PM',
            'numero' => '502'
        ]);

        $this->assertEquals(false, $newModel->getPretepourpublication());
    }

    public function testSetMainPhoto() {
        $photoName = 'myphoto.jpg';

        $modelRepository = DmServer::getEntityManager(DmServer::CONFIG_DB_KEY_EDGECREATOR)->getRepository(TranchesEnCoursModeles::class);

        $modelId = $modelRepository->findOneBy([
            'pays' => 'fr',
            'magazine' => 'PM',
            'numero' => '502'
        ])->getId();

        $response = $this->buildAuthenticatedServiceWithTestUser("/edgecreator/model/v2/$modelId/photo/main", TestCommon::$edgecreatorUser, 'PUT', [
            'photoname' => $photoName
        ])->call();

        $objectResponse = json_decode($response->getContent());

        $this->assertEquals(['modelid' => $modelId, 'photoname' => $photoName], (array) $objectResponse->mainphoto);

        $newModel = $modelRepository->findOneBy([
            'pays' => 'fr',
            'magazine' => 'PM',
            'numero' => '502'
        ]);

        $this->assertEquals($photoName, $newModel->getNomphotoprincipale());
    }
}
