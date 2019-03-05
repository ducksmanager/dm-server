<?php
namespace App\Tests\Controller;


use App\Entity\Coverid\Covers;
use App\Helper\SimilarImagesHelper;
use App\Tests\Fixtures\CoaEntryFixture;
use App\Tests\Fixtures\CoverIdFixture;
use App\Tests\TestCommon;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;

class CoverIdTest extends TestCommon
{

    public static $uploadDestination = '/tmp/test.jpg';

    public static $exampleImageToUpload = 'cover_example_to_upload.jpg';
    public static $imageToUpload = 'cover_example_to_upload_willberemoved.jpg';

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

    protected function getEmNamesToCreate(): array
    {
        return ['coa','coverid'];
    }

    public function setUp()
    {
        parent::setUp();
        self::runCommand('doctrine:fixtures:load -q -n --em=coa --group=coa');
        $urls = [
            'fr/DDD 1' => 'cover_example.jpg',
            'fr/DDD 2' => 'cover_example_2.jpg',
            'fr/MP 300' => 'cover_example_3.jpg',
            'fr/XXX 111' => 'cover_example_4.jpg'
        ];
        foreach($urls as $issueNumber => $url) {
            $this->loadFixture('coverid', new CoverIdFixture($issueNumber, $url));
        }

        @unlink(self::$uploadDestination);
    }

    public function tearDown()
    {
        parent::tearDown();
        @unlink(self::$imageToUpload);
    }

    private function mockCoverSearchResults($mockedResponse): void
    {
        SimilarImagesHelper::$mockedResults = json_encode($mockedResponse);
    }

    public function testGetIssueListByIssueCodes(): void
    {
        $coverId1 = $this->getEm('coverid')->getRepository(Covers::class)->find(1);
        $coverId3 = $this->getEm('coverid')->getRepository(Covers::class)->find(3);
        $this->mockCoverSearchResults(self::$coverSearchResultsSimple);
        $response = $this->buildAuthenticatedServiceWithTestUser(
            '/cover-id/issuecodes/'
            . implode(',', [$coverId1->getId(), $coverId3->getId()]), self::$dmUser)->call();

        $objectResponse = json_decode($this->getResponseContent($response));

        $this->assertInternalType('object', $objectResponse);
        $this->assertCount(2, get_object_vars($objectResponse));

        $this->assertObjectHasAttribute('issuecode', $objectResponse->{$coverId1->getId()});
        $this->assertEquals('fr/DDD 1', $objectResponse->{$coverId1->getId()}->issuecode);

        $this->assertObjectHasAttribute('issuecode', $objectResponse->{$coverId3->getId()});
        $this->assertEquals('fr/MP 300', $objectResponse->{$coverId3->getId()}->issuecode);
    }

    public function testCoverIdSearchMultipleUploads(): void
    {
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

    public function testCoverIdSearch(): void
    {
        $coverId1 = $this->getEm('coverid')->getRepository(Covers::class)->find(1);
        $this->mockCoverSearchResults(self::$coverSearchResultsSimple);
        $this->assertFileNotExists(self::$uploadDestination);

        $similarCoverIssuePublicationCode = 'fr/DDD';
        $similarCoverIssueNumber = '10';

        $this->loadFixture('coa', new CoaEntryFixture('fr/AR 101', $coverId1->getUrl(),
            $similarCoverIssuePublicationCode, $similarCoverIssueNumber));

        $response = $this->buildAuthenticatedServiceWithTestUser(
            '/cover-id/search', self::$dmUser, 'POST', [], [
                'wtd_jpg' => self::getCoverIdSearchUploadImage()
            ]
        )->call();

        $this->assertFileExists(self::$uploadDestination);
        $this->assertJsonStringEqualsJsonString(json_encode([
            'issues' => [
                'fr/DDD 1' => [
                    'countrycode' => 'fr',
                    'publicationcode' => 'fr/DDD',
                    'publicationtitle' => 'Dynastie',
                    'issuenumber' => '1',
                    'coverid' => 1
                ]
            ],
            'imageIds' => [1]
            ]), $this->getResponseContent($response));
    }

    public function testCoverIdSearchManyResults(): void
    {
        $this->mockCoverSearchResults(self::$coverSearchResultsMany);
        $this->assertFileNotExists(self::$uploadDestination);

        $similarCoverIssuePublicationCode = 'fr/DDD';
        $similarCoverIssueNumber = '10';

        $coverId1 = $this->getEm('coverid')->getRepository(Covers::class)->find(1);
        $this->loadFixture('coa', new CoaEntryFixture('fr/AR 101', $coverId1->getUrl(),
            $similarCoverIssuePublicationCode, $similarCoverIssueNumber));
        $this->loadFixture('coverid', new CoverIdFixture($similarCoverIssuePublicationCode.' '.$similarCoverIssueNumber, $coverId1->getUrl()));

        $response = $this->buildAuthenticatedServiceWithTestUser(
            '/cover-id/search', self::$dmUser, 'POST', [], [
                'wtd_jpg' => self::getCoverIdSearchUploadImage()
            ]
        )->call();

        $this->assertFileExists(self::$uploadDestination);
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

    public function testCoverIdSearchSizeTooSmall(): void
    {
        $this->mockCoverSearchResults([
            'type' => 'IMAGE_SIZE_TOO_SMALL'
        ]);

        $this->assertFileNotExists(self::$uploadDestination);

        $response = $this->buildAuthenticatedServiceWithTestUser(
            '/cover-id/search', self::$dmUser, 'POST', [], [
                'wtd_jpg' => self::getCoverIdSearchUploadImage()
            ]
        )->call();

        $this->assertJsonStringEqualsJsonString(json_encode([
            'type' => 'IMAGE_SIZE_TOO_SMALL'
            ]), $this->getResponseContent($response));
    }

    public function testCoverIdSearchInvalidFileName(): void
    {
        $this->mockCoverSearchResults(self::$coverSearchResultsSimple);
        $this->assertFileNotExists(self::$uploadDestination);

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

    public function testDownloadCover(): void
    {
        /** @var BinaryFileResponse $response */
        $response = $this->buildAuthenticatedServiceWithTestUser('/cover-id/download/1', self::$dmUser)
            ->call();

        file_put_contents(self::$uploadDestination, $this->getResponseContent($response));
        $type=\exif_imagetype(self::$uploadDestination);
        $this->assertEquals(IMAGETYPE_JPEG, $type);
    }

    private static function getCoverIdSearchUploadImage(): UploadedFile
    {
        copy(self::getPathToFileToUpload(self::$exampleImageToUpload), self::getPathToFileToUpload(self::$imageToUpload));
        return new UploadedFile(
            self::getPathToFileToUpload(self::$imageToUpload),
            self::$imageToUpload,
            'image/jpeg'
        );
    }
}
