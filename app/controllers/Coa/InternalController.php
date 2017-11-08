<?php

namespace DmServer\Controllers\Coa;

use Coa\Contracts\Results\SimpleIssueWithCoverId;
use Coa\Models\InducksCountryname;
use Coa\Models\InducksEntry;
use Coa\Models\InducksEntryurl;
use Coa\Models\InducksIssue;
use Coa\Models\InducksPerson;
use Coa\Models\InducksPublication;
use Coa\Models\InducksStory;
use Coa\Models\InducksStoryversion;
use Coverid\Models\Covers;
use DmServer\Controllers\AbstractController;
use DmServer\DmServer;
use DmServer\ModelHelper;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\Join;
use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use DDesrosiers\SilexAnnotations\Annotations as SLX;

/**
 * @SLX\Controller(prefix="/internal/coa")
 */
class InternalController extends AbstractController
{
    protected static function wrapInternalService($app, $function) {
        return parent::returnErrorOnException($app, DmServer::CONFIG_DB_KEY_COA, $function);
    }

    /**
     * @SLX\Route(
     *     @SLX\Request(method="GET", uri="countrynames/{countryCodes}"),
     *     @SLX\Assert(variable="countryCodes", regex="^((?<countrycode_regex>[a-z]+),){0,9}(?&countrycode_regex)$"),
     *     @SLX\Value(variable="countryCodes", default=null)
     * )
     * @param Application $app
     * @param string $countryCodes
     * @return JsonResponse
     */
    function listCountries(Application $app, $countryCodes) {
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

    /**
     * @SLX\Route(
     *     @SLX\Request(method="GET", uri="publicationtitles/{publicationCodes}"),
     *     @SLX\Assert(variable="publicationCodes", regex="^((?P<publicationcode_wildcard_regex>[a-z]+/%)|((?P<publicationcode_regex>[a-z]+/[-A-Z0-9]+),){0,9}(?&publicationcode_regex))$")
     * )
     * @param Application $app
     * @param string $publicationCodes
     * @return JsonResponse
     */
    function listPublications(Application $app, $publicationCodes) {
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

    /**
     * @SLX\Route(
     *     @SLX\Request(method="GET", uri="issues/{publicationCode}"),
     *     @SLX\Assert(variable="publicationCode", regex="^(?P<publicationcode_regex>[a-z]+/[-A-Z0-9]+)$")
     * )
     * @param Application $app
     * @param string $publicationCode
     * @return JsonResponse
     */
    function listIssuesFromPublicationCode(Application $app, $publicationCode) {
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

    /**
     * @SLX\Route(
     *     @SLX\Request(method="GET", uri="issuesbycodes/{issuecodes}"),
     *     @SLX\Assert(variable="issuecodes", regex="^((?P<issuecode_regex>[a-z]+/[- A-Z0-9]+),){0,19}(?&issuecode_regex)$")
     * )
     * @param Application $app
     * @param string $issuecodes
     * @return JsonResponse
     */
    function listIssuesFromIssueCodes(Application $app, $issuecodes) {
        return self::wrapInternalService($app, function(EntityManager $coaEm) use ($app, $issuecodes) {
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
                function($issue) use (&$issues, $app) {

                    if (empty($issues[$issue['issuecode']])) {
                        $app['monolog']->addError('No COA data exists for this issue : ' . $issue['issuecode']);
                        unset($issues[$issue['issuecode']]);
                    }
                    else {
                        /** @var SimpleIssueWithCoverId $issueObject */
                        $issueObject = $issues[$issue['issuecode']];
                        $issueObject->setCoverid($issue['coverid']);
                    }
                }
            );

            return new JsonResponse(ModelHelper::getSerializedArray($issues));
        });
    }

    /**
     * @SLX\Route(
     *     @SLX\Request(method="GET", uri="issuesbycoverurl/{urls}"),
     *     @SLX\Assert(variable="urls", regex="^((?P<url_regex>.+),){0,19}(?&url_regex)$")
     * )
     * @param Application $app
     * @param string $urls
     * @return JsonResponse
     */
    function listIssuesByCoverUrls(Application $app, $urls) {
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
            $resultsRelatedIssueCodes = $qbRelatedIssues->getQuery()->getResult();

            $issueCodes = array_map(function($result) {
                return ['issuecode' => $result['issuecode'], 'url' => $result['url'] ];
            }, $resultsRelatedIssueCodes);

            return new JsonResponse(['relatedissuecodes' => $issueCodes]);
        });
    }

    /**
     * @SLX\Route(
     *     @SLX\Request(method="GET", uri="authorsfullnames/{authors}")
     * )
     * @param Application $app
     * @param string $authors
     * @return JsonResponse
     */
    function listAuthorsFromAuthorCodes(Application $app, $authors) {
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


    /**
     * @SLX\Route(
     *     @SLX\Request(method="GET", uri="storydetails/{storyCodes}"),
     *     @SLX\Assert(variable="storyCodes", regex="^((?P<storycode_regex>[-/A-Za-z0-9 ?&]+),){0,49}(?&storycode_regex)$")
     * )
     * @param Application $app
     * @param string $storyCodes
     * @return JsonResponse
     */
    function listStoryDetailsFromStoryCodes(Application $app, $storyCodes) {
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
}
