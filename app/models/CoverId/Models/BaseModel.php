<?php
namespace CoverId\Models;

/**
 * Children classes can be generated with (to be run from the app folder):
 * ../vendor/bin/doctrine orm:convert-mapping --namespace=CoverId\\Models\\ --force --from-database annotation ./models --extend CoverId\\Models\\BaseModel
 * ../vendor/bin/doctrine orm:generate-entities models --filter=CoverId\\\\Models
 *
 * Recreate the DB schema with (to be run from the app folder) :
 * ../vendor/bin/doctrine orm:schema-tool:create --namespace=CoverId\\Models\\
 */
class BaseModel
{
    const COVER_ID_VALIDATION = '[0-9]+';
}