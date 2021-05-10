<?php
declare(strict_types = 1);

namespace XcoreCMS\InlineEditing\Tests\Mock;

use XcoreCMS\InlineEditing\Model\Simple\PersistenceLayerInterface;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
final class PersistenceLayer implements PersistenceLayerInterface
{
    /**
     * @var array<string, array<string, string>>
     */
    public $data = [];

    /**
     * {@inheritDoc}
     */
    public function getNamespaceContent(string $namespace, string $locale): array
    {
        return $this->data[$namespace . '.' . $locale] ?? [];
    }

    /**
     * {@inheritDoc}
     */
    public function saveContent(string $namespace, string $name, string $locale, string $content): bool
    {
        $this->data[$namespace . '.' . $locale][$name] = $content;
        return true;
    }

    /**
     * For debug
     * @param array<string, array<string, string>> $data
     */
    public function setData(array $data): void
    {
        $this->data = $data;
    }
}
