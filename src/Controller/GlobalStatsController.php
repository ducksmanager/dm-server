<?php

namespace App\Controller;

use App\Helper\JsonResponseFromObject;
use App\Service\GlobalStatsService;
use Symfony\Component\Routing\Annotation\Route;

class GlobalStatsController
{
    /**
     * @Route(methods={"GET"}, path="/global-stats/user/{userIds}")
     */
    public function getUsersStats(GlobalStatsService $globalStatsService, string $userIds): JsonResponseFromObject
    {
        $userIdsArray = explode(',', $userIds);
        return new JsonResponseFromObject([
            'points' => $globalStatsService->getUsersPoints($userIdsArray),
            'stats' => $globalStatsService->getUsersQuickStats($userIdsArray)
        ]);
    }
}