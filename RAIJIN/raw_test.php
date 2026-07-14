<?php

echo 'Available Drivers: '.implode(', ', PDO::getAvailableDrivers())."\n";
echo 'PHP Version: '.PHP_VERSION."\n\n";

$server = getenv('DB_HOST') ?: 'sksby.dyndns.org';
$port = getenv('DB_PORT') ?: '1688';
$database = getenv('DB_DATABASE') ?: 'ezsystem';
$username = getenv('DB_USERNAME') ?: 'intravis';
$password = getenv('DB_PASSWORD') ?: 'isen@777';
$objectToCheck = getenv('DB_OBJECT') ?: 'webstocklist';

$opensslConf = getenv('OPENSSL_CONF') ?: (__DIR__.'/openssl_custom.cnf');
if (is_string($opensslConf) && $opensslConf !== '' && is_file($opensslConf)) {
    putenv("OPENSSL_CONF={$opensslConf}");
}

function mask(string $value): string
{
    if ($value === '') {
        return '';
    }

    if (strlen($value) <= 2) {
        return str_repeat('*', strlen($value));
    }

    return substr($value, 0, 1).str_repeat('*', max(strlen($value) - 2, 1)).substr($value, -1);
}

function escapeIdentifier(string $identifier): string
{
    return '['.str_replace(']', ']]', $identifier).']';
}

function tryConnect(string $label, string $dsn, string $username, string $password, string $objectToCheck): void
{
    echo "=== {$label} ===\n";
    echo "Server: {$GLOBALS['server']}:{$GLOBALS['port']} | DB: {$GLOBALS['database']} | User: {$GLOBALS['username']} | Pass: ".mask($GLOBALS['password'])."\n";
    echo "DSN: {$dsn}\n";
    if (getenv('OPENSSL_CONF')) {
        echo 'OPENSSL_CONF: '.getenv('OPENSSL_CONF')."\n";
    }

    $pdoOptions = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];

    if (defined('PDO::SQLSRV_ATTR_QUERY_TIMEOUT')) {
        $pdoOptions[PDO::SQLSRV_ATTR_QUERY_TIMEOUT] = 5;
    }

    $startedAt = microtime(true);

    try {
        $pdo = new PDO($dsn, $username, $password, $pdoOptions);
        $elapsedMs = (int) round((microtime(true) - $startedAt) * 1000);
        echo "RESULT: SUCCESS ({$elapsedMs} ms)\n";

        $version = $pdo->query('SELECT @@VERSION AS version')->fetch();
        if (is_array($version) && isset($version['version'])) {
            echo 'SQL Server: '.trim((string) $version['version'])."\n";
        }

        $dbName = $pdo->query('SELECT DB_NAME() AS db_name')->fetch();
        if (is_array($dbName) && isset($dbName['db_name'])) {
            echo 'Current DB: '.(string) $dbName['db_name']."\n";
        }

        $stmt = $pdo->prepare("
            SELECT TOP (10)
                s.name AS schema_name,
                o.name AS object_name,
                o.type_desc AS type_desc
            FROM sys.objects o
            INNER JOIN sys.schemas s ON s.schema_id = o.schema_id
            WHERE o.name LIKE ?
              AND o.type IN ('U', 'V')
            ORDER BY s.name, o.name
        ");
        $stmt->execute([$objectToCheck]);
        $matches = $stmt->fetchAll();

        if (! is_array($matches) || count($matches) === 0) {
            echo "Object '{$objectToCheck}' (table/view) tidak ketemu.\n\n";

            return;
        }

        echo 'Match object (TOP '.count($matches)."):\n";
        foreach ($matches as $row) {
            echo '- '.$row['schema_name'].'.'.$row['object_name'].' ('.$row['type_desc'].")\n";
        }

        $first = $matches[0];
        $schema = (string) $first['schema_name'];
        $object = (string) $first['object_name'];
        $qualified = escapeIdentifier($schema).'.'.escapeIdentifier($object);

        $dataStmt = $pdo->query("SELECT TOP (5) * FROM {$qualified}");
        $rows = $dataStmt->fetchAll();
        $rowCount = is_array($rows) ? count($rows) : 0;
        echo "Sample data: {$rowCount} row(s)\n";
        if ($rowCount > 0) {
            $keys = array_keys((array) $rows[0]);
            echo 'Columns ('.count($keys).'): '.implode(', ', $keys)."\n";
        }

        echo "\n";
    } catch (Throwable $e) {
        $elapsedMs = (int) round((microtime(true) - $startedAt) * 1000);
        echo "RESULT: FAILED ({$elapsedMs} ms)\n";
        echo 'ERROR: '.$e->getMessage()."\n\n";
    }
}

echo "Target: {$server}:{$port} | DB={$database} | User={$username} | Object={$objectToCheck}\n";
if (is_string($opensslConf) && $opensslConf !== '') {
    echo "OpenSSL config candidate: {$opensslConf}\n";
}
echo "\n";

tryConnect(
    'Test 1 (Encrypt=no, TrustServerCertificate=true)',
    "sqlsrv:Server={$server},{$port};Database={$database};TrustServerCertificate=true;Encrypt=no",
    $username,
    $password,
    $objectToCheck
);

tryConnect(
    'Test 2 (Encrypt=yes, TrustServerCertificate=true)',
    "sqlsrv:Server={$server},{$port};Database={$database};TrustServerCertificate=true;Encrypt=yes",
    $username,
    $password,
    $objectToCheck
);

tryConnect(
    'Test 3 (Encrypt=no, TrustServerCertificate=false)',
    "sqlsrv:Server={$server},{$port};Database={$database};TrustServerCertificate=false;Encrypt=no",
    $username,
    $password,
    $objectToCheck
);
