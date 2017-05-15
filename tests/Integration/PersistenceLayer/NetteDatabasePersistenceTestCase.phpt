<?php
declare(strict_types = 1);

namespace XcoreCMS\InlineEditing\Tests\Integration\PersistenceLayer;

use Nette\Database\Connection;
use XcoreCMS\InlineEditing\Model\PersistenceLayer\NetteDatabase;
use Tester\Environment;

require __DIR__ . '/../../bootstrap.php';

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 * @dataProvider ../../databases.ini
 */
class NetteDatabasePersistenceTestCase extends BasePersistenceTestCase
{
    /**
     *
     */
    protected function initPersistentLayer(): void
    {
        $params = Environment::loadData();
        $connection = new Connection($params['dsn'], $params['user'], $params['password']);
        $this->persistentLayer = new NetteDatabase('inline_content', $connection);
    }
}

(new NetteDatabasePersistenceTestCase)->run();
