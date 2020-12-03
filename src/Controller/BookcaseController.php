<?php
namespace App\Controller;

use App\Entity\Dm\BibliothequeOrdreMagazines;
use App\Service\BookcaseService;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookcaseController extends AbstractController
{
    /**
     * @Route(methods={"GET"}, path="/bookcase/{username}")
     */
    public function getBookcase(BookcaseService $bookcaseService, string $username): Response
    {
        $isCurrentUser = $this->getSessionUsername() === $username;
        $data = $bookcaseService->getUserBookcase($username, $isCurrentUser);
        return is_null($data) ? new Response('KO', Response::HTTP_FORBIDDEN) : new JsonResponse($data);
    }

    /**
     * @Route(methods={"GET"}, path="/bookcase/{username}/sort")
     */
    public function getBookcaseSorting(BookcaseService $bookcaseService, string $username): Response
    {
        $isCurrentUser = $this->getSessionUsername() === $username;
        $data = $bookcaseService->getBookcaseSorting($username, $isCurrentUser);
        return is_null($data) ? new Response(Response::HTTP_FORBIDDEN) : new JsonResponse($data);
    }

    /**
     * @Route(methods={"POST"}, path="/bookcase/sort")
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function updateBookcaseSorting(Request $request): Response
    {
        $sessionUser = $this->getSessionUser();
        if (empty($sessionUser)) {
            return new Response('KO', Response::HTTP_UNAUTHORIZED);
        }

        $sorts = $request->request->get('sorts');

        if (is_array($sorts)) {
            $dmEm = $this->getEm('dm');
            $qbMissingSorts = $dmEm->createQueryBuilder();
            $qbMissingSorts
                ->delete(BibliothequeOrdreMagazines::class, 'sorts')
                ->where('sorts.idUtilisateur = :userId')
                ->setParameter(':userId', $sessionUser['id']);
            $qbMissingSorts->getQuery()->execute();

            $maxSort = -1;
            foreach ($sorts as $publicationCode) {
                $sort = new BibliothequeOrdreMagazines();
                $sort->setPublicationcode($publicationCode);
                $sort->setOrdre(++$maxSort);
                $sort->setIdUtilisateur($sessionUser['id']);
                $dmEm->persist($sort);
            }
            $dmEm->flush();
            return new JsonResponse(['max' => $maxSort]);
        }
        return new Response('Invalid sorts parameter', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Route(methods={"GET"}, path="/bookcase/{username}/textures")
     */
    public function getBookcaseTextures(BookcaseService $bookcaseService, string $username): JsonResponse
    {
        return new JsonResponse($bookcaseService->getBookcaseTextures($username));
    }

    /**
     * @Route(methods={"POST"}, path="/bookcase/textures")
     */
    public function updateBookcaseTextures(Request $request, BookcaseService $bookcaseService): Response
    {
        $sessionUsername = $this->getSessionUsername();
        if (empty($sessionUsername)) {
            return new Response('KO', Response::HTTP_UNAUTHORIZED);
        }
        $bookcaseService->updateBookcaseTextures($sessionUsername, $request->request->all());

        return new Response('OK', Response::HTTP_OK);
    }
}