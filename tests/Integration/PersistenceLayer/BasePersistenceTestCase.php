<?php
declare(strict_types = 1);

namespace XcoreCMS\InlineEditing\Tests\Integration\PersistenceLayer;

use XcoreCMS\InlineEditing\Model\PersistenceLayerInterface;
use Tester\Assert;
use Tester\TestCase;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
abstract class BasePersistenceTestCase extends TestCase
{
    /**
     * @var PersistenceLayerInterface
     */
    protected $persistentLayer;

    /**
     * Init persistent layer - ex. connection to db
     */
    abstract protected function initPersistentLayer(): void;

    /**
     *
     */
    public function setUp(): void
    {
        $this->initPersistentLayer();
    }

    public function testGetNamespaceContent(): void
    {
        $data = $this->persistentLayer->getNamespaceContent('spaceX', 'cs');
        Assert::type('array', $data);
    }

    public function testSaveContent(): void
    {
        $result = $this->persistentLayer->saveContent('spaceX', 'nameZ', 'cs', 'contentZ');
        Assert::same(true, $result);
    }
}
