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

if ((isset($_POST['stayform']) === false) || ($helper->isLoggedIn() === false)) {
    header('Location: home.php');
    exit;
}

$result = $db->executePreparedQuery('SELECT * FROM Form WHERE compkey=? AND formid=?', 'ss', [[$_SESSION['compkey'], $formid]]);

if (($result === false) || (count($result[0]) === 0)) {
    header('Location: home.php');
    exit;
}

if ($result[0][0]['stay'] === 'No') {
    header('Location: home.php');
    exit;
}

basicValidation($_POST['stayform']);

if ((mb_strlen($_POST['addinfo']) === 0) || (mb_strlen($_POST['addinfo']) > 500)) {
    $_POST['addinfo'] = null;
}

$qry    = "SELECT * FROM Stay WHERE formid='{$formid}'";
$result = $db->makeQuery($qry);
$row    = mysqli_fetch_assoc($result);

$qry    = "SELECT * FROM Room WHERE formid='{$formid}' ORDER BY roomno ASC";
$result = $db->makeQuery($qry);

if (($result !== false) && ($_POST['stayform'] !== null)) {
    $i      = 0;
    $j      = 0;
    $params = [];
    for (; $i < min(count($_POST['stayform']), $row['roomnum']); $i++) {
        $row2       = mysqli_fetch_assoc($result);
        $strfields  = [
                       0 => 'location',
                       1 => 'hotel1',
                       2 => 'hotel2',
                       3 => 'hotel3',
                       4 => 'checkin1',
                       5 => 'checkout1',
                      ];
        $intfields  = [
                       6  => 'frombudget',
                       7  => 'tobudget',
                       8  => 'singlenum',
                       9  => 'doublenum',
                       10 => 'triplenum',
                      ];
        $typestring = '';
        $qstring    = [];
        $params     = [];
        foreach ($strfields as $key => $value) {
            if ($row2[$value] !== $_POST['stayform'][$i][$key]) {
                $log->addUpdate($row2[$value], $_POST['stayform'][$i][$key], $formid, $value);
                $typestring .= 's';
                array_push($qstring, "$value=?");
                array_push($params, $_POST['stayform'][$i][$key]);
            }
        }

        foreach ($intfields as $key => $value) {
            if ($row2[$value] !== $_POST['stayform'][$i][$key]) {
                $log->addUpdate($row2[$value], $_POST['stayform'][$i][$key], $formid, $value);
                $typestring .= 'i';
                array_push($qstring, "$value=?");
                array_push($params, (int) $_POST['stayform'][$i][$key]);
            }
        }

        if ($typestring !== '') {
            $query = 'UPDATE Room SET '.implode(',', $qstring)." WHERE formid='$formid' AND roomno=$i";
            $db->executePreparedQuery($query, $typestring, [$params]);
        }
    }//end for

    if (count($_POST['stayform']) > $row['roomnum']) {
        $params = [];
        $j      = 0;
        $db->makeQuery("INSERT INTO Room(formid, roomno) VALUES('{$formid}', $i)");
        $strfields  = [
                       0 => 'location',
                       1 => 'hotel1',
                       2 => 'hotel2',
                       3 => 'hotel3',
                       4 => 'checkin1',
                       5 => 'checkout1',
                      ];
        $intfields  = [
                       6  => 'frombudget',
                       7  => 'tobudget',
                       8  => 'singlenum',
                       9  => 'doublenum',
                       10 => 'triplenum',
                      ];
        $typestring = '';
        $qstring    = [];
        $params     = [];
        foreach ($strfields as $key => $value) {
            if (null !== $_POST['stayform'][$i][$key]) {
                $log->addUpdate(null, $_POST['stayform'][$i][$key], $formid, $value);
                $typestring .= 's';
                array_push($qstring, "$value=?");
                array_push($params, $_POST['stayform'][$i][$key]);
            }
        }

        foreach ($intfields as $key => $value) {
            if (null !== $_POST['stayform'][$i][$key]) {
                $log->addUpdate(null, $_POST['stayform'][$i][$key], $formid, $value);
                $typestring .= 'i';
                array_push($qstring, "$value=?");
                array_push($params, (int) $_POST['stayform'][$i][$key]);
            }
        }

        if ($typestring !== '') {
            $query = 'UPDATE Room SET '.implode(',', $qstring)." WHERE formid='$formid' AND roomno=$i";
            $db->executePreparedQuery($query, $typestring, [$params]);
        }
    } else if (count($_POST['stayform']) < $row['roomnum']) {
        $rooms = count($_POST['stayform']);
        $query = "DELETE FROM Room WHERE formid='{$formid}' AND roomno >= {$rooms}";
        $db->makeQuery($query);
    }//end if
}//end if

$roomnum = count($_POST['stayform']);
if ($row['addinfo'] !== $_POST['addinfo']) {
    $log->addUpdate($row['addinfo'], $_POST['addinfo'], $formid, 'addinfo');
}

$db->executePreparedQuery("UPDATE Stay SET addinfo=?,roomnum=? WHERE formid='$formid'", 'si', [[$_POST['addinfo'], $roomnum]]);


header('Location: hotelstay.php?id='.$formid);
exit;



/**
 * Performs basic validation
 *
 * @param array $formarray Regform POST array.
 *
 * @return void.
 */
function basicValidation(array &$formarray)
{
    $check = new Check();

    for ($i = 0; $i < count($formarray); $i++) {
        if (mb_strlen($formarray[$i][0]) === 0) {
            $formarray[$i][0] = null;
        } else if (mb_strlen($formarray[$i][0]) > 100) {
            $formarray[$i][0] = mb_substr($formarray[8], 0, 100);
        }

        if ((mb_strlen($formarray[$i][1]) === 0) || (mb_strlen($formarray[$i][1]) > 100)) {
            $formarray[$i][1] = null;
        }

        if ((mb_strlen($formarray[$i][2]) === 0) || (mb_strlen($formarray[$i][2]) > 100)) {
            $formarray[$i][2] = null;
        }

        if ((mb_strlen($formarray[$i][3]) === 0) || (mb_strlen($formarray[$i][3]) > 100)) {
            $formarray[$i][3] = null;
        }

        if ($check->isValidDate($formarray[$i][4], $formarray[$i][5]) === false) {
            $formarray[$i][4] = null;
            $formarray[$i][5] = null;
        }

        if ((mb_strlen($formarray[$i][6]) > 9)
            || (mb_strlen($formarray[$i][7]) > 9)
            || ($check->isNonZeroInt($formarray[$i][6]) === false)
            || ($check->isNonZeroInt($formarray[$i][7]) === false)
            || ($formarray[$i][6] > $formarray[$i][7])
        ) {
            $formarray[$i][6] = null;
            $formarray[$i][7] = null;
        }

        if ((mb_strlen($formarray[$i][8]) > 2) || ($check->isNonZeroInt($formarray[$i][8]) === false)) {
            $formarray[$i][8] = null;
        }

        if ((mb_strlen($formarray[$i][9]) > 2) || ($check->isNonZeroInt($formarray[$i][9]) === false)) {
            $formarray[$i][9] = null;
        }

        if ((mb_strlen($formarray[$i][10]) > 2) || ($check->isNonZeroInt($formarray[$i][10]) === false)) {
            $formarray[$i][10] = null;
        }
    }//end for

}//end basicValidation()
