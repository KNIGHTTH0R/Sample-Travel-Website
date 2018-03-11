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

if ((isset($_POST['carform']) === false) || ($helper->isLoggedIn() === false)) {
    header('Location: home.php');
    exit;
}

$result = $db->executePreparedQuery('SELECT * FROM Form WHERE compkey=? AND formid=?', 'ss', [[$_SESSION['compkey'], $formid]]);

if (($result === false) || (count($result[0]) === 0)) {
    header('Location: home.php');
    exit;
}

if ($result[0][0]['car'] === 'No') {
    header('Location: home.php');
    exit;
}

basicValidation($_POST['carform']);

if ((mb_strlen($_POST['addinfo']) === 0) || (mb_strlen($_POST['addinfo']) > 500)) {
    $_POST['addinfo'] = null;
}

$qry    = "SELECT * FROM Car WHERE formid='{$formid}'";
$result = $db->makeQuery($qry);
$row    = mysqli_fetch_assoc($result);

$qry    = "SELECT * FROM Carbooking WHERE formid='{$formid}' ORDER BY carno ASC";
$result = $db->makeQuery($qry);

if (($result !== false) && ($_POST['carform'] !== null)) {
    $i      = 0;
    $j      = 0;
    $params = [];
    for (; $i < min(count($_POST['carform']), $row['carnum']); $i++) {
        $row2       = mysqli_fetch_assoc($result);
        $strfields  = [
                       0 => 'location',
                       1 => 'fromdate',
                       2 => 'todate',
                       3 => 'cartype',
                      ];
        $intfields  = [4 => 'noofcars'];
        $typestring = '';
        $qstring    = [];
        $params     = [];
        foreach ($strfields as $key => $value) {
            if ($row2[$value] !== $_POST['carform'][$i][$key]) {
                $log->addUpdate($row2[$value], $_POST['carform'][$i][$key], $formid, $value);
                $typestring .= 's';
                array_push($qstring, "$value=?");
                array_push($params, $_POST['carform'][$i][$key]);
            }
        }

        foreach ($intfields as $key => $value) {
            if ($row2[$value] !== $_POST['carform'][$i][$key]) {
                $log->addUpdate($row2[$value], $_POST['carform'][$i][$key], $formid, $value);
                $typestring .= 'i';
                array_push($qstring, "$value=?");
                array_push($params, (int) $_POST['carform'][$i][$key]);
            }
        }

        if ($typestring !== '') {
            $query = 'UPDATE Carbooking SET '.implode(',', $qstring)." WHERE formid='$formid' AND carno=$i";
            $db->executePreparedQuery($query, $typestring, [$params]);
        }
    }//end for

    if (count($_POST['carform']) > $row['carnum']) {
        $params = [];
        $j      = 0;
        $db->makeQuery("INSERT INTO Carbooking(formid, carno) VALUES('{$formid}', $i)");
        $strfields  = [
                       0 => 'location',
                       1 => 'fromdate',
                       2 => 'todate',
                       3 => 'cartype',
                      ];
        $intfields  = [4 => 'noofcars'];
        $typestring = '';
        $qstring    = [];
        $params     = [];
        foreach ($strfields as $key => $value) {
            if (null !== $_POST['carform'][$i][$key]) {
                $log->addUpdate(null, $_POST['carform'][$i][$key], $formid, $value);
                $typestring .= 's';
                array_push($qstring, "$value=?");
                array_push($params, $_POST['carform'][$i][$key]);
            }
        }

        foreach ($intfields as $key => $value) {
            if (null !== $_POST['carform'][$i][$key]) {
                $log->addUpdate(null, $_POST['carform'][$i][$key], $formid, $value);
                $typestring .= 'i';
                array_push($qstring, "$value=?");
                array_push($params, (int) $_POST['carform'][$i][$key]);
            }
        }

        if ($typestring !== '') {
            $query = 'UPDATE Carbooking SET '.implode(',', $qstring)." WHERE formid='$formid' AND carno=$i";
            $db->executePreparedQuery($query, $typestring, [$params]);
        }
    } else if (count($_POST['carform']) < $row['carnum']) {
        $rooms = count($_POST['carform']);
        $query = "DELETE FROM Carbooking WHERE formid='{$formid}' AND carno >= {$rooms}";
        $db->makeQuery($query);
    }//end if
}//end if

$carnum = count($_POST['carform']);
if ($row['addinfo'] !== $_POST['addinfo']) {
    $log->addUpdate($row['addinfo'], $_POST['addinfo'], $formid, 'addinfo');
}

$db->executePreparedQuery("UPDATE Car SET addinfo=?,carnum=? WHERE formid='$formid'", 'si', [[$_POST['addinfo'], $carnum]]);



header('Location: car.php?id='.$formid);
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

        if ($check->isValidDate($formarray[$i][1], $formarray[$i][2]) === false) {
            $formarray[$i][1] = null;
            $formarray[$i][2] = null;
        }

        if ((mb_strlen($formarray[$i][3]) === 0) || (mb_strlen($formarray[$i][3]) > 100)) {
            $formarray[$i][3] = null;
        }

        if ((mb_strlen($formarray[$i][4]) > 2) || ($check->isNonZeroInt($formarray[$i][4]) === false)) {
            $formarray[$i][4] = null;
        }
    }//end for

}//end basicValidation()
