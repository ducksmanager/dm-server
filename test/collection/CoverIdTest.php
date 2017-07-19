<?php
namespace DmServer\Test;

use Dm\Contracts\Dtos\SimilarImagesOutput;
use DmServer\SimilarImagesHelper;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;

class CoverIdTest extends TestCommon
{
    static $coverIds = null;
    static $coverUrls = null;

    static $uploadDestination = ['/tmp', 'test.jpg'];

    static $exampleImageToUpload = 'cover_example_to_upload.jpg';

    public function setUp()
    {
        parent::setUp();
        self::createCoaData();
        list(self::$coverIds, self::$coverUrls) = self::createCoverIds();

        @unlink(implode(DIRECTORY_SEPARATOR, self::$uploadDestination));
        copy(self::getPathToFileToUpload(self::$exampleImage), self::getPathToFileToUpload(self::$exampleImageToUpload));

        $this->mockCoverSearchResults([
            "bounding_rects" => [
                "height" => 846,
                "width"  => 625,
                "x" => 67,
                "y" => 44
            ],
            "image_ids" => [1],
            "scores" => [58.0],
            "tags" => [''],
            "type" => "SEARCH_RESULTS"
        ]);
    }

    public function tearDown()
    {
        parent::tearDown();
        @unlink(self::getPathToFileToUpload(self::$exampleImageToUpload));
    }

    private function mockCoverSearchResults($mockedResponse) {
        SimilarImagesHelper::$mockedResults = json_encode($mockedResponse);
    }

    public function testGetIssueListByIssueCodes() {
        $response = $this->buildAuthenticatedServiceWithTestUser(
            '/cover-id/issuecodes/'
            . implode(',', [self::$coverIds[0], self::$coverIds[2]]), TestCommon::$dmUser)->call();

        $objectResponse = json_decode($response->getContent());

        $this->assertInternalType('object', $objectResponse);
        $this->assertEquals(2, count(get_object_vars($objectResponse)));

        $this->assertObjectHasAttribute('issuecode', $objectResponse->{self::$coverIds[0]});
        $this->assertEquals('fr/DDD 1', $objectResponse->{self::$coverIds[0]}->issuecode);

        $this->assertObjectHasAttribute('issuecode', $objectResponse->{self::$coverIds[2]});
        $this->assertEquals('fr/MP 300', $objectResponse->{self::$coverIds[2]}->issuecode);
    }

    public function testCoverIdSearchMultipleUploads() {
        $response = $this->buildAuthenticatedServiceWithTestUser(
            '/cover-id/search', TestCommon::$dmUser, 'POST', [], [
                'wtd_jpg' => self::getCoverIdSearchUploadImage(),
                'wtd_jpg2' => self::getCoverIdSearchUploadImage()
            ]
        )->call();

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertEquals('Invalid number of uploaded files : should be 1, was 2', $response->getContent());
    }

    public function testCoverIdSearch() {
        $this->assertFileNotExists(implode(DIRECTORY_SEPARATOR, self::$uploadDestination));

        $similarCoverIssuePublicationCode = 'fr/DDD';
        $similarCoverIssueNumber = '10';
        self::createEntryLike('fr/AR 101', self::$coverUrls[array_values(self::$coverIds)[0]], $similarCoverIssuePublicationCode, $similarCoverIssueNumber);

        $response = $this->buildAuthenticatedServiceWithTestUser(
            '/cover-id/search', TestCommon::$dmUser, 'POST', [], [
                'wtd_jpg' => self::getCoverIdSearchUploadImage()
            ]
        )->call();

        $this->assertFileExists(implode(DIRECTORY_SEPARATOR, self::$uploadDestination));
        $this->assertJsonStringEqualsJsonString(json_encode([
            "issues" => [
                "fr/DDD 1" => [
                    "countrycode" => "fr",
                    "publicationcode" => "fr/DDD",
                    "publicationtitle" => "Dynastie",
                    "issuenumber" => "1",
                    "coverid" => 1
                    ]/*, // Related issue: same cover story code
                $similarCoverIssuePublicationCode.' '.$similarCoverIssueNumber => [
                    "countrycode" => explode('/', $similarCoverIssuePublicationCode)[0],
                    "publicationcode" => $similarCoverIssuePublicationCode,
                    "publicationtitle" => "Dynastie",
                    "issuenumber" => $similarCoverIssueNumber,
                    "coverid" => count(self::$coverIds) + 1
                    ]*/
                ]
            ]), $response->getContent());
    }

    public function testCoverIdSearchSizeTooSmall() {
        $this->mockCoverSearchResults([
            "type" => "IMAGE_SIZE_TOO_SMALL"
        ]);

        $this->assertFileNotExists(implode(DIRECTORY_SEPARATOR, self::$uploadDestination));

        $response = $this->buildAuthenticatedServiceWithTestUser(
            '/cover-id/search', TestCommon::$dmUser, 'POST', [], [
                'wtd_jpg' => self::getCoverIdSearchUploadImage()
            ]
        )->call();

        $this->assertJsonStringEqualsJsonString(json_encode([
            "type" => "IMAGE_SIZE_TOO_SMALL"
            ]), $response->getContent());
    }

    public function testCoverIdSearchInvalidFileName() {
        $this->assertFileNotExists(implode(DIRECTORY_SEPARATOR, self::$uploadDestination));

        $response = $this->buildAuthenticatedServiceWithTestUser(
            '/cover-id/search', TestCommon::$dmUser, 'POST', [], [
                'wtd_invalid_jpg' => self::getCoverIdSearchUploadImage()
            ]
        )->call();

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertEquals('Invalid upload file : expected file name wtd_jpg', $response->getContent());
    }

    public function testDownloadCover() {
        /** @var BinaryFileResponse $response */
        $response = $this->buildAuthenticatedServiceWithTestUser('/cover-id/download/1', TestCommon::$dmUser)
            ->call();

        file_put_contents('/tmp/test.jpg', $response->getContent());
        $type=\exif_imagetype('/tmp/test.jpg');
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
