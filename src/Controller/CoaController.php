<?php

namespace App\Controller;

use App\Entity\Coa\InducksCountryname;
use App\Entity\Coa\InducksIssue;
use App\Entity\Coa\InducksPublication;
use App\Entity\Coverid\Covers;
use App\EntityTransform\SimpleIssueWithCoverId;
use App\Service\CoaService;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Query\Expr\OrderBy;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CoaController extends AbstractController
{
    /**
     * @Route(methods={"GET"}, path="/coa/list/countries/{_locale}/{countryCodes}", defaults={"countryCodes"=""})
     */
    public function listCountriesFromCodes(?string $countryCodes, Request $request): Response
    {
        $locale = $request->getLocale();
        $coaEm = $this->getEm('coa');
        $qb = $coaEm->createQueryBuilder();
        $qb
            ->select('inducks_countryname.countrycode, inducks_countryname.countryname')
            ->from(InducksCountryname::class, 'inducks_countryname')
            ->where($qb->expr()->eq('inducks_countryname.languagecode', ':locale'))
            ->setParameter(':locale', $locale);

        if (empty($countryCodes)) {
            $qb
                ->andWhere($qb->expr()->neq('inducks_countryname.countrycode', ':fakeCountry'))
                ->setParameter(':fakeCountry', 'zz');
        } else {
            $qb->andWhere($qb->expr()->in('inducks_countryname.countrycode', explode(',', $countryCodes)));
        }

        $qb->addOrderBy(new OrderBy('inducks_countryname.countryname'));

        $results = $qb->getQuery()->getResult();
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
     *     path="/coa/list/publications"
     * )
     */
    public function listPublications(CoaService $coaService): Response
    {
        return new JsonResponse($coaService->getPublicationTitles());
    }

    /**
     * @Route(
     *     methods={"GET"},
     *     path="/coa/list/publications/{publicationCodesOrCountry}",
     *     requirements={"publicationCodesOrCountry"="^([a-z]+|((?P<publicationcode_regex>[a-z]+/[-A-Z0-9]+),){0,9}[a-z]+/[-A-Z0-9]+)$"}
     * )
     */
    public function listPublicationsFromPublicationCodes(string $publicationCodesOrCountry, CoaService $coaService): Response
    {
        return new JsonResponse(
            preg_match('#^[a-z]+$#', $publicationCodesOrCountry)
                ? $coaService->getPublicationTitlesFromCountry($publicationCodesOrCountry)
                : $coaService->getPublicationTitles(array_unique(explode(',', $publicationCodesOrCountry)))
        );
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
            fn($issue) => preg_replace('#[ ]+#', ' ', $issue['issuenumber']),
            $results
        );
        return new JsonResponse($issueNumbers);
    }

    /**
     * @Route(
     *     methods={"GET"},
     *     path="/coa/list/issues/count"
     * )
     */
    public function countIssuesPerPublicationCode(CoaService $coaService): Response
    {
        return new JsonResponse($coaService->getIssueCount());
    }

    /**
     * @Route(
     *     methods={"GET"},
     *     path="/coa/list/issues/withTitle/{publicationCode}",
     *     requirements={"publicationCode"="^(?P<publicationcode_regex>[a-z]+/[-A-Z0-9]+)$"}
     * )
     */
    public function listIssuesWithTitleFromPublicationCode(CoaService $coaService, string $publicationCode): Response
    {
        return new JsonResponse($coaService->getIssueNumbersFromPublicationCode($publicationCode));
    }

    /**
     * @Route(
     *     methods={"GET"},
     *     path="/coa/list/issues/withDetails/{publicationCode}",
     *     requirements={"publicationCode"="^(?P<publicationcode_regex>[a-z]+/[-A-Z0-9]+)$"}
     * )
     */
    public function listIssuesWithDetailsFromPublicationCode(CoaService $coaService, string $publicationCode): Response
    {
        return new JsonResponse($coaService->getIssueNumbersWithDetailsFromPublicationCode($publicationCode));
    }

    /**
     * @Route(
     *     methods={"GET"},
     *     path="/coa/list/issues/withTitle/asArray/{publicationCode}",
     *     requirements={"publicationCode"="^(?P<publicationcode_regex>[a-z]+/[-A-Z0-9]+)$"}
     * )
     */
    public function listIssuesWithTitleFromPublicationCodeAsArray(CoaService $coaService, string $publicationCode): Response
    {
        return new JsonResponse($coaService->getIssueNumbersFromPublicationCodeAsArray($publicationCode));
    }

    /**
     * @Route(
     *     methods={"GET"},
     *     path="/coa/list/issuesbycodes/{issueCodes}",
     *     requirements={"issueCodes"="^((?P<issuecode_regex>[a-z]+/[-A-Z0-9 ]+),){0,3}[a-z]+/[-A-Z0-9 ]+$"}
     * )
     */
    public function listIssuesFromIssueCodes(string $issueCodes, LoggerInterface $logger, CoaService $coaService): Response
    {
        $coaEm = $this->getEm('coa');
        $issueCodesList = explode(',', $issueCodes);

        $qbIssueInfo = $coaEm->createQueryBuilder();
        $qbIssueInfo
            ->select('inducks_publication.countrycode, inducks_publication.publicationcode, inducks_publication.title, inducks_issue.issuenumber, inducks_issue.issuecode')
            ->from(InducksIssue::class, 'inducks_issue')
            ->join(InducksPublication::class, 'inducks_publication', Join::WITH, 'inducks_issue.publicationcode = inducks_publication.publicationcode');

        $qbIssueInfo->where($qbIssueInfo->expr()->in('inducks_issue.issuecode', $issueCodesList));

        $resultsIssueInfo = array_map(function($issue) {
            $issue['issuenumber'] = preg_replace('#[ ]+#', ' ', $issue['issuenumber']);
            return $issue;
        }, $qbIssueInfo->getQuery()->getResult());

        /** @var SimpleIssueWithCoverId[] $issues */
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
            ->select('covers.id AS coverid, covers.issuecode, '.CoveridController::getFullUrlFunc($qbCoverInfo).' as coverurl')
            ->from(Covers::class, 'covers');

        $qbCoverInfo->where($qbCoverInfo->expr()->in('covers.issuecode', $issueCodesList));

        $resultsCoverInfo = $qbCoverInfo->getQuery()->getResult();

        array_walk(
            $resultsCoverInfo,
            function ($issue) use (&$issues, $logger) {
                if (empty($issues[$issue['issuecode']])) {
                    $logger->error('No COA data exists for this issue : ' . $issue['issuecode']);
                    unset($issues[$issue['issuecode']]);
                } else {
                    $issues[$issue['issuecode']]->setCoverid($issue['coverid']);
                    $issues[$issue['issuecode']]->setCoverurl($issue['coverurl']);
                }
            }
        );

        $longIssueCodes = array_keys($issues);
        $quotations = array_map(function ($quotationData) use ($longIssueCodes, $logger, $issues) {
            $logger->info(print_r($quotationData, true));
            $issueCode = $quotationData['issuecode'];
            foreach($longIssueCodes as $longIssueCode) {
                $issueCode = preg_replace('#[ ]+#', ' ', $longIssueCode);
                if ($issueCode === $quotationData['issuecode']) {
                    $issues[$longIssueCode]->setQuotation([
                        'min' => $quotationData['estimationmin'],
                        'max' => $quotationData['estimationmax']
                    ]);
                }
            }
        }, $coaService->getIssueQuotations($issueCodesList));

        return new JsonResponse(self::getSimpleArray($issues));
    }

    /**
     * @Route(methods={"GET"}, path="/coa/authorsfullnames/{authors}")
     */
    public function listAuthorsFromAuthorCodes(string $authors, CoaService $coaService): JsonResponse
    {
        return new JsonResponse(
            array_unique($coaService->getAuthorNames(explode(',', $authors)))
        );
    }

    /**
     * @Route(methods={"GET"}, path="/coa/authorsfullnames/search/{partialAuthorName}")
     */
    public function listAuthorsFromPartialName(string $partialAuthorName, CoaService $coaService): JsonResponse
    {
        return new JsonResponse(
            $coaService->getAuthorNamesFromPartialName($partialAuthorName)
        );
    }

    /**
     * @Route(
     *     methods={"POST"},
     *     path="/coa/stories/search"
     * )
     */
    public function listStoriesFromKeywords(CoaService $coaService, Request $request) : JsonResponse
    {
        $keywords = $request->request->get('keywords');
        return new JsonResponse(
            $coaService->getStoriesByKeywords(explode(' ', $keywords))
        );
    }

    /**
     * @Route(
     *     methods={"POST"},
     *     path="/coa/issues/decompose"
     * )
     */
    public function decomposeIssueCodes(CoaService $coaService, Request $request) : JsonResponse
    {
        return new JsonResponse(
            $coaService->decomposeIssueCodes(explode(',', $request->request->get('issueCodes')))
        );
    }

    /**
     * @Route(
     *     methods={"GET"},
     *     path="/coa/list/issues/withStoryVersionCode/{storyVersionCode}",
     *     requirements={"storyVersionCode"="^(?P<storyversioncode_regex>[-/A-Za-z0-9 ?&]+)$"}
     * )
     */
    public function listIssuesFromStoryVersionCode(string $storyVersionCode, CoaService $coaService) : JsonResponse
    {
        return new JsonResponse(
            $coaService->listIssuesFromStoryVersionCode($storyVersionCode)
        );
    }

    /**
     * @Route(
     *     methods={"GET"},
     *     path="/coa/list/details/{publicationCode}/{issueNumber}",
     *     requirements={"publicationCode"="^(?P<publicationcode_regex>[a-z]+/[-A-Z0-9]+)$"})
     */
    public function listUrlsFromIssue(string $publicationCode, string $issueNumber, CoaService $coaService): JsonResponse
    {
        return new JsonResponse(
            [
                'releaseDate' => $coaService->getIssueReleaseDate($publicationCode, $issueNumber),
                'entries' => $coaService->listEntriesFromIssue($publicationCode, $issueNumber)
            ]
        );
    }

    /**
     * @Route(
     *     methods={"GET"},
     *     path="/coa/quotations/{publicationCodes}",
     *     requirements={"publicationCodes"="^((?P<publicationcode_regex>[a-z]+/[-A-Z0-9]+),){0,9}[a-z]+/[-A-Z0-9]+$"})"
     * )
     */
    public function listQuotations(CoaService $coaService, string $publicationCodes): JsonResponse
    {
        $quotations = $coaService->getQuotations(explode(',', $publicationCodes));
        return new JsonResponse($quotations);
    }

    /**
     * @Route(
     *     methods={"GET"},
     *     path="/coa/quotations/{issueCodes}",
     *     requirements={"issueCodes"="^((?P<issuecode_regex>[a-z]+/[-A-Z0-9 ]+),){0,3}[a-z]+/[-A-Z0-9 ]+$"}
     * )
     */
    public function getIssueQuotations(CoaService $coaService, string $issueCodes): JsonResponse
    {
        return new JsonResponse($coaService->getIssueQuotations(explode(',', $issueCodes)));
    }
}
