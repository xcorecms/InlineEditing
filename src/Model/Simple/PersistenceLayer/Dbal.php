<?php

declare(strict_types=1);

/*
 * This file is part of the some package.
 * (c) Jakub Janata <jakubjanata@gmail.com>
 * For the full copyright and license information, please view the LICENSE file.
 */

namespace XcoreCMS\InlineEditing\Model\Simple\PersistenceLayer;

use Doctrine\DBAL\Connection;

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
        $stmt = $this->connection->prepare($sql);
        // name, content
        /** @var array<array<string, string>> $items */
        $items = $stmt->executeQuery($args)->fetchAllAssociative();

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
        $stmt = $this->connection->prepare($sql);
        $stmt->executeStatement($args);
        return true;
    }

    protected function getDriverName(): string
    {
        return $this->connection->getDriver()->getDatabasePlatform()->getName();
    }
}
