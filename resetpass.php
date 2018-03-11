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

if (isset($_POST['useremail']) === true) {
    if (checkUseremail($_POST['useremail']) === true) {
        $_SESSION['error'] = 'Check your email to reset password';
    } else {
        $_SESSION['error'] = 'Reset password failed';
    }

    header('location: login.php');
    exit;
} else {
    $log->addError('Malformed reset query', $ipAddr, true);
    header('location: login.php');
    exit;
}


function checkUseremail(string $useremail)
{
    $log    = new Log();
    $check  = new Check();
    $db     = new Db();
    $ipAddr = $GLOBALS['ipAddr'];
    $row    = null;
    if (strpos($useremail, '@') === false) {
        if (strlen($useremail) > 20 || $check->isAlphanumeric($useremail, '_') === false) {
            $log->addError('Invalid username', $ipAddr, true);
            header('location: login.php');
            exit;
        }

        $result = $db->executePreparedQuery('SELECT * FROM Employee WHERE username=?', 's', [[$useremail]]);
        if (($result === false) || (count($result[0]) === 0)) {
            $log->addError('Invalid username', $ipAddr, true);
            header('location: login.php');
            exit;
        } else {
            $row = $result[0][0];
        }
    } else {
        if (filter_var($useremail, FILTER_VALIDATE_EMAIL) === false) {
            $log->addError('Invalid email', $ipAddr, true);
            header('location: login.php');
            exit;
        }

        $result = $db->executePreparedQuery('SELECT * FROM Employee WHERE email=?', 's', [[$useremail]]);
        if (($result === false) || (count($result[0]) === 0)) {
            $log->addError('Invalid email', $ipAddr, true);
            header('location: login.php');
            exit;
        } else {
            $row = $result[0][0];
        }
    }//end if

    if ($db->makeQuery('DELETE FROM Forgotlog WHERE created < DATE_SUB(NOW(), INTERVAL 1 DAY)') === false) {
        $this->addError('Forgotlog log clear failed!', $ipaddr);
    }

    $username = $row['username'];
    $email    = $row['email'];

    if ($db->isUnique('Forgotlog', 'username', 's', $username) === false) {
        $log->addError('Only one reset email can be sent every 24 hours', $ipAddr, true);
        return false;
    }

    $randhelp  = new Helper();
    $keystring = $randhelp->randomString(16);
    while ($db->isUnique('Forgotlog', 'keystring', 's', $keystring) === false) {
        $keystring = $randhelp->randomString(16);
    }

    //Send email
    $emailOptions = [];

    $emailOptions['toEmail'] = [$email];
    $emailOptions['Subject'] = 'Password reset request for username '.$username;
    $emailOptions['Body']    = 'Click on the following link to reset password:<br> <a href="https://kaustubhk.com/tj/resetpass?id='.$keystring.'">Click here</a>';
    $emailOptions['AltBody'] = 'Copy the following link to reset password: https://kaustubhk.com/tj/reset?id='.$keystring;

    include_once 'Mailer.inc';
    $mailer = new Mailer();
    $mailer->sendHTMLMail($emailOptions);
    //If successful
    $db->executePreparedQuery('INSERT INTO Forgotlog(username, email, keystring) VALUES(?,?,?)', 'sss', [[$username, $email, $keystring]]);
    return true;

}//end checkUseremail()
