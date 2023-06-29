<?php

declare(strict_types=1);

/*
 * This file is part of the some package.
 * (c) Jakub Janata <jakubjanata@gmail.com>
 * For the full copyright and license information, please view the LICENSE file.
 */

namespace XcoreCMS\InlineEditing\Model\Simple\PersistenceLayer;

use LogicException;
use XcoreCMS\InlineEditing\Model\Simple\PersistenceLayerInterface;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
abstract class AbstractPersistenceLayer implements PersistenceLayerInterface
{
    /**
     * @var string
     */
    private $tableName;

    /**
     * @var string
     */
    protected $driverName;

    /**
     * @param string $tableName
     */
    public function __construct(string $tableName)
    {
        $this->tableName = $tableName;
    }

    /**
     * @param literal-string $sql
     * @param string[] $args
     * @return array<string, string>
     */
    abstract protected function getKeyPairResult(string $sql, array $args): array;

    /**
     * @param literal-string $sql
     * @param string[] $args
     * @return bool
     */
    abstract protected function updateOrInsertRecord(string $sql, array $args): bool;

    /**
     * @return string
     */
    abstract protected function getDriverName(): string;

    /**
     * {@inheritDoc}
     */
    public function getNamespaceContent(string $namespace, string $locale): array
    {
        /** @var literal-string $sql */
        $sql = "SELECT name, content FROM $this->tableName WHERE namespace = ? AND locale = ?";
        return $this->getKeyPairResult($sql, [$namespace, $locale]);
    }

    /**
     * {@inheritDoc}
     */
    public function saveContent(string $namespace, string $name, string $locale, string $content): bool
    {
        /** @var literal-string $sql */
        $sql = "INSERT INTO $this->tableName (namespace, name, locale, content) VALUES (?, ?, ?, ?) ";

        $driver = $this->detectDbDriver();

        if ($driver === 'mysql') {
            $sql = "$sql ON DUPLICATE KEY UPDATE content = VALUES(content)";
        } elseif ($driver === 'pgsql') {
            $sql = "$sql ON CONFLICT (namespace, name, locale) DO UPDATE SET content = EXCLUDED.content";
        } else {
            throw new LogicException("Unknown driver '$driver'");
        }

        return $this->updateOrInsertRecord($sql, [$namespace, $name, $locale, $content]);
    }

    /**
     * @return string
     */
    private function detectDbDriver(): string
    {
        if ($this->driverName === null) {
            $driverName = $this->getDriverName();

            $this->driverName = '';

            if (strpos($driverName, 'mysql') !== false) {
                $this->driverName = 'mysql';
            } elseif (strpos($driverName, 'pgsql') !== false || strpos($driverName, 'postgre') !== false) {
                $this->driverName = 'pgsql';
            }
        }

        return $this->driverName;
    }
}
