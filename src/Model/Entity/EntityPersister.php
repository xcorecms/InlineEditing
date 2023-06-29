<?php

declare(strict_types=1);

namespace XcoreCMS\InlineEditing\Model\Entity;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use XcoreCMS\InlineEditing\Exception\InvalidDataException;
use XcoreCMS\InlineEditing\Model\Entity\HtmlEntityElement\Element;
use XcoreCMS\InlineEditing\Model\Entity\HtmlEntityElement\ElementEntityBaseContainer;
use XcoreCMS\InlineEditing\Model\Entity\HtmlEntityElement\ElementEntityContainer;
use XcoreCMS\InlineEditing\Model\Entity\Mapper\InlineMapperInterface;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
class EntityPersister
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var ValidatorInterface|null */
    private $validator;

    /** @var ElementEntityContainer[] */
    private $entityElementContainers;

    /** @var PropertyAccessor */
    private $propertyAccessor;

    public function __construct(EntityManagerInterface $entityManager, ValidatorInterface $validator = null)
    {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->entityElementContainers = [];
        $this->propertyAccessor = new PropertyAccessor();
    }

    /**
     * @param Element $element
     * @param callable|null $onBeforeValidation
     */
    public function update(Element $element, callable $onBeforeValidation = null): void
    {
        $key = $element->getEntityHash();
        $container = $this->entityElementContainers[$key] ?? null;

        if ($container === null) {
            // load entity from database
            $e = $this->entityManager->find($element->getClassName(), $element->getId());
            $container = new ElementEntityContainer($e);
            $this->entityElementContainers[$key] = $container;
        }

        $container->addElement($element);

        $entity = $container->getEntity();

        if (is_callable($onBeforeValidation)) {
            $message = $onBeforeValidation($entity);
            if ($message) {
                $element->setError(3, $message);
            }
        }

        $property = $element->getProperty();
        $value = $element->getValue();

        // validate symfony
        if ($this->validator !== null) {
            $violations = $this->validator->validatePropertyValue($entity, $property, $value);
            if (isset($violations[0])) {
                $element->setError(2, (string) $violations[0]->getMessage());
            }
        }

        // validate and set user custom
        if ($entity instanceof InlineMapperInterface) {
            try {
                $entity->setInlineData($property, $value);
            } catch (InvalidDataException $exception) {
                $element->setError(2, $exception->getMessage());
                return;
            }
        } else {
            $this->propertyAccessor->setValue($entity, $property, $value);
        }
    }

    public function flush(): ElementEntityBaseContainer
    {
        foreach ($this->entityElementContainers as $container) {
            $entity = $container->getEntity();

            // validate symfony
            if ($this->validator !== null) {
                $violations = $this->validator->validate($entity);

                if (count($violations)) {
                    /** @var ConstraintViolationInterface $violation */
                    $violation = $violations[0];

                    trigger_error(sprintf(
                        'Constraint violation in %s::$%s. Returned message "%s" for given %s %s.',
                        get_class($violation->getRoot()),
                        $violation->getPropertyPath(),
                        $violation->getMessage(),
                        gettype($violation->getInvalidValue()),
                        var_export($violation->getInvalidValue(), true)
                    ));

                    $container->setInvalid((string) $violation->getMessage());
                    $this->entityManager->detach($entity);
                    continue;
                }
            }

            // revalidate
            foreach ($container->getElements() as $element) {
                if ($element->isValid() === false) {
                    $container->setInvalid();
                    $this->entityManager->detach($entity);
                    continue 2;
                }
            }
        }

        // ok
        $this->entityManager->flush();

        return new ElementEntityBaseContainer($this->entityElementContainers);
    }
}
