<?php

/**
 * Car booking
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
    header('Location: home.php');
    exit;
}

$compkey = $_SESSION['compkey'];
$formid  = $_GET['id'];
if (strlen($formid) !== 10) {
    header('Location: home.php');
    exit;
}

$result = $db->executePreparedQuery('SELECT * FROM Form WHERE compkey=? AND formid=?', 'ss', [[$compkey, $formid]]);

if (($result !== false) && (count($result[0]) > 0)) {
    if ($result[0][0]['car'] === 'No') {
        header('Location: home.php');
        exit;
    }

    $qry    = "SELECT * FROM Car WHERE formid='".$formid."'";
    $result = $db->makeQuery($qry);
    if ($result === false) {
        header('Location: home.php');
        exit;
    }

    $row = mysqli_fetch_assoc($result);
} else {
    header('Location: home.php');
    exit;
}

if ($row['addinfo'] !== null) {
    $prevVals['addinfo'] = htmlspecialchars($row['addinfo'], (ENT_QUOTES | ENT_SUBSTITUTE), 'UTF-8');
} else {
    $prevVals['addinfo'] = '';
}
$qry       = "SELECT * FROM Carbooking WHERE formid='".$formid."' ORDER BY carno ASC";
$rowresult = $db->makeQuery($qry);
$carnum    = 0;
$attrVals  = ['location','fromdate', 'todate', 'cartype', 'noofcars'];
if (($rowresult !== false) && (mysqli_num_rows($rowresult) > 0)) {
    while ($rowcar = mysqli_fetch_assoc($rowresult)) {
        foreach ($attrVals as $key => $field) {
            if ($rowcar[$field] !== null) {
                $prevVals[$carnum][$field] = 'value="'.htmlspecialchars($rowcar[$field], (ENT_QUOTES | ENT_SUBSTITUTE), 'UTF-8').'"';
            } else {
                $prevVals[$carnum][$field] = '';
            }
        }

        $carnum++;
    }
}
?>

  <!DOCTYPE html>
  <html lang="en">

  <head>
    <title>Car details</title>
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
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>
    <script>
    var currCars = <?php echo $carnum; ?>;
    function validateForm() {
      var invalid_fields = $('.error-text').filter(function() {
        return $(this).text().trim() != "";
      }).length;
      return (invalid_fields === 0);
    }

    var alreadyCreated = false;
    var currCars = <?php echo $carnum; ?>;
    function setCars() {
        var bookdiv = document.getElementById('bookings');
        var original = document.getElementById('first');
        var clone = original.cloneNode(true);
        clone.id = '';
        var inputs = clone.getElementsByTagName('input');
        for (var i = 0; i < 5; i++) {
          var tfield = 'carform[' + currCars + '][' + i + ']'
          inputs[i].id = tfield;
          inputs[i].setAttribute('name', tfield);
          inputs[i].value = '';
        }
        inputs[1].className = 'datepicker' + currCars;
        inputs[2].className = 'datepicker' + currCars;
        bookdiv.appendChild(clone);
        $(".datepicker" + currCars).datepicker({
          dateFormat: "dd-mm-yy",
          minDate: 0
        });
        currCars++;
    }
    $(document).ready(function() {
      $(function() {
        $(".datepicker").datepicker({
          dateFormat: "dd-mm-yy",
          minDate: 0
        });
      });
      $('document').on('keyup', '.max100', function() {
        if ($(this).val().length > 100) {
          $(this).next().text("Maximum length 100");
        } else {
          $(this).next().text("");
        }
      });
      $('document').on('keyup', '.max500', function() {
        if ($(this).val().length > 500) {
          $(this).next().text("Maximum length 500");
        } else {
          $(this).next().text("");
        }
      });
      $('#addbooking').click(function() {
        setCars();
      });

    });
    </script>
    <!-- //js -->
  </head>

  <body>
    <?php require_once 'navbar.php'; ?>
    <div class="container infobox radio-style">
      <form class="form-horizontal" method="post" action="carform.php">
        <input type="hidden" name="formid" value="<?php echo $formid; ?>">
        <fieldset>
          <legend>Car bookings</legend>
          <div id="bookings">
            <div id="first">
            <?php for ($i=0; $i<$carnum; $i++) {?>
              <div class="form-group">
                <label class="col-xs-12 col-md-2 control-label text-left" for="<?php echo "carform[$i]"; ?>[0]">Location</label>
                <div class="col-xs-12 col-md-10">
                  <input type="text" id="<?php echo "carform[$i]"; ?>[0]" name="<?php echo "carform[$i]"; ?>[0]" placeholder="Location" <?php echo $prevVals[$i]['location']; ?> class="max100">
                </div>
                <div class="error=text"></div>
              </div>
              <div class="form-group">
                <label class="col-xs-12 col-md-2 control-label text-left" for="<?php echo "carform[$i]"; ?>[1]">Dates</label>
                <div class="col-xs-12 col-md-10">
                  From&nbsp;&nbsp;&nbsp;<input type="text" class="datepicker" name="<?php echo "carform[$i]"; ?>[1]" id="<?php echo "carform[$i]"; ?>[1]" readonly <?php echo $prevVals[$i]['fromdate']; ?>> &nbsp;&nbsp;&nbsp;to&nbsp;&nbsp;&nbsp;
                  <input type="text" class="datepicker" name="<?php echo "carform[$i]"; ?>[2]" id="<?php echo "carform[$i]"; ?>[2]" readonly <?php echo $prevVals[$i]['todate']; ?>>
                </div>
              </div>
              <div class="form-group">
                <label class="col-xs-12 col-md-2 control-label text-left" for="<?php echo "carform[$i]"; ?>[3]">Car type</label>
                <div class="col-xs-12 col-md-10">
                  <input type="text" name="<?php echo "carform[$i]"; ?>[3]" id="<?php echo "carform[$i]"; ?>[3]" <?php echo $prevVals[$i]['cartype']; ?> class="max100">
                </div>
              </div>
              <div class="form-group">
                <label class="col-xs-12 col-md-2 control-label text-left" for="<?php echo "carform[$i]"; ?>[4]">Cars required</label>
                <div class="col-xs-12 col-md-10">
                  <input type="number" name="<?php echo "carform[$i]"; ?>[4]" id="<?php echo "carform[$i]"; ?>[4]" min="1" max="60" <?php echo $prevVals[$i]['noofcars']; ?>>
                </div>
              </div>
              <hr>
             <?php if($i === 0) {echo '</div>';} } ?>
          </div>
          <div class="form-group">
            <label class="col-xs-12 col-md-2 control-label text-left"></label>
            <div class="col-xs-12 col-md-10">
              <button type="button" class="btn btn-info" id="addbooking">
                <span class="glyphicon glyphicon-plus"></span> Add bookings
              </button>
            </div>
          </div>
        </fieldset>
        <fieldset>
          <legend>Other</legend>
          <div class="form-group">
            <label class="col-xs-12 col-md-2 control-label text-left">Additional information</label>
            <div class="col-xs-12 col-md-10">
              <textarea class="form-control max500" id="addinfo" name="addinfo"><?php echo $prevVals['addinfo']; ?></textarea>
            </div>
          </div>
        </fieldset>
        <div class="col-xs-12 col-md-10 col-md-offset-2">
          <button type="submit" class="btn btn-info">Save</button>
        </div>
      </form>
    </div>
    <?php require_once 'footer.php'; ?>
  </body>

  </html>
