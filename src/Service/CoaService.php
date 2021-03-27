<?php
namespace App\Service;

use App\Entity\Coa\InducksEntry;
use App\Entity\Coa\InducksIssue;
use App\Entity\Coa\InducksIssuequotation;
use App\Entity\Coa\InducksPerson;
use App\Entity\Coa\InducksPublication;
use App\Entity\Coa\InducksStory;
use App\Entity\Coa\InducksStoryversion;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Query\QueryException;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;
use stdClass;

class CoaService
{
    /**@var EntityManager */
    private static $coaEm;

    public function __construct(ManagerRegistry $doctrineManagerRegistry)
    {
        self::$coaEm = $doctrineManagerRegistry->getManager('coa');
    }

    /**
     * @param string[] $authorCodes
     * @return string[]
     * @throws QueryException
     */
    public function getAuthorNames(array $authorCodes) : array {
        if (empty($authorCodes)) {
            return [];
        }
        $qbAuthorsFullNames = self::$coaEm->createQueryBuilder();
        $qbAuthorsFullNames
            ->select('p.personcode, p.fullname')
            ->from(InducksPerson::class, 'p')
            ->where($qbAuthorsFullNames->expr()->in('p.personcode', array_unique($authorCodes)))
            ->indexBy('p', 'p.personcode');

        $fullNamesResults = $qbAuthorsFullNames->getQuery()->getResult();
        return array_map(function(array $person) {
            return $person['fullname'];
        }, $fullNamesResults);
    }

    /**
     * @param string $partialAuthorName
     * @return object
     * @throws QueryException
     */
    public function getAuthorNamesFromPartialName(string $partialAuthorName) : object {
        if (strlen($partialAuthorName) < 3) {
            return new stdClass();
        }
        $qbAuthorsFullNames = self::$coaEm->createQueryBuilder();
        $qbAuthorsFullNames
            ->select('distinct p.personcode, p.fullname')
            ->from(InducksPerson::class, 'p')
            ->where($qbAuthorsFullNames->expr()->like('p.fullname', $qbAuthorsFullNames->expr()->literal("%$partialAuthorName%")))
            ->indexBy('p', 'p.personcode');

        $fullNamesResults = $qbAuthorsFullNames->getQuery()->getResult();
        return (object) array_map(function(array $person) {
            return $person['fullname'];
        }, $fullNamesResults);
    }

    /**
     * @param string[] $issueCodes
     * @return array
     * @throws QueryException
     */
    public function decomposeIssueCodes(array $issueCodes) : array {
        $qbIssueCodes = self::$coaEm->createQueryBuilder();
        $qbIssueCodes
            ->select('issue.issuecode, issue.publicationcode, issue.issuenumber')
            ->from(InducksIssue::class, 'issue')
            ->andWhere($qbIssueCodes->expr()->in('issue.issuecode', ':issueCodes'))
            ->setParameter('issueCodes', $issueCodes)
            ->indexBy('issue', 'issue.issuecode');

        return $qbIssueCodes->getQuery()->getArrayResult();
    }

    /**
     * @param string[] $storyCodes
     * @param string[] $associatedPublicationCodes
     * @param string[] $associatedIssueNumbers
     * @return array[]
     * @throws QueryException
     */
    public function getStoryDetails(array $storyCodes, array $associatedPublicationCodes, array $associatedIssueNumbers) : array
    {
        if (empty($storyCodes)) {
            return [];
        }
        $qbStoryDetails = self::$coaEm->createQueryBuilder();
        $qbStoryDetails
            ->select('story.storycode, story.storycomment, entry.title, story.title as originaltitle')
            ->from(InducksStory::class, 'story')
            ->join(InducksStoryversion::class, 'storyversion', Join::WITH, 'story.storycode = storyversion.storycode')
            ->join(InducksEntry::class, 'entry', Join::WITH, 'storyversion.storyversioncode = entry.storyversioncode')
            ->join(InducksIssue::class, 'issue', Join::WITH, 'entry.issuecode = issue.issuecode');

        foreach($storyCodes as $idx => $storyCode) {
            $qbStoryDetails->orWhere("story.storycode = :storycode$idx and issue.publicationcode = :publicationcode$idx and issue.issuenumber = :issuenumber$idx")
                ->setParameter("storycode$idx", $storyCode)
                ->setParameter("publicationcode$idx", $associatedPublicationCodes[$idx])
                ->setParameter("issuenumber$idx", $associatedIssueNumbers[$idx]);
        }
        $qbStoryDetails->orderBy('story.storycode');
        $qbStoryDetails->indexBy('story', 'story.storycode');

        $storyDetailsResults = $qbStoryDetails->getQuery()->getResult();

        $storyDetails = array_map(function(array $story) {
            return [
                'storycomment' => $story['storycomment'],
                'title' => $story['title'] ?? $story['originaltitle']
            ];
        }, $storyDetailsResults);

        // Empty properties if the story couldn't be found
        foreach($storyCodes as $storyCode) {
            if (!isset($storyDetails[$storyCode])) {
                $storyDetails[$storyCode] = [
                    'storycomment' => '',
                    'title' => '?'
                ];
            }
        }
        return $storyDetails;
    }

