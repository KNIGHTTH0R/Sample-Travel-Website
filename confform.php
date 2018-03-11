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

$formid = $_POST['formid'];
if (mb_strlen($formid) !== 10) {
    header('Location: home.php');
    exit;
}

if ((isset($_POST['confform']) === false) || ($helper->isLoggedIn() === false)) {
    header('Location: home.php');
    exit;
}

$result = $db->executePreparedQuery('SELECT * FROM Form WHERE compkey=? AND formid=?', 'ss', [[$_SESSION['compkey'], $formid]]);

if (($result === false) || (count($result[0]) === 0)) {
    header('Location: home.php');
    exit;
}

if ($result[0][0]['conf'] === 'No') {
    header('Location: home.php');
    exit;
}


basicValidation($_POST['confform']);
if (($_POST['confform'][16] === 'yes') && (isset($_POST['meal']))) {
    basicMealValidation($_POST['meal']);
}

$qry    = "SELECT * FROM Conference WHERE formid='$formid'";
$result = $db->makeQuery($qry);
$row1   = mysqli_fetch_assoc($result);

$qry    = "SELECT * FROM Meal WHERE formid='$formid'";
$result = $db->makeQuery($qry);
$row2   = mysqli_fetch_assoc($result);

$row = array_merge($row1, $row2);

$strfields = [
              0  => 'conftype',
              2  => 'fromdate1',
              3  => 'todate1',
              4  => 'fromdate2',
              5  => 'todate2',
              6  => 'fromdate3',
              7  => 'todate3',
              10 => 'fromtime',
              11 => 'totime',
              12 => 'seatarrang',
              13 => 'stagereq',
              14 => 'fromstage',
              15 => 'tostage',
              16 => 'mealreq',
              17 => 'custom',
              32 => 'location',
              38 => 'addinfo',
              39 => 'hotel1',
              40 => 'hotel2',
              41 => 'hotel3',
             ];
$intfields = [
              1 => 'maxnum',
              8 => 'frombudget',
              9 => 'tobudget',
             ];

$typestring = '';
$qstring    = [];
$params     = [];
foreach ($strfields as $key => $value) {
    if ($row[$value] !== $_POST['confform'][$key]) {
        $log->addUpdate($row[$value], $_POST['confform'][$key], $formid, $value);
        $typestring .= 's';
        array_push($qstring, "$value=?");
        array_push($params, $_POST['confform'][$key]);
    }
}

foreach ($intfields as $key => $value) {
    if ($row[$value] !== $_POST['confform'][$key]) {
        $log->addUpdate($row[$value], $_POST['confform'][$key], $formid, $value);
        $typestring .= 'i';
        array_push($qstring, "$value=?");
        array_push($params, (int) $_POST['confform'][$key]);
    }
}

if ($typestring !== '') {
    $query = 'UPDATE Conference SET '.implode(',', $qstring)." WHERE formid='$formid'";
    $db->executePreparedQuery($query, $typestring, [$params]);
}

if (($_POST['confform'][16] === 'yes') && (isset($_POST['meal']))) {
    // $strfields  = [
    //                17 => 'breakreq',
    //                18 => 'breaktime',
    //                20 => 'lunchreq',
    //                21 => 'lunchtime',
    //                23 => 'dinreq',
    //                24 => 'dintime',
    //                26 => 'snackreq',
    //                27 => 'snacktime',
    //                29 => 'liqreq',
    //                30 => 'liqtime',
    //               ];
    $intfields = [
                   // 19 => 'breakdur',
                   // 22 => 'lunchdur',
                   // 25 => 'dindur',
                   // 28 => 'snackdur',
                   // 31 => 'liqdur',
                  0 => 'breakdays',
                  1 => 'lunchdays',
                  2 => 'dindays',
                  3 => 'teadays',
                  4 => 'snackdays',
                  5 => 'liqdays',
                 ];
    $typestring = '';
    $qstring    = [];
    $params     = [];
    // foreach ($strfields as $key => $value) {
    //     if ($row[$value] !== $_POST['meal'][$key]) {
    //         $log->addUpdate($row[$value], $_POST['meal'][$key], $formid, $value);
    //         $typestring .= 's';
    //         array_push($qstring, "$value=?");
    //         array_push($params, $_POST['meal'][$key]);
    //     }
    // }

    foreach ($intfields as $key => $value) {
        if ($row[$value] !== $_POST['meal'][$key]) {
            $log->addUpdate($row[$value], $_POST['meal'][$key], $formid, $value);
            $typestring .= 'i';
            array_push($qstring, "$value=?");
            array_push($params, (int) $_POST['meal'][$key]);
        }
    }

    if ($typestring !== '') {
        $query = 'UPDATE Meal SET '.implode(',', $qstring)." WHERE formid='$formid'";
        $db->executePreparedQuery($query, $typestring, [$params]);
    }
}//end if

header('Location: conference.php?id='.$formid);
exit;


