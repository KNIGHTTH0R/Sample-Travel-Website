<?php
/**
 * Database access functions.
 */
class Db
{

    /**
     * Connection variable.
     *
     * @var object $_conn
     */
    private $_conn = null;

    /**
     * Last prepared statement.
     *
     * @var object $_conn
     */
    private $_prep = null;

    const HOST = '';
    const USER = '';
    const PASS = '';
    const DB   = 'jourDB';


    /**
     * Creates a new connection to database.
     *
     * @return void
     * @throws Exception If connection fails.
     */
    private function _connect()
    {
        $this->_conn = mysqli_connect(self::HOST, self::USER, self::PASS, self::DB);
        if (!$this->_conn) {
            throw new Exception('Database connection failed');
        }

        mysqli_set_charset($this->_conn, 'utf8mb4');

    }//end _connect()


    /**
     * Disconnects from database.
     *
     * @return void
     */
    private function _disconnect()
    {
        if (mysqli_close($this->_conn) === true) {
            $this->conn = null;
        } else {
            $log->addError('Connection disconnect failure');
        }

    }//end _disconnect()


    /**
     * Prepare a query and execute it.
     *
     * @param string $stmt   Statement to be prepared.
     * @param string $type   Type string for prepared statement.
     * @param array  $params Two Dimensional array for parameters. Each row is for one statement to be executed.
     *
     * @return array $result Three Dimensional associative array for query result. One row for each statement executed.
     */
    public function executePreparedQuery(string $stmt, string $type, array $params)
    {
        $this->_connect();
        $this->_prep = mysqli_prepare($this->_conn, $stmt);
        if ($this->_prep === false) {
            $this->prep = null;
            $this->_disconnect();
            return false;
        }

        $noofparams = count($params[0]);
        $bindparams = array_fill(0, $noofparams, null);
        if (mysqli_stmt_bind_param($this->_prep, $type, ...$bindparams) === false) {
            $this->_disconnect();
            return false;
        }

        foreach ($params as $rowno => $row) {
            for ($i = 0; $i < $noofparams; $i++) {
                $bindparams[$i] = $row[$i];
            }

            $success = mysqli_execute($this->_prep);
            if ($success === false) {
                $this->_disconnect();
                return false;
            } else {
                $result[$rowno] = $this->_fetchAssocStatement($this->_prep);
            }
        }

        $this->_disconnect();
        return $result;

    }//end executePreparedQuery()


    /**
     * Converts result of prepared statement to array.
     *
     * @param object $stmt Prepared statement.
     *
     * @return array $result Two Dimensional associative array for query result.
     */
    private function _fetchAssocStatement($stmt)
    {
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $allresult = [];
            $result    = [];
            $md        = $stmt->result_metadata();
            $params    = [];
            while ($field = $md->fetch_field()) {
                $params[] = &$result[$field->name];
            }

            call_user_func_array([$stmt, 'bind_result'], $params);
            $i = 0;
            while ($stmt->fetch()) {
                $allresult[$i++] = $result;
            }

            return $allresult;
        }

        return null;

    }//end _fetchAssocStatement()


    /**
     * Checks if given value is already exists in table.
     *
     * @param string $tablename Statement to be prepared.
     * @param string $fieldname Type string for prepared statement.
     * @param string $type      Type string for prepared statement.
     * @param mixed  $value     Value to be checked.
     *
     * @return boolean true  If value exists.
     *                 false If value does not exist.
     */
    public function isUnique(string $tablename, string $fieldname, string $type, $value)
    {
        $result = $this->executePreparedQuery('SELECT * FROM '.$tablename.' WHERE '.$fieldname.'=?', $type, [[$value]]);
        if (($result === false) || (count($result[0]) > 0)) {
            return false;
        }

        return true;

    }//end isUnique()


    /**
     * Execute query.
     *
     * @param string $qry Query.
     *
     * @return object  Query result if successful.
     *         boolean False If unsuccessful.
     */
    public function makeQuery(string $qry)
    {
        $this->_connect();
        $result = mysqli_query($this->_conn, $qry);
        $this->_disconnect();
        return $result;

    }//end makeQuery()


}//end class
