<?php

declare(strict_types=1);

namespace XcoreCMS\InlineEditing\Tests\Mock;

use Psr\Cache\CacheItemInterface;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
class CacheItem implements CacheItemInterface
{
    public string $key;
    public mixed $value;
    public bool $isHit;

    public function __construct(string $key, mixed $value, bool $isHit)
    {
        $this->key = $key;
        $this->value = $value;
        $this->isHit = $isHit;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function get(): mixed
    {
        return $this->value;
    }

    public function isHit(): bool
    {
        return $this->isHit;
    }

    public function set(mixed $value): static
    {
        $this->value = $value;
        return $this;
    }

    public function expiresAt(?\DateTimeInterface $expiration): static
    {
        return $this;
    }

    public function expiresAfter(\DateInterval|int|null $time): static
    {
        return $this;
    }
}
