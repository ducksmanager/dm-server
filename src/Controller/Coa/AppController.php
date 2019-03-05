<?php

namespace App\Controller\Coa;

use App\Controller\AbstractController;
use App\Entity\Coa\InducksCountryname;
use App\Entity\Coa\InducksIssue;
use App\Entity\Coa\InducksPerson;
use App\Entity\Coa\InducksPublication;
use App\Entity\Coa\InducksStory;
use App\Entity\Coverid\Covers;
use App\EntityTransform\SimpleIssueWithCoverId;
use Doctrine\ORM\Query\Expr\Join;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AppController extends AbstractController
{
    /**
     * @Route(methods={"GET"}, path="/coa/list/countries/{locale}/{countryCodes}", defaults={"countryCodes"=""})
     */
    public function listCountriesFromCodes(string $locale, ?string $countryCodes): Response
    {
        $coaEm = $this->getEm('coa');
        $qb = $coaEm->createQueryBuilder();
        $qb
            ->select('inducks_countryname.countrycode, inducks_countryname.countryname')
            ->from(InducksCountryname::class, 'inducks_countryname')
            ->where($qb->expr()->eq('inducks_countryname.languagecode', ':locale'));
        $parameters = [':locale' => $locale];

        if (empty($countryCodes)) {
            $qb->andWhere($qb->expr()->neq('inducks_countryname.countrycode', ':fakeCountry'));
            $parameters[':fakeCountry'] = 'fake';
        } else {
            $qb->andWhere($qb->expr()->in('inducks_countryname.countrycode', explode(',', $countryCodes)));
        }

        $results = $qb->getQuery()
            ->setParameters($parameters)
            ->getResult();
        $countryNames = [];
        array_walk(
            $results,
            function ($result) use (&$countryNames) {
                $countryNames[$result['countrycode']] = $result['countryname'];
            }
        );
        return new JsonResponse($countryNames);
    }

    /**
     * @Route(
     *     methods={"GET"},
     *     path="/coa/list/publications/{publicationCodes}",
     *     requirements={"publicationCodes"="^([a-z]+|((?P<publicationcode_regex>[a-z]+/[-A-Z0-9]+),){0,9}[a-z]+/[-A-Z0-9]+)$"}
     * )
     */
    public function listPublicationsFromPublicationCodes(string $publicationCodes): Response
    {
        $coaEm = $this->getEm('coa');
        $qb = $coaEm->createQueryBuilder();
        $qb
            ->select('inducks_publication.publicationcode, inducks_publication.title')
            ->from(InducksPublication::class, 'inducks_publication');

        if (preg_match('#^[a-z]+$#', $publicationCodes)) {
            $qb->where($qb->expr()->like('inducks_publication.publicationcode', "'$publicationCodes/%'"));
        } else {
            $qb->where($qb->expr()->in('inducks_publication.publicationcode', explode(',', $publicationCodes)));
        }
        $qb->orderBy('inducks_publication.title');

        $results = $qb->getQuery()->getResult();
        $publicationTitles = [];
        array_walk(
            $results,
            function ($result) use (&$publicationTitles) {
                $publicationTitles[$result['publicationcode']] = $result['title'];
            }
        );
        return new JsonResponse($publicationTitles);
    }

    /**
     * @Route(
     *     methods={"GET"},
     *     path="/coa/list/issues/{publicationCode}",
     *     requirements={"publicationCode"="^(?P<publicationcode_regex>[a-z]+/[-A-Z0-9]+)$"}
     * )
     */
    public function listIssuesFromPublicationCode(string $publicationCode): Response
    {
        $coaEm = $this->getEm('coa');
        $qb = $coaEm->createQueryBuilder();
        $qb
            ->select('inducks_issue.issuenumber')
            ->from(InducksIssue::class, 'inducks_issue');

        $qb->where($qb->expr()->eq('inducks_issue.publicationcode', "'" . $publicationCode . "'"));

        $results = $qb->getQuery()->getResult();
        $issueNumbers = array_map(
            function ($issue) {
                return $issue['issuenumber'];
            },
            $results
        );
        return new JsonResponse($issueNumbers);
    }

    /**
     * @Route(
     *     methods={"GET"},
     *     path="/coa/list/issuesbycodes/{issueCodes}",
     *     requirements={"issueCodes"="^((?P<issuecode_regex>[a-z]+/[-A-Z0-9 ]+),){0,3}[a-z]+/[-A-Z0-9 ]+$"}
     * )
     */
    public function listIssuesFromIssueCodes(string $issueCodes, LoggerInterface $logger): Response
    {
        $coaEm = $this->getEm('coa');
        $issuecodesList = explode(',', $issueCodes);

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
            function ($issue) use (&$issues) {
                $issues[$issue['issuecode']] = SimpleIssueWithCoverId::buildWithoutCoverId($issue['countrycode'], $issue['publicationcode'], $issue['title'], $issue['issuenumber']);
            }
        );

        $coverInfoEm = $this->getEm('coverid');
        $qbCoverInfo = $coverInfoEm->createQueryBuilder();
        $qbCoverInfo
            ->select('covers.id AS coverid, covers.issuecode')
            ->from(Covers::class, 'covers');

        $qbCoverInfo->where($qbCoverInfo->expr()->in('covers.issuecode', $issuecodesList));

        $resultsCoverInfo = $qbCoverInfo->getQuery()->getResult();

        array_walk(
            $resultsCoverInfo,
            function ($issue) use (&$issues, $logger) {

                if (empty($issues[$issue['issuecode']])) {
                    $logger->error('No COA data exists for this issue : ' . $issue['issuecode']);
                    unset($issues[$issue['issuecode']]);
                } else {
                    /** @var SimpleIssueWithCoverId $issueObject */
                    $issueObject = $issues[$issue['issuecode']];
                    $issueObject->setCoverid($issue['coverid']);
                }
            }
        );

        return new JsonResponse(self::getSimpleArray($issues));
    }

    /**
     * @Route(methods={"GET"}, path="/coa/authorsfullnames/{authors}")
     */
    public function listAuthorsFromAuthorCodes(string $authors): JsonResponse
    {
        $authorsList = array_unique(explode(',', $authors));

        $qbAuthorsFullNames = $this->getEm('coa')->createQueryBuilder();
        $qbAuthorsFullNames
            ->select('p.personcode, p.fullname')
            ->from(InducksPerson::class, 'p')
            ->where($qbAuthorsFullNames->expr()->in('p.personcode', $authorsList));

        $fullNamesResults = $qbAuthorsFullNames->getQuery()->getResult();

        $fullNames = [];
        array_walk($fullNamesResults, function($authorFullName) use (&$fullNames) {
            $fullNames[$authorFullName['personcode']] = $authorFullName['fullname'];
        });
        return new JsonResponse($fullNames);
    }

    /**
     * @Route(
     *     methods={"GET"},
     *     path="/coa/storydetails/{storyCodes}",
     *     requirements={"storyCodes"="^((?P<storycode_regex>[-/A-Za-z0-9 ?&]+),){0,49}[-/A-Za-z0-9 ?&]+$"}
     * )
     */
    public function listStoryDetailsFromStoryCodes(string $storyCodes): JsonResponse
    {
        $storyList = array_unique(explode(',', $storyCodes));

        $qbStoryDetails = $this->getEm('coa')->createQueryBuilder();
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
        return new JsonResponse($storyDetails);
    }
}
