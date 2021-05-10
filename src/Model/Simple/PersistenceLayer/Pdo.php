<?php
declare(strict_types = 1);

/*
 * This file is part of the some package.
 * (c) Jakub Janata <jakubjanata@gmail.com>
 * For the full copyright and license information, please view the LICENSE file.
 */

namespace XcoreCMS\InlineEditing\Model\Simple\PersistenceLayer;

use PDO as XPDO;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
class Pdo extends AbstractPersistenceLayer
{
    /** @var XPDO */
    private $pdo;

    public function __construct(string $tableName, XPDO $pdo)
    {
        parent::__construct($tableName);
        $this->pdo = $pdo;
    }

    /**
     * {@inheritDoc}
     */
    protected function getKeyPairResult(string $sql, array $args): array
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(1, $args[0]);
        $stmt->bindValue(2, $args[1]);
        $stmt->execute();

        return $stmt->fetchAll(XPDO::FETCH_KEY_PAIR) ?: [];
    }

    /**
     * {@inheritDoc}
     */
    protected function updateOrInsertRecord(string $sql, array $args): bool
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(1, $args[0]);
        $stmt->bindValue(2, $args[1]);
        $stmt->bindValue(3, $args[2]);
        $stmt->bindValue(4, $args[3]);
        return $stmt->execute();
    }

    protected function getDriverName(): string
    {
        return $this->pdo->getAttribute(\PDO::ATTR_DRIVER_NAME);
    }
}
