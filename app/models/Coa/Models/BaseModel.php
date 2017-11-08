<?php
namespace Coa\Models;

/**
 * Children classes can be generated with (to be run from the app folder) :
 * ../vendor/bin/doctrine orm:convert-mapping --namespace=Coa\\Models\\ --force --from-database annotation ./models --extend Coa\\Models\\BaseModel
 * ../vendor/bin/doctrine orm:generate-entities models --filter=Coa\\\\Models --no-backup
 *
 * Recreate the DB schema with (to be run from the app folder) :
 * ../vendor/bin/doctrine orm:schema-tool:create --namespace=Coa\\Models\\
 */
class BaseModel
{
    const PUBLICATION_CODE_VALIDATION = '[a-z]+/[-A-Z0-9]+';
    const ISSUE_NUMBER_VALIDATION =     '[-A-Z0-9 ]+';
}