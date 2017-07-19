<?php
namespace DmServer\Test;

use DmServer\Controllers\AbstractController;
use DmServer\DmServer;
use EdgeCreator\Models\EdgecreatorIntervalles;
use EdgeCreator\Models\EdgecreatorModeles2;
use EdgeCreator\Models\EdgecreatorValeurs;
use EdgeCreator\Models\ImagesMyfonts;
use EdgeCreator\Models\ImagesTranches;
use EdgeCreator\Models\TranchesEnCoursModeles;
use EdgeCreator\Models\TranchesEnCoursValeurs;
use Symfony\Component\HttpFoundation\Response;

class EdgeCreatorTest extends TestCommon
{

    private function getEm() {
        return DmServer::getEntityManager(DmServer::CONFIG_DB_KEY_EDGECREATOR);
    }

    /**
     * @param string $countryCode
     * @param string $publicationCode
     * @param string $issueCode
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

    public function testCreateV2Model()
    {
        $response = $this->buildAuthenticatedServiceWithTestUser('/edgecreator/v2/model/fr/DDD/10',
            TestCommon::$edgecreatorUser, 'PUT'
        )->call();

        $createdModel = $this->getEm()->getRepository(TranchesEnCoursModeles::class)->findOneBy([
            'pays' => 'fr',
            'magazine' => 'DDD',
            'numero' => '10'
        ]);

        $objectResponse = json_decode($response->getContent());

        $this->assertEquals($createdModel->getId(), $objectResponse->modelid);
    }

    public function testCreateV2ModelAlreadyExisting()
    {
        $this->buildAuthenticatedServiceWithTestUser('/edgecreator/v2/model/fr/DDD/10',
            TestCommon::$edgecreatorUser, 'PUT'
        )->call();

        // Another time
        $response = $this->buildAuthenticatedServiceWithTestUser('/edgecreator/v2/model/fr/DDD/10',
            TestCommon::$edgecreatorUser, 'PUT'
        )->call();

        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        $this->assertContains('UNIQUE constraint failed', $response->getContent());
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
        ])), $responseModel);
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

        $response = $this->buildAuthenticatedServiceWithTestUser("/edgecreator/v2/step/clone/{$model->getId()}/1/to/2", TestCommon::$edgecreatorUser, 'POST')->call();

        $objectResponse = json_decode($response->getContent());

        $this->assertEquals([json_decode(json_encode([
            ['old' => 1, 'new' => 2]
        ]))], $objectResponse->newStepNumbers);
    }

    public function testCloneStepNothingToClone() {
        $model = $this->getV2Model('fr', 'PM', '502');

        $response = $this->buildAuthenticatedServiceWithTestUser("/edgecreator/v2/step/clone/{$model->getId()}/3/to/4", TestCommon::$edgecreatorUser, 'POST')->call();

        $this->assertEquals('No values to clone for '.json_encode([
            'idModele' => '1',
            'ordre' => '3'
        ]), $response->getContent());
    }

    public function testUpdateNonExistingStep() {
        $model = $this->getV2Model('fr', 'PM', '502');

        $response = $this->buildAuthenticatedServiceWithTestUser("/edgecreator/v2/step/{$model->getId()}/3", TestCommon::$edgecreatorUser, 'POST', [
            'options' => [
                'Couleur' => '#DDDDDD',
                'Pos_x' => '1',
                'Pos_y' => '2'
            ]
        ])->call();

        $this->assertEquals('No option exists for this step and no function name was provided', $response->getContent());
    }

    public function testUpdateStepWithInvalidOptions() {
        $model = $this->getV2Model('fr', 'PM', '502');

        $response = $this->buildAuthenticatedServiceWithTestUser("/edgecreator/v2/step/{$model->getId()}/3", TestCommon::$edgecreatorUser, 'POST', [
            'options' => '2'
        ])->call();

        $this->assertEquals('Invalid options input : 2', $response->getContent());
    }

    public function testUpdateStepWithEmptyOptions() {
        $model = $this->getV2Model('fr', 'PM', '502');

        $response = $this->buildAuthenticatedServiceWithTestUser("/edgecreator/v2/step/{$model->getId()}/3", TestCommon::$edgecreatorUser, 'POST', [
            'options!!!' => []
        ])->call();

        $this->assertEquals('No options provided, ignoring step 3', $response->getContent());
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

    public function testInsertStep() {
        $model = $this->getV2Model('fr', 'PM', '502');

        $response = $this->buildAuthenticatedServiceWithTestUser("/edgecreator/v2/step/{$model->getId()}/2", TestCommon::$edgecreatorUser, 'POST', [
            'stepfunctionname' => 'Remplir',
            'options' => [
                'Couleur' => '#AAAAAA',
                'Pos_x' => '5',
                'Pos_y' => '10'
            ]
        ])->call();

        $objectResponse = json_decode($response->getContent());

        $this->assertEquals(
            [
                [
                    'name' => 'Couleur',
                    'value' => '#AAAAAA'
                ],[
                    'name' => 'Pos_x',
                    'value' => '5'
                ],[
                    'name' => 'Pos_y',
                    'value' => '10'
                ]
            ],
            json_decode(json_encode($objectResponse->valueids), true)
        );
        /** @var TranchesEnCoursValeurs[] $values */
        $values = $this->getEm()->getRepository(TranchesEnCoursValeurs::class)->findBy([
            'idModele' => $model->getId(),
            'ordre' => 2
        ]);

