<?php
declare(strict_types = 1);

namespace XcoreCMS\InlineEditing\Tests\Mock\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
class Article
{

    /**
     * @var int
     * @ORM\Column(type="integer")
     * @ORM\Id
     */
    private $id = 1;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $title = 'super title';

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $content = '<div>hello</div>';

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @Assert\NotBlank
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title)
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
    public function setContent(string $content)
    {
        $this->content = $content;
    }
}
