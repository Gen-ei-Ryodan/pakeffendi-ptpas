<?php

namespace App\Services;

use PDO;
use PDOException;

class RemoteStockService
{
    private ?PDO $pdo = null;

    protected function connection(): PDO
    {
        if ($this->pdo === null) {
            $host = config('database.connections.remote_stock.host', 'ptpasonline.dyndns.org');
            $port = config('database.connections.remote_stock.port', '1699');
            $database = config('database.connections.remote_stock.database', 'EzSystem');
            $username = config('database.connections.remote_stock.username', 'intravis');
            $password = config('database.connections.remote_stock.password', 'isen@777');

            $this->pdo = new PDO(
                "dblib:version=7.0;host={$host}:{$port};dbname={$database}",
                $username,
                $password,
                [
                    PDO::ATTR_TIMEOUT => 5,
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );
        }

        return $this->pdo;
    }

    public function getStockBySku(string $sku): ?float
    {
        $pdo = $this->connection();
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
        $placeholders = implode(',', array_fill(0, count($skus), '?'));
        $stmt = $pdo->prepare("SELECT stockid, totalqty FROM vwtotalqtystock WHERE stockid IN ({$placeholders})");
        $stmt->execute(array_values($skus));
        $rows = $stmt->fetchAll();

        $result = [];
        foreach ($rows as $row) {
            $result[$row['stockid']] = (float) $row['totalqty'];
        }

        return $result;
    }

    public function getAllStock(): array
    {
        $pdo = $this->connection();
        $stmt = $pdo->query('SELECT stockid, totalqty FROM vwtotalqtystock WHERE totalqty > 0 ORDER BY stockid');

        return $stmt->fetchAll();
    }

    public function testConnection(): array
    {
        try {
            $pdo = $this->connection();
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
