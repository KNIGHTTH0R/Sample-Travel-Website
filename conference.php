<?php

/**
 * Conference
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
    if ($result[0][0]['conf'] === 'No') {
        header('Location: home.php');
        exit;
    }

    $qry1    = "SELECT * FROM Conference WHERE formid='".$formid."'";
    $result1 = $db->makeQuery($qry1);
    $qry2    = "SELECT * FROM Meal WHERE formid='".$formid."'";
    $result2 = $db->makeQuery($qry2);
    if (($result1 === false) || ($result2 === false)) {
        header('Location: home.php');
        exit;
    }

    $row1 = mysqli_fetch_assoc($result1);
    $row2 = mysqli_fetch_assoc($result2);
    $row  = array_merge($row1, $row2);
} else {
    header('Location: home.php');
    exit;
}

$prevVals = [];

$prevVals['conftype'][0] = ($row['conftype'] === 'res') ? 'checked' : '';
$prevVals['conftype'][1] = ($row['conftype'] === 'nonres') ? 'checked' : '';

$attrVals = ['location' ,'maxnum', 'fromdate1', 'todate1', 'fromdate2', 'todate2', 'fromdate3', 'todate3', 'frombudget', 'tobudget', 'fromtime', 'totime', 'fromstage', 'tostage', 'hotel1', 'hotel2', 'hotel3', 'custom'];
foreach ($attrVals as $key => $field) {
    if ($row[$field] !== null) {
        $prevVals[$field] = 'value="'.htmlspecialchars($row[$field], (ENT_QUOTES | ENT_SUBSTITUTE), 'UTF-8').'"';
    } else {
        $prevVals[$field] = '';
    }
}

if ($row['addinfo'] !== null) {
    $prevVals['addinfo'] = htmlspecialchars($row['addinfo'], (ENT_QUOTES | ENT_SUBSTITUTE), 'UTF-8');
} else {
    $prevVals['addinfo'] = '';
}

$radioVals = ['stagereq', 'mealreq'];
foreach ($radioVals as $key => $field) {
    if ($row[$field] === 'yes') {
        $prevVals[$field][0] = 'checked';
        $prevVals[$field][1] = '';
    } else if ($row[$field] === 'no') {
        $prevVals[$field][0] = '';
        $prevVals[$field][1] = 'checked';
    } else {
        $prevVals[$field][0] = '';
        $prevVals[$field][1] = '';
    }
}

?>

  <!DOCTYPE html>
  <html lang="en">

  <head>
    <title>Conference details</title>
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
    function validateDate() {
      var date1 = parseDate($('#confform\\[2\\]').val());
      var date2 = parseDate($('#confform\\[3\\]').val());
      if (date1 && date2) {
        var noofdays = Math.round((date2 - date1) / (86400000)) + 1;
        if ((noofdays <= 0) || (noofdays >= 60)) {
          $('#confform\\[3\\]').next().text("Invalid dates");
          $('.meal').fadeOut();
        } else {
          $('#confform\\[3\\]').next().text("");
          setDays();
        }
      }
      var date3 = parseDate($('#confform\\[4\\]').val());
      var date4 = parseDate($('#confform\\[5\\]').val());
      if (date3 && date4) {
        var noofdays = Math.round((date4 - date3) / (86400000)) + 1;
        if ((noofdays <= 0) || (noofdays >= 60)) {
          $('#confform\\[5\\]').next().text("Invalid dates");
        } else {
          $('#confform\\[5\\]').next().text("");
          setDays();
        }
      }
      var date5 = parseDate($('#confform\\[6\\]').val());
      var date6 = parseDate($('#confform\\[7\\]').val());
      if (date5 && date6) {
        var noofdays = Math.round((date6 - date5) / (86400000)) + 1;
        if ((noofdays <= 0) || (noofdays >= 60)) {
          $('#confform\\[7\\]').next().text("Invalid dates");
        } else {
          $('#confform\\[7\\]').next().text("");
          setDays();
        }
      }
    }

    function validateForm() {
      var invalid_fields = $('.error-text').filter(function() {
        return $(this).text().trim() != "";
      }).length;
      return (invalid_fields === 0);
    }

    var days = [];
    var alreadyCreated = false;
    days[0] = <?php if($row['breakdays']) { echo $row['breakdays'];} else { echo 0;} ?>;
    days[1] = <?php if($row['lunchdays']) { echo $row['lunchdays'];} else { echo 0;} ?>;
    days[2] = <?php if($row['dindays']) { echo $row['dindays'];} else { echo 0;} ?>;
    days[3] = <?php if($row['teadays']) { echo $row['teadays'];} else { echo 0;} ?>;
    days[4] = <?php if($row['snackdays']) { echo $row['snackdays'];} else { echo 0;} ?>;
    days[5] = <?php if($row['liqdays']) { echo $row['liqdays'];} else { echo 0;} ?>;

    function parseDate(dateStr) {
      if (dateStr === '') {
        return null;
      }
      var parts = dateStr.split("-");
      return new Date(parts[2], parts[1] - 1, parts[0]);
    }

    var currDays = 1;
    function setDays() {
      var date1 = parseDate($('#confform\\[2\\]').val());
      var date2 = parseDate($('#confform\\[3\\]').val());
      if (date1 && date2) {
        var noofdays = Math.round((date2 - date1) / (86400000)) + 1;
        if ((noofdays <= 0) || (noofdays >= 60)) {
          $('.meal').fadeOut();
          return;
        }
        var tablediv = document.getElementById('mealtable');
        var bodydiv = document.getElementById('mealbody');
        if (currDays < noofdays) {
          var original = document.getElementById('firstrow');
          while (currDays < noofdays) {
            var clone = original.cloneNode(true);
            var tds = clone.getElementsByTagName('td');
            tds[0].innerHTML = 'Day ' + (currDays+1);
            var inputs = clone.getElementsByTagName('input');
            var labels = clone.getElementsByTagName('label');
            for (var i = 0; i < 6; i++) {
              var dayfield = days[i];
              var tfield = 'meal[' + i + '][' + currDays + ']'
              inputs[i].id = tfield;
              inputs[i].setAttribute('name', tfield);
              labels[i].setAttribute('for', tfield);
              if (!alreadyCreated && (dayfield&(1<<currDays)) !== 0) {
                inputs[i].checked = true;
              } else {
                inputs[i].checked = false;
              }
            }
            clone.id = 'row'+(currDays+1);
            bodydiv.appendChild(clone);
            currDays++;
          }
        } else if(currDays > noofdays) {
          while(currDays > noofdays) {
            bodydiv.removeChild(bodydiv.lastChild);
            currDays--;
          }
        }
        $('.meal').fadeIn();
        alreadyCreated = true;
      } else {
        $('.meal').fadeOut();
      }
    }

    function convertToSec(timeStr) {
      var first = timeStr.split(' ');
      var second = first[0].split(':');
      var minutes = +second[1];
      var hours = (+second[0])%12;
      if (first[1].trim() == 'PM') {
        hours += 12;
      }
      return ((hours*60) + minutes);
    }

    function checkTime() {
      var time1Str = $('#confform\\[10\\]').val();
      var time2Str = $('#confform\\[11\\]').val();
      if (time1Str && time2Str) {
        if (convertToMin(time1Str) > convertToMin(time2Str)) {
          $('#confform\\[11\\]').next().text("Invalid time");
        } else {
          $('#confform\\[11\\]').next().text("");
        }
      }
    }



    $(document).ready(function() {
      $(function() {
        $(".datepicker").datepicker({
          dateFormat: "dd-mm-yy",
          minDate: 0
        });
      });
      var validate = {
        timeFormat: 'h:mm p',
        interval: 30,
        dynamic: false,
        dropdown: true,
        scrollbar: true,
        change: function(time) {
            checkTime();
        }
      };
      $('#confform\\[10\\], #confform\\[11\\]').timepicker(validate);
      $('#confform\\[2\\],#confform\\[3\\],#confform\\[4\\],#confform\\[5\\],#confform\\[6\\],#confform\\[7\\]').change(function() {
        validateDate();
      });
      <?php
      if ($row['fromdate3'] !== null) {
        echo 'var curDates = 2;';
      } else if ($row['fromdate2'] !== null) {
        echo 'var curDates = 1;';
      } else {
        echo 'var curDates = 0;';
      }
      ?>
      $('#adddates').click(function() {
        if (curDates === 1) {
          curDates++;
          $("#date3").fadeIn();
          $('#adddates').fadeOut();
        }
        if (curDates === 0) {
          curDates++;
          $("#date2").fadeIn();
          $("#datel1").html('Tentative dates 1')
        }
      });
      $('.max100').keyup(function() {
      if ($(this).val().length > 100) {
          $(this).next().text("Maximum length 100");
        } else {
          $(this).next().text("");
        }
      });
      $('.max500').keyup(function() {
        if ($(this).val().length > 500) {
          $(this).next().text("Maximum length 500");
        } else {
          $(this).next().text("");
        }
      });
      <?php
      if ($row['hotel3'] !== null) {
        echo 'var currHotels = 3;';
      } else if ($row['hotel2'] !== null) {
        echo 'var currHotels = 2;';
      } else if ($row['hotel1'] !== null) {
        echo 'var currHotels = 1;';
      } else {
        echo 'var currHotels = 0;';
      }
      ?>
      $('#addhotels').click(function() {
        if (currHotels === 2) {
          currHotels++;
          $("#hotel3").fadeIn();
          $('#addhotels').fadeOut();
        } else if (currHotels === 1) {
          currHotels++;
          $("#hotel2").fadeIn();
          $("#hotell1").html('Hotel 1');
        } else if (currHotels === 0) {
          currHotels++;
          $("#hotel1").fadeIn();
        }
      });
      var elemShowList = {'confform[13][0]':'.stage'};
      var elemHideList = {'confform[13][1]':'.stage'};
      $('.disp-select [type="radio"]').click(function() {
        elemVal = $(this).val();
        elemId = $(this).attr('id');
        if(elemId in elemShowList) {
          $(elemShowList[elemId]).fadeIn();
        } else if(elemId in elemHideList) {
          $(elemHideList[elemId]).fadeOut();
        }
      });

      $('.meal-select [type="radio"]').click(function() {
        elemVal = $(this).val();
        elemId = $(this).attr('id');
        if (elemId === 'confform[13][0]') {
          setDays();
        } else if(elemId === 'confform[13][1]') {
          $('.meal').fadeOut();
        }
      });

      $('#confform\\[12\\]').change(function() {
        if ($(this).val().indexOf("Custom") !== -1) {
          $("#customseat").fadeIn();
        } else {
          $("#customseat").fadeOut();
        }
      });
      var original = document.getElementById('firstrow');
      var inputs = original.getElementsByTagName('input');
      for (var i = 0; i < 6; i++) {
        if ((days[i]&1) !== 0) {
          inputs[i].checked = true;
        } else {
          inputs[i].checked = false;
        }
      }
      setDays();
    });
    </script>
    <!-- //js -->
  </head>

  <body>
    <?php require_once 'navbar.php'; ?>
    <div class="container infobox radio-style">
      <form class="form-horizontal" method="post" action="confform.php" onsubmit="return validateForm()">
        <input type="hidden" name="formid" value="<?php echo $formid; ?>">
        <fieldset>
          <legend>General details</legend>
          <div class="form-group">
            <label class="col-xs-12 col-md-2 control-label text-left" for="confform[0][0]">Conference type</label>
            <div class="col-xs-12 col-md-10">
              <div class="radio">
                <input name="confform[0]" id="confform[0][0]" value="res" type="radio" <?php echo $prevVals['conftype'][0]; ?>>
                <label for="confform[0][0]">Residential
                </label>&nbsp;&nbsp;&nbsp;&nbsp;
                <input name="confform[0]" id="confform[0][1]" value="nonres" type="radio" <?php echo $prevVals['conftype'][1]; ?>>
                <label for="confform[0][1]">Non-Residential
                </label>
              </div>
            </div>
          </div>
          <div class="form-group">
            <label class="col-xs-12 col-md-2 control-label text-left" for="autocomplete">Location</label>
            <div class="col-xs-12 col-md-10">
              <input type="text" class="max100" id="autocomplete" name="confform[32]" placeholder="Location" <?php echo $prevVals['location']; ?>>
            </div>
          </div>
          <div class="form-group" <?php if($row['hotel1']===null) {echo 'style="display:none;"';} ?> id="hotel1">
            <label class="col-xs-12 col-md-2 control-label text-left" id="hotell1" for="confform[39]">Hotel</label>
            <div class="col-xs-12 col-md-10">
              <input type="text" class="max100" name="confform[39]" id="confform[39]" <?php echo $prevVals['hotel1']; ?>>
              <div class="error-text"></div>
            </div>
          </div>
          <div class="form-group" <?php if($row['hotel2']===null) {echo 'style="display:none;"';} ?> id="hotel2">
            <label class="col-xs-12 col-md-2 control-label text-left" for="confform[40]">Hotel 2</label>
            <div class="col-xs-12 col-md-10">
              <input type="text" class="max100" name="confform[40]" id="confform[40]" <?php echo $prevVals['hotel2']; ?>>
              <div class="error-text"></div>
            </div>
          </div>
          <div class="form-group" <?php if($row['hotel3']===null) {echo 'style="display:none;"';} ?> id="hotel3">
            <label class="col-xs-12 col-md-2 control-label text-left" for="confform[41]">Hotel 3</label>
            <div class="col-xs-12 col-md-10">
              <input type="text" class="max100" name="confform[41]" id="confform[41]" <?php echo $prevVals['hotel3']; ?>>
              <div class="error-text"></div>
            </div>
          </div>
          <div class="form-group">
            <label class="col-xs-12 col-md-2 control-label text-left"></label>
            <div class="col-xs-12 col-md-10">
              <button type="button" class="btn btn-info" id="addhotels">
                <span class="glyphicon glyphicon-plus"></span> Add hotels
              </button>
            </div>
          </div>
          <div class="form-group" id="date1">
            <label class="col-xs-12 col-md-2 control-label text-left" id="datel1" for="confform[2]">Dates</label>
            <div class="col-xs-12 col-md-10">
              From&nbsp;&nbsp;&nbsp;<input type="text" class="datepicker" name="confform[2]" id="confform[2]" readonly <?php echo $prevVals['fromdate1']; ?>> &nbsp;&nbsp;&nbsp;to&nbsp;&nbsp;&nbsp;
              <input type="text" class="datepicker" name="confform[3]" id="confform[3]" readonly <?php echo $prevVals['todate1']; ?>>
              <div class="error-text"></div>
            </div>
          </div>
          <div class="form-group" <?php if($row['fromdate2']===null) {echo 'style="display:none;"';} ?> id="date2">
            <label class="col-xs-12 col-md-2 control-label text-left" for="confform[4]">Tentative dates 2</label>
            <div class="col-xs-12 col-md-10">
              From&nbsp;&nbsp;&nbsp;<input type="text" class="datepicker" name="confform[4]" id="confform[4]" readonly <?php echo $prevVals['fromdate2']; ?>> &nbsp;&nbsp;&nbsp;to&nbsp;&nbsp;&nbsp;
              <input type="text" class="datepicker" name="confform[5]" id="confform[5]" readonly <?php echo $prevVals['todate2']; ?>>
              <div class="error-text"></div>
            </div>
          </div>
          <div class="form-group" <?php if($row['fromdate3']===null) {echo 'style="display:none;"';} ?> id="date3">
            <label class="col-xs-12 col-md-2 control-label text-left" for="confform[6]">Tentative dates 3</label>
            <div class="col-xs-12 col-md-10">
              From&nbsp;&nbsp;&nbsp;<input type="text" class="datepicker" name="confform[6]" id="confform[6]" readonly <?php echo $prevVals['fromdate3']; ?>> &nbsp;&nbsp;&nbsp;to&nbsp;&nbsp;&nbsp;
              <input type="text" class="datepicker" name="confform[7]" id="confform[7]" readonly <?php echo $prevVals['todate3']; ?>>
              <div class="error-text"></div>
            </div>
          </div>
          <div class="form-group">
            <label class="col-xs-12 col-md-2 control-label text-left" for="confform[1]">Maximum guests</label>
            <div class="col-xs-12 col-md-10">
              <input type="number" name="confform[1]" id="confform[1]" min="0" max="99999" <?php echo $prevVals['maxnum']; ?>>
            </div>
          </div>
          <div class="form-group">
            <label class="col-xs-12 col-md-2 control-label text-left">Budget range</label>
            <div class="col-xs-12 col-md-10">
              From&nbsp;&nbsp;&nbsp;₹<input type="number" name="confform[8]" id="confform[8]" min="0" max="999999999" <?php echo $prevVals['frombudget']; ?>> &nbsp;&nbsp;&nbsp;to&nbsp;&nbsp;&nbsp;
              ₹<input type="number" name="confform[9]" id="confform[9]" min="0" max="999999999" <?php echo $prevVals['tobudget']; ?>>
            </div>
          </div>
        </fieldset>
        <fieldset>
          <legend>Hall details</legend>
          <div class="form-group">
            <label class="col-xs-12 col-md-2 control-label text-left">Hall timings</label>
            <div class="col-xs-12 col-md-10">
              From&nbsp;&nbsp;&nbsp;<input class="timepicker" name="confform[10]" id="confform[10]" <?php echo $prevVals['fromtime']; ?> readonly> &nbsp;&nbsp;&nbsp;to&nbsp;&nbsp;&nbsp;
              <input class="timepicker" name="confform[11]" id="confform[11]" <?php echo $prevVals['totime']; ?> readonly>
              <div class="error-text"></div>
            </div>
          </div>
          <div class="form-group">
            <label class="col-xs-12 col-md-2 control-label text-left">Seatings</label>
            <div class="col-xs-12 col-md-10">
              <select id="confform[12]" name="confform[12]">
                <option hidden disabled value="" <?php if($row['seatarrang'] === null) {echo 'selected';}?>>-- Select an option --</option>
                <option value="Cluster" <?php if($row['seatarrang'] === 'Cluster') {echo 'selected';}?>>Cluster</option>
                <option value="Theatre" <?php if($row['seatarrang'] === 'Theatre') {echo 'selected';}?>>Theatre</option>
                <option value="U-shaped" <?php if($row['seatarrang'] === 'U-shaped') {echo 'selected';}?>>U-shaped</option>
                <option value="Mixed" <?php if($row['seatarrang'] === 'Mixed') {echo 'selected';}?>>Mixed</option>
                <option value="Cluster+Theatre" <?php if($row['seatarrang'] === 'Cluster+Theatre') {echo 'selected';}?>>Cluster+Theatre</option>
                <option value="Custom" <?php if($row['seatarrang'] === 'Custom') {echo 'selected';}?>>Custom</option>
              </select>
            </div>
          </div>
          <div class="form-group" <?php if($row['seatarrang'] !== 'Custom') {echo 'style="display:none;"';} ?> id="customseat">
            <label class="col-xs-12 col-md-2 control-label text-left" for="confform[17]">Arrangement</label>
            <div class="col-xs-12 col-md-10">
              <input class="max100" type="text" name="confform[17]" id="confform[17]" <?php echo $prevVals['custom']; ?>>
              <div class="error-text"></div>
            </div>
          </div>
          <div class="form-group">
            <label class="col-xs-12 col-md-2 control-label text-left" for="confform[13][0]">Stage</label>
            <div class="col-xs-12 col-md-10">
              <div class="radio disp-select">
                <input name="confform[13]" id="confform[13][0]" value="yes" type="radio" <?php echo $prevVals['stagereq'][0];?>>
                <label for="confform[13][0]">Yes
                </label>&nbsp;&nbsp;&nbsp;&nbsp;
                <input name="confform[13]" id="confform[13][1]" value="no" type="radio" <?php echo $prevVals['stagereq'][1];?>>
                <label for="confform[13][1]">No
                </label>
              </div>
            </div>
          </div>
          <div class="form-group stage" <?php if ($row['stagereq'] !=='yes' ) { echo 'style="display:none;"';}?>>
            <label class="col-xs-12 col-md-2 control-label text-left" for="confform[14]">Approximate Stage dimensions</label>
            <div class="col-xs-12 col-md-10">
              <input name="confform[14]" id="confform[14]" type="number" min="0" max="999999" <?php echo $prevVals['fromstage']; ?>> &nbsp;&nbsp;&nbsp;X&nbsp;&nbsp;&nbsp;
              <input name="confform[15]" id="confform[15]" type="number" min="0" max="999999" <?php echo $prevVals['tostage']; ?>> &nbsp;ft<sup>2</sup>
            </div>
          </div>
          <div class="form-group">
            <label class="col-xs-12 col-md-2 control-label text-left" for="confform[16][0]">Meal</label>
            <div class="col-xs-12 col-md-10">
              <div class="radio meal-select">
                <input name="confform[16]" id="confform[16][0]" value="yes" type="radio" <?php echo $prevVals['mealreq'][0];?>>
                <label for="confform[16][0]">Yes
                </label>&nbsp;&nbsp;&nbsp;&nbsp;
                <input name="confform[16]" id="confform[16][1]" value="no" type="radio" <?php echo $prevVals['mealreq'][1];?>>
                <label for="confform[16][1]">No
                </label>
              </div>
            </div>
          </div>
        </fieldset>
        <fieldset class="meal" <?php if ($row['mealreq'] !=='yes' ) { echo 'style="display:none;"';}?>>
          <legend>Meals</legend>
          <table id="mealtable" class="blueTable">
            <tr><th></th><th>Breakfast</th><th>Lunch</th><th>Dinner</th><th>High Tea</th><th>Snacks</th><th>Liquor</th></tr><tbody id="mealbody">
            <tr id="firstrow"><td>Day 1</td><td><input name="meal[0][0]" id="meal[0][0]" type="checkbox"><label for="meal[0][0]"></label></td><td><input name="meal[1][0]" id="meal[1][0]" type="checkbox"><label for="meal[1][0]"></label></td><td><input name="meal[2][0]" id="meal[2][0]" type="checkbox"><label for="meal[2][0]"></label></td><td><input name="meal[3][0]" id="meal[3][0]" type="checkbox"><label for="meal[3][0]"></label></td><td><input name="meal[4][0]" id="meal[4][0]" type="checkbox"><label for="meal[4][0]"></label></td><td><input name="meal[5][0]" id="meal[5][0]" type="checkbox"><label for="meal[5][0]"></label></td></tr></tbody>
          </table>
        </fieldset>
        <fieldset>
          <legend>Other</legend>
          <div class="form-group">
            <label class="col-xs-12 col-md-2 control-label text-left" for="confform[38]">Additional information</label>
            <div class="col-xs-12 col-md-8">
              <textarea class="form-control max500" id="confform[38]" name="confform[38]"><?php echo $prevVals['addinfo']; ?></textarea>
              <div class="error-text"></div>
            </div>
          </div>
        </fieldset>
        <div class="col-xs-12 col-md-10 col-md-offset-2">
          <button type="submit" class="btn btn-info">Save</button>
        </div>
      </form>
    </div>
    <?php require_once 'footer.php'; ?>
    <script>
    var autocomplete;
    var countryRestrict = { 'country': 'in' };

    function initAuto() {
      autocomplete = new google.maps.places.Autocomplete(
        /** @type {!HTMLInputElement} */
        (document.getElementById('autocomplete')), {
          types: ['geocode'],
          componentRestrictions: countryRestrict
        });
    }
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=<api_key>&libraries=places&callback=initAuto" async defer></script>
  </body>

  </html>