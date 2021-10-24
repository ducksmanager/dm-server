<?php
namespace App\Service;

use App\Entity\Dm\Users;
use App\Entity\Dm\UsersOptions;
use App\EntityTransform\UserWithOptionValue;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;

class UsersOptionsService {

    private static ObjectManager $dmEm;
    public const OPTION_NAME_SUGGESTION_NOTIFICATION_COUNTRY = 'suggestion_notification_country';
    public const OPTION_NAME_SALES_NOTIFICATION_PUBLICATIONS = 'sales_notification_publications';

    public function __construct(ManagerRegistry $doctrineManagerRegistry)
    {
        self::$dmEm = $doctrineManagerRegistry->getManager('dm');
    }

    /**
     * @param Users $user
     * @param string $optionKey
     * @return UserWithOptionValue
     */
    public function getOptionValueForUser(Users $user, string $optionKey) {
        return new UserWithOptionValue(
            $user,
            array_map(
                fn(UsersOptions $option) => $option->getOptionValeur(), self::$dmEm->getRepository(UsersOptions::class)->findBy([
                    'user' => $user,
                    'optionNom' => $optionKey
                ])
            )
        );
    }

    /**
     * @param string $optionKey
     * @return UserWithOptionValue[]
     */
    public function getOptionValueAllUsers(string $optionKey): array {
        /** @var UsersOptions[] $valuesAllUsers */
        $valuesAllUsers = self::$dmEm->getRepository(UsersOptions::class)->findBy([
            'optionNom' => $optionKey
        ]);

        /** @var UserWithOptionValue[] $valuesAssoc */
        $valuesAssoc = [];
        foreach($valuesAllUsers as $value) {
            if (!isset($valuesAssoc[$value->getUser()->getId()])) {
                $valuesAssoc[$value->getUser()->getId()] = new UserWithOptionValue($value->getUser(), []);
            }
            $valuesAssoc[$value->getUser()->getId()]->addValue($value->getOptionValeur());
        }

        return $valuesAssoc;
    }
}
