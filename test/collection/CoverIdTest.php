<?php
namespace DmServer\Test;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use DmServer\CoverIdController;

class CoverIdTest extends TestCommon
{
    static $coverIds = null;

    static $uploadDestination = ['/tmp', 'test.jpg'];

    static $exampleImageToUpload = 'cover_example_to_upload.jpg';

    public function setUp()
    {
        parent::setUp();
        self::createCoaData();
        self::$coverIds = self::createCoverIds();

        @unlink(implode(DIRECTORY_SEPARATOR, self::$uploadDestination));
        copy(self::getPathToFileToUpload(self::$exampleImage), self::getPathToFileToUpload(self::$exampleImageToUpload));

        CoverIdController::$similarImagesEngine = 'mocked';
    }

    public function tearDown()
    {
        parent::tearDown();
        @unlink(self::getPathToFileToUpload(self::$exampleImageToUpload));
    }

    public function testGetIssueListByIssueCodes() {
        $service = $this->buildAuthenticatedServiceWithTestUser(
            '/cover-id/issuecodes/'
            . implode(',', [self::$coverIds[0], self::$coverIds[2]]), TestCommon::$testUser);
        $response = $service->call();

        $objectResponse = json_decode($response->getContent());

        $this->assertInternalType('object', $objectResponse);
        $this->assertEquals(2, count(get_object_vars($objectResponse)));

        $this->assertInternalType('string', $objectResponse->{self::$coverIds[0]});
        $this->assertEquals('fr/DDD 1', $objectResponse->{self::$coverIds[0]});

        $this->assertInternalType('string', $objectResponse->{self::$coverIds[2]});
        $this->assertEquals('fr/MP 300', $objectResponse->{self::$coverIds[2]});
    }

    public function testCoverIdSearchMultipleUploads() {
        $service = $this->buildAuthenticatedServiceWithTestUser(
            '/cover-id/search', TestCommon::$testUser, 'POST', array(), array(
                'wtd_jpg' => self::getCoverIdSearchUploadImage(),
                'wtd_jpg2' => self::getCoverIdSearchUploadImage()
            )
        );
        $response = $service->call();

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertEquals('Invalid number of uploaded files : should be 1, was 2', $response->getContent());
    }

    public function testCoverIdSearch() {
        $this->assertFileNotExists(implode(DIRECTORY_SEPARATOR, self::$uploadDestination));

        $service = $this->buildAuthenticatedServiceWithTestUser(
            '/cover-id/search', TestCommon::$testUser, 'POST', array(), array(
                'wtd_jpg' => self::getCoverIdSearchUploadImage()
            )
        );
        $response = $service->call();

        $this->assertFileExists(implode(DIRECTORY_SEPARATOR, self::$uploadDestination));
        $this->assertJson($response->getContent());
    }

    public function testCoverIdSearchInvalidFileName() {
        $this->assertFileNotExists(implode(DIRECTORY_SEPARATOR, self::$uploadDestination));

        $service = $this->buildAuthenticatedServiceWithTestUser(
            '/cover-id/search', TestCommon::$testUser, 'POST', array(), array(
                'wtd_invalid_jpg' => self::getCoverIdSearchUploadImage()
            )
        );
        $response = $service->call();

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertEquals('Invalid upload file : expected file name wtd_jpg', $response->getContent());
    }

    public function testDownloadCover() {
        $service = $this->buildAuthenticatedServiceWithTestUser(
            '/cover-id/download/webusers/2010/12/fr_ddd_001a_001.jpg', TestCommon::$testUser);
        /** @var BinaryFileResponse $response */
        $response = $service->call();

        file_put_contents('/tmp/test.jpg', $response->getContent());
        $type=exif_imagetype('/tmp/test.jpg');
        $this->assertEquals(IMAGETYPE_JPEG, $type);   //A specific image type

    }

    private static function getCoverIdSearchUploadImage() {
        return new UploadedFile(
            self::getPathToFileToUpload(self::$exampleImageToUpload),
            self::$exampleImageToUpload,
            'image/jpeg'
        );
    }
}
