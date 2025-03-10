<?php namespace mcech\sql;

use PDOStatement;
use PDOException;

/**
 * A table of data  representing a database result,  which is  usually generated
 * by executing a statement that queries the database.
 *
 * A Result  object  maintains  a cursor  pointing to  its current  row of data.
 * Initially  the cursor is  positioned before the first row.  The next() method
 * moves the cursor to the next row, and because it returns false when there are
 * no more rows in the Result object,  it can be used in a while loop to iterate
 * through the result set.
 */
class Result {
    /**
     * Get the column names.
     *
     * @return array         An array containing the suggested column names
     *
     * @throws PDOException If a database access error occurs
     */
    public function getColumnNames(): array {
        $result = array();
        for ($i = 0; $i < $this->stmt->columnCount(); ++$i) {
            array_push($result, $this->stmt->getColumnMeta($i)['name']);
        }
        return $result;
    }

    /**
     * Moves the  cursor forward  one row from  its current  position.  A Result
     * cursor is initially  positioned before  the first row;  the first call to
     * the method  next makes the  first row the  current row;  the second  call
     * makes the second row the current row, and so on.
     *
     * @return bool         true    If the new current row is valid;
     *                      false   If there are no more rows
     *
     * @throws PDOException If a database access error occurs
     */
    public function next(): bool {
        $this->row = $this->stmt->fetch();
        return $this->row !== false;
    }

    /**
     * Gets the value of the designated column in the current row of this Result
     * object.
     *
     * @param  int|string   $column The first column is 0, the second is 1, ...
     *                              or
     *                              The label for the column  specified with the
     *                              SQL AS clause.  If the SQL AS clause was not
     *                              specified, then the label is the name of the
     *                              column.
     *
     * @return mixed        An Object holding the column value
     *
     * @throws PDOException If a database access error occurs
     */
    public function get(int|string $column): mixed {
        return $this->row[$column];
    }

    function __construct(PDOStatement $stmt) {
        $this->stmt = $stmt;
    }

    private PDOStatement $stmt;
    private $row;
}
?>
