<?php

declare(strict_types=1);

namespace XcoreCMS\InlineEditing\Model\Entity\Mapper;

use XcoreCMS\InlineEditing\Exception\InvalidDataException;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
interface InlineMapperInterface
{
    /**
     * Method for getting value
     * @param string $property
     * @return mixed
     */
    public function getInlineData(string $property);

    /**
     * Method for user validation and setting property value
     * @param string $property
     * @param mixed $data
     * @return void
     * @throws InvalidDataException
     */
    public function setInlineData(string $property, $data): void;
}
