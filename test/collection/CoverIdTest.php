<?php
namespace Wtd\Test;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Wtd\CoverIdController;

class CoverIdTest extends TestCommon
{
    static $coverIds = null;

    static $uploadDestination = ['/tmp', 'test.jpg'];

    static $exampleImage = 'cover_example.jpg';
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
            . implode(',', [self::$coverIds[0], self::$coverIds[2]]), TestCommon::$testUser, 'GET');
        $response = $service->call();

        $arrayResponse = json_decode($response->getContent());

        $this->assertInternalType('array', $arrayResponse);
        $this->assertEquals(2, count($arrayResponse));

        $this->assertInternalType('string', $arrayResponse[0]);
        $this->assertEquals('fr/DDD   1', $arrayResponse[0]);

        $this->assertInternalType('string', $arrayResponse[1]);
        $this->assertEquals('fr/MP    2', $arrayResponse[1]);
    }

    public function testCoverIdSearchMultipleUploads() {
        $service = $this->buildAuthenticatedServiceWithTestUser(
            '/cover-id/search', TestCommon::$testUser, 'POST', array(), array(
                'WTD_jpg' => self::getCoverIdSearchUploadImage(),
                'WTD_jpg2' => self::getCoverIdSearchUploadImage()
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
                'WTD_jpg' => self::getCoverIdSearchUploadImage()
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
                'WTD_invalid_jpg' => self::getCoverIdSearchUploadImage()
            )
        );
        $response = $service->call();

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertEquals('Invalid upload file : expected file name WTD_jpg', $response->getContent());
    }

    private static function getPathToFileToUpload($fileName) {
        return implode(DIRECTORY_SEPARATOR, array(__DIR__, '..', 'fixtures', $fileName));
    }

    private static function getCoverIdSearchUploadImage() {
        return new UploadedFile(
            self::getPathToFileToUpload(self::$exampleImageToUpload),
            self::$exampleImageToUpload,
            'image/jpeg'
        );
    }
}
