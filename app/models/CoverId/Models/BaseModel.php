<?php
namespace CoverId\Models;

/**
 * Children classes are generated with (to be run from the app folder):
 * doctrine orm:convert-mapping --namespace=CoverId\\Models\\ --force --from-database annotation ./models --extend CoverId\\Models\\BaseModel
 * doctrine orm:generate-entities models --filter=CoverId\\\\Models
 */
class BaseModel
{
    const COVER_ID_VALIDATION = '[0-9]+';
}