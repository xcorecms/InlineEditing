<?php
declare(strict_types = 1);

namespace XcoreCMS\InlineEditing\Tests\Integration\PersistenceLayer;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use XcoreCMS\InlineEditing\Model\PersistenceLayer\Dbal;
use Tester\Environment;

require __DIR__ . '/../../bootstrap.php';

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 * @dataProvider ../../databases.ini
 */
class DbalPersistenceTestCase extends BasePersistenceTestCase
{
    /**
     *
     */
    protected function initPersistentLayer(): void
    {
        $params = Environment::loadData();
        $connection = DriverManager::getConnection($params, new Configuration);

        $this->persistentLayer = new Dbal('inline_content', $connection);
    }
}

(new DbalPersistenceTestCase)->run();