function convertToInt(array $checkboxes)
{
    $a = 0;
    for ($i = 0; $i < 30; $i++) {
        if (isset($checkboxes[$i]) === true) {
            $a = ($a | (1 << $i));
        }
    }

    return $a;

}//end convertToInt()


/**
 * Performs basic validation
 *
 * @param array $formarray Conference POST array.
 *
 * @return void.
 */
function basicValidation(array &$formarray)
{
    $check = new Check();
    if (isset($formarray[0]) === false || ($formarray[0] !== 'res' && $formarray[0] !== 'nonres')) {
        $formarray[0] = null;
    }

    if (mb_strlen($formarray[32]) === 0) {
        $formarray[32] = null;
    } else if (mb_strlen($formarray[32]) > 100) {
        $formarray[32] = mb_substr($formarray[32], 0, 100);
    }

    if ((mb_strlen($formarray[1]) > 5) || ($check->isNonZeroInt($formarray[1]) === false)) {
        $formarray[1] = null;
    }

    if ($check->isValidDate($formarray[2], $formarray[3]) === false) {
        $formarray[2] = null;
        $formarray[3] = null;
    }

    if ($check->isValidDate($formarray[4], $formarray[5]) === false) {
        $formarray[4] = null;
        $formarray[5] = null;
    }

    if ($check->isValidDate($formarray[6], $formarray[7]) === false) {
        $formarray[6] = null;
        $formarray[7] = null;
    }

    if ((mb_strlen($formarray[8]) > 9)
        || (mb_strlen($formarray[9]) > 9)
        || ($check->isNonZeroInt($formarray[8]) === false)
        || ($check->isNonZeroInt($formarray[9]) === false)
        || ($formarray[8] > $formarray[9])
    ) {
        $formarray[8] = null;
        $formarray[9] = null;
    }

    if (($check->isValidTime($formarray[10]) === false) || ($check->isValidTime($formarray[11]) === false)) {
        $formarray[10] = null;
        $formarray[11] = null;
    }

    $seatTypes = [
                  'Cluster',
                  'Theatre',
                  'U-shaped',
                  'Mixed',
                  'Cluster+Theatre',
                  'Custom',
                 ];

    if (in_array($formarray[12], $seatTypes) === false) {
        $formarray[12] = null;
    }

    if (($formarray[12] !== 'Custom')
        || (isset($formarray[17]) === false) || (mb_strlen($formarray[17]) === 0) || (mb_strlen($formarray[17]) > 100)
    ) {
        $formarray[17] = null;
    }

    if (isset($formarray[13]) === false || ($formarray[13] !== 'yes' && $formarray[13] !== 'no')) {
        $formarray[13] = null;
    }

    if ($formarray[13] === 'yes') {
        if (((mb_strlen($formarray[14]) > 6)
            || (mb_strlen($formarray[15]) > 6)
            || ($check->isNonZeroInt($formarray[14]) === false)
            || ($check->isNonZeroInt($formarray[15]) === false))
        ) {
            $formarray[14] = null;
            $formarray[15] = null;
        }
    } else {
        $formarray[14] = null;
        $formarray[15] = null;
    }

    if (isset($formarray[16]) === false || ($formarray[16] !== 'yes' && $formarray[16] !== 'no')) {
        $formarray[16] = null;
    }

    if ((isset($formarray[38]) === false) || (mb_strlen($formarray[38]) === 0) || (mb_strlen($formarray[38]) > 500)) {
        $formarray[38] = null;
    }

    if ((isset($formarray[39]) === false) || (mb_strlen($formarray[39]) === 0) || (mb_strlen($formarray[39]) > 50)) {
        $formarray[39] = null;
    }

    if ((isset($formarray[40]) === false) || (mb_strlen($formarray[40]) === 0) || (mb_strlen($formarray[40]) > 50)) {
        $formarray[40] = null;
    }

    if ((isset($formarray[41]) === false) || (mb_strlen($formarray[41]) === 0) || (mb_strlen($formarray[41]) > 50)) {
        $formarray[41] = null;
    }

}//end basicValidation()


/**
 * Performs basic meal array validation
 *
 * @param array $formarray Meal POST array.
 *
 * @return void.
 */
function basicMealValidation(array &$formarray)
{
    if (isset($formarray[0]) === true) {
        $formarray[0] = convertToInt($formarray[0]);
    } else {
        $formarray[0] = null;
    }

    if (isset($formarray[1]) === true) {
        $formarray[1] = convertToInt($formarray[1]);
    } else {
        $formarray[1] = null;
    }

    if (isset($formarray[2]) === true) {
        $formarray[2] = convertToInt($formarray[2]);
    } else {
        $formarray[2] = null;
    }

    if (isset($formarray[3]) === true) {
        $formarray[3] = convertToInt($formarray[3]);
    } else {
        $formarray[3] = null;
    }

    if (isset($formarray[4]) === true) {
        $formarray[4] = convertToInt($formarray[4]);
    } else {
        $formarray[4] = null;
    }

    if (isset($formarray[5]) === true) {
        $formarray[5] = convertToInt($formarray[5]);
    } else {
        $formarray[5] = null;
    }

}//end basicMealValidation()
