<?php

namespace App\Tests\Fixtures;

use App\Entity\Dm\Users;
use App\Entity\EdgeCreator\EdgecreatorIntervalles;
use App\Entity\EdgeCreator\EdgecreatorModeles2;
use App\Entity\EdgeCreator\EdgecreatorValeurs;
use App\Entity\EdgeCreator\ImagesTranches;
use App\Entity\EdgeCreator\TranchesEnCoursModeles;
use App\Entity\EdgeCreator\TranchesEnCoursModelesImages;
use App\Entity\EdgeCreator\TranchesEnCoursValeurs;
use DateTime;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Exception;

class EdgeCreatorFixture implements FixtureInterface
{
    /** @var Users $user  */
    public static Users $user;

    public static function createModelEcV1(ObjectManager $ecEm, string $userName, string $publicationCode, string $stepNumber, string $functionName, string $optionName, string $optionValue, string $firstIssueNumber, string $lastIssueNumber): void
    {
        $model = new EdgecreatorModeles2();
        [$country, $magazine] = explode('/', $publicationCode);
        $ecEm->persist(
            $model
                ->setPays($country)
                ->setMagazine($magazine)
                ->setOrdre($stepNumber)
                ->setNomFonction($functionName)
                ->setOptionNom($optionName)
        );
        $ecEm->flush();
        $idOption = $model->getId();

        $ecEm->persist(
            ($value = new EdgecreatorValeurs())
                ->setIdOption($idOption)
                ->setOptionValeur($optionValue)
        );
        $ecEm->flush();
        $valueId = $value->getId();

        $ecEm->persist(
            (new EdgecreatorIntervalles())
                ->setIdValeur($valueId)
                ->setNumeroDebut($firstIssueNumber)
                ->setNumeroFin($lastIssueNumber)
                ->setUsername($userName)
        );

        $ecEm->flush();
    }

    /**
     * @return TranchesEnCoursModeles|null
     */
    public static function createModelEcV2(ObjectManager $ecEm, ?string $userName, string $publicationCode, string $issueNumber, array $steps): ?TranchesEnCoursModeles
    {
        [$country, $magazine] = explode('/', $publicationCode);

        $ecEm->persist(
            ($ongoingModel = new TranchesEnCoursModeles())
                ->setPays($country)
                ->setMagazine($magazine)
                ->setNumero($issueNumber)
                ->setUsername($userName)
                ->setActive(true)
        );

        foreach ($steps as $stepNumber => $step) {
            foreach ($step['options'] as $optionName => $optionValue) {
                $ecEm->persist(
                    (new TranchesEnCoursValeurs())
                        ->setIdModele($ongoingModel)
                        ->setOrdre($stepNumber)
                        ->setNomFonction($step['functionName'])
                        ->setOptionNom($optionName)
                        ->setOptionValeur($optionValue)
                );
            }
        }

        $ecEm->flush();

        return $ongoingModel;
    }

    /**
     * @param ObjectManager $ecEm
     * @throws Exception
     */
    public function load(ObjectManager $ecEm) : void
    {
        self::createModelEcV1($ecEm, self::$user->getUsername(), 'fr/DDD', 1, 'Remplir', 'Couleur', '#FF0000', 1, 3);

        // Model v2

        // $ongoingModel1
        self::createModelEcV2($ecEm, self::$user->getUsername(), 'fr/PM', '502', [
            1 => [
                'functionName' => 'Remplir',
                'options' => [
                    'Couleur' => '#FF00FF',
                    'Pos_x' => '0'
                ]
            ],
            2 => [
                'functionName' => 'TexteMyFonts',
                'options' => [
                    'Couleur_texte' => '#000000'
                ]
            ]
        ]);

        $ongoingModel2 = self::createModelEcV2($ecEm, null, 'fr/PM', '503', []);

        $ecEm->persist(
            ($edgePicture = new ImagesTranches())
                ->setNomfichier('photo1.jpg')
                ->setDateheure(new DateTime('today'))
                ->setHash(sha1('test'))
                ->setIdUtilisateur(self::$user->getId())
        );

        $ecEm->persist(
            (new TranchesEnCoursModelesImages())
                ->setIdModele($ongoingModel2)
                ->setIdImage($edgePicture)
                ->setEstphotoprincipale(true)
        );

        self::createModelEcV2($ecEm, null, 'fr/MP', '400', []);
        self::createModelEcV2($ecEm, null, 'fr/MP', '401', []);

        $ecEm->flush();
    }
}
