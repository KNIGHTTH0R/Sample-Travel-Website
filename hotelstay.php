<?php

/**
 * Hotel stay
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
    if ($result[0][0]['stay'] === 'No') {
        header('Location: home.php');
        exit;
    }

    $qry    = "SELECT * FROM Stay WHERE formid='".$formid."'";
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

$prevVals = [];

if ($row['addinfo'] !== null) {
    $prevVals['addinfo'] = htmlspecialchars($row['addinfo'], (ENT_QUOTES | ENT_SUBSTITUTE), 'UTF-8');
} else {
    $prevVals['addinfo'] = '';
}
$qry       = "SELECT * FROM Room WHERE formid='".$formid."' ORDER BY roomno ASC";
$rowresult = $db->makeQuery($qry);
$roomnum   = 0;
$attrVals  = ['checkin1', 'checkout1', 'frombudget', 'tobudget', 'singlenum', 'doublenum', 'triplenum', 'location','hotel1','hotel2','hotel3'];
if (($rowresult !== false) && (mysqli_num_rows($rowresult) > 0)) {
    while ($rowroom = mysqli_fetch_assoc($rowresult)) {
      foreach ($attrVals as $key => $field) {
          if ($rowroom[$field] !== null) {
              $prevVals[$roomnum][$field] = 'value="'.htmlspecialchars($rowroom[$field], (ENT_QUOTES | ENT_SUBSTITUTE), 'UTF-8').'"';
          } else {
              $prevVals[$roomnum][$field] = '';
          }
      }
      $roomnum++;
    }
}

?>


  <!DOCTYPE html>
  <html lang="en">

  <head>
    <title>Hotel booking details</title>
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
    invalidList = [];

    function validateDate(date1, date2, elem) {
      var noofdays = Math.round((date2 - date1) / (86400000)) + 1;
      if (elem === undefined) {
        return ((noofdays > 0) && (noofdays < 60));
      } else {
        if (((noofdays > 0) && (noofdays < 60)) == false) {
          elem.parentElement.parentElement.className = 'form-group field-error';
          if (invalidList.indexOf(elem.id) === -1) {
            invalidList.push(elem.id);
          }
        } else {
          elem.parentElement.parentElement.className = 'form-group';
          var pos = $.inArray(elem.id, invalidList);
          if (pos !== -1) {
            invalidList.splice(pos, 1);
          }
        }
      }
    }

    function validateForm() {
      var invalid_fields = $('.error-text').filter(function() {
        return $(this).text().trim() != "";
      }).length;
      return (invalid_fields === 0);
    }

    var alreadyCreated = false;
    var currRooms = <?php echo $roomnum; ?>;
    function setRooms() {
        var bookdiv = document.getElementById('bookings');
        var original = document.getElementById('first');
        var clone = original.cloneNode(true);
        clone.id = '';
        var buttons = clone.getElementsByTagName('button');
        buttons[0].setAttribute("onclick",'addHotel('+currRooms+')');
        var divs = clone.getElementsByTagName('div');
        divs[3].id = 'hotel1' + currRooms;
        divs[6].id = 'hotel2' + currRooms;
        divs[9].id = 'hotel3' + currRooms;


        var inputs = clone.getElementsByTagName('input');
        for (var i = 0; i < 11; i++) {
          var tfield = 'stayform[' + currRooms + '][' + i + ']'
          inputs[i].id = tfield;
          inputs[i].setAttribute('name', tfield);
          // if (alreadyCreated) {
          inputs[i].value = '';
          // }
        }
        bookdiv.appendChild(clone);
        inputs[4].className = 'datepicker' + currRooms;
        inputs[5].className = 'datepicker' + currRooms;
        $(".datepicker" + currRooms).datepicker({
          dateFormat: "dd-mm-yy",
          minDate: 0
        });
        currRooms++;
    }

    function addHotel(elemno) {
        if (!$("#hotel1"+elemno).is(":visible")) {
          $("#hotel1"+elemno).fadeIn();
        } else if (!$("#hotel2"+elemno).is(":visible")) {
          $("#hotel2"+elemno).fadeIn();
          $("#hotell1"+elemno).html('Hotel 1');
        } else if (!$("#hotel3"+elemno).is(":visible")) {
          $("#hotel3"+elemno).fadeIn();
          $('#addhotels'+elemno).fadeOut();
        }
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
        setRooms();
      });
    });
    </script>
    <!-- //js -->
  </head>

  <body>
    <?php require_once 'navbar.php'; ?>
    <div class="container infobox radio-style">
      <form class="form-horizontal" method="post" action="stayform.php" onsubmit="return validateForm()">
        <input type="hidden" name="formid" value="<?php echo $formid; ?>">
        <fieldset>
          <legend>Hotel bookings</legend>
          <div id="bookings">
            <div id="first">
            <?php for ($i=0; $i<$roomnum; $i++) {?>
              <div class="form-group">
                <label class="col-xs-12 col-md-2 control-label text-left" for="<?php echo 'stayform['.$i.']'; ?>[0]">Location</label>
                <div class="col-xs-12 col-md-10">
                  <input type="text" id="<?php echo 'stayform['.$i.']'; ?>[0]" name="<?php echo 'stayform['.$i.']'; ?>[0]" placeholder="Location" <?php echo $prevVals[$i]['location']; ?> class="max100">
                </div>
                <div class="error=text"></div>
              </div>
              <div class="form-group" <?php if(!$prevVals[$i]['hotel1']) {echo 'style="display:none;"';} ?> id="hotel1<?php echo "$i"; ?>">
                <label class="col-xs-12 col-md-2 control-label text-left" id="hotell1<?php echo "$i"; ?>" for="<?php echo 'stayform['.$i.']'; ?>[1]">Hotel</label>
                <div class="col-xs-12 col-md-10">
                  <input type="text" class="max100" name="<?php echo 'stayform['.$i.']'; ?>[1]" id="<?php echo 'stayform['.$i.']'; ?>[1]" <?php echo $prevVals[$i]['hotel1']; ?>>
                  <div class="error-text"></div>
                </div>
              </div>
              <div class="form-group" <?php if(!$prevVals[$i]['hotel2']) {echo 'style="display:none;"';} ?> id="hotel2<?php echo "$i"; ?>">
                <label class="col-xs-12 col-md-2 control-label text-left" for="<?php echo 'stayform['.$i.']'; ?>[2]">Hotel 2</label>
                <div class="col-xs-12 col-md-10">
                  <input type="text" class="max100" name="<?php echo 'stayform['.$i.']'; ?>[2]" id="<?php echo 'stayform['.$i.']'; ?>[2]" <?php echo $prevVals[$i]['hotel2']; ?>>
                  <div class="error-text"></div>
                </div>
              </div>
              <div class="form-group" <?php if(!$prevVals[$i]['hotel3']) {echo 'style="display:none;"';} ?> id="hotel3<?php echo "$i"; ?>">
                <label class="col-xs-12 col-md-2 control-label text-left" for="<?php echo 'stayform['.$i.']'; ?>[3]">Hotel 3</label>
                <div class="col-xs-12 col-md-10">
                  <input type="text" class="max100" name="<?php echo 'stayform['.$i.']'; ?>[3]" id="<?php echo 'stayform['.$i.']'; ?>[3]" <?php echo $prevVals[$i]['hotel3']; ?>>
                  <div class="error-text"></div>
                </div>
              </div>
              <div class="form-group">
                <label class="col-xs-12 col-md-2 control-label text-left"></label>
                <div class="col-xs-12 col-md-10">
                  <button type="button" onclick="addHotel(<?php echo "$i"; ?>)" class="btn btn-info">
                    <span class="glyphicon glyphicon-plus"></span> Add hotels
                  </button>
                </div>
              </div>
              <div class="form-group">
                <label class="col-xs-12 col-md-2 control-label text-left" for="<?php echo 'stayform['.$i.']'; ?>[4]">Check-in date</label>
                <div class="col-xs-12 col-md-10">
                  <input type="text" class="datepicker" name="<?php echo 'stayform['.$i.']'; ?>[4]" id="<?php echo 'stayform['.$i.']'; ?>[4]" readonly <?php echo $prevVals[$i]['checkin1']; ?>>
                </div>
              </div>
              <div class="form-group">
                <label class="col-xs-12 col-md-2 control-label text-left" for="<?php echo 'stayform['.$i.']'; ?>[5]">Check-out date</label>
                <div class="col-xs-12 col-md-10">
                  <input type="text" class="datepicker" name="<?php echo 'stayform['.$i.']'; ?>[5]" id="<?php echo 'stayform['.$i.']'; ?>[5]" readonly <?php echo $prevVals[$i]['checkout1']; ?>>
                </div>
              </div>
              <div class="form-group">
                <label class="col-xs-12 col-md-2 control-label text-left">Budget range</label>
                <div class="col-xs-12 col-md-10">
                  <input type="number" name="<?php echo 'stayform['.$i.']'; ?>[6]" id="<?php echo 'stayform['.$i.']'; ?>[6]" min="0" max="999999999" <?php echo $prevVals[$i]['frombudget']; ?>> &nbsp;&nbsp;&nbsp;to&nbsp;&nbsp;&nbsp;
                  <input type="number" name="<?php echo 'stayform['.$i.']'; ?>[7]" id="<?php echo 'stayform['.$i.']'; ?>[7]" min="0" max="999999999" <?php echo $prevVals[$i]['tobudget']; ?>>
                </div>
              </div>
              <div class="form-group">
                <label class="col-xs-12 col-md-2 control-label text-left">Rooms required</label>
                <div class="col-xs-12 col-md-10">
                  Single&nbsp;&nbsp;&nbsp;<input type="number" name="<?php echo 'stayform['.$i.']'; ?>[8]" id="<?php echo 'stayform['.$i.']'; ?>[8]" min="1" max="60" <?php echo $prevVals[$i]['singlenum']; ?>><br>
                  Double&nbsp;&nbsp;&nbsp;<input type="number" name="<?php echo 'stayform['.$i.']'; ?>[9]" id="<?php echo 'stayform['.$i.']'; ?>[9]" min="1" max="60" <?php echo $prevVals[$i]['doublenum']; ?>><br>
                  Triple&nbsp;&nbsp;&nbsp;<input type="number" name="<?php echo 'stayform['.$i.']'; ?>[10]" id="<?php echo 'stayform['.$i.']'; ?>[10]" min="1" max="60" <?php echo $prevVals[$i]['triplenum']; ?>><br>
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