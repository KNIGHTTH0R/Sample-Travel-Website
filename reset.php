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

if ($helper->isLoggedIn() === true) {
    header('Location: home.php');
    exit;
}

$keystring  = $_GET['id'];
if (strlen($resetid) !== 16) {
    header('Location: home.php');
    exit;
}

if ($db->isUnique('Forgotlog', 'keystring', 's', $keystring) === true) {
    header('Location: home.php');
    exit;
}

?>

<!DOCTYPE html>
  <html>

  <head>
    <title>Reset password</title>
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
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    <script>
    function checkValid() {
      var password = document.getElementById('password').value;
      if(password.length > 8) {
        document.getElementById('pass1').className = 'form-group field-error';
        return false;
      }
      document.getElementById('pass1').className = 'form-group';
      return true;
    }

    function checkSame() {
      var password = document.getElementById('password').value;
      var cpassword = document.getElementById('confpassword').value;
      if (password.value !== cpassword.value) {
        document.getElementById('pass2').className = 'form-group field-error';
        return false;
      }
      document.getElementById('pass2').className = 'form-group';
      return true;
    }
    function validateReset() {
      return (checkValid() && checkSame());
    }
    </script>
  </head>

  <body>
    <?php require_once 'navbar.php'; ?>
    <div class="container">
      <div class="row">
        <div class="col-xs-12 col-md-6 col-md-offset-6 infobox radio-style">
          <form method="post" action="resetform.php" accept-charset="utf-8" onsubmit="return validateReset();">
            <fieldset>
              <legend>Login</legend>
              <?php if(isset($_SESSION) && isset($_SESSION['error'])) {
                echo "<div style=\"color: red;margin-bottom: 15px;\">{$_SESSION['error']}</div>";
                unset($_SESSION['error']);
              }
              ?>
              <input name="keystring" type="hidden" value="<?php echo $keystring; ?>">
              <div class="form-group" id="pass1">
                <div class="row">
                  <label class="col-xs-12 col-md-3 control-label text-left" onblur="checkValid();checkSame();" for="password">Password</label>
                  <div class="col-xs-12 col-md-9">
                    <input name="password" id="password" type="password">
                    <div class="genericerror">Minimum length 8</div>
                  </div>
                </div>
              </div>
              <div class="form-group" id="pass2">
                <div class="row">
                  <label class="col-xs-12 col-md-3 control-label text-left" onblur="checkSame();" for="confpassword">Confirm password</label>
                  <div class="col-xs-12 col-md-9">
                    <input id="confpassword" type="password">
                    <div class="genericerror">Passwords do not match</div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-xs-12 col-md-9 col-md-offset-3">
                  <button type="submit" class="btn btn-info">Login</button>
                </div>
              </div>
            </fieldset>
          </form>
        </div>
      </div>
    </div>
    <?php require_once 'footer.php'; ?>
  </body>

  </html>