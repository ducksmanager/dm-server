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

    private function getEm() {
        return DmServer::getEntityManager(DmServer::CONFIG_DB_KEY_EDGECREATOR);
    }

    /**
     * @return TranchesEnCoursModeles
     */
    private function getV2Model($countryCode, $publicationCode, $issueCode)
    {
        return $this->getEm()->getRepository(TranchesEnCoursModeles::class)->findOneBy([
            'pays' => $countryCode,
            'magazine' => $publicationCode,
            'numero' => $issueCode,
        ]);
    }

    public function setUp()
    {
        parent::setUp();
        $collectionUserInfo = self::createTestCollection();
        self::setSessionUser($this->app, $collectionUserInfo);
        self::createEdgeCreatorData();
    }

    public function testLoadV2Model() {
        $model = $this->getV2Model('fr', 'PM', '502');

        $response = $this->buildAuthenticatedServiceWithTestUser("/edgecreator/v2/model/{$model->getId()}", TestCommon::$edgecreatorUser, 'GET')->call();

        $responseModel = json_decode($response->getContent());

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
        ])), json_decode($responseModel));
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

        $createdModel = $this->getEm()->getRepository(EdgecreatorModeles2::class)->findOneBy([
            'pays' => 'fr',
            'magazine' => 'PM',
            'ordre' => '1'
        ]);

        $this->assertEquals($createdModel->getId(), $objectResponse->optionid);

        $createdValue = $this->getEm()->getRepository(EdgecreatorValeurs::class)->findOneBy([
            'optionValeur' => 'hello'
        ]);

        $this->assertEquals($createdValue->getId(), $objectResponse->valueid);

        $createdInterval = $this->getEm()->getRepository(EdgecreatorIntervalles::class)->findOneBy([
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
        $model = $this->getV2Model('fr', 'PM', '502');

        $response = $this->buildAuthenticatedServiceWithTestUser("/edgecreator/step/clone/{$model->getId()}/1/to/2", TestCommon::$edgecreatorUser, 'POST')->call();

        $objectResponse = json_decode($response->getContent());

        $this->assertEquals([json_decode(json_encode([
            ['old' => 1, 'new' => 2]
        ]))], $objectResponse->newStepNumbers);
    }

    public function testUpdateStep() {
        $model = $this->getV2Model('fr', 'PM', '502');

        $response = $this->buildAuthenticatedServiceWithTestUser("/edgecreator/v2/step/{$model->getId()}/1", TestCommon::$edgecreatorUser, 'POST', [
            'options' => [
                'Couleur' => '#DDDDDD',
                'Pos_x' => '1',
                'Pos_y' => '2'
            ]
        ])->call();

        $objectResponse = json_decode($response->getContent());

        $this->assertEquals(
            [
                [
                    'name' => 'Couleur',
                    'value' => '#DDDDDD'
                ],[
                    'name' => 'Pos_x',
                    'value' => '1'
                ],[
                    'name' => 'Pos_y',
                    'value' => '2'
                ]
            ],
            json_decode(json_encode($objectResponse->valueids), true)
        );
        /** @var TranchesEnCoursValeurs[] $values */
        $values = $this->getEm()->getRepository(TranchesEnCoursValeurs::class)->findBy([
            'idModele' => $model->getId(),
            'ordre' => 2
        ]);

        // Unchanged
        $this->assertEquals(2, $values[0]->getOrdre());
        $this->assertEquals('Couleur_texte', $values[0]->getOptionNom());
        $this->assertEquals('#000000', $values[0]->getOptionValeur());
    }

    public function testShiftStep() {
        $model = $this->getV2Model('fr', 'PM', '502');

        $response = $this->buildAuthenticatedServiceWithTestUser("/edgecreator/step/shift/{$model->getId()}/1/inclusive", TestCommon::$edgecreatorUser, 'POST')->call();
        $objectResponse = json_decode($response->getContent());

        $this->assertEquals(json_decode(json_encode([
            ['old' => 1, 'new' => 2],
            ['old' => 2, 'new' => 3]
        ])), $objectResponse->shifts);
    }

    public function testDeleteStep() {
        $model = $this->getV2Model('fr', 'PM', '502');
        $stepToRemove = 1;

        $response = $this->buildAuthenticatedServiceWithTestUser("/edgecreator/step/{$model->getId()}/$stepToRemove", TestCommon::$edgecreatorUser, 'DELETE')->call();
        $objectResponse = json_decode($response->getContent());

        $this->assertEquals([
            'removed' => [
                'model' => $model->getId(),
                'step' => $stepToRemove
            ]
        ], json_decode(json_encode($objectResponse), true));

        $values = $this->getEm()->getRepository(TranchesEnCoursValeurs::class)->findBy([
            'idModele' => $model->getId(),
            'ordre' => $stepToRemove
        ]);

        $this->assertEquals(0, count($values));
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

        $createdPreview = $this->getEm()->getRepository(ImagesMyfonts::class)->findOneBy([
            'texte' => 'Hello preview'
        ]);

        $this->assertEquals($createdPreview->getId(), $objectResponse->previewid);
    }

    public function testDeleteMyFontsPreview() {
        $em = $this->getEm();

        $newPreview = new ImagesMyfonts();
        $em->persist($newPreview);
        $em->flush();

        $newPreviewId = $newPreview->getId();

        $this->buildAuthenticatedServiceWithTestUser("/edgecreator/myfontspreview/$newPreviewId", TestCommon::$edgecreatorUser, 'DELETE')->call();

        $this->assertNull($em->getRepository(ImagesMyfonts::class)->find($newPreviewId));
    }

    public function testDeactivateModel() {
        $model = $this->getV2Model('fr', 'PM', '502');

        $response = $this->buildAuthenticatedServiceWithTestUser("/edgecreator/model/v2/{$model->getId()}/deactivate", TestCommon::$edgecreatorUser, 'POST')
            ->call();

        $objectResponse = json_decode($response->getContent());

        $this->assertEquals($model->getId(), $objectResponse->deactivated);

        $newModel = $this->getV2Model('fr', 'PM', '502');
        $this->assertEquals(false, $newModel->getActive());
    }

    public function testSetModelReadyForPublication() {
        $model = $this->getV2Model('fr', 'PM', '502');

        $response = $this->buildAuthenticatedServiceWithTestUser("/edgecreator/model/v2/{$model->getId()}/readytopublish/1", TestCommon::$edgecreatorUser, 'POST')
            ->call();

        $objectResponse = json_decode($response->getContent());

        $this->assertEquals(['modelid' => $model->getId(), 'readytopublish' => true], (array) $objectResponse->readytopublish);

        $newModel = $this->getV2Model('fr', 'PM', '502');
        $this->assertEquals(true, $newModel->getPretepourpublication());


        $response = $this->buildAuthenticatedServiceWithTestUser("/edgecreator/model/v2/{$model->getId()}/readytopublish/0", TestCommon::$edgecreatorUser, 'POST')
            ->call();

        $objectResponse = json_decode($response->getContent());

        $this->assertEquals(['modelid' => $model->getId(), 'readytopublish' => false], (array) $objectResponse->readytopublish);

        $newModel = $this->getV2Model('fr', 'PM', '502');
        $this->assertEquals(false, $newModel->getPretepourpublication());
    }

    public function testSetMainPhoto() {
        $photoName = 'myphoto.jpg';

        $model = $this->getV2Model('fr', 'PM', '502');

        $response = $this->buildAuthenticatedServiceWithTestUser("/edgecreator/model/v2/{$model->getId()}/photo/main", TestCommon::$edgecreatorUser, 'PUT', [
            'photoname' => $photoName
        ])->call();

        $objectResponse = json_decode($response->getContent());

        $this->assertEquals(['modelid' => $model->getId(), 'photoname' => $photoName], (array) $objectResponse->mainphoto);

        $newModel = $this->getV2Model('fr', 'PM', '502');
        $this->assertEquals($photoName, $newModel->getNomphotoprincipale());
    }
}
