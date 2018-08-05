<?php
declare(strict_types=1);

namespace XcoreCMS\InlineEditing\Model\Entity\HtmlEntityElement;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
class ElementEntityContainer
{
    /** @var mixed */
    private $entity;

    /** @var Element[] */
    private $elements;

    /** @var string */
    private $message;

    /** @var bool|null */
    private $valid;

    /**
     * @param mixed $entity
     */
    public function __construct($entity)
    {
        $this->entity = $entity;
        $this->elements = [];
        $this->message = '';
    }

    /**
     * @param Element $element
     * @return Element
     */
    public function addElement(Element $element): Element
    {
        return $this->elements[$element->getProperty()] = $element;
    }

    /**
     * @return mixed
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @return Element[]
     */
    public function getElements(): array
    {
        return $this->elements;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setInvalid(string $message = null)
    {
        if ($message) {
            $this->message = $message;
        }

        // invalidate elements
        foreach ($this->elements as $element) {
            if ($element->getStatus() === 0) {
                $element->setError(1);
            }
        }
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        if ($this->valid === false) {
            return false;
        }

        foreach ($this->elements as $element) {
            if ($element->isValid() === false) {
                return $this->valid = false;
            }
        }

        return true;
    }

    /**
     * @return array
     */
    public function generateResponse(): array
    {
        $response = $this->message ? ['' => ['message' => $this->message]] : [];

        foreach ($this->elements as $element) {
            $response[$element->getHtmlId()] = $element->generateResponse();
        }

        return $response;
    }
}
