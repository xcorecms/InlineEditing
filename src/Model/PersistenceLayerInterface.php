<?php
declare(strict_types = 1);

/*
 * This file is part of the some package.
 * (c) Jakub Janata <jakubjanata@gmail.com>
 * For the full copyright and license information, please view the LICENSE file.
 */

namespace XcoreCMS\InlineEditing\Model;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
interface PersistenceLayerInterface
{
    /**
     * @param string $namespace
     * @param string $locale
     * @return array - output format ["name" => "value", ...]
     */
    public function getNamespaceContent(string $namespace, string $locale): array;

    /**
     * @param string $namespace
     * @param string $name
     * @param string $locale
     * @param string $content
     * @return bool - TRUE on success or FALSE on failure
     */
    public function saveContent(string $namespace, string $name, string $locale, string $content): bool;
}