        $this->assertEquals(3, count($values));
        $this->assertEquals('Couleur', $values[0]->getOptionNom());
        $this->assertEquals('#AAAAAA', $values[0]->getOptionValeur());

        $this->assertEquals('Pos_x', $values[1]->getOptionNom());
        $this->assertEquals('5', $values[1]->getOptionValeur());

        $this->assertEquals('Pos_y', $values[2]->getOptionNom());
        $this->assertEquals('10', $values[2]->getOptionValeur());
    }

    public function testShiftStep() {
        $model = $this->getV2Model('fr', 'PM', '502');

        $response = $this->buildAuthenticatedServiceWithTestUser("/edgecreator/v2/step/shift/{$model->getId()}/1/inclusive", TestCommon::$edgecreatorUser, 'POST')->call();
        $objectResponse = json_decode($response->getContent());

        $this->assertEquals(json_decode(json_encode([
            ['old' => 1, 'new' => 2],
            ['old' => 2, 'new' => 3]
        ])), $objectResponse->shifts);
    }

    public function testDeleteStep() {
        $model = $this->getV2Model('fr', 'PM', '502');
        $stepToRemove = 1;

        $response = $this->buildAuthenticatedServiceWithTestUser("/edgecreator/v2/step/{$model->getId()}/$stepToRemove", TestCommon::$edgecreatorUser, 'DELETE')->call();
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

        $designers = ['designer1'];
        $photographers = ['photograph1', 'photograph2'];

        $response = $this->buildAuthenticatedServiceWithTestUser("/edgecreator/model/v2/{$model->getId()}/readytopublish/1", TestCommon::$edgecreatorUser, 'POST', [
            'designers' => $designers,
            'photographers' => $photographers
        ])
            ->call();

        $objectResponse = json_decode($response->getContent(), true);

        $this->assertEquals($model->getId(), $objectResponse['model']['id']);
        $this->assertEquals(implode(',', $photographers), $objectResponse['model']['photographes']);
        $this->assertEquals(implode(',', $designers), $objectResponse['model']['createurs']);

        $newModel = $this->getV2Model('fr', 'PM', '502');
        $this->assertEquals(true, $newModel->getPretepourpublication());
    }


    public function testSetModelNotReadyForPublication() {
        $model = $this->getV2Model('fr', 'PM', '502');
        $model->setPhotographes('a,b');
        $model->setCreateurs('c,d');
        $this->getEm()->flush($model);

        $response = $this->buildAuthenticatedServiceWithTestUser("/edgecreator/model/v2/{$model->getId()}/readytopublish/0", TestCommon::$edgecreatorUser, 'POST')
            ->call();

        $objectResponse = json_decode($response->getContent());

        $this->assertEquals($model->getId(), $objectResponse->model->id);
        $this->assertEquals($model->getPretepourpublication(), $objectResponse->readytopublish);
        $this->assertEquals('a,b', $objectResponse->model->photographes); // should be unchanged
        $this->assertEquals('c,d', $objectResponse->model->createurs); // should be unchanged

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

    public function testAddMultipleEdgePhoto() {
        $hash = sha1('test');

        $response = $this->buildAuthenticatedServiceWithTestUser("/edgecreator/multiple_edge_photo", TestCommon::$edgecreatorUser, 'PUT', [
            'hash' => sha1('test2'),
            'filename' => 'photo2.jpg'
        ])->call();

        $objectResponse = json_decode($response->getContent());
        $this->assertEquals(['id' => 2 ], (array) $objectResponse->photo);
    }

    public function testGetMultipleEdgePhotos() {
        $response = $this->buildAuthenticatedServiceWithTestUser("/edgecreator/multiple_edge_photo/today", TestCommon::$edgecreatorUser, 'GET')->call();

        $photo = $this->getEm()->getRepository(ImagesTranches::class)->findOneBy([
            'hash' => sha1('test')
        ]);

        $objectResponse = json_decode($response->getContent());
        $this->assertEquals(1, count($objectResponse));
        $photoResult = $objectResponse[0];
        $this->assertEquals($photo->getId(), $photoResult->id);
        $this->assertEquals($photo->getIdUtilisateur(), $photoResult->idUtilisateur);
        $this->assertEquals($photo->getHash(), $photoResult->hash);
        $this->assertEquals($photo->getDateHeure()->getTimestamp(), $photoResult->dateheure->timestamp);
    }

    public function testGetMultipleEdgePhotoByHash() {

        $hash = sha1('test');
        $response = $this->buildAuthenticatedServiceWithTestUser("/edgecreator/multiple_edge_photo/hash/{$hash}", TestCommon::$edgecreatorUser, 'GET')->call();

        $photo = $this->getEm()->getRepository(ImagesTranches::class)->findOneBy([
            'hash' => sha1('test')
        ]);

        $photoResult = json_decode($response->getContent());
        $this->assertEquals($photo->getId(), $photoResult->id);
        $this->assertEquals($photo->getIdUtilisateur(), $photoResult->idUtilisateur);
        $this->assertEquals($photo->getHash(), $photoResult->hash);
        $this->assertEquals($photo->getDateHeure()->getTimestamp(), $photoResult->dateheure->timestamp);
    }
}
