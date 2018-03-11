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

if ((isset($_GET['book']) === true) && (in_array($_GET['book'], ['conference', 'stay', 'car']))) {
    $_SESSION['book'] = $_GET['book'];
} else {
    $_SESSION['book'] = null;
}

if ($helper->isLoggedIn() === false) {
    header('Location: login.php');
    exit;
}

$compkey = $_SESSION['compkey'];
$qry     = "SELECT * FROM Form WHERE compkey='".$compkey."' ORDER BY created DESC";
$result  = $db->makeQuery($qry);
$limit   = mysqli_num_rows($result);
$tstring = '';
if ($limit !== 0) {
    for ($i = 0; $i < $limit; $i++) {
        $row = mysqli_fetch_assoc($result);

        $formname = htmlspecialchars($row['formname'], (ENT_QUOTES | ENT_SUBSTITUTE), 'UTF-8');
        if ($row['created'] !== null) {
            $formname .= '<br>Submitted: '.$row['created'];
        } else {
            $formname .= '';
        }

        $conflink = '';
        $staylink = '';
        $carlink  = '';
        if ($row['conf'] === 'Yes') {
            $conflink = '<br><a href="conference.php?id='.$row['formid'].'">Conference booking link</a>';
        }

        if ($row['stay'] === 'Yes') {
            $staylink = '<br><a href="hotelstay.php?id='.$row['formid'].'">Hotel booking link</a>';
        }

        if ($row['car'] === 'Yes') {
            $carlink = '<br><a href="car.php?id='.$row['formid'].'">Car booking link</a>';
        }

        if (($i % 3) === 0) {
            $offsettext = '';
            $starttext  = '<div class="row">';
            $endtext    = '';
        } else if (($i % 3) === 1) {
            $offsettext = 'col-md-offset-1';
            $starttext  = '';
            $endtext    = '';
        } else {
            $offsettext = 'col-md-offset-1';
            $starttext  = '';
            $endtext    = '</div>';
        }

        $formid   = $row['formid'];
        $text     = '<p> Form name : '.$formname.$conflink.$staylink.$carlink.'<br><br><a href="preview.php?id='.$formid.'"><button type="button" class="btn btn-default btn-lg">Show preview</button></a><br><br><a href="removeform.php?id='.$formid.'"><button type="button" class="btn btn-default btn-lg">Remove form</button></a></p>';
        $tstring .= $starttext.'<div class="infobox postit-yellow col-md-3 '.$offsettext.' col-xs-12">
        <div class="pin"></div>'.$text.'</div>'.$endtext;
    }//end for
}//end if
?>
  <!DOCTYPE html>
  <html>

  <head>
    <title>Forms</title>
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
    <script type="text/javascript">
    function validate(id, regex, length) {
      var field = document.getElementById(id);
      if ((field.value == "")) {
        field.parentElement.parentElement.className = 'form-group field-error';
        return false;
      }
      if (length && (field.value.length > length)) {
        field.parentElement.parentElement.className = 'form-group field-error length';
        return false;
      }
      if (!(field.value.match(regex))) {
        field.parentElement.parentElement.className = 'form-group field-error character';
        return false;
      } else {
        field.parentElement.parentElement.className = 'form-group';
        return true;
      }
    }

    function validateForm() {
      if (validate('newform[0]', /^.{1,50}$/, 50)) {
        for (var i = 1; i <= 3; i++) {
          var choice_elems = document.getElementsByName('newform[' + i + ']');
          if ((!choice_elems[0].checked) && (!choice_elems[1].checked)) {
            return false
          }
        }
      } else {
        return false;
      }
      return true;
    }
    </script>
  </head>

  <body>
    <?php require_once 'navbar.php'; ?>
    <div class="container">

      <div class="infobox container radio-style" id="newform">
        <h1>Select which services you want to avail.</h1>
        <form class="form-horizontal" action="newform.php" method="post" accept-charset="utf-8" onsubmit="return validateForm()">
          <div class="form-group">
            <label class="col-xs-12 col-md-2 control-label text-left" for="newform[0]">Form name</label>
            <div class="col-xs-12 col-md-10">
              <input name="newform[0]" id="newform[0]" type="text" placeholder="Name" onblur="validate('newform[0]',/^.{1,50}$/,50)">
              <div class="genericerror">Invalid name</div>
              <div class="lengtherror">Maximum length 50</div>
            </div>
          </div>
          <div class="form-group">
            <label class="col-xs-12 col-md-4 control-label text-left" for="newform[1]">Conference booking</label>
            
          <!-- </div>
          <div class="form-group"> -->
            <label class="col-xs-12 col-md-4 control-label text-left" for="newform[2]">Hotel stay booking</label>
          <!-- </div>
          <div class="form-group"> -->
            <label class="col-xs-12 col-md-4 control-label text-left" for="newform[3]">Car booking</label>
            <div class="col-xs-12 col-md-4">
              <div class="radio">
                <input name="newform[1]" id="newform[1][0]" value="Yes" type="radio" 
                <?php
                if ($_SESSION['book'] === 'conference') {
                    echo 'checked >';
                } else {
                    echo '>';
                }
                ?>
                <label for="newform[1][0]">Yes
                </label>&nbsp;&nbsp;&nbsp;&nbsp;
                <input name="newform[1]" id="newform[1][1]" value="No" type="radio" 
                <?php
                if (($_SESSION['book'] === 'stay') || (($_SESSION['book'] === 'car'))) {
                    echo 'checked >';
                } else {
                    echo '>';
                }
                ?>
                <label for="newform[1][1]">No
                </label>
              </div>
            </div>
            <div class="col-xs-12 col-md-4">
              <div class="radio">
                <input name="newform[2]" id="newform[2][0]" value="Yes" type="radio" 
                <?php
                if ($_SESSION['book'] === 'stay') {
                    echo 'checked >';
                } else {
                    echo '>';
                }
                ?>
                <label for="newform[2][0]">Yes
                </label>&nbsp;&nbsp;&nbsp;&nbsp;
                <input name="newform[2]" id="newform[2][1]" value="No" type="radio" 
                <?php
                if (($_SESSION['book'] === 'conference') || (($_SESSION['book'] === 'car'))) {
                    echo 'checked >';
                } else {
                    echo '>';
                }
                ?>
                <label for="newform[2][1]">No
                </label>
              </div>
            </div>
            <div class="col-xs-12 col-md-4">
              <div class="radio">
                <input name="newform[3]" id="newform[3][0]" value="Yes" type="radio"
                <?php
                if ($_SESSION['book'] === 'car') {
                    echo 'checked >';
                } else {
                    echo '>';
                }
                ?>
                <label for="newform[3][0]">Yes
                </label>&nbsp;&nbsp;&nbsp;&nbsp;
                <input name="newform[3]" id="newform[3][1]" value="No" type="radio" 
                <?php
                if (($_SESSION['book'] === 'stay') || (($_SESSION['book'] === 'conference'))) {
                    echo 'checked >';
                } else {
                    echo '>';
                }
                ?>
                <label for="newform[3][1]">No
                </label>
              </div>
            </div>

          </div>
          <button type="submit" class="btn btn-info"> <span class="glyphicon glyphicon-plus"></span> Submit form</button>
        </form>
        <br>
        <br>
      </div>
        <?php
        echo $tstring;
        ?>
    </div>
    </div>
        <?php require_once 'footer.php'; ?>
  </body>
<?php
unset($_SESSION['book']);
?>
  </html>