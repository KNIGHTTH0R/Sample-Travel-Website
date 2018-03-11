<?php

/**
 * New form
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
$db     = new Db();
$ipAddr = $helper->getIpAddress();

if ($helper->isLoggedIn() === false) {
    $log->addError('Login first!', $ipAddr, true);
    header('Location: home.php');
    exit;
}

if (isset($_POST['revform']) === false) {
    header('Location: home.php');
    exit;
}

if (mb_strlen($_POST['revform'][0]) > 50) {
    $log->addError('Review for field too long', $ipAddr, true);
    header('Location review.php');
    exit;
}

if (mb_strlen($_POST['revform'][1]) > 500) {
    $log->addError('Review too long', $ipAddr, true);
    header('Location review.php');
    exit;
}

$query = 'INSERT INTO Review(revfor,revtext) VALUES(?,?)';
$db->executePreparedQuery($query, 'ss', [[$_POST['revform'][0], $_POST['revform'][1]]]);

$emailBody  = '<b>Review for:</b> '.$_POST['revform'][0];
$emailBody .= '<br><b>Review text:</b> '.$_POST['revform'][1];

$emailOptions = [];

$emailOptions['toEmail'] = ['kaustubhkhavnekar@gmail.com'];
$emailOptions['Subject'] = 'New review';
$emailOptions['ReplyTo'] = $_SESSION['email'];
$emailOptions['Body']    = $emailBody;

// echo $emailBody;
require_once 'Mailer.inc';
$mailer = new Mailer();
$mailer->sendHTMLMail($emailOptions);

header('Location: home.php');
exit;
