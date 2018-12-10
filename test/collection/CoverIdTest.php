<?php
namespace DmServer\Test;

use DmServer\SimilarImagesHelper;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;

class CoverIdTest extends TestCommon
{
    public static $coverIds;
    public static $coverUrls;

    public static $uploadDestination = ['/tmp', 'test.jpg'];

    public static $exampleImageToUpload = 'cover_example_to_upload.jpg';

    public static $coverSearchResultsSimple = [
        'bounding_rects' => [
            'height' => 846,
            'width' => 625,
            'x' => 67,
            'y' => 44
        ],
        'image_ids' => [1],
        'scores' => [58.0],
        'tags' => [''],
        'type' => 'SEARCH_RESULTS'
    ];

    public static $coverSearchResultsMany = [
        'bounding_rects' => [
            'height' => 846,
            'width' => 625,
            'x' => 67,
            'y' => 44
        ],
        'image_ids' => [1,2,3,4,5,6,7,8,9,10,11],
        'scores' => [58.0,59.0,60.0,61.0,62.0,63.0,64.0,65.0,66.0,67.0,68.0],
        'tags' => [''],
        'type' => 'SEARCH_RESULTS'
    ];

    public function setUp()
    {
        parent::setUp();
        self::createCoaData();
        [self::$coverIds, self::$coverUrls] = self::createCoverIds();

        @unlink(implode(DIRECTORY_SEPARATOR, self::$uploadDestination));
        copy(self::getPathToFileToUpload(self::$exampleImage), self::getPathToFileToUpload(self::$exampleImageToUpload));
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
        $this->mockCoverSearchResults(self::$coverSearchResultsSimple);
        $response = $this->buildAuthenticatedServiceWithTestUser(
            '/cover-id/issuecodes/'
            . implode(',', [self::$coverIds[0], self::$coverIds[2]]), self::$dmUser)->call();

        $objectResponse = json_decode($this->getResponseContent($response));

        $this->assertIsObject($objectResponse);
        $this->assertCount(2, get_object_vars($objectResponse));

        $this->assertObjectHasAttribute('issuecode', $objectResponse->{self::$coverIds[0]});
        $this->assertEquals('fr/DDD 1', $objectResponse->{self::$coverIds[0]}->issuecode);

        $this->assertObjectHasAttribute('issuecode', $objectResponse->{self::$coverIds[2]});
        $this->assertEquals('fr/MP 300', $objectResponse->{self::$coverIds[2]}->issuecode);
    }

    public function testCoverIdSearchMultipleUploads() {
        $this->mockCoverSearchResults(self::$coverSearchResultsSimple);
        $response = $this->buildAuthenticatedServiceWithTestUser(
            '/cover-id/search', self::$dmUser, 'POST', [], [
                'wtd_jpg' => self::getCoverIdSearchUploadImage(),
                'wtd_jpg2' => self::getCoverIdSearchUploadImage()
            ]
        )->call();

        $this->assertUnsuccessfulResponse($response, function(Response $response) {
            $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
            $this->assertEquals('Invalid number of uploaded files : should be 1, was 2',$response->getContent());
        });
    }

    public function testCoverIdSearch() {
        $this->mockCoverSearchResults(self::$coverSearchResultsSimple);
        $this->assertFileNotExists(implode(DIRECTORY_SEPARATOR, self::$uploadDestination));

        $similarCoverIssuePublicationCode = 'fr/DDD';
        $similarCoverIssueNumber = '10';
        self::createEntryLike('fr/AR 101', self::$coverUrls[array_values(self::$coverIds)[0]],
            $similarCoverIssuePublicationCode, $similarCoverIssueNumber);

        $response = $this->buildAuthenticatedServiceWithTestUser(
            '/cover-id/search', self::$dmUser, 'POST', [], [
                'wtd_jpg' => self::getCoverIdSearchUploadImage()
            ]
        )->call();

        $this->assertFileExists(implode(DIRECTORY_SEPARATOR, self::$uploadDestination));
        $this->assertJsonStringEqualsJsonString(json_encode([
            'issues' => [
                'fr/DDD 1' => [
                    'countrycode' => 'fr',
                    'publicationcode' => 'fr/DDD',
                    'publicationtitle' => 'Dynastie',
                    'issuenumber' => '1',
                    'coverid' => 1
                ]/*, // Related issue: same cover story code
                $similarCoverIssuePublicationCode.' '.$similarCoverIssueNumber => [
                    "countrycode" => explode('/', $similarCoverIssuePublicationCode)[0],
                    "publicationcode" => $similarCoverIssuePublicationCode,
                    "publicationtitle" => "Dynastie",
                    "issuenumber" => $similarCoverIssueNumber,
                    "coverid" => count(self::$coverIds) + 1
                    ]*/
                ],
            'imageIds' => [1]
            ]), $this->getResponseContent($response));
    }

