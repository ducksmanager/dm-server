<?php

namespace Wtd\Test;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;

class SchemaWithClasses
{
    /** @var array $modelClasses  */
    public $modelClasses;

    /** @var SchemaTool $schemaTool  */
    public $schemaTool;

    public static function createFromEntityManager(EntityManager $em) {
        $schemaWithClasses = new SchemaWithClasses();
        $schemaWithClasses->setSchemaTool(new SchemaTool($em));
        $schemaWithClasses->setModelClasses($em->getMetadataFactory()->getAllMetadata());

        return $schemaWithClasses;
    }

    public function recreateSchema() {
        $this->getSchemaTool()->dropSchema($this->getModelClasses());
        $this->getSchemaTool()->createSchema($this->getModelClasses());
    }

    /**
     * @return array
     */
    public function getModelClasses()
    {
        return $this->modelClasses;
    }

    /**
     * @return SchemaTool
     */
    public function getSchemaTool()
    {
        return $this->schemaTool;
    }

    /**
     * @param array $modelClasses
     */
    public function setModelClasses($modelClasses)
    {
        $this->modelClasses = $modelClasses;
    }

    /**
     * @param SchemaTool $schemaTool
     */
    public function setSchemaTool($schemaTool)
    {
        $this->schemaTool = $schemaTool;
    }
}