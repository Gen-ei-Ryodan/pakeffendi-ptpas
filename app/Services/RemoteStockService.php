<?php

namespace App\Services;

use PDO;
use PDOException;

class RemoteStockService
{
    private ?PDO $pdo = null;
    private ?bool $connected = null;

    public function __construct()
    {
        $this->configureOpenSsl();
    }

    /**
     * Set OPENSSL_CONF untuk kompatibilitas dengan SQL Server 2005 (TLS lama).
     */
    private function configureOpenSsl(): void
    {
        $opensslConf = config('remote_stock.openssl_conf')
            ?: base_path('openssl_custom.cnf');

        if (! getenv('OPENSSL_CONF') && is_string($opensslConf) && $opensslConf !== '' && is_file($opensslConf)) {
            putenv("OPENSSL_CONF={$opensslConf}");
        }
    }

    protected function connection(): ?PDO
    {
        if ($this->connected === false) {
            return null;
        }

        if ($this->pdo === null) {
            $host = config('database.connections.remote_stock.host', 'ptpasonline.dyndns.org');
            $port = config('database.connections.remote_stock.port', '1699');
            $database = config('database.connections.remote_stock.database', 'EzSystem');
            $username = config('database.connections.remote_stock.username', 'intravis');
            $password = config('database.connections.remote_stock.password', 'isen@777');

            try {
                if (extension_loaded('pdo_sqlsrv')) {
                    $dsn = "sqlsrv:Server={$host},{$port};Database={$database};Encrypt=no;TrustServerCertificate=true;LoginTimeout=10;";
                } else {
                    $dsn = "dblib:version=7.0;host={$host}:{$port};dbname={$database}";
                }

                $this->pdo = new PDO(
                    $dsn,
                    $username,
                    $password,
                    [
                        PDO::ATTR_TIMEOUT => 10,
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    ]
                );

                $this->connected = true;
            } catch (PDOException $e) {
                $this->connected = false;
                logger()->warning('RemoteStockService: connection failed - '.$e->getMessage());

                return null;
            }
        }

        return $this->pdo;
    }

    public function getStockBySku(string $sku): ?float
    {
        $pdo = $this->connection();
        if (! $pdo) {
            return null;
        }

        $stmt = $pdo->prepare('SELECT totalqty FROM vwtotalqtystock WHERE stockid = ?');
        $stmt->execute([$sku]);
        $row = $stmt->fetch();

        return $row ? (float) $row['totalqty'] : null;
    }

    public function getStockBatch(array $skus): array
    {
        if (empty($skus)) {
            return [];
        }

        $pdo = $this->connection();
        if (! $pdo) {
            return [];
        }

        try {
            $placeholders = implode(',', array_fill(0, count($skus), '?'));
            $stmt = $pdo->prepare("SELECT stockid, totalqty FROM vwtotalqtystock WHERE stockid IN ({$placeholders})");
            $stmt->execute(array_values($skus));
            $rows = $stmt->fetchAll();

            $result = [];
            foreach ($rows as $row) {
                $result[$row['stockid']] = (float) $row['totalqty'];
            }

            return $result;
        } catch (PDOException $e) {
            logger()->warning('RemoteStockService: query failed - '.$e->getMessage());

            return [];
        }
    }

    public function testConnection(): array
    {
        $pdo = $this->connection();

        if (! $pdo) {
            return [
                'success' => false,
                'error' => 'Could not connect to remote database server.',
            ];
        }

        try {
            $stmt = $pdo->query('SELECT @@VERSION AS ver');
            $row = $stmt->fetch();

            $stmtCount = $pdo->query('SELECT COUNT(*) AS cnt FROM vwtotalqtystock');
            $count = $stmtCount->fetch();

            return [
                'success' => true,
                'version' => $row['ver'] ?? 'N/A',
                'total_stock_records' => (int) ($count['cnt'] ?? 0),
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