    /**
     * @param ?string[] $publicationCodes
     * @return array
     * @throws QueryException
     */
    public function getPublicationTitles(?array $publicationCodes = null) : array {
        $qb = (self::$coaEm->createQueryBuilder())
            ->select('inducks_publication.publicationcode, inducks_publication.title')
            ->from(InducksPublication::class, 'inducks_publication');
        if (!empty($publicationCodes)) {
            $qb->where($qb->expr()->in('inducks_publication.publicationcode', $publicationCodes));
        }
        $qb
            ->orderBy('inducks_publication.title')
            ->indexBy('inducks_publication', 'inducks_publication.publicationcode');

        $results = $qb->getQuery()->getArrayResult();
        return array_map(function(array $person) {
            return $person['title'];
        }, $results);
    }

    /**
     * @param string $country
     * @return string[]
     * @throws QueryException
     */
    public function getPublicationTitlesFromCountry(string $country) : array {
        $qb = (self::$coaEm->createQueryBuilder());
        $qb
            ->select('inducks_publication.publicationcode, inducks_publication.title')
            ->from(InducksPublication::class, 'inducks_publication')
            ->where($qb->expr()->like('inducks_publication.publicationcode', "'$country/%'"))
            ->orderBy('inducks_publication.title')
            ->indexBy('inducks_publication', 'inducks_publication.publicationcode');

        $results = $qb->getQuery()->getArrayResult();
        return array_map(function(array $person) {
            return $person['title'];
        }, $results);
    }

    public function getIssueCount() : array
    {
        $qb = (self::$coaEm->createQueryBuilder())
            ->select('inducks_issue.publicationcode, count(inducks_issue.issuenumber) AS count')
            ->from(InducksIssue::class, 'inducks_issue')
            ->groupBy('inducks_issue.publicationcode')
            ->indexBy('inducks_issue', 'inducks_issue.publicationcode');

        return array_map(function(array $result) {
            return (int) $result['count'];
        }, $qb->getQuery()->getResult());
    }

    public function getIssueNumbersFromPublicationCode(string $publicationCode) : stdClass
    {
        $qb = (self::$coaEm->createQueryBuilder())
            ->select('inducks_issue.issuenumber, inducks_issue.title')
            ->from(InducksIssue::class, 'inducks_issue');

        if (!is_null($publicationCode)) {
            $qb->where($qb->expr()->eq('inducks_issue.publicationcode', $qb->expr()->literal($publicationCode)));
        }

        $results = $qb->getQuery()->getResult();
        $issueNumbers = new stdClass();
        foreach($results as $result) {
            $issueNumber = preg_replace('#[ ]+#', ' ', $result['issuenumber']);
            $issueNumbers->$issueNumber = $result['title'];
        }
        return $issueNumbers;
    }

    public function getIssueNumbersFromPublicationCodeAsArray(string $publicationCode) : array
    {
        $qb = (self::$coaEm->createQueryBuilder())
            ->select('inducks_issue.issuenumber, inducks_issue.title')
            ->from(InducksIssue::class, 'inducks_issue');

        if (!is_null($publicationCode)) {
            $qb->where($qb->expr()->eq('inducks_issue.publicationcode', $qb->expr()->literal($publicationCode)));
        }

        $results = $qb->getQuery()->getResult();
        $issueNumbers = array_map(function(array $result) {
            $issueNumber = preg_replace('#[ ]+#', ' ', $result['issuenumber']);
            return ['issueNumber' => $issueNumber, 'title' => $result['title']];
        }, $results);
        return $issueNumbers;
    }

