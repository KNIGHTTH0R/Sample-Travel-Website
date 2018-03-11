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
 * Checks if specified field is unique.
 *
 * @param string $input   Regform input.
 * @param string $fieldno Regform number of field to be checked.
 *
 * @return boolean
 */
function isUniqueValidations(string $input, string $fieldno)
{
    $db = new Db();
    switch ($fieldno) {
        case '1':
        return $db->isUnique('Employee', 'username', 's', $input);

        case '2':
        return $db->isUnique('Employee', 'email', 's', $input);

        default:
            //http_response_code(400);
        return false;
    }//end switch

}//end isUniqueValidations()


if (isset($_POST) === true) {
    if ((isset($_POST['input']) === true) && (isset($_POST['field']) === true)) {
        if (isUniqueValidations($_POST['input'], $_POST['field']) === true) {
            echo '1';
        } else {
            echo '0';
        }
    }
}
