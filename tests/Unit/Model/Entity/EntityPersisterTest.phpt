<?php
declare(strict_types=1);

namespace XcoreCMS\InlineEditing\Tests\Unit\Model;

use Doctrine\ORM\EntityManager;
use Mockery;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use XcoreCMS\InlineEditing\Model\Entity\EntityPersister;
use XcoreCMS\InlineEditing\Model\Entity\HtmlEntityElement\Element;
use XcoreCMS\InlineEditing\Tests\Mock\Entity\Article;
use XcoreCMS\InlineEditing\Tests\Mock\Entity\ArticleCustom;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../../../bootstrap.php';

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
class EntityPersisterTest extends TestCase
{
    /**
     *
     */
    public function testUpdate(): void
    {
        $article = new Article;

        $em = Mockery::mock(EntityManager::class);
        $em
            ->shouldReceive('find')
            ->withArgs([Article::class, 1])
            ->andReturn($article);

        $persister = new EntityPersister($em);
        $element = new Element(Article::class, 1, 'title', 'new title');
        $persister->update($element);
        Assert::true($element->isValid());
        Assert::same('new title', $article->getTitle());
    }

    /**
     *
     */
    public function testUpdateError(): void
    {
        $article = new Article;

        $em = Mockery::mock(EntityManager::class);
        $em
            ->shouldReceive('find')
            ->withArgs([Article::class, 1])
            ->andReturn($article);

        $validator = Mockery::mock(ValidatorInterface::class);
        $validator
            ->shouldReceive('validatePropertyValue')
            ->withArgs([Article::class, 'title', ''])
            ->andReturn(new ConstraintViolationList([new ConstraintViolation('invalid', '', [], '', '', '')]));

        $persister = new EntityPersister($em, $validator);
        $element = new Element(Article::class, 1, 'title', '');
        $persister->update($element);
        Assert::false($element->isValid());
        Assert::same('invalid', $element->getMessage());
    }

    /**
     *
     */
    public function testUpdateMapper(): void
    {
        $article = new ArticleCustom;

        $em = Mockery::mock(EntityManager::class);

        $em
            ->shouldReceive('find')
            ->withArgs([ArticleCustom::class, 2])
            ->andReturn($article);

        $persister = new EntityPersister($em);
        $element = new Element(ArticleCustom::class, 2, 'title', 'new title');
        $persister->update($element);
        Assert::same('title', $article->getTitle());
    }

    /**
     *
     */
    public function testUpdateMapperError(): void
    {
        $article = new ArticleCustom;

        $em = Mockery::mock(EntityManager::class);

        $em
            ->shouldReceive('find')
            ->withArgs([ArticleCustom::class, 2])
            ->andReturn($article);

        $persister = new EntityPersister($em);
        $element = new Element(ArticleCustom::class, 2, 'exception', '');
        $persister->update($element);
        Assert::false($element->isValid());
        Assert::same('custom message', $element->getMessage());
    }

    /**
     *
     */
    public function tearDown(): void
    {
        Mockery::close();
    }
}

(new EntityPersisterTest)->run();
