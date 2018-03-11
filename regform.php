<?php

/**
 * Registration
 *
 * PHP version 7
 */

/**
 * Required files
 *
 * Description of files required.
 *
 * Helper.inc Utility functions.
 * Log.inc    Error and normal records.
 * Check.inc  Form validation utility functions.
 * Db.inc     Database access functions.
 */
if (isset($_SESSION) === false) {
    session_start();
}

header('Content-Type: text/html; charset=utf-8');

require_once 'Helper.inc';
require_once 'Log.inc';
require_once 'Check.inc';
require_once 'Db.inc';

$helper = new Helper();
$log    = new Log();
$ipAddr = $helper->getIpAddress();

if ($helper->isLoggedIn() === true) {
    $log->addError('Already logged in, logout first!', $ipAddr, true);
    header('Location: home.php');
    exit;
}

if (isset($_POST['regform']) === false) {
    $log->addError('Invalid registration request', $ipAddr, true);
    header('Location: register.php');
    exit;
}

if (basicValidation($_POST['regform']) === true && isUniqueValidations($_POST['regform']) === true) {
    if ($_POST['regform'][5] === 'no') {
        $key = registerCompany($_POST['regform']);
        if ($key !== false) {
            if (registerUser($_POST['regform'], $key) === false) {
                $log->addError('User registration failed', $ipAddr);
                header('Location: register.php');
                exit;
            } else {
                header('Location: home.php');
                exit;
            }
        } else {
            $log->addError('Company registration failed', $ipAddr);
            header('Location: register.php');
            exit;
        }
    } else {
        if (registerUser($_POST['regform']) === false) {
            $log->addError('User registration failed', $ipAddr);
            header('Location: register.php');
            exit;
        } else {
            header('Location: home.php');
            exit;
        }
    }//end if
} else {
    header('Location: register.php');
    exit;
}//end if


/**
 * Performs basic validation
 *
 * @param array $formarray Regform POST array.
 *
 * @return boolean
 */
function basicValidation(array $formarray)
{
    $log    = new Log();
    $check  = new Check();
    $ipAddr = $GLOBALS['ipAddr'];
    if ((mb_strlen($formarray[0]) > 50)) {
        $log->addError('Invalid name', $ipAddr, true);
        return false;
    }

    if ((mb_strlen($formarray[1]) > 20 || $check->isAlphanumeric($formarray[1], '_')) === false) {
        $log->addError('Invalid username', $ipAddr, true);
        return false;
    }

    if (filter_var($formarray[2], FILTER_VALIDATE_EMAIL) === false) {
        $log->addError('Invalid email', $ipAddr, true);
        return false;
    }

    if ((mb_strlen($formarray[3]) > 20 || $check->isPhoneNumber($formarray[3])) === false) {
        $log->addError('Invalid phoneno', $ipAddr, true);
        return false;
    }

    if (mb_strlen($formarray[4]) < 8) {
        $log->addError('Invalid password', $ipAddr, true);
        return false;
    }

    if (isset($formarray[5]) === false) {
        $log->addError('Company already registered or not?', $ipAddr, true);
        return false;
    }

    if ($formarray[5] === 'yes') {
        if ((mb_strlen($formarray[6]) < 8)) {
            return true;
        }

        $log->addError('Invalid company key', $ipAddr, true);
        return false;
    } else if ($formarray[5] === 'no') {
        if (mb_strlen($formarray[7]) > 50) {
            $log->addError('Invalid company name', $ipAddr, true);
            return false;
        }

        if (mb_strlen($formarray[8]) > 300) {
            $log->addError('Invalid company address', $ipAddr, true);
            return false;
        }

        if (mb_strlen($formarray[9]) > 50) {
            $log->addError('Invalid company industry', $ipAddr, true);
            return false;
        }

        if (mb_strlen($formarray[10]) > 8) {
            $log->addError('Invalid company key', $ipAddr, true);
            return false;
        }

        return true;
    } else {
        $log->addError('Invalid choice', $ipAddr, true);
        return false;
    }//end if

}//end basicValidation()


/**
 * Checks if fields are unique.
 *
 * @param array $formarray Regform POST array.
 *
 * @return boolean
 */
function isUniqueValidations(array $formarray)
{
    $log    = new Log();
    $db     = new Db();
    $ipAddr = $GLOBALS['ipAddr'];
    if ($db->isUnique('Employee', 'username', 's', $formarray[1]) === false) {
        $log->addError('Username taken', $ipAddr, true);
        return false;
    }

    if ($db->isUnique('Employee', 'email', 's', $formarray[2]) === false) {
        $log->addError('Email already registered', $ipAddr, true);
        return false;
    }

    if ($formarray[5] === 'no') {
        if ($db->isUnique('Company', 'compkey', 's', $formarray[10]) === false) {
            $log->addError('Company key already exists', $ipAddr, true);
            return false;
        }
    } else if ($db->isUnique('Company', 'compkey', 's', $formarray[6]) === true) {
        $log->addError('Company key doesn\'t exist', $ipAddr, true);
        return false;
    }

    return true;

}//end isUniqueValidations()


function registerCompany(array $formarray)
{
    $log    = new Log();
    $helper = new Helper();
    $db     = new Db();
    $params = [
               [
                $formarray[10],
                $formarray[7],
                $formarray[8],
                $formarray[9],
               ],
              ];
    $query  = 'INSERT INTO Company(compkey,name,address,industry) VALUES(?,?,?,?)';
    if ($db->executePreparedQuery($query, 'ssss', $params) === false) {
        return false;
    }

    return $formarray[10];

}//end registerCompany()


function registerUser(array $formarray, string $key=null)
{
    $db = new Db();
    if ($key === null) {
        $key = $formarray[6];
    }

    $password = password_hash($formarray[4], PASSWORD_DEFAULT, ['cost' => 9]);
    $params   = [
                 [
                  $formarray[0],
                  $formarray[1],
                  $formarray[2],
                  $formarray[3],
                  $password,
                  $key,
                 ],
                ];
    $query    = 'INSERT INTO Employee(name, username, email, contactnum, password, compkey) VALUES(?,?,?,?,?,?)';
    if ($db->executePreparedQuery($query, 'ssssss', $params) === false) {
        return false;
    }

    return true;

}//end registerUser()