    public function testCoverIdSearchManyResults() {
        $this->mockCoverSearchResults(self::$coverSearchResultsMany);
        $this->assertFileNotExists(implode(DIRECTORY_SEPARATOR, self::$uploadDestination));

        $similarCoverIssuePublicationCode = 'fr/DDD';
        $similarCoverIssueNumber = '10';
        self::createEntryLike('fr/AR 101', self::$coverUrls[array_values(self::$coverIds)[0]],
            $similarCoverIssuePublicationCode, $similarCoverIssueNumber);

        $response = $this->buildAuthenticatedServiceWithTestUser(
            '/cover-id/search', self::$dmUser, 'POST', [], [
                'wtd_jpg' => self::getCoverIdSearchUploadImage()
            ]
        )->call();

        $this->assertFileExists(implode(DIRECTORY_SEPARATOR, self::$uploadDestination));
        $this->assertJsonStringEqualsJsonString(json_encode([
            'issues' => [
                'fr/DDD 1' => [
                    'countrycode' => 'fr',
                    'publicationcode' => 'fr/DDD',
                    'publicationtitle' => 'Dynastie',
                    'issuenumber' => '1',
                    'coverid' => 1
                ],
                'fr/DDD 10' => [
                    'countrycode' => 'fr',
                    'publicationcode' => 'fr/DDD',
                    'publicationtitle' => 'Dynastie',
                    'issuenumber' => '10',
                    'coverid' => 5
                ],
                'fr/DDD 2' => [
                    'countrycode' => 'fr',
                    'publicationcode' => 'fr/DDD',
                    'publicationtitle' => 'Dynastie',
                    'issuenumber' => '2',
                    'coverid' => 2
                ],
                'fr/MP 300' => [
                    'countrycode' => 'fr',
                    'publicationcode' => 'fr/MP',
                    'publicationtitle' => 'Parade',
                    'issuenumber' => '300',
                    'coverid' => 3
                ]
            ],
            'imageIds' => [1,2,3,4,5,6,7,8,9,10]
        ]), $this->getResponseContent($response));
    }

    public function testCoverIdSearchSizeTooSmall() {
        $this->mockCoverSearchResults([
            'type' => 'IMAGE_SIZE_TOO_SMALL'
        ]);

        $this->assertFileNotExists(implode(DIRECTORY_SEPARATOR, self::$uploadDestination));

        $response = $this->buildAuthenticatedServiceWithTestUser(
            '/cover-id/search', self::$dmUser, 'POST', [], [
                'wtd_jpg' => self::getCoverIdSearchUploadImage()
            ]
        )->call();

        $this->assertJsonStringEqualsJsonString(json_encode([
            'type' => 'IMAGE_SIZE_TOO_SMALL'
            ]), $this->getResponseContent($response));
    }

    public function testCoverIdSearchInvalidFileName() {
        $this->mockCoverSearchResults(self::$coverSearchResultsSimple);
        $this->assertFileNotExists(implode(DIRECTORY_SEPARATOR, self::$uploadDestination));

        $response = $this->buildAuthenticatedServiceWithTestUser(
            '/cover-id/search', self::$dmUser, 'POST', [], [
                'wtd_invalid_jpg' => self::getCoverIdSearchUploadImage()
            ]
        )->call();

        $this->assertUnsuccessfulResponse($response, function(Response $response) {
            $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
            $this->assertEquals('Invalid upload file : expected file name wtd_jpg', $response->getContent());
        });
    }

    public function testDownloadCover() {
        /** @var BinaryFileResponse $response */
        $response = $this->buildAuthenticatedServiceWithTestUser('/cover-id/download/1', self::$dmUser)
            ->call();

        file_put_contents('/tmp/test.jpg', $this->getResponseContent($response));
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
