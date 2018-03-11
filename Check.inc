<?php

/**
 * Form validation utility functions.
 */
class Check
{


    /**
     * Checks if string is alphanumeric, with option for additional allowed characters.
     *
     * @param string $input      String to be checked.
     * @param string $additional Other characters allowed. Escape special regex characters.
     *
     * @return boolean
     */
    public function isAlphaNumeric(string $input, string $additional)
    {
        return (preg_match('/^[a-zA-Z0-9'.$additional.']+$/', $input) === 1);

    }//end isAlphaNumeric()


    /**
     * Checks if string is a valid phone number.
     *
     * @param string $input String to be checked.
     *
     * @return boolean
     */
    public function isPhoneNumber(string $input)
    {
        return (preg_match('/^((\+|00|0)?[1-9]{2}|0)?[1-9]( ?[0-9]){8,12}$/', $input) === 1);

    }//end isPhoneNumber()


    /**
     * Checks if string is a positive non-zero integer.
     *
     * @param string $input String to be checked.
     *
     * @return boolean
     */
    public function isNonZeroInt(string $input)
    {
        return (preg_match('/^[0-9]*[1-9][0-9]*$/', $input) === 1);

    }//end isNonZeroInt()


    /**
     * Checks if two strings repsenting date1 to date 2 (in dd-mm-yyyy format) are valid.
     *
     * @param string $date1 From date string to be checked.
     * @param string $date2 To date string to be checked.
     *
     * @return boolean
     */
    public function isValidDate(string $date1, string $date2)
    {
        if ((preg_match('/^[0-9]{1,2}-[0-9]{1,2}-[0-9]{4}$/', $date1) !== 1)
            || (preg_match('/^[0-9]{1,2}-[0-9]{1,2}-[0-9]{4}$/', $date2) !== 1)
        ) {
            return false;
        }

        $td1 = explode('-', $date1);
        $td2 = explode('-', $date2);
        if ((checkdate($td1[1], $td1[0], $td1[2]) === false)
            || (checkdate($td2[1], $td2[0], $td2[2]) === false)
        ) {
            return false;
        }

        return (strtotime($date1)) <= (strtotime($date2));

    }//end isValidDate()


    /**
     * Checks if time is valid.
     *
     * @param string $time Time string to be checked.
     *
     * @return boolean
     */
    public function isValidTime(string $time)
    {
        return (preg_match('/^([1-9]|(10|11|12)):(00|30) (AM|PM)$/', $time) === 1);

    }//end isValidTime()


}//end class
