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
$ipAddr = $helper->getIpAddress();

if ($helper->isLoggedIn() === false) {
    $log->addError('Login first!', $ipAddr, true);
    header('Location: home.php');
    exit;
}

if (isset($_POST['newform']) === false) {
    $log->addError('Invalid request', $ipAddr, true);
    header('Location: manageforms.php#newform');
    exit;
}

if (basicValidation($_POST['newform']) === true) {
    $formid = registerForm($_POST['newform']);
    if ($formid !== false) {
        header('Location: manageforms.php');
        exit;
    } else {
        header('Location: manageforms.php#newform');
        exit;
    }
} else {
    header('Location: manageforms.php#newform');
    exit;
}//end if


/**
 * Performs basic validation
 *
 * @param array $formarray Newform POST array.
 *
 * @return boolean
 */
function basicValidation(array $formarray)
{
    $log    = new Log();
    $ipAddr = $GLOBALS['ipAddr'];
    if ((mb_strlen($formarray[0]) > 50)) {
        $log->addError('Invalid form name', $ipAddr, true);
        return false;
    }

    for ($i = 1; $i <= 3; $i++) {
        if (($formarray[$i] !== 'Yes') && ($formarray[$i] !== 'No')) {
            return false;
        }
    }

    return true;

}//end basicValidation()


function registerForm(array $formarray)
{
    $log    = new Log();
    $helper = new Helper();
    $db     = new Db();

    $compkey = $_SESSION['compkey'];
    $formid  = null;
    $exists  = false;
    do {
        $formid = $helper->randomString(10);
        if ($db->isUnique('Form', 'formid', 's', $formid) === false) {
            $exists = true;
        }
    } while ($exists === true);

    if ($formarray[1] === 'Yes') {
        $qry = "INSERT INTO Conference(formid,compkey) VALUES('".$formid."','".$compkey."')";
        if ($db->makeQuery($qry) === false) {
            $log->addError('Conference form creation failed', $ipAddr);
            return false;
        }

        $qry = "INSERT INTO Meal(formid,compkey) VALUES('".$formid."','".$compkey."')";
        if ($db->makeQuery($qry) === false) {
            $log->addError('Meal form creation failed', $ipAddr);
            return false;
        }
    }

    if ($formarray[2] === 'Yes') {
        $qry = "INSERT INTO Stay(formid,compkey,roomnum) VALUES('".$formid."','".$compkey."',1)";
        if ($db->makeQuery($qry) === false) {
            $log->addError('Stay form creation failed', $ipAddr);
            return false;
        }

        $qry = "INSERT INTO Room(formid,roomno) VALUES('$formid',0)";
        if ($db->makeQuery($qry) === false) {
            $log->addError('Stay form creation failed', $ipAddr);
            return false;
        }
    }

    if ($formarray[3] === 'Yes') {
        $qry = "INSERT INTO Car(formid,compkey,carnum) VALUES('".$formid."','".$compkey."',1)";
        if ($db->makeQuery($qry) === false) {
            $log->addError('Car form creation failed', $ipAddr);
            return false;
        }

        $qry = "INSERT INTO Carbooking(formid,carno) VALUES('$formid',0)";
        if ($db->makeQuery($qry) === false) {
            $log->addError('Car form creation failed', $ipAddr);
            return false;
        }
    }

    $params = [
               [
                $formid,
                $formarray[0],
                $compkey,
                $formarray[1],
                $formarray[2],
                $formarray[3],
               ],
              ];
    $query  = 'INSERT INTO Form(formid,formname,compkey,conf,stay,car) VALUES(?,?,?,?,?,?)';
    if ($db->executePreparedQuery($query, 'ssssss', $params) === false) {
        return false;
    }

    return $formid;

}//end registerForm()
