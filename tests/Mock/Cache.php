<?php

declare(strict_types=1);

namespace XcoreCMS\InlineEditing\Tests\Mock;

use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
class Cache implements CacheItemPoolInterface
{
    /**
     * @var CacheItemInterface[]|CacheItem[]
     */
    public array $data = [];

    public function getItem(string $key): CacheItemInterface
    {
        return $this->data[$key] = $this->data[$key] ?? new CacheItem($key, null, false);
    }

    /**
     * @param string[] $keys
     * @return array<string, CacheItemInterface>
     */
    public function getItems(array $keys = []): iterable
    {
        $items = [];
        foreach ($keys as $key) {
            $items[$key] = $this->getItem($key);
        }

        return $items;
    }

    public function hasItem(string $key): bool
    {
        return $this->getItem($key)->isHit();
    }

    public function clear(): bool
    {
        $this->data = [];
        return true;
    }

    public function deleteItem(string $key): bool
    {
        unset($this->data[$key]);
        return true;
    }

    /**
     * @param string[] $keys
     */
    public function deleteItems(array $keys): bool
    {
        foreach ($keys as $key) {
            $this->deleteItem($key);
        }
        return true;
    }

    public function save(CacheItemInterface $item): bool
    {
        $item->isHit = true;
        $this->data[$item->getKey()] = $item;
        return true;
    }

    public function saveDeferred(CacheItemInterface $item): bool
    {
        $this->save($item);
        return true;
    }

    public function commit(): bool
    {
        foreach ($this->data as $item) {
            $item->isHit = true;
        }

        return true;
    }

    /**
     * For debug
     * @param array<string, string> $data
     */
    public function setData(array $data): void
    {
        foreach ($data as $key => $value) {
            $this->data['__inline_prefix_' . $key] = new CacheItem($key, $value, true);
        }
    }
}
