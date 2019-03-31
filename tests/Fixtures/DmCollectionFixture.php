<?php

namespace App\Tests\Fixtures;

use App\Entity\Dm\Achats;
use App\Entity\Dm\BibliothequeOrdreMagazines;
use App\Entity\Dm\Numeros;
use App\Entity\Dm\Users;
use App\Entity\Dm\UsersPermissions;
use App\Tests\TestCommon;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class DmCollectionFixture implements FixtureInterface
{
    protected $username;
    protected $roles = [];
    protected $withPublicationSorts = true;

    /**
     * @param string $username
     * @param string[] $roles
     * @param boolean $withPublicationSorts
     */
    public function __construct(string $username = null, $roles = [], $withPublicationSorts = true) {
        $this->username = $username;
        $this->roles = $roles;
        $this->withPublicationSorts = $withPublicationSorts;
    }

    /**
     * @param ObjectManager $dmEntityManager
     * @param $username
     * @param $password
     * @param array $roles
     * @return Users|null
     */
    protected static function createUser(ObjectManager $dmEntityManager, $username, $password, $roles = []): ?Users
    {
        $user = new Users();

        $dmEntityManager->persist(
            $user
                ->setBetauser(false)
                ->setUsername($username)
                ->setPassword(sha1($password))
                ->setEmail('test@ducksmanager.net')
                ->setDateinscription(\DateTime::createFromFormat('Y-m-d', '2000-01-01'))
                ->setDernieracces(\DateTime::createFromFormat('Y-m-d', '2000-01-01'))
                ->setAccepterpartage(true)
                ->setRecommandationslistemags(true)
                ->setAffichervideo(true)
        );

        foreach($roles as $role=>$privilege) {
            $userPermission = new UsersPermissions();
            $dmEntityManager->persist(
                $userPermission
                    ->setUsername($username)
                    ->setRole($role)
                    ->setPrivilege($privilege)
            );
        }

        $dmEntityManager->flush();

        return $user;
    }

    public function load(ObjectManager $dmEntityManager) : void
    {
        $user = self::createUser($dmEntityManager, $this->username, TestCommon::$testDmUsers[$this->username] ?? 'password', $this->roles);

        $numero1 = new Numeros();
        try {
            $dmEntityManager->persist(
                $numero1
                    ->setPays('fr')
                    ->setMagazine('DDD')
                    ->setNumero('1')
                    ->setEtat('indefini')
                    ->setIdAcquisition('-2')
                    ->setAv(false)
                    ->setIdUtilisateur($user->getId())
                    ->setDateajout(new \DateTime())
            );

            $numero2 = new Numeros();
            $dmEntityManager->persist(
                $numero2
                    ->setPays('fr')
                    ->setMagazine('MP')
                    ->setNumero('300')
                    ->setEtat('bon')
                    ->setAv(false)
                    ->setIdUtilisateur($user->getId())
                    ->setDateajout(new \DateTime())
            );

            $numero3 = new Numeros();
            $dmEntityManager->persist(
                $numero3
                    ->setPays('fr')
                    ->setMagazine('MP')
                    ->setNumero('301')
                    ->setEtat('mauvais')
                    ->setAv(true)
                    ->setIdUtilisateur($user->getId())
                    ->setDateajout(new \DateTime())
            );

            $purchase1 = new Achats();
            $dmEntityManager->persist(
                $purchase1
                    ->setDate(\DateTime::createFromFormat('Y-m-d', '2010-01-01'))
                    ->setDescription('Purchase')
                    ->setIdUser($user->getId())
            );

            if ($this->withPublicationSorts) {
                $publicationSort1 = new BibliothequeOrdreMagazines();
                $dmEntityManager->persist(
                    $publicationSort1
                        ->setPublicationcode('fr/DDD')
                        ->setIdUtilisateur($user->getId())
                        ->setOrdre(1)
                );

                $publicationSort2 = new BibliothequeOrdreMagazines();
                $dmEntityManager->persist(
                    $publicationSort2
                        ->setPublicationcode('fr/JM')
                        ->setIdUtilisateur($user->getId())
                        ->setOrdre(2)
                );
            }

            $dmEntityManager->flush();
            $dmEntityManager->clear();

        } catch (\Exception $e) {

        }
    }
}