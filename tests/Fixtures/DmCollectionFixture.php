<?php

namespace App\Tests\Fixtures;

use App\Entity\Dm\Abonnements;
use App\Entity\Dm\Achats;
use App\Entity\Dm\BibliothequeOrdreMagazines;
use App\Entity\Dm\Numeros;
use App\Entity\Dm\TranchesPretes;
use App\Entity\Dm\Users;
use App\Entity\Dm\UsersContributions;
use App\Entity\Dm\UsersOptions;
use App\Entity\Dm\UsersPermissions;
use App\Tests\TestCommon;
use DateTime;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;

class DmCollectionFixture implements FixtureInterface
{
    public static string $username;
    public static array $roles = [];
    public static bool $withPublicationSorts = true;

    /**
     * @param ObjectManager $dmEm
     * @param $username
     * @param $password
     * @param array $roles
     * @return Users|null
     */
    protected static function createUser(ObjectManager $dmEm, $username, $password, $roles = []): ?Users
    {
        $dmEm->persist(
            ($user = new Users())
                ->setBetauser(false)
                ->setUsername($username)
                ->setPassword(sha1($password))
                ->setEmail('test@ducksmanager.net')
                ->setDateinscription(DateTime::createFromFormat('Y-m-d', '2000-01-01'))
                ->setAccepterpartage(true)
                ->setRecommandationslistemags(true)
                ->setAffichervideo(true)
                ->setPrecedentacces(new DateTime('-2 days midnight'))
                ->setOptions([])
        );

        foreach($roles as $role=>$privilege) {
            $dmEm->persist(
                (new UsersPermissions())
                    ->setUsername($username)
                    ->setRole($role)
                    ->setPrivilege($privilege)
            );
        }

        $dmEm->flush();

        return $user;
    }

    public function load(ObjectManager $dmEm) : void
    {
        $user = self::createUser(
            $dmEm,
            self::$username,
            TestCommon::$testDmUsers[self::$username] ?? 'password',
            self::$roles
        );

        $dmEm->persist(
            (new Numeros())
                ->setPays('fr')
                ->setMagazine('DDD')
                ->setNumero('1')
                ->setEtat('indefini')
                ->setIdAcquisition(1)
                ->setIsOnSale(false)
                ->setIdUtilisateur($user->getId())
                ->setDateajout(new DateTime())
        );

        $dmEm->persist(
            (new Numeros())
                ->setPays('fr')
                ->setMagazine('MP')
                ->setNumero('300')
                ->setEtat('bon')
                ->setIsOnSale(false)
                ->setIdUtilisateur($user->getId())
                ->setDateajout(new DateTime())
        );

        $dmEm->persist(
            (new Numeros())
                ->setPays('fr')
                ->setMagazine('MP')
                ->setNumero('301')
                ->setEtat('mauvais')
                ->setIsOnSale(true)
                ->setIdUtilisateur($user->getId())
                ->setDateajout(new DateTime())
        );

        $dmEm->persist(
            (new Achats())
                ->setDate(DateTime::createFromFormat('Y-m-d', '2010-01-01'))
                ->setDescription('Purchase')
                ->setIdUser($user->getId())
        );

        $edge1 = $dmEm->getRepository(TranchesPretes::class)->findOneBy([
            'publicationcode' => 'fr/JM',
            'issuenumber' => '3001'
        ]);

        $dmEm->persist(
            (new UsersContributions())
                ->setTranche($edge1)
                ->setUser($user)
                ->setDate(new DateTime())
                ->setContribution('photographe')
                ->setPointsNew(50)
                ->setPointsTotal(50)
        );

        $dmEm->persist(
            (new UsersOptions())
                ->setUser($user)
                ->setOptionNom('suggestion_notification_country')
                ->setOptionValeur('fr')
        );

        $dmEm->persist(
            (new Abonnements())
                ->setUser($user)
                ->setPays('fr')
                ->setMagazine('MP')
                ->setDateDebut((new DateTime())->sub(new \DateInterval('P3M')))
                ->setDateFin((new DateTime())->add(new \DateInterval('P3M')))
        );

        if (self::$withPublicationSorts) {
            $dmEm->persist(
                (new BibliothequeOrdreMagazines())
                    ->setPublicationcode('fr/DDD')
                    ->setIdUtilisateur($user->getId())
                    ->setOrdre(1)
            );

            $dmEm->persist(
                (new BibliothequeOrdreMagazines())
                    ->setPublicationcode('fr/JM')
                    ->setIdUtilisateur($user->getId())
                    ->setOrdre(2)
            );
        }

        $dmEm->flush();
        $dmEm->clear();
    }
}
