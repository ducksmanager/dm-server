<?php
namespace App\Helper;

use App\Entity\EdgeCreator\TranchesEnCoursModeles;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class JsonResponseFromObject extends JsonResponse
{
    public function __construct($object) {
        parent::__construct(self::serializeToJson($object), Response::HTTP_OK, [], true);
    }

    private static function getNormalizer(): ObjectNormalizer {
        $defaultContext = [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                if (get_class($object) === TranchesEnCoursModeles::class) {
                    /** @var TranchesEnCoursModeles $object */
                    return $object->getId();
                }
                return null;
            }
        ];
        return new ObjectNormalizer(null, null, null, null, null, null, $defaultContext);
    }

    private static function serializeToJson($object): string {
        $serializer = new Serializer([self::getNormalizer()], [new JsonEncoder()]);
        return $serializer->serialize($object, 'json');
    }
}
