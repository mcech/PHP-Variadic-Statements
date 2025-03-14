<?php namespace mcech\sql;

require_once('Result.php');

use PDO;
use PDOStatement;
use PDOException;

/**
 * This class establishes a connection (session) to a specific database.
 * SQL statements are executed and results are returned  within the context of a
 * connection.
 *
 * It is  strongly recommended  to  explicitly  commit  or  roll back  an active
 * transaction prior to destruction of a session.
 */
class Database {
    /**
     * Attempts to establish a connection to the given database URL.
     *
     * @param  string       $url     A database url of the form
     *                                   protocol:host=hostname;dbname=database
     * @param  string       $user    The  database  user  on  whose  behalf  the
     *                               connection is being made
     * @param  string       $passwd  The users password
     *
     * @throws PDOException If a database access error occurs
     */
    public function __construct(string $url, string $user, string $passwd) {
        $this->con = new PDO($url, $user, $passwd);
        $this->con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /**
     * Initiates a transaction.
     *
     * @throws PDOException If a database access error occurs
     */
    public function begin() {
        if (!$this->con->beginTransaction()) {
            throw new PDOException();
        }
    }

    /**
     * Executes  the SQL query  and returns  the Result object  generated by the
     * query.
     *
     * @param  string       $sql     A  SQL statement  that  may contain  one or
     *                               more '?' parameter placeholders
     * @param  mixed...     $params  The objects containing  the input parameter
     *                               values
     *
     * @return Result       A Result object that contains  the data  produced by
     *                      the query
     *
     * @throws PDOException If a database access error occurs
     */
    public function executeQuery(string $sql, mixed... $params): Result {
        $stmt = $this->prepare($sql, $params);
        if (!$stmt->execute()) {
            throw new PDOException();
        }
        return new Result($stmt);
    }

    /**
     * Execute an SQL statement and returns the number of affected rows.
     *
     * @param  string       $sql     A  SQL statement  that  may contain  one or
     *                               more '?' parameter placeholders
     * @param  mixed...     $params  The objects containing  the input parameter
     *                               values
     *
     * @return int          Returns  the number  of rows that  were modified  or
     *                      deleted by the SQL statement.
     *
     * @throws PDOException If a database access error occurs
     */
    public function executeUpdate(string $sql, mixed ...$params): int {
        $stmt = $this->prepare($sql, $params);
        if (!$stmt->execute()) {
            throw new PDOException();
        }
        return $stmt->rowCount();
    }

    /**
     * Returns the ID of the last inserted row or sequence value.
     *
     * @return int          Returns the ID of the last inserted row, or the last
     *                      value  from  a sequence  object,  depending  on  the
     *                      underlying driver.
     *
     * @throws PDOException If a database access error occurs
     */
    public function lastInsertID(): int {
        return $this->con->lastInsertId();
    }

    /**
     * Commits a transaction
     *
     * @throws PDOException If a database access error occurs
     */
    public function commit() {
        if (!$this->con->commit()) {
            throw new PDOException();
        }
    }

    /**
     * Rolls back a transaction
     *
     * @throws PDOException If a database access error occurs
     */
    public function rollback() {
        if (!$this->con->rollBack()) {
            throw new PDOException();
        }
    }

    private function prepare(string $sql, array $params): PDOStatement {
        $stmt = $this->con->prepare($sql);
        for ($i = 0; $i < count($params); ++$i) {
            if (!$stmt->bindParam($i + 1, $params[$i])) {
                throw new PDOException();
            }
        }
        return $stmt;
    }

    private PDO $con;
}
?>
