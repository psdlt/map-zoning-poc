<?php

declare(strict_types=1);

namespace App\Doctrine\DBAL\Platform;

use Doctrine\DBAL\Platforms\PostgreSQL100Platform;
use Doctrine\DBAL\Types\Types;

class ExtendedPostgreSqlPlatform extends PostgreSQL100Platform
{
    /**
     * {@inheritdoc}
     */
    protected function initializeDoctrineTypeMappings()
    {
        parent::initializeDoctrineTypeMappings();

        $this->doctrineTypeMapping['polygon'] = Types::STRING;
    }
}
