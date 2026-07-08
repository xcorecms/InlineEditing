<?php

declare(strict_types=1);

/*
 * This file is part of the some package.
 * (c) Jakub Janata <jakubjanata@gmail.com>
 * For the full copyright and license information, please view the LICENSE file.
 */

namespace XcoreCMS\InlineEditing\Model\Simple\PersistenceLayer;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\AbstractMySQLPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
class Dbal extends AbstractPersistenceLayer
{
    /**
     * @var Connection
     */
    private $connection;

    public function __construct(string $tableName, Connection $connection)
    {
        parent::__construct($tableName);
        $this->connection = $connection;
    }

    /**
     * {@inheritDoc}
     */
    protected function getKeyPairResult(string $sql, array $args): array
    {
        // name, content
        /** @var array<array<string, string>> $items */
        $items = $this->connection->executeQuery($sql, $args)->fetchAllAssociative();

        $result = [];
        foreach ($items as $item) {
            $result[$item['name']] = $item['content'];
        }
        return $result;
    }

    /**
     * {@inheritDoc}
     */
    protected function updateOrInsertRecord(string $sql, array $args): bool
    {
        $this->connection->executeStatement($sql, $args);
        return true;
    }

    protected function getDriverName(): string
    {
        $platform = $this->connection->getDatabasePlatform();
        if ($platform instanceof AbstractMySQLPlatform) {
            return 'mysql';
        }

        if ($platform instanceof PostgreSQLPlatform) {
            return 'pgsql';
        }

        return 'unknown';
    }
}
