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


?>
  <!DOCTYPE html>
  <html lang="en">

  <head>
    <title>Review</title>
    <!-- for-mobile-apps -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <script type="text/javascript">
    addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false);

    function hideURLbar() { window.scrollTo(0, 1); }
    </script>
    <link href="style2.css" rel="stylesheet" type="text/css" media="all" />
    <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
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
      flag = validate('revform[0]',/^.{1,50}$/,50);
      flag &= validate('revform[1]',/^.{1,500}$/,500);
      return flag;
    }
    </script>
  </head>

  <body>
    <?php require_once 'navbar.php'; ?>
    <div class="wrapper">
      <div class="container infobox">
        <form class="form-horizontal" method="post" action="revform.php" onsubmit="return validateForm()">
          <fieldset>
            <div class="form-group">
              <label class="col-xs-12 col-md-2 control-label text-left" for="revform[0]">Review for</label>
              <div class="col-xs-12 col-md-10">
                <input name="revform[0]" id="revform[0]" type="text" onblur="validate('revform[0]',/^.{1,50}$/,50)">
                <div class="genericerror">Invalid field</div>
                <div class="lengtherror">Maximum length 50</div>
              </div>
            </div>
            <div class="form-group">
              <label class="col-xs-12 col-md-2 control-label text-left" for="revform[1]">Review</label>
              <div class="col-xs-12 col-md-10">
                <textarea class="form-control" id="revform[1]" name="revform[1]" onblur="validate('revform[1]',/^(.|\n){1,500}$/,500)"></textarea>
                <div class="genericerror">Invalid field</div>
                <div class="lengtherror">Maximum length 500</div>
              </div>
            </div>
          </fieldset>
          <div class="col-xs-12 col-md-10 col-md-offset-2">
            <button type="submit" class="btn btn-info">Submit</button>
          </div>
        </form>
      </div>
    </div>
    <?php require_once 'footer.php'; ?>
  </body>

  </html>