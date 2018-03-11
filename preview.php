<?php

/**
 * Forms
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
require_once 'Db.inc';

$helper = new Helper();
$db     = new Db();

if ($helper->isLoggedIn() === false) {
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

$formstring = '';

if ($result[0][0]['conf'] === 'Yes') {
    $qry        = "SELECT * FROM Conference WHERE formid='$formid'";
    $formresult = $db->makeQuery($qry);
    $row1       = mysqli_fetch_assoc($formresult);
    $tstring    = '';
    $mappings   = [
                   'conftype'   => 'Conference type',
                   'location'   => 'Location',
                   'hotel1'     => 'Hotel',
                   'hotel2'     => 'Alternative hotel',
                   'hotel3'     => 'Alternative hotel',
                   'fromdate1'  => 'Start date',
                   'todate1'    => 'End date',
                   'fromdate2'  => 'Alternative start date',
                   'todate2'    => 'Alternative end date',
                   'fromdate3'  => 'Alternative start date',
                   'todate3'    => 'Alternative end date',
                   'maxnum'     => 'Maximum guests',
                   'frombudget' => 'Minimum budget',
                   'tobudget'   => 'Maximum budget',
                   'fromtime'   => 'Hall booking start time',
                   'totime'     => 'Hall booking end time',
                   'seatarrang' => 'Seatings',
                   'custom'     => 'Custom Seating type',
                   'stagereq'   => 'Stage',
                   'fromstage'  => 'Stage length',
                   'tostage'    => 'Stage width',
                   'mealreq'    => 'Meal',
                   'addinfo'    => 'Additional information',
                  ];
    if ($row1['conftype'] === 'res') {
        $row1['conftype'] = 'Residential';
    } else if ($row1['conftype'] === 'nonres') {
        $row1['conftype'] = 'Non-Residential';
    }

    foreach ($mappings as $field => $mappedfield) {
        if ($row1[$field] !== null && $row1[$field] !== '') {
            if ($row1[$field] === 'yes') {
                    $row1[$field] = 'Yes';
            } else if ($row1[$field] === 'no') {
                $row1[$field] = 'No';
            }

            $tstring .= "<b>$mappedfield:</b> ".htmlspecialchars($row1[$field], (ENT_QUOTES | ENT_SUBSTITUTE), 'UTF-8').'<br>';
        }
    }

    if ($tstring !== '') {
        $formstring .= '<p class="h2">Conference details</p>';
        $formstring .= $tstring;
    }
}//end if

if ($result[0][0]['stay'] === 'Yes') {
    $qry        = "SELECT * FROM Room WHERE formid='".$formid."' ORDER BY roomno ASC";
    $formresult = $db->makeQuery($qry);
    $tstring    = '<p class="h2">Room bookings</p><ul>';

    $mappings = [
                 'location'   => 'Location',
                 'hotel1'     => 'Hotel',
                 'hotel2'     => 'Alternative hotel',
                 'hotel3'     => 'Alternative hotel',
                 'checkin1'   => 'Check-in date',
                 'checkout1'  => 'Check-out date',
                 'frombudget' => 'Minimum budget',
                 'tobudget'   => 'Maximum budget',
                 'singlenum'  => 'Single rooms',
                 'doublenum'  => 'Double rooms',
                 'triplenum'  => 'Triple rooms',
                ];
    while ($row1 = mysqli_fetch_assoc($formresult)) {
        $rstring = '<li>';
        foreach ($mappings as $field => $mappedfield) {
            if ($row1[$field] !== null && $row1[$field] !== '') {
                if ($row1[$field] === 'yes') {
                    $row1[$field] = 'Yes';
                } else if ($row1[$field] === 'no') {
                    $row1[$field] = 'No';
                }

                $rstring .= "<b>$mappedfield:</b> ".htmlspecialchars($row1[$field], (ENT_QUOTES | ENT_SUBSTITUTE), 'UTF-8').'<br>';
            }
        }

        if ($rstring !== '<li>') {
            $rstring .= '</li>';
            $tstring .= $rstring;
        }
    }

    if ($tstring !== '<p class="h2">Room bookings</p><ul>') {
        $formstring .= $tstring;
        $formstring .= '</ul>';
    }
}//end if

if ($result[0][0]['car'] === 'Yes') {
    $qry        = "SELECT * FROM Carbooking WHERE formid='{$formid}' ORDER BY carno ASC";
    $formresult = $db->makeQuery($qry);
    $tstring    = '<p class="h2">Car bookings</p><ul>';

    $mappings = [
                 'location' => 'Location',
                 'fromdate' => 'Start date',
                 'todate'   => 'End date',
                 'cartype'  => 'Car type',
                 'noofcars' => 'Number of cars',
                ];
    while ($row1 = mysqli_fetch_assoc($formresult)) {
        $rstring = '<li>';
        foreach ($mappings as $field => $mappedfield) {
            if ($row1[$field] !== null && $row1[$field] !== '') {
                if ($row1[$field] === 'yes') {
                    $row1[$field] = 'Yes';
                } else if ($row1[$field] === 'no') {
                    $row1[$field] = 'No';
                }

                $rstring .= "<b>$mappedfield:</b> ".htmlspecialchars($row1[$field], (ENT_QUOTES | ENT_SUBSTITUTE), 'UTF-8').'<br>';
            }
        }

        if ($rstring !== '<li>') {
            $rstring .= '</li>';
            $tstring .= $rstring;
        }
    }

    if ($tstring !== '<p class="h2">Car bookings</p><ul>') {
        $formstring .= $tstring;
        $formstring .= '</ul>';
    }
}//end if

if ($formstring === '') {
    $formstring = '<p>Nothing to show! Fill the form first.</p>';
}
?>
  <!DOCTYPE html>
  <html>

  <head>
    <title>Preview</title>
    <!-- for-mobile-apps -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <script type="text/javascript">
    addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false);

    function hideURLbar() { window.scrollTo(0, 1); }
    </script>
    <!-- //for-mobile-apps -->
    <!-- //custom-theme -->
    <link href="style2.css" rel="stylesheet" type="text/css" media="all" />
    <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css?family=Satisfy" rel="stylesheet"> 
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    <script type="text/javascript"></script>
  </head>

  <body>
    <?php require_once 'navbar.php'; ?>
    <div class="wrapper">
      <div class="infobox container radio-style">
        <?php echo $formstring; ?>
      </div>
  </div>
    <?php require_once 'footer.php'; ?>
  </body>
  </html>