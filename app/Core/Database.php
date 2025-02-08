<?php
namespace app\Core;

use PDO;
use PDOException;
use PDOStatement;
use RuntimeException;

class Database {
    private static ?self $instance = null;
    private ?PDO $pdo = null;
    private array $config;

    private function __construct() {
        $this->config = [
            'host' => DB_HOST,
            'port' => DB_PORT,
            'dbname' => DB_NAME,
            'user' => DB_USER,
            'pass' => DB_PASS
        ];
        $this->initConnection();
    }

    public static function getInstance(): self {
        return self::$instance ?? new self();
    }

    private function initConnection(): void {
        try {
            $tempDsn = "pgsql:host={$this->config['host']};port={$this->config['port']}";
            $tempPDO = new PDO($tempDsn, $this->config['user'], $this->config['pass']);
            $tempPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            if (!$this->databaseExists($tempPDO)) {
                $tempPDO->exec("CREATE DATABASE {$this->config['dbname']}");
            }

            $this->connect();
        } catch (PDOException $e) {
            throw new RuntimeException("Database initialization failed: {$e->getMessage()}");
        }
    }

    private function databaseExists(PDO $pdo): bool {
        $stmt = $pdo->prepare("SELECT 1 FROM pg_database WHERE datname = :dbname");
        $stmt->execute(['dbname' => $this->config['dbname']]);
        return (bool)$stmt->fetchColumn();
    }

    private function connect(): void {
        $dsn = "pgsql:host={$this->config['host']};port={$this->config['port']};dbname={$this->config['dbname']}";
        try {
            $this->pdo = new PDO($dsn, $this->config['user'], $this->config['pass'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]);
        } catch (PDOException $e) {
            throw new RuntimeException("Database connection failed: {$e->getMessage()}");
        }
    }

    public function query(string $sql, array $params = []): PDOStatement {
        $this->pdo ??= $this->connect();

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            throw new RuntimeException("Query execution failed: {$e->getMessage()}");
        }
    }

    public function fetchAll(string $sql, array $params = []): array {
        return $this->query($sql, $params)->fetchAll();
    }

    public function fetch(string $sql, array $params = []): ?array {
        return $this->query($sql, $params)->fetch() ?: null;
    }

    public function fetchColumn(string $sql, array $params = []): array {
        return $this->query($sql, $params)->fetchAll(PDO::FETCH_COLUMN);
    }

    public function execute(string $sql, array $params = []): int {
        return $this->query($sql, $params)->rowCount();
    }

    public function lastInsertId(): string {
        return $this->pdo?->lastInsertId() ?? throw new RuntimeException("No active connection");
    }
}