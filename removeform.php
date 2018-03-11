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
    header('Location: login.php');
    exit;
}

if (isset($_GET['id']) === false) {
    $log->addError('Invalid request', $ipAddr, true);
    header('Location: manageforms.php');
    exit;
}

$formid = $_GET['id'];
if (mb_strlen($formid) !== 10) {
    $log->addError('Invalid request', $ipAddr, true);
    header('Location: manageforms.php');
    exit;
}



$result = $db->executePreparedQuery('SELECT * FROM Form WHERE compkey=? AND formid=?', 'ss', [[$_SESSION['compkey'], $formid]]);

if (($result === false) || (count($result[0]) === 0)) {
    header('Location: manageforms.php');
    exit;
}

if ($db->makeQuery("DELETE FROM Conference WHERE formid='$formid'") === false) {
    $log->addError('Conference delete query error', $ipAddr, false);
    exit;
}

if ($db->makeQuery("DELETE FROM Meal WHERE formid='$formid'") === false) {
    $log->addError('Meal delete query error', $ipAddr, false);
    exit;
}

if ($db->makeQuery("DELETE FROM Stay WHERE formid='$formid'") === false) {
    $log->addError('Stay delete query error', $ipAddr, false);
    exit;
}

if ($db->makeQuery("DELETE FROM Room WHERE formid='$formid'") === false) {
    $log->addError('Room delete query error', $ipAddr, false);
    exit;
}

if ($db->makeQuery("DELETE FROM Car WHERE formid='$formid'") === false) {
    $log->addError('Car delete query error', $ipAddr, false);
    exit;
}

if ($db->makeQuery("DELETE FROM Carbooking WHERE formid='$formid'") === false) {
    $log->addError('Carbooking delete query error', $ipAddr, false);
    exit;
}

if ($db->makeQuery("DELETE FROM Form WHERE formid='$formid'") === false) {
    $log->addError('Form delete query error', $ipAddr, false);
    exit;
}

header('Location: manageforms.php');
exit;
