<?php
declare(strict_types = 1);

namespace XcoreCMS\InlineEditing\Tests\Mock;

use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
class Cache implements CacheItemPoolInterface
{
    /**
     * @var CacheItemInterface[]|Cache[]
     */
    public $data = [];

    /**
     * @param string $key
     * @return CacheItemInterface
     */
    public function getItem($key): CacheItemInterface
    {
        return $this->data[$key] = $this->data[$key] ?? new CacheItem($key, null, false);
    }

    /**
     * @param string[] $keys
     * @return array|\Traversable
     */
    public function getItems(array $keys = [])
    {
        $items = [];
        foreach ($keys as $key) {
            $items[$key] = $this->getItem($key);
        }

        return $items;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function hasItem($key): bool
    {
        return $this->getItem($key)->isHit();
    }

    /**
     * @return bool
     */
    public function clear(): bool
    {
        $this->data = [];
        return true;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function deleteItem($key): bool
    {
        unset($this->data[$key]);
        return true;
    }

    /**
     * @param string[] $keys
     * @return bool
     */
    public function deleteItems(array $keys): bool
    {
        foreach ($keys as $key) {
            $this->deleteItem($key);
        }
        return true;
    }

    /**
     * @param CacheItemInterface|CacheItem $item
     * @return bool
     */
    public function save(CacheItemInterface $item): bool
    {
        $item->isHit = true;
        $this->data[$item->getKey()] = $item;
        return true;
    }

    /**
     * Sets a cache item to be persisted later.
     *
     * @param CacheItemInterface $item
     *   The cache item to save.
     *
     * @return bool
     *   False if the item could not be queued or if a commit was attempted and failed. True otherwise.
     */
    public function saveDeferred(CacheItemInterface $item): bool
    {
        $this->save($item);
        return true;
    }

    /**
     * @return bool
     */
    public function commit(): bool
    {
        foreach ($this->data as $item) {
            $item->isHit = true;
        }

        return true;
    }

    /**
     * For debug
     * @param array $data
     */
    public function setData(array $data): void
    {
        foreach ($data as $key => $value) {
            $this->data['__inline_prefix_' . $key] = new CacheItem($key, $value, true);
        }
    }
}
