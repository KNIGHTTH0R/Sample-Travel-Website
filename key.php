<?php
/**
 * Required files
 *
 * Description of files required.
 *
 * db.inc     Database access functions.
 */

require_once 'Db.inc';


/**
 * Checks if company.
 *
 * @param string $key Regform key entered.
 *
 * @return mixed string  Company name if success.
 *               boolean false if no company exists.
 */
function findCompany(string $key)
{
    $db     = new Db();
    $query  = 'SELECT name FROM Company where compkey=?';
    $result = $db->executePreparedQuery($query, 's', [[$key]]);
    if (($result === false) || (count($result[0]) === 0)) {
        return false;
    }

    $row = $result[0][0];
    return $row['name'];

}//end findCompany()


if ((isset($_POST) === true)
    && (isset($_POST['key']) === true)
    && (mb_strlen($_POST['key']) < 50)
) {
    $result = findCompany($_POST['key']);
    if ($result === false) {
        echo '0';
    } else {
        echo $result;
    }
} else {
    echo '0';
}
