<?php
declare(strict_types = 1);

namespace XcoreCMS\InlineEditing\Tests\Integration\PersistenceLayer;

use Dibi\Connection;
use XcoreCMS\InlineEditing\Model\PersistenceLayer\Dibi;
use Tester\Environment;

require __DIR__ . '/../../bootstrap.php';

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 * @dataProvider ../../databases.ini
 */
class DibiPersistenceTestCase extends BasePersistenceTestCase
{
    /**
     *
     */
    protected function initPersistentLayer(): void
    {
        $params = Environment::loadData();
        $params['database'] = $params['dbname'];

        if ($params['driver'] === 'pdo_mysql') {
            $params['driver'] = 'mysqli';
        } elseif ($params['driver'] === 'pdo_pgsql') {
            $params['driver'] = 'postgre';
            $params['string'] = 'host=' . $params['host'] . ' port=' . $params['port'] . ' dbname=' . $params['dbname'];
        }

        $connection = new Connection($params);
        $this->persistentLayer = new Dibi('inline_content', $connection);
    }
}

(new DibiPersistenceTestCase)->run();