    public function getStoriesByKeywords(array $keywords) : array
    {
        $condition = "MATCH(inducks_entry.title) AGAINST (:search)";

        $rsm = (new ResultSetMapping())
            ->addScalarResult('storyversioncode', 'storyversioncode')
            ->addScalarResult('title', 'title');

        $query = self::$coaEm->createNativeQuery("
            SELECT inducks_storyversion.storyversioncode, inducks_entry.title AS title, $condition AS score
            FROM inducks_entry
            INNER JOIN inducks_storyversion ON inducks_entry.storyversioncode = inducks_storyversion.storyversioncode
            WHERE $condition
            GROUP BY inducks_storyversion.storycode
            ORDER BY score DESC, inducks_entry.title
            LIMIT 11
        ", $rsm);

        $query->setParameter(':search', implode(',', $keywords));

        $results = $query->getArrayResult();

        $hasMore = false;
        if (count($results) > 10) {
            $results = array_slice($results, 0, 10);
            $hasMore = true;
        }
        return [
            'results' => array_map(function($result) {
                return [
                    'code' => $result['storyversioncode'],
                    'title' => $result['title'],
                ];
            }, $results),
            'hasmore' => $hasMore
        ];
    }

    public function listIssuesFromStoryVersionCode(string $storyVersionCode) : array
    {
        $qb = self::$coaEm->createQueryBuilder();
        $qb
            ->select('inducks_issue.issuecode, inducks_issue.publicationcode, inducks_issue.issuenumber')
            ->from(InducksIssue::class, 'inducks_issue')
            ->innerJoin(InducksEntry::class, 'inducks_entry', Join::WITH, 'inducks_issue.issuecode = inducks_entry.issuecode')
            ->where($qb->expr()->eq('inducks_entry.storyversioncode', ':storyversioncode'))
            ->setParameters(['storyversioncode' => $storyVersionCode])
            ->orderBy('inducks_issue.publicationcode, inducks_issue.issuenumber');

        $results = $qb->getQuery()->getArrayResult();

        return [
            'results' => array_map(function($result) {
                return [
                    'code' => $result['issuecode'],
                    'publicationcode' => $result['publicationcode'],
                    'issuenumber' => $result['issuenumber'],
                ];
            }, $results)
        ];
    }

    public function listEntriesFromIssue(string $publicationCode, string $issueNumber) : array
    {
        $rsm = (new ResultSetMapping())
            ->addScalarResult('storycode', 'storycode')
            ->addScalarResult('kind', 'kind')
            ->addScalarResult('entirepages', 'entirepages')
            ->addScalarResult('url', 'url')
            ->addScalarResult('position', 'position')
            ->addScalarResult('title', 'title');

        $query = self::$coaEm->createNativeQuery("
            SELECT
                inducks_storyversion.storycode,
                kind,
                entirepages,
                inducks_entry.title,
                CONCAT(IF(sitecode = 'thumbnails', 'webusers', sitecode), '/', url) AS url,
                position
            FROM inducks_issue
            INNER JOIN inducks_entry ON inducks_issue.issuecode = inducks_entry.issuecode
            INNER JOIN inducks_storyversion ON inducks_entry.storyversioncode = inducks_storyversion.storyversioncode
            LEFT JOIN inducks_entryurl ON inducks_entry.entrycode = inducks_entryurl.entrycode
            WHERE inducks_issue.publicationcode = :publicationCode
              AND (REPLACE(issuenumber, ' ', '') = :issueNumber)
            GROUP BY inducks_entry.entrycode, position
            ORDER BY position
        ", $rsm);

        $query->setParameters(compact('publicationCode', 'issueNumber'));
        return $query->getArrayResult();
    }

    public function getIssueReleaseDate(string $publicationCode, string $issueNumber) : ?string
    {
        $rsm = (new ResultSetMapping())
            ->addScalarResult('oldestdate', 'oldestdate');

        $releaseDateQuery = self::$coaEm->createNativeQuery("
            SELECT issue.oldestdate
            FROM inducks_issue issue
            WHERE issue.publicationcode = :publicationcode
              AND REPLACE(issue.issuenumber, '', '') = :issuenumber", $rsm)
            ->setParameter('publicationcode', $publicationCode)
            ->setParameter('issuenumber', $issueNumber);

        return $releaseDateQuery->getSingleScalarResult();
    }

    /**
     * @param string[] $publicationCodes
     * @return InducksIssuequotation[]
     */
    public function getQuotations(array $publicationCodes) : array
    {
        $qb = self::$coaEm->createQueryBuilder();
        $qb->select('quotation')
            ->from(InducksIssuequotation::class, 'quotation')
            ->where($qb->expr()->in('quotation.publicationcode', $publicationCodes))
            ->andWhere('quotation.estimationmin IS NOT NULL');
        return $qb->getQuery()->getArrayResult();
    }

    public function getIssueQuotations(array $issueCodes) : array
    {
        $issueCodes = array_map(function(string $issuecode) {
            return preg_replace('#[ ]+#', ' ', $issuecode);
        }, $issueCodes);
        $qb = self::$coaEm->createQueryBuilder();
        $qb->select('quotation')
            ->from(InducksIssuequotation::class, 'quotation')
            ->where($qb->expr()->in('quotation.issuecode', $issueCodes))
            ->andWhere('quotation.estimationmin IS NOT NULL');
        return $qb->getQuery()->getArrayResult();
    }
}
