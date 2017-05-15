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
class DibiPdoPersistenceTestCase extends BasePersistenceTestCase
{
    /**
     *
     */
    protected function initPersistentLayer(): void
    {
        $params = Environment::loadData();
        $params['driver'] = 'pdo';

        $connection = new Connection($params);
        $this->persistentLayer = new Dibi('inline_content', $connection);
    }
}

(new DibiPdoPersistenceTestCase)->run();
