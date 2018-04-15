<?php
namespace Coverid\Models;

/**
 * Children classes can be generated with (to be run from the app folder):
 * ../vendor/bin/doctrine orm:convert-mapping --namespace=Coverid\\Models\\ --force --from-database annotation ./models --extend Coverid\\Models\\BaseModel
 * ../vendor/bin/doctrine orm:generate-entities models --filter=Coverid\\\\Models --no-backup
 *
 * Recreate the DB schema with (to be run from the app folder) :
 * ../vendor/bin/doctrine orm:schema-tool:create --namespace=Coverid\\Models\\
 */
class BaseModel
{
    public const COVER_ID_VALIDATION = '[0-9]+';
}
