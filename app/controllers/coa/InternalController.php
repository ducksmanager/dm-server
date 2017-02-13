<?php

namespace DmServer\Controllers\Coa;

use Coa\Contracts\Results\SimpleIssueWithUrl;
use Coa\Models\BaseModel;
use Coa\Models\InducksCountryname;
use Coa\Models\InducksIssue;
use Coa\Models\InducksPerson;
use Coa\Models\InducksPublication;
use Coa\Models\InducksStory;
use CoverId\Models\Covers;
use DmServer\Controllers\AbstractController;
use DmServer\DmServer;
use DmServer\ModelHelper;
use Doctrine\ORM\Query\Expr\Join;
use Exception;
use Silex\Application;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class InternalController extends AbstractController
{
    /**
     * @param $routing ControllerCollection
     */
    public static function addRoutes($routing)
    {
        $routing->get(
            '/internal/coa/countrynames/{countryCodes}',
            function (Request $request, Application $app, $countryCodes) {
                return AbstractController::return500ErrorOnException($app, function() use ($countryCodes) {
                    $qb = DmServer::getEntityManager(DmServer::CONFIG_DB_KEY_COA)->createQueryBuilder();
                    $qb
                        ->select('inducks_countryname.countrycode, inducks_countryname.countryname')
                        ->from(InducksCountryname::class, 'inducks_countryname');

                    if ($countryCodes !== null) {
                        $qb->where($qb->expr()->in('inducks_countryname.countrycode', explode(',', $countryCodes)));
                    }

                    $results = $qb->getQuery()->getResult();
                    $countryNames = [];
                    array_walk(
                        $results,
                        function($result) use (&$countryNames) {
                            $countryNames[$result['countrycode']] = $result['countryname'];
                        }
                    );
                    return new JsonResponse(ModelHelper::getSerializedArray($countryNames));
                });
            }
        )->value('countryCodes', null);

        $routing->get(
            '/internal/coa/publicationtitles/{publicationCodes}',
            function (Request $request, Application $app, $publicationCodes) {
                return AbstractController::return500ErrorOnException($app, function() use ($publicationCodes) {
                    $qb = DmServer::getEntityManager(DmServer::CONFIG_DB_KEY_COA)->createQueryBuilder();
                    $qb
                        ->select('inducks_publication.publicationcode, inducks_publication.title')
                        ->from(InducksPublication::class, 'inducks_publication');

                    if (preg_match('#^[a-z]+/%$#', $publicationCodes)) {
                        $qb->where($qb->expr()->like('inducks_publication.publicationcode', "'".$publicationCodes."'"));
                    }
                    else {
                        $qb->where($qb->expr()->in('inducks_publication.publicationcode', explode(',', $publicationCodes)));
                    }

                    $results = $qb->getQuery()->getResult();
                    $publicationTitles = [];
                    array_walk(
                        $results,
                        function($result) use (&$publicationTitles) {
                            $publicationTitles[$result['publicationcode']] = $result['title'];
                        }
                    );
                    return new JsonResponse(ModelHelper::getSerializedArray($publicationTitles));
                });
            }
        )->assert('publicationCodes', '.+');

        $routing->get(
            '/internal/coa/issues/{publicationCode}',
            function (Request $request, Application $app, $publicationCode) {
                return AbstractController::return500ErrorOnException($app, function() use ($publicationCode) {
                    $qb = DmServer::getEntityManager(DmServer::CONFIG_DB_KEY_COA)->createQueryBuilder();
                    $qb
                        ->select('inducks_issue.issuenumber')
                        ->from(InducksIssue::class, 'inducks_issue');

                    $qb->where($qb->expr()->eq('inducks_issue.publicationcode', "'".$publicationCode."'"));

                    $results = $qb->getQuery()->getResult();
                    $issueNumbers = array_map(
                        function($issue) {
                            return $issue['issuenumber'];
                        },
                        $results
                    );
                    return new JsonResponse(ModelHelper::getSerializedArray($issueNumbers));
                });
            }
        )->assert('publicationCode', '.+');

        $routing->get(
            '/internal/coa/issuesbycodes/{issuecodes}',
            function (Request $request, Application $app, $issuecodes) {
                return AbstractController::return500ErrorOnException($app, function() use ($issuecodes) {
                    $issuecodesList = explode(',', $issuecodes);

                    $qbIssueInfo = DmServer::getEntityManager(DmServer::CONFIG_DB_KEY_COA)->createQueryBuilder();
                    $qbIssueInfo
                        ->select('inducks_publication.countrycode, inducks_publication.title, inducks_issue.issuenumber, inducks_issue.issuecode')
                        ->from(InducksIssue::class, 'inducks_issue')
                        ->join(InducksPublication::class, 'inducks_publication', Join::WITH, 'inducks_issue.publicationcode = inducks_publication.publicationcode');

                    $qbIssueInfo->where($qbIssueInfo->expr()->in('inducks_issue.issuecode', $issuecodesList));

                    $resultsIssueInfo = $qbIssueInfo->getQuery()->getResult();

                    array_walk(
                        $resultsIssueInfo,
                        function($issue) use (&$issues) {
                            $issues[$issue['issuecode']] = SimpleIssueWithUrl::buildWithoutUrl($issue['countrycode'], $issue['title'], $issue['issuenumber']);
                        }
                    );

                    $qbCoverInfo = DmServer::getEntityManager(DmServer::CONFIG_DB_KEY_COVER_ID)->createQueryBuilder();
                    $qbCoverInfo
                        ->select('covers.id, covers.url, covers.issuecode')
                        ->from(Covers::class, 'covers');

                    $qbCoverInfo->where($qbCoverInfo->expr()->in('covers.issuecode', $issuecodesList));

                    $resultsCoverInfo = $qbCoverInfo->getQuery()->getResult();

                    array_walk(
                        $resultsCoverInfo,
                        function($issue) use (&$issues) {

                            if (empty($issues[$issue['issuecode']])) {
                                throw new Exception('No COA data exists for this issue : ' . $issue['issuecode']);
                            }
                            /** @var SimpleIssueWithUrl $issueObject */
                            $issueObject = $issues[$issue['issuecode']];
                            $url = $issue['url'];
                            if (strpos($url, 'webusers') === 0) {
                                $url = 'webusers/'.$url;
                            }
                            $issueObject->setFullurl($url);
                        }
                    );

                    return new JsonResponse(ModelHelper::getSerializedArray($issues));
                });
            }
        )->assert('issuecodes', '^([a-z]+/[- A-Z0-9]+,)*[a-z]+/[- A-Z0-9]+$');

        $routing->get(
            '/internal/coa/authorsfullnames/{authors}',
            function (Request $request, Application $app, $authors) {
                return AbstractController::return500ErrorOnException($app, function() use($authors) {
                    $authorsList = array_unique(explode(',', $authors));

                    $qbAuthorsFullNames = DmServer::getEntityManager(DmServer::CONFIG_DB_KEY_COA)->createQueryBuilder();
                    $qbAuthorsFullNames
                        ->select('p.personcode, p.fullname')
                        ->from(InducksPerson::class, 'p')
                        ->where($qbAuthorsFullNames->expr()->in('p.personcode', $authorsList));

                    $fullNamesResults = $qbAuthorsFullNames->getQuery()->getResult();

                    $fullNames = [];
                    array_walk($fullNamesResults, function($authorFullName) use (&$fullNames) {
                        $fullNames[$authorFullName['personcode']] = $authorFullName['fullname'];
                    });
                    return new JsonResponse(ModelHelper::getSerializedArray($fullNames));
                });
            }
        );

        $routing->get(
            '/internal/coa/storydetails/{storyCodes}',
            function (Request $request, Application $app, $storyCodes) {
                return AbstractController::return500ErrorOnException($app, function() use($storyCodes) {
                    $storyList = array_unique(explode(',', $storyCodes));

                    $qbStoryDetails = DmServer::getEntityManager(DmServer::CONFIG_DB_KEY_COA)->createQueryBuilder();
                    $qbStoryDetails
                        ->select('story.storycode, story.title, story.storycomment')
                        ->from(InducksStory::class, 'story')
                        ->where($qbStoryDetails->expr()->in('story.storycode', $storyList));

                    $storyDetailsResults = $qbStoryDetails->getQuery()->getResult();

                    $storyDetails = [];
                    array_walk($storyDetailsResults, function($story) use (&$storyDetails) {
                        $storyDetails[$story['storycode']] = [
                            'storycomment' => $story['storycomment'],
                            'title' => $story['title']
                        ];
                    });
                    return new JsonResponse(ModelHelper::getSerializedArray($storyDetails));
                });
            }
        )->assert('storyCodes', self::getParamAssertRegex(BaseModel::STORY_CODE_VALIDATION, 50));
    }
}
