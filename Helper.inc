<?php

/**
 * Utility functions.
 */
class Helper
{


    /**
     * Checks if user is logged in.
     *
     * @return boolean
     */
    public function isLoggedIn()
    {
        return ((isset($_SESSION) === true) && (isset($_SESSION['username']) === true));

    }//end isLoggedIn()


    /**
     * Returns IP address from page is being viewed.
     *
     * @return string
     */
    public function getIpAddress()
    {
        return $_SERVER['REMOTE_ADDR'];

    }//end getIpAddress()


    /**
     * Returns a random alphanumeric string [a-zA-Z0-9] of specified length.
     *
     * @param integer $length Length of random string.
     *
     * @return string
     */
    public function randomString(int $length)
    {
        $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $str      = '';
        $max      = 61;
        for ($i = 0; $i < $length; ++$i) {
            $str .= $keyspace[random_int(0, $max)];
        }

        return $str;

    }//end randomString()


    /**
     * Sets error string which will be displayed to user on next page.
     *
     * @param string $error Error message which will be displayed to user.
     *
     * @return void
     */
    public function setError(string $error)
    {
        if (isset($_SESSION) === false) {
            session_start();
        }

        $_SESSION['error'] = $error;

    }//end setError()


    /**
     * Retrives error string error string if set previously.
     *
     * @return string Error string.
     *         bool   False if error string not set.
     */
    public function getError()
    {
        if (isset($_SESSION) === false) {
            session_start();
        }

        if (isset($_SESSION['error']) === true) {
            $error = $_SESSION['error'];
            unset($_SESSION['error']);
            return $error;
        } else {
            return false;
        }

    }//end getError()


}//end class
