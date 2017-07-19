<?php

namespace DmServer\Controllers\Coa;

use Coa\Contracts\Results\SimpleIssueWithCoverId;
use Coa\Models\BaseModel;
use Coa\Models\InducksCountryname;
use Coa\Models\InducksEntry;
use Coa\Models\InducksEntryurl;
use Coa\Models\InducksIssue;
use Coa\Models\InducksPerson;
use Coa\Models\InducksPublication;
use Coa\Models\InducksStory;
use Coa\Models\InducksStoryversion;
use CoverId\Models\Covers;
use DmServer\Controllers\AbstractController;
use DmServer\DmServer;
use DmServer\ModelHelper;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\Join;
use Exception;
use Silex\Application;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class InternalController extends AbstractController
{
    protected static function wrapInternalService($app, $function) {
        return parent::returnErrorOnException($app, DmServer::CONFIG_DB_KEY_COA, $function);
    }

    /**
     * @param $routing ControllerCollection
     */
    public static function addRoutes($routing)
    {
        $routing->get(
            '/internal/coa/countrynames/{countryCodes}',
            function (Request $request, Application $app, $countryCodes) {
                return self::wrapInternalService($app, function(EntityManager $coaEm) use ($countryCodes) {
                    $qb = $coaEm->createQueryBuilder();
                    $qb
                        ->select('inducks_countryname.countrycode, inducks_countryname.countryname')
                        ->from(InducksCountryname::class, 'inducks_countryname');

                    if (!empty($countryCodes)) {
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
                return self::wrapInternalService($app, function(EntityManager $coaEm) use ($publicationCodes) {
                    $qb = $coaEm->createQueryBuilder();
                    $qb
                        ->select('inducks_publication.publicationcode, inducks_publication.title')
                        ->from(InducksPublication::class, 'inducks_publication');

                    if (preg_match('#^[a-z]+/%$#', $publicationCodes)) {
                        $qb->where($qb->expr()->like('inducks_publication.publicationcode', "'".$publicationCodes."'"));
                    }
                    else {
                        $qb->where($qb->expr()->in('inducks_publication.publicationcode', explode(',', $publicationCodes)));
                    }
                    $qb->orderBy('inducks_publication.title');

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
                return self::wrapInternalService($app, function(EntityManager $coaEm) use ($publicationCode) {
                    $qb = $coaEm->createQueryBuilder();
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
                return self::wrapInternalService($app, function(EntityManager $coaEm) use ($issuecodes) {
                    $issuecodesList = explode(',', $issuecodes);

                    $qbIssueInfo = $coaEm->createQueryBuilder();
                    $qbIssueInfo
                        ->select('inducks_publication.countrycode, inducks_publication.publicationcode, inducks_publication.title, inducks_issue.issuenumber, inducks_issue.issuecode')
                        ->from(InducksIssue::class, 'inducks_issue')
                        ->join(InducksPublication::class, 'inducks_publication', Join::WITH, 'inducks_issue.publicationcode = inducks_publication.publicationcode');

                    $qbIssueInfo->where($qbIssueInfo->expr()->in('inducks_issue.issuecode', $issuecodesList));

                    $resultsIssueInfo = $qbIssueInfo->getQuery()->getResult();

                    $issues = [];

                    array_walk(
                        $resultsIssueInfo,
                        function($issue) use (&$issues) {
                            $issues[$issue['issuecode']] = SimpleIssueWithCoverId::buildWithoutCoverId($issue['countrycode'], $issue['publicationcode'], $issue['title'], $issue['issuenumber']);
                        }
                    );

                    $qbCoverInfo = DmServer::getEntityManager(DmServer::CONFIG_DB_KEY_COVER_ID)->createQueryBuilder();
                    $qbCoverInfo
                        ->select('covers.id AS coverid, covers.issuecode')
                        ->from(Covers::class, 'covers');

                    $qbCoverInfo->where($qbCoverInfo->expr()->in('covers.issuecode', $issuecodesList));

                    $resultsCoverInfo = $qbCoverInfo->getQuery()->getResult();

                    array_walk(
                        $resultsCoverInfo,
                        function($issue) use (&$issues) {

                            if (empty($issues[$issue['issuecode']])) {
                                throw new Exception('No COA data exists for this issue : ' . $issue['issuecode'], Response::HTTP_BAD_REQUEST);
                            }
                            /** @var SimpleIssueWithCoverId $issueObject */
                            $issueObject = $issues[$issue['issuecode']];

                            $issueObject->setCoverid($issue['coverid']);
                        }
                    );

                    return new JsonResponse(ModelHelper::getSerializedArray($issues));
                });
            }
        )->assert('issuecodes', '^([a-z]+/[- A-Z0-9]+,)*[a-z]+/[- A-Z0-9]+$');

        $routing->get(
            '/internal/coa/issuesbycoverurl/{urls}',
            function (Request $request, Application $app, $urls) {
                return self::wrapInternalService($app, function(EntityManager $coaEm) use ($urls) {
                    $urlList = explode(',', $urls);

                    $qbRelatedIssues = $coaEm->createQueryBuilder();

                    $qbRelatedIssues
                        ->select('distinct relatedentry.issuecode, relatedentryurl.url')
                        ->from(InducksEntryurl::class, 'originentryurl')
                        ->join(InducksEntry::class, 'originentry', Join::WITH, 'originentryurl.entrycode = originentry.entrycode')
                        ->join(InducksStoryversion::class, 'originstoryversion', Join::WITH, 'originentry.storyversioncode = originstoryversion.storyversioncode')
                        ->join(InducksStoryversion::class, 'relatedstoryversion', Join::WITH, 'originstoryversion.storycode = relatedstoryversion.storycode')
                        ->join(InducksEntry::class, 'relatedentry', Join::WITH, 'relatedstoryversion.storyversioncode = relatedentry.storyversioncode')
                        ->join(InducksIssue::class, 'relatedissue', Join::WITH, 'relatedentry.issuecode = relatedissue.issuecode')
                        ->join(InducksEntryurl::class, 'relatedentryurl', Join::WITH, 'relatedentry.entrycode = relatedentryurl.entrycode');

                    $qbRelatedIssues->where($qbRelatedIssues->expr()->in('originentryurl.url', $urlList));
                    $qbRelatedIssues->andWhere($qbRelatedIssues->expr()->neq('originstoryversion.storyversioncode', 'relatedstoryversion.storyversioncode'));
$sql = $qbRelatedIssues->getQuery()->getSQL();
                    $resultsRelatedIssueCodes = $qbRelatedIssues->getQuery()->getResult();

                    $issueCodes = array_map(function($result) {
                        return ['issuecode' => $result['issuecode'], 'url' => $result['url'] ];
                    }, $resultsRelatedIssueCodes);

                    return new JsonResponse(['relatedissuecodes' => $issueCodes]);
                });
            }
        )->assert('urls', self::getParamAssertRegex('.+', 20));

        $routing->get(
            '/internal/coa/authorsfullnames/{authors}',
            function (Request $request, Application $app, $authors) {
                return self::wrapInternalService($app, function(EntityManager $coaEm) use($authors) {
                    $authorsList = array_unique(explode(',', $authors));

                    $qbAuthorsFullNames = $coaEm->createQueryBuilder();
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
                return self::wrapInternalService($app, function(EntityManager $coaEm) use($storyCodes) {
                    $storyList = array_unique(explode(',', $storyCodes));

                    $qbStoryDetails = $coaEm->createQueryBuilder();
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
