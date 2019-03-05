<?php
namespace App\Helper;

use App\Entity\EdgeCreator\TranchesEnCoursModeles;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class JsonResponseFromObject extends JsonResponse
{
    public function __construct($object) {
        parent::__construct(self::serializeToJson($object), \Symfony\Component\HttpFoundation\Response::HTTP_OK, [], true);
    }

    private static function getNormalizer() {
        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceHandler(function ($object) {
            if (get_class($object) === TranchesEnCoursModeles::class) {
                /** @var TranchesEnCoursModeles $object */
                return $object->getId();
            }
            return null;
        });
        return $normalizer;
    }

    private static function serializeToJson($object): string {
        $serializer = new Serializer([self::getNormalizer()], [new JsonEncoder()]);
        return $serializer->serialize($object, 'json');
    }

    public static function deserializeFromJson($object, $class): string {
        $serializer = new Serializer([self::getNormalizer()], [new JsonEncoder()]);
        return $serializer->deserialize($object, $class,'json');
    }
}