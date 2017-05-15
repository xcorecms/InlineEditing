<?php
declare(strict_types = 1);

namespace XcoreCMS\InlineEditing\Tests\Mock;

use Psr\Cache\CacheItemInterface;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
class CacheItem implements CacheItemInterface
{
    /**
     * @var string
     */
    public $key;

    /**
     * @var mixed
     */
    public $value;

    /**
     * @var bool
     */
    public $isHit;

    /**
     * @param string $key
     * @param mixed $value
     * @param bool $isHit
     */
    public function __construct(string $key, $value, bool $isHit)
    {
        $this->key = $key;
        $this->value = $value;
        $this->isHit = $isHit;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return mixed
     */
    public function get()
    {
        return $this->value;
    }

    /**
     * @return bool
     */
    public function isHit(): bool
    {
        return $this->isHit;
    }

    /**
     * @param mixed $value
     * @return static
     */
    public function set($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @param \DateTimeInterface|null $expiration
     * @return static
     */
    public function expiresAt($expiration)
    {
        return $this;
    }

    /**
     * @param int|\DateInterval|null $time
     * @return static
     */
    public function expiresAfter($time)
    {
        return $this;
    }
}
