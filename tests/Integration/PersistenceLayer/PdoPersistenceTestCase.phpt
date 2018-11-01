<?php
declare(strict_types = 1);

namespace XcoreCMS\InlineEditing\Tests\Integration\PersistenceLayer;

use Tester\Environment;
use XcoreCMS\InlineEditing\Model\Simple\PersistenceLayer\Pdo;

require __DIR__ . '/../../bootstrap.php';

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 * @dataProvider ../../databases.ini
 */
class PdoPersistenceTestCase extends BasePersistenceTestCase
{
    /**
     *
     */
    protected function initPersistentLayer(): void
    {
        $params = Environment::loadData();
        $params['driver'] = 'pdo';

        $pdo = new \PDO($params['dsn'], $params['user'], $params['password']);
        $this->persistentLayer = new Pdo('inline_content', $pdo);
    }
}

(new PdoPersistenceTestCase)->run();
