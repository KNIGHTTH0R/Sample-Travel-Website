<?php

/**
 * Login
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

if ($log->failurePresent($ipAddr, $_POST['useremail']) === true) {
    $log->addError('Too many failures, try again in 10 minutes', $ipAddr, true);
    $log->addFail('Too many failures', $ipAddr, $_POST['useremail']);
    header('location: login.php');
    exit;
}

if ((isset($_POST['useremail']) === true) && (isset($_POST['password']) === true)) {
    checkLogin($_POST['useremail'], $_POST['password']);
    header('location: login.php');
    exit;
} else {
    $log->addError('Malformed login query', $ipAddr, true);
    header('location: login.php');
    exit;
}


function checkLogin(string $useremail, string $password)
{
    $log    = new Log();
    $check  = new Check();
    $db     = new Db();
    $ipAddr = $GLOBALS['ipAddr'];
    $row    = null;
    if (strpos($useremail, '@') === false) {
        if (strlen($useremail) > 20 || $check->isAlphanumeric($useremail, '_') === false) {
            $log->addError('Username/Email and password combination invalid', $ipAddr, true);
            $log->addFail('Username validation failed', $ipAddr);
            header('location: login.php');
            exit;
        }

        $result = $db->executePreparedQuery('SELECT * FROM Employee WHERE username=?', 's', [[$useremail]]);
        if (($result === false) || (count($result[0]) === 0)) {
            $log->addError('Username/Email and password combination invalid', $ipAddr, true);
            $log->addFail('No account with this username', $ipAddr);
            header('location: login.php');
            exit;
        } else {
            $row = $result[0][0];
        }
    } else {
        if (filter_var($useremail, FILTER_VALIDATE_EMAIL) === false) {
            $log->addError('Username/Email and password combination invalid', $ipAddr, true);
            $log->addFail('Email validation failed', $ipAddr);
            header('location: login.php');
            exit;
        }

        $result = $db->executePreparedQuery('SELECT * FROM Employee WHERE email=?', 's', [[$useremail]]);
        if (($result === false) || (count($result[0]) === 0)) {
            $log->addError('Username/Email and password combination invalid', $ipAddr, true);
            $log->addFail('No account with this username', $ipAddr);
            header('location: login.php');
            exit;
        } else {
            $row = $result[0][0];
        }
    }//end if
    if (password_verify($password, $row['password']) === true) {
        $_SESSION['username'] = $row['username'];
        $_SESSION['compkey']  = $row['compkey'];
        $_SESSION['email']    = $row['email'];
        if (isset($_SESSION['book']) === true) {
            header('location: manageforms.php');
            exit;
        } else {
            header('location: home.php');
            exit;
        }
    } else {
        $log->addError('Username/Email and password combination invalid', $ipAddr, true);
        $log->addFail('Password invalid', $ipAddr);
        header('location: login.php');
        exit;
    }

}//end checkLogin()
