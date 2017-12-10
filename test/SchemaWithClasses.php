<?php

namespace DmServer\Test;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\ToolsException;

class SchemaWithClasses
{
    /** @var array $modelClasses  */
    public $modelClasses;

    /** @var SchemaTool $schemaTool  */
    public $schemaTool;

    /** @var EntityManager $em  */
    public $em;

    /** @var array $cachedCreateSchemaSql  */
    private $cachedCreateSchemaSql = [];

    /** @var array $cachedDropSchemaSql  */
    private $cachedDropSchemaSql = [];

    public static function createFromEntityManager(EntityManager $em) {
        $schemaWithClasses = new self();
        $schemaWithClasses->setEm($em);
        $schemaWithClasses->setSchemaTool(new SchemaTool($em));
        $schemaWithClasses->setModelClasses($em->getMetadataFactory()->getAllMetadata());

        return $schemaWithClasses;
    }

    /**
     * @throws ToolsException
     */
    public function recreateSchema() {
        $this->dropSchemaCached($this->getModelClasses());
        $this->createSchemaCached($this->getModelClasses());
    }

    public function dropSchemaCached(array $classes)
    {
        if (count($this->cachedDropSchemaSql) === 0) {
            $this->cachedDropSchemaSql = $this->getSchemaTool()->getDropSchemaSQL($classes);
        }

        $conn = $this->em->getConnection();
        foreach ($this->cachedDropSchemaSql as $sql) {
            try {
                $conn->executeQuery($sql);
            } catch (\Exception $e) {
            }
        }
    }

    public function createSchemaCached(array $classes)
    {
        if (count($this->cachedCreateSchemaSql) === 0) {
            $this->cachedCreateSchemaSql = $this->getSchemaTool()->getCreateSchemaSql($classes);
        }
        $conn = $this->em->getConnection();

        foreach ($this->cachedCreateSchemaSql as $sql) {
            try {
                $conn->executeQuery($sql);
            } catch (\Exception $e) {
                throw ToolsException::schemaToolFailure($sql, $e);
            }
        }
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

    /**
     * @param EntityManager $em
     */
    public function setEm($em)
    {
        $this->em = $em;
    }
}