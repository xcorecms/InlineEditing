<?php
declare(strict_types = 1);

/*
 * This file is part of the some package.
 * (c) Jakub Janata <jakubjanata@gmail.com>
 * For the full copyright and license information, please view the LICENSE file.
 */

namespace XcoreCMS\InlineEditing\Model\PersistenceLayer;

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

    /**
     * Doctrine constructor.
     * @param string $tableName
     * @param Connection $connection
     */
    public function __construct(string $tableName, Connection $connection)
    {
        parent::__construct($tableName);
        $this->connection = $connection;
    }

    /**
     * @param string $sql
     * @param array $args
     * @return array
     */
    protected function getKeyPairResult(string $sql, array $args): array
    {
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(1, $args[0]);
        $stmt->bindValue(2, $args[1]);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_KEY_PAIR);
    }

    /**
     * @param string $sql
     * @param array $args
     * @return bool
     */
    protected function updateOrInsertRecord(string $sql, array $args): bool
    {
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(1, $args[0]);
        $stmt->bindValue(2, $args[1]);
        $stmt->bindValue(3, $args[2]);
        $stmt->bindValue(4, $args[3]);
        return $stmt->execute();
    }

    /**
     * @return string
     */
    protected function getDriverName(): string
    {
        return $this->connection->getDriver()->getName();
    }
}
