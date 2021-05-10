<?php
declare(strict_types = 1);

namespace XcoreCMS\InlineEditing\Tests\Mock\Entity;

use XcoreCMS\InlineEditing\Exception\InvalidDataException;
use XcoreCMS\InlineEditing\Model\Entity\Mapper\InlineMapperInterface;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
class ArticleCustom implements InlineMapperInterface
{
    /**
     * @var int
     * @ORM\Column(type="integer")
     * @ORM\Id
     */
    private $id = 2;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $title = 'super title2';

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $content = '<div>hello2</div>';

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    /**
     * Method for getting value
     * @param string $property
     * @return mixed
     */
    public function getInlineData(string $property)
    {
        return $property;
    }

    /**
     * Method for user validation and setting property value
     * @param string $property
     * @param mixed $data
     * @return void
     * @throws InvalidDataException
     */
    public function setInlineData(string $property, $data): void
    {
        if ($property === 'exception' || $data === '') {
            throw new InvalidDataException('custom message');
        }
        $this->title = $property;
    }
}
