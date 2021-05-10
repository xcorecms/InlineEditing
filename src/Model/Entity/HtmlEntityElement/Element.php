<?php

declare(strict_types=1);

namespace XcoreCMS\InlineEditing\Model\Entity\HtmlEntityElement;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
class Element
{
    /**
     * @var string
     * @phpstan-var class-string
     */
    private $className;

    /** @var int|string */
    private $id;

    /** @var string */
    private $property;

    /** @var mixed */
    private $value;

    /** @var int */
    private $status;

    /** @var string */
    private $message;

    /** @var string */
    private $entityHash;

    /**
     * @param string $className
     * @phpstan-param class-string $className
     * @param int|string $id
     * @param string $property
     * @param mixed $value
     */
    public function __construct(string $className, $id, string $property, $value)
    {
        $this->className = $className;
        $this->id = $id;
        $this->property = $property;
        $this->value = $value;

        $this->status = 0;
        $this->message = '';
    }

    /**
     * @return string
     * @phpstan-return class-string
     */
    public function getClassName(): string
    {
        return $this->className;
    }

    /**
     * @return int|string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getProperty(): string
    {
        return $this->property;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param int $status
     * @param string $message
     */
    public function setError(int $status, string $message = ''): void
    {
        $this->status = $status;
        $this->message = $message;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->status === 0;
    }

    /**
     * @return string
     */
    public function getEntityHash(): string
    {
        return $this->entityHash = $this->entityHash ?? md5("{$this->className}_{$this->id}");
    }

    /**
     * @return string
     */
    public function getHtmlId(): string
    {
        return "inline_{$this->className}_{$this->id}_{$this->property}";
    }

    /**
     * @return array<string, string|int>
     */
    public function generateResponse(): array
    {
        $response = ['status' => $this->status];

        if ($this->isValid() === false) {
            $response['message'] = $this->message;
        }

        return $response;
    }
}
