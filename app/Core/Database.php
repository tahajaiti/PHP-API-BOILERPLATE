<?php
namespace app\Core;

use PDO;
use Exception;
use PDOException;
use PDOStatement;

/**
 * Class Database
 *
 * Handles database connections and queries using PDO.
 */
class Database
{
    private static ?Database $instance = null;
    private string $host;
    private string $port;
    private string $dbname;
    private string $user;
    private string $password;
    private ?PDO $pdo = null;

    /**
     * Database constructor.
     *
     * Initializes the database connection using global constants.
     *
     * @throws Exception
     */
    private function __construct()
    {
        $this->host = DB_HOST;
        $this->port = DB_PORT;
        $this->dbname = DB_NAME;
        $this->user = DB_USER;
        $this->password = DB_PASS;
        $this->initDatabase();
    }

    /**
     * Returns an instance of the Database class.
     *
     * @return Database
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Initializes the database by checking if the database exists.
     * If not, it creates a new one and connects to it.
     *
     * @throws Exception
     */
    private function initDatabase(): void
    {
        $dsn = "pgsql:host=" . $this->host . ";port=" . $this->port;

        try {
            $tempPDO = new PDO($dsn, $this->user, $this->password);
            $tempPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $tempPDO->prepare("SELECT 1 FROM pg_database WHERE datname = :dbname");
            $stmt->execute(['dbname' => $this->dbname]);
            $exists = $stmt->fetchColumn();

            if (!$exists) {
                $tempPDO->exec("CREATE DATABASE " . $this->dbname);
            }

            $tempPDO = null;

            $this->connect();
        } catch (PDOException $e) {
            throw new \RuntimeException("Database initialization failed: " . $e->getMessage());
        }
    }

    /**
     * Establishes a connection to the database.
     *
     * @throws Exception
     */
    private function connect(): void
    {
        $dsn = "pgsql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->dbname;
        try {
            $this->pdo = new PDO($dsn, $this->user, $this->password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            echo 'connected';
        } catch (PDOException $e) {
            throw new \RuntimeException("Database connection failed: " . $e->getMessage());
        }
    }

    /**
     * Prepares and executes a SQL statement.
     *
     * @param string $sql The SQL query to execute.
     * @param array $params The parameters to bind to the query.
     * @return PDOStatement|null
     * @throws Exception
     */
    public function prepareExecute(string $sql, array $params = []): ?PDOStatement
    {
        if ($this->pdo === null) {
            $this->connect();
        }

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            throw new \RuntimeException("Query execution failed: " . $e->getMessage());
        }
    }

    /**
     * Fetches all rows from a SQL query.
     *
     * @param string $sql The SQL query to execute.
     * @param array $params The parameters to bind to the query.
     * @return array
     * @throws Exception
     */
    public function fetchAll(string $sql, array $params = []): array
    {
        $stmt = $this->prepareExecute($sql, $params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Fetches a single row from a SQL query.
     *
     * @param string $sql The SQL query to execute.
     * @param array $params The parameters to bind to the query.
     * @return array|null
     * @throws Exception
     */
    public function fetch(string $sql, array $params = []): ?array
    {
        $stmt = $this->prepareExecute($sql, $params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Fetches a single column from a SQL query.
     *
     * @param string $sql The SQL query to execute.
     * @param array $params The parameters to bind to the query.
     * @return array
     * @throws Exception
     */
    public function fetchCol(string $sql, array $params = []): array
    {
        $stmt = $this->prepareExecute($sql, $params);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Executes a SQL statement and returns the number of affected rows.
     *
     * @param string $sql The SQL query to execute.
     * @param array $params The parameters to bind to the query.
     * @return int
     * @throws Exception
     */
    public function execute(string $sql, array $params = []): int
    {
        $stmt = $this->prepareExecute($sql, $params);
        return $stmt->rowCount();
    }

    /**
     * Returns the last inserted ID.
     *
     * @return int
     * @throws Exception
     */
    public function lastInsertId(): int
    {
        if ($this->pdo === null) {
            $this->connect();
        }
        return (int) $this->pdo->lastInsertId();
    }
}