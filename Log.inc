<?php
/**
 * Log
 *
 * PHP version 7
 */

/**
 * Required files
 *
 * Description of files required.
 *
 * Db.inc     Database access functions.
 */
require_once 'Db.inc';

/**
 * Error and normal records.
 */
class Log
{


    /**
     * Adds records to (login) failure log. Clears records older than 10 minutes.
     *
     * @param string $record        String to be logged.
     * @param string $ipaddr        User IP address.
     * @param string $attempteduser Username/email for which login was attempted.
     *
     * @return void
     */
    public function addFail(string $record, string $ipaddr, string $attempteduser=null)
    {
        $db = new Db();
        if ($db->makeQuery('DELETE FROM Faillog WHERE created < (NOW() - INTERVAL 10 MINUTE)') === false) {
            $this->addError('Failure log clear failed!', $ipaddr);
        }

        $query  = 'INSERT INTO Faillog(record, ipaddr, attempteduser) VALUES(?,?,?)';
        $params = [
                   [
                    $record,
                    $ipaddr,
                    $attempteduser,
                   ],
                  ];
        if ($db->executePreparedQuery($query, 'sss', $params) === false) {
            $this->addError('Failure log insert failure. Message was:'.$record, $ipaddr);
        }

    }//end addFail()


    /**
     * Checks if 3 or more failures are prsent from same IP address or for same user.
     *
     * @param string $ipaddr        User IP address.
     * @param string $attempteduser Username/email for which login was attempted.
     *
     * @return boolean
     */
    public function failurePresent(string $ipaddr, string $attempteduser=null)
    {
        $db = new Db();
        if ($db->makeQuery('DELETE FROM Faillog WHERE created < (NOW() - INTERVAL 10 MINUTE)') === false) {
            $this->addError('Failure log clear failed!', $ipaddr);
        }

        $query  = 'SELECT * FROM Faillog WHERE ipaddr=? OR attempteduser=?';
        $params = [
                   [
                    $ipaddr,
                    $attempteduser,
                   ],
                  ];
        $result = $db->executePreparedQuery($query, 'ss', $params);
        if ($result === false) {
            $this->addError('Failure log check failure.', $ipaddr);
            return false;
        } else {
            if (count($result[0]) >= 3) {
                return true;
            }

            return false;
        }

    }//end failurePresent()


    /**
     * Adds records to error log.
     *
     * @param string  $record  String to be logged.
     * @param string  $ipaddr  User IP address.
     * @param boolean $display Display error to user if true.
     *
     * @return void
     */
    public function addError(string $record, string $ipaddr=null, bool $display=false)
    {
        $db       = new Db();
        $username = null;
        if ($display === true) {
            if (isset($_SESSION) === false) {
                session_start();
            }

            $_SESSION['error'] = $record;
            if ((isset($_SESSION) === true) && (isset($_SESSION['username']) === true)) {
                $username = $_SESSION['username'];
            }
        }

        $query  = 'INSERT INTO Errorlog(record, ipaddr, username) VALUES(?,?,?)';
        $params = [
                   [
                    $record,
                    $ipaddr,
                    $username,
                   ],
                  ];
        if ($db->executePreparedQuery($query, 'sss', $params) === false) {
            trigger_error('Error log insert failure. Message was:'.$record, E_USER_WARNING);
        }

    }//end addError()


    /**
     * Adds records to error log.
     *
     * @param mixed  $oldrecord Old value.
     * @param mixed  $newrecord New value.
     * @param string $formid    Form id.
     * @param string $field     Field id.
     *
     * @return void
     */
    public function addUpdate($oldrecord, $newrecord, string $formid, string $field)
    {
        $db = new Db();
        if ((isset($_SESSION) === true) && (isset($_SESSION['username']) === true)) {
            $username = $_SESSION['username'];
        } else {
            $username = null;
        }

        $query  = 'INSERT INTO Updatelog(oldrecord, newrecord, formid, field, username, sent) VALUES(?,?,?,?,?,0)';
        $params = [
                   [
                    $oldrecord,
                    $newrecord,
                    $formid,
                    $field,
                    $username,
                   ],
                  ];
        if ($db->executePreparedQuery($query, 'sssss', $params) === false) {
            $this->addError('Update log insert failure. Oldrecord:'.$oldrecord.' Newrecord:'.$newrecord.' Formid:'.$newrecord.'Field:'.$field);
        }

    }//end addUpdate()


}//end class
