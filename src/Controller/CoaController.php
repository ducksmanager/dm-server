<?php

namespace App\Controller;

use App\Entity\Coa\InducksCountry;
use App\Entity\Coa\InducksCountryname;
use App\Entity\Coa\InducksIssue;
use App\Entity\Coa\InducksPublication;
use App\Entity\Coverid\Covers;
use App\Entity\Dm\Numeros;
use App\EntityTransform\IssueWithCoverIdAndPopularity;
use App\Service\CoaService;
use Doctrine\ORM\Query\Expr\Join;
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
            ->select('inducks_country.countrycode, inducks_countryname.countryname, inducks_country.countryname AS default_countryname')
            ->from(InducksCountry::class, 'inducks_country')
            ->leftJoin(InducksCountryname::class, 'inducks_countryname', Join::WITH, $qb->expr()->andX(
                $qb->expr()->eq(
                'inducks_countryname.countrycode','inducks_country.countrycode'
            ), $qb->expr()->eq('inducks_countryname.languagecode', ':locale')
            ))
            ->setParameter(':locale', $locale);

        if (empty($countryCodes)) {
            $qb
                ->andWhere($qb->expr()->neq('inducks_country.countrycode', ':fakeCountry'))
                ->setParameter(':fakeCountry', 'zz');
        } else {
            $qb->andWhere($qb->expr()->in('inducks_country.countrycode', explode(',', $countryCodes)));
        }

        $results = $qb->getQuery()->getResult();
        $countryNames = [];
        array_walk(
            $results,
            function ($result) use (&$countryNames) {
                $countryNames[$result['countrycode']] = $result['countryname'] ?? $result['default_countryname'];
            }
        );
        asort($countryNames);
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
     *     requirements={"publicationCodesOrCountry"="^([a-z]+|((?P<publicationcode_regex>[a-z]+/[-A-Z0-9]+),){0,19}[a-z]+/[-A-Z0-9]+)$"}
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
    public function listIssuesFromPublicationCode(CoaService $coaService, string $publicationCode): Response
    {
        $issueNumbers = $coaService->listIssuesFromPublicationCodes([$publicationCode]);
        return new JsonResponse($issueNumbers[$publicationCode] ?? []);
    }

    /**
     * @Route(
     *     methods={"GET"},
     *     path="/coa/list/issues/multiple/{publicationCodes}",
     *     requirements={"publicationCodes"="^((?P<publicationcode_regex>[a-z]+/[-A-Z0-9]+),){0,9}[a-z]+/[-A-Z0-9]+$"})"
     * )
     */
    public function listIssuesFromPublicationCodes(CoaService $coaService, string $publicationCodes): Response
    {
        return new JsonResponse($coaService->listIssuesFromPublicationCodes(explode(',', $publicationCodes)));
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

        /** @var IssueWithCoverIdAndPopularity[] $issues */
        $issues = [];

        array_walk(
            $resultsIssueInfo,
            function ($issue) use (&$issues) {
                $issues[$issue['issuecode']] = IssueWithCoverIdAndPopularity::buildWithoutCoverId($issue['countrycode'], $issue['publicationcode'], $issue['title'], $issue['issuenumber']);
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
        $shortIssueCodes = array_combine(
            $longIssueCodes,
            array_map(
                fn(string $longIssueCode) => preg_replace('#[ ]+#', ' ', $longIssueCode),
                $longIssueCodes
            )
        );
        $quotations = array_map(function ($quotationData) use ($shortIssueCodes, $longIssueCodes, $issues) {
            $issueCode = $quotationData['issuecode'];
            foreach($longIssueCodes as $longIssueCode) {
                if ($shortIssueCodes[$longIssueCode] === $quotationData['issuecode']) {
                    $issues[$longIssueCode]->setQuotation([
                        'min' => $quotationData['estimationmin'],
                        'max' => $quotationData['estimationmax']
                    ]);
                }
            }
        }, $coaService->getIssueQuotations($issueCodesList));
        $logger->info(print_r($shortIssueCodes, true));

        $dmEm = $this->getEm('dm');
        $qb = $dmEm->createQueryBuilder();
        $qb->select('issues.issuecode, COUNT(DISTINCT issues.idUtilisateur) AS popularity')
            ->from(Numeros::class, 'issues')
            ->andWhere('issues.issuecode in (:issuecodes)')
            ->setParameter(':issuecodes', array_values($shortIssueCodes))
            ->groupBy('issues.issuecode')
            ->indexBy('issues', 'issues.issuecode');

        $popularityResults = $qb->getQuery()->getResult();
        foreach($popularityResults as $shortIssueCode => $popularityResult) {
            $issues[array_search($shortIssueCode, $shortIssueCodes, true)]->setPopularity($popularityResult['popularity']);
        }

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
     *     path="/coa/stories/search/{withIssues}",
     *     defaults={"withIssues"=""}
     * )
     */
    public function listStoriesFromKeywords(CoaService $coaService, Request $request, $withIssues) : JsonResponse
    {
        $keywords = $request->request->get('keywords');
        $withIssues = $withIssues === 'withIssues';
        return new JsonResponse(
            $coaService->getStoriesByKeywords(explode(' ', $keywords), $withIssues)
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
     *     path="/coa/list/issues/withStoryVersionCode/{storyCode}",
     *     requirements={"storyCode"="^(?P<storyversioncode_regex>[-/A-Za-z0-9 ?&]+)$"}
     * )
     */
    public function listIssuesFromStoryCode(string $storyCode, CoaService $coaService) : JsonResponse
    {
        return new JsonResponse(
            $coaService->listIssuesFromStoryCode($storyCode)
        );
    }

    /**
     * @Route(
     *     methods={"GET"},
     *     path="/coa/list/issues/recent",
     * )
     */
    public function listRecentIssues(CoaService $coaService) : JsonResponse
    {
        return new JsonResponse(
            $coaService->listRecentIssues()
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
