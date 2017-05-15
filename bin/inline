#!/usr/bin/php
<?php
declare(strict_types=1);

/*
 * parameters:
 *      dns - required
 *      username - required
 *      password - optional
 *      tableName - optional (default `inline_content`)
 *
 * example:
 *      php install.php dns="mysql:host=127.0.0.1;dbname=test" username=root password=pass tableName=table
 */

if (!extension_loaded('pdo')) {
    echo "PHP extension 'pdo' is not loaded.\n";
    exit(1);
}

// remove script name
array_shift($argv);

$dns = null;
$username = null;
$password = null;
$tableName = 'inline_content';

foreach($argv as $arg)
{
    if (strpos($arg, '=') === false) {
        echo "Invalid argument format '$arg'\n";
        exit(1);
    }

    [$name, $value] = explode('=', $arg, 2);

    switch ($name) {
        case 'dns':
            $dns = $value;
            break;
        case 'username':
            $username = $value;
            break;
        case 'password':
            $password = $value;
            break;
        case 'tableName':
            $tableName = $value;
            break;
        default:
            echo "Invalid argument '$name'\n";
            exit(1);
    }
}

// check required parameters
if ($dns === null) {
    echo "Missing parameter dns\n";
    exit(1);
}

if ($username === null) {
    echo "Missing parameter dns\n";
    exit(1);
}

if (strpos($dns, 'mysql') === 0) {
    $sql = "CREATE TABLE `$tableName` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `namespace` varchar(255) NOT NULL,
            `locale` varchar(2) NOT NULL,
            `name` varchar(255) NOT NULL,
            `content` text NOT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `unique` (`namespace`,`locale`,`name`),
            KEY `index` (`namespace`,`locale`,`name`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

} elseif (strpos($dns, 'pgsql') === 0 || strpos($dns, 'postgre') === 0) {
    $sql = "CREATE TABLE $tableName (
            id integer NOT NULL,
                namespace character varying NOT NULL,
                locale character(2) NOT NULL,
                name character varying NOT NULL,
                content text NOT NULL
            );
        
            CREATE SEQUENCE {$tableName}_id_seq
                START WITH 1
                INCREMENT BY 1
                NO MINVALUE
                NO MAXVALUE
                CACHE 1;
        
            ALTER TABLE ONLY $tableName ALTER COLUMN id SET DEFAULT nextval('{$tableName}_id_seq'::regclass);
            SELECT pg_catalog.setval('{$tableName}_id_seq', 1, false);
            ALTER TABLE ONLY $tableName ADD CONSTRAINT {$tableName}_id PRIMARY KEY (id);
            ALTER TABLE ONLY $tableName ADD CONSTRAINT {$tableName}_unique UNIQUE (namespace, locale, name);
            CREATE INDEX {$tableName}_index ON $tableName USING btree (namespace, locale, name);";

} else {
    echo "Invalid pdo driver. Supported: mysql|pgsql|postgre\n";
    exit(1);
}


$pdo = new PDO($dns, $username, $password);
$pdo->exec($sql);

echo "Done\n";
