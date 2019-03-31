<?php

namespace App\Tests\Fixtures;

use App\Entity\Dm\Users;
use App\Entity\EdgeCreator\EdgecreatorIntervalles;
use App\Entity\EdgeCreator\EdgecreatorModeles2;
use App\Entity\EdgeCreator\EdgecreatorValeurs;
use App\Entity\EdgeCreator\ImagesTranches;
use App\Entity\EdgeCreator\TranchesEnCoursContributeurs;
use App\Entity\EdgeCreator\TranchesEnCoursModeles;
use App\Entity\EdgeCreator\TranchesEnCoursModelesImages;
use App\Entity\EdgeCreator\TranchesEnCoursValeurs;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class EdgeCreatorFixture implements FixtureInterface
{
    /** @var Users $user  */
    protected $user;

    /**
     * @param Users $user
     */
    public function __construct(Users $user = null) {
        $this->user = $user;
    }

    public static function createModelEcV1(ObjectManager $edgeCreatorEntityManager, string $userName, string $publicationCode, string $stepNumber, string $functionName, string $optionName, string $optionValue, string $firstIssueNumber, string $lastIssueNumber): void
    {
        $model = new EdgecreatorModeles2();
        [$country, $magazine] = explode('/', $publicationCode);
        $edgeCreatorEntityManager->persist(
            $model
                ->setPays($country)
                ->setMagazine($magazine)
                ->setOrdre($stepNumber)
                ->setNomFonction($functionName)
                ->setOptionNom($optionName)
        );
        $edgeCreatorEntityManager->flush();
        $idOption = $model->getId();

        $value = new EdgecreatorValeurs();
        $edgeCreatorEntityManager->persist(
            $value
                ->setIdOption($idOption)
                ->setOptionValeur($optionValue)
        );
        $edgeCreatorEntityManager->flush();
        $valueId = $value->getId();

        $interval = new EdgecreatorIntervalles();
        $edgeCreatorEntityManager->persist(
            $interval
                ->setIdValeur($valueId)
                ->setNumeroDebut($firstIssueNumber)
                ->setNumeroFin($lastIssueNumber)
                ->setUsername($userName)
        );

        $edgeCreatorEntityManager->flush();
    }

    /**
     * @param ObjectManager $edgeCreatorEntityManager
     * @param string $userName
     * @param string $publicationCode
     * @param string $issueNumber
     * @param array $steps
     * @return TranchesEnCoursModeles|null
     */
    public static function createModelEcV2(ObjectManager $edgeCreatorEntityManager, ?string $userName, string $publicationCode, string $issueNumber, array $steps): ?TranchesEnCoursModeles
    {
        [$country, $magazine] = explode('/', $publicationCode);

        $ongoingModel = new TranchesEnCoursModeles();
        $edgeCreatorEntityManager->persist(
            $ongoingModel
                ->setPays($country)
                ->setMagazine($magazine)
                ->setNumero($issueNumber)
                ->setUsername($userName)
                ->setActive(true)
                ->setPretepourpublication(false)
        );

        foreach ($steps as $stepNumber => $step) {
            foreach ($step['options'] as $optionName => $optionValue) {
                $ongoingModel1Step1Value1 = new TranchesEnCoursValeurs();
                $edgeCreatorEntityManager->persist(
                    $ongoingModel1Step1Value1
                        ->setIdModele($ongoingModel)
                        ->setOrdre($stepNumber)
                        ->setNomFonction($step['functionName'])
                        ->setOptionNom($optionName)
                        ->setOptionValeur($optionValue)
                );
            }
        }

        $edgeCreatorEntityManager->flush();

        return $ongoingModel;
    }

    /**
     * @param ObjectManager $ecEntityManager
     * @throws \Exception
     */
    public function load(ObjectManager $ecEntityManager) : void
    {
        self::createModelEcV1($ecEntityManager, $this->user->getUsername(), 'fr/DDD', 1, 'Remplir', 'Couleur', '#FF0000', 1, 3);

        // Model v2

        // $ongoingModel1
        self::createModelEcV2($ecEntityManager, $this->user->getUsername(), 'fr/PM', '502', [
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

        $ongoingModel2 = self::createModelEcV2($ecEntityManager, null, 'fr/PM', '503', []);

        $edgePicture = new ImagesTranches();
        $ecEntityManager->persist(
            $edgePicture
                ->setNomfichier('photo1.jpg')
                ->setDateheure(new \DateTime('today'))
                ->setHash(sha1('test'))
                ->setIdUtilisateur($this->user->getId())
        );

        $ongoingModel2MainEdgePicture = new TranchesEnCoursModelesImages();
        $ecEntityManager->persist(
            $ongoingModel2MainEdgePicture
                ->setIdModele($ongoingModel2)
                ->setIdImage($edgePicture)
                ->setEstphotoprincipale(true)
        );

        $ecEntityManager->flush();

        $ongoingModel2Contributor1 = new TranchesEnCoursContributeurs();
        $ecEntityManager->persist(
            $ongoingModel2Contributor1
                ->setIdModele($ongoingModel2)
                ->setIdUtilisateur($this->user->getId())
                ->setContribution('photographe')
        );

        // $ongoingModel3
        self::createModelEcV2($ecEntityManager, null, 'fr/MP', '400', []);

        $ongoingModel4 = self::createModelEcV2($ecEntityManager, null, 'fr/MP', '401', []);

        $ongoingModel4Contributor1 = new TranchesEnCoursContributeurs();
        $ecEntityManager->persist(
            $ongoingModel4Contributor1
                ->setIdModele($ongoingModel4)
                ->setIdUtilisateur($this->user->getId())
                ->setContribution('createur')
        );

        $ecEntityManager->flush();
    }
}