<?php
declare(strict_types=1);

namespace XcoreCMS\InlineEditing\Model\Entity\HtmlEntityElement;

use ArrayAccess;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
class ElementEntityBaseContainer implements ArrayAccess
{
    /** @var ElementEntityContainer[] */
    private $containers;

    /**
     * @param ElementEntityContainer[] $containers
     */
    public function __construct(array $containers = [])
    {
        $this->containers = $containers;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        foreach ($this->containers as $container) {
            if ($container->isValid() === false) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return array
     */
    public function generateResponse(): array
    {
        $response = [];

        foreach ($this->containers as $container) {
            $response += $container->generateResponse();
        }

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset): bool
    {
        return isset($this->containers[$offset]);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset): ElementEntityContainer
    {
        return $this->containers[$offset];
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value): void
    {
        $this->containers[$offset] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset): void
    {
        unset($this->containers[$offset]);
    }
}
