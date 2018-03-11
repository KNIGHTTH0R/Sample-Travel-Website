<?php
if (isset($_SESSION) === false) {
    session_start();
}

header('Content-Type: text/html; charset=utf-8');

require_once 'Helper.inc';
$help = new Helper();
if ($help->isLoggedIn() === true) {
    header('Location: home.php');
    exit;
}
?>
  <!DOCTYPE html>
  <html lang="en">

  <head>
    <title>Register</title>
    <!-- for-mobile-apps -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
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
    <script type="text/javascript">
    var uniqueList = {'1':0, '2':0};
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

    function checkUnique(id, regex, fieldno) {
      if (validate(id, regex)) {
        var field = document.getElementById(id);
        $.post("unique.php", { input: field.value, field: fieldno }).done(function(data) {
          if (data == '0') {
            field.parentElement.parentElement.className = 'form-group field-error unique';
            uniqueList[fieldno] = 0;
            return false;
          } else {
            field.parentElement.parentElement.className = 'form-group';
            uniqueList[fieldno] = 1;
            return true;
          }
        });
      } else {
        uniqueList[fieldno] = 0;
        return false;
      }
    }

    function checkKey() {
      if (validate('regform[6]', /^.{8}$/)) {
        var field = document.getElementById('regform[6]');
        $.post("key.php", { key: field.value }).done(function(data) {
          if (data == '0') {
            field.parentElement.parentElement.className = 'form-group field-error';
            document.getElementById('keydiv').value = '';
            return false;
          } else {
            field.parentElement.parentElement.className = 'form-group validkey';
            document.getElementById('keydiv').innerHTML = 'Company name: ' + data;
            return true;
          }
        });
      } else {
        document.getElementById('keydiv').value = '';
        return false;
      }
    }

    function validateForm() {
      flag = true;
      flag &= validate('regform[0]', /^.{1,50}$/, 50);
      flag &= uniqueList['1'];
      console.log(flag);
      flag &= uniqueList['2'];
      console.log(flag);
      flag &= validate('regform[3]', /^((\+|00|0)?[1-9]{2}|0)?[1-9]( ?[0-9]){8,12}$/);
      flag &= validate('regform[4]', /^.{8,}$/);
      if (flag == 1) {
        var choice_elems = document.getElementsByName('regform[5]');
        var choice = null;
        for(var i = 0; i < choice_elems.length; i++){
            if(choice_elems[i].checked){
                choice = choice_elems[i].value;
                break;
            }
        }
        if (choice == 'yes') {
          flag &= validate('regform[6]', /^.{8}$/);
          return (flag == 1);
        } else if (choice == 'no') {
          flag &= validate('regform[7]', /^.{1,50}$/, 50);
          flag &= validate('regform[8]', /^.{1,250}$/, 250);
          flag &= validate('regform[9]', /^.{1,50}$/, 50);
          flag &= validate('regform[10]',/^.{1,50}$/,50);
          return (flag == 1);
        } else {
          return false;
        }
      } else {
        return false;
      }
    }

    $(document).ready(function() {
      $("#regform\\[5\\]\\[0\\]").click(function() {
        $('#companysection').fadeIn();
        $("#compnr1, #compnr2, #compnr3, #compnr4").fadeOut();
        $("#compr1").fadeIn();
      });
      $('#regform\\[5\\]\\[1\\]').click(function() {
        $('#companysection').fadeIn();
        $("#compr1").fadeOut();
        $("#compnr1, #compnr2, #compnr3, #compnr4").fadeIn();
      });
    });
    </script>
  </head>

  <body>
        <?php require_once 'navbar.php'; ?>
    <div class="container infobox radio-style">
        <?php
        $error = $help->getError();
        if ($error !== false) {
    ?>
              <div class="row">
                <div class="col-xs-12 col-md-4">
                  <div class="alert alert-danger alert-dismissable">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <strong>Error!</strong>
                    <?php echo $error; ?>
                  </div>
                </div>
              </div>
        <?php } ?>
      <form class="form-horizontal" method="post" accept-charset="utf-8" action="regform.php" onsubmit="return validateForm()">
        <br>
          <fieldset>
            <legend>User Registration</legend>
            <div class="form-group">
              <label class="col-xs-12 col-md-2 control-label text-left" for="regform[0]">Your name</label>
              <div class="col-xs-12 col-md-10">
                <input name="regform[0]" id="regform[0]" type="text" placeholder="Name" onblur="validate('regform[0]',/^.{1,50}$/,50)">
                <div class="genericerror">Invalid name</div>
                <div class="lengtherror">Maximum length 50</div>
              </div>
            </div>
            <div class="form-group">
              <label class="col-xs-12 col-md-2 control-label text-left" for="regform[1]">Account username</label>
              <div class="col-xs-12 col-md-10">
                <input name="regform[1]" id="regform[1]" type="text" placeholder="Username" onblur="validate('regform[1]',/^[a-zA-Z0-9_]{1,20}$/,20)" onchange="checkUnique('regform[1]', /^[a-zA-Z0-9_]{1,20}$/, '1') ">
                <div class="genericerror">Invalid username</div>
                <div class="charerror">Only alphanumeric characters and underscore allowed</div>
                <div class="lengtherror">Maximum length 20</div>
                <div class="uniqueerror">Username taken</div>
              </div>
            </div>
            <div class="form-group">
              <label class="col-xs-12 col-md-2 control-label text-left" for="regform[2]">Your email</label>
              <div class="col-xs-12 col-md-10">
                <input name="regform[2]" id="regform[2]" type="email" placeholder="email@example.com" onblur="validate('regform[2]',/^.+$/)" onchange="checkUnique('regform[2]', /^.+$/, '2') ">
                <div class="genericerror">Invalid email</div>
                <div class="uniqueerror">Email taken</div>
              </div>
            </div>
            <div class="form-group">
              <label class="col-xs-12 col-md-2 control-label text-left" for="regform[3]">Your contact number</label>
              <div class="col-xs-12 col-md-10">
                <input name="regform[3]" id="regform[3]" type="tel" placeholder="Contact number" onblur="validate('regform[3]',/^((\+|00|0)?[1-9]{2}|0)?[1-9]( ?[0-9]){8,12}$/)">
                <div class="genericerror">Invalid contact number</div>
              </div>
            </div>
            <div class="form-group">
              <label class="col-xs-12 col-md-2 control-label text-left" for="regform[4]">Account password</label>
              <div class="col-xs-12 col-md-10">
                <input name="regform[4]" id="regform[4]" type="password" onblur="validate('regform[4]',/^.{8,}$/)">
                <div class="genericerror">Minimum length 8</div>
              </div>
            </div>
            <div class="form-group">
              <label class="col-xs-12 col-md-2 control-label text-left" for="regform[5]">Company already registered?</label>
              <div class="col-xs-12 col-md-10">
                <div class="radio">
                  <input name="regform[5]" id="regform[5][0]" value="yes" type="radio">
                  <label for="regform[5][0]">Yes
                  </label>&nbsp;&nbsp;&nbsp;&nbsp;
                  <input name="regform[5]" id="regform[5][1]" value="no" type="radio">
                  <label for="regform[5][1]">No
                  </label>
                </div>
              </div>
            </div>
          </fieldset>
          <fieldset id="companysection">
            <legend>Company details</legend>
            <div class="form-group" id="compr1">
              <label class="col-xs-12 col-md-2 control-label text-left" for="regform[6]">Company key</label>
              <div class="col-xs-12 col-md-10">
                <input name="regform[6]" id="regform[6]" type="text" onblur="validate('regform[6]',/^.{8}$/)" onchange="checkKey()">
                <div class="genericerror">Invalid key</div>
                <div id="keydiv"></div>
              </div>
            </div>
            <div class="form-group" id="compnr1">
              <label class="col-xs-12 col-md-2 control-label text-left" for="regform[7]">Company name</label>
              <div class="col-xs-12 col-md-10">
                <input name="regform[7]" id="regform[7]" type="text" placeholder="Name" onblur="validate('regform[7]',/^.{1,50}$/,50)">
                <div class="genericerror">Invalid company name</div>
                <div class="lengtherror">Maximum length 50</div>
              </div>
            </div>
            <div class="form-group" id="compnr2">
              <label class="col-xs-12 col-md-2 control-label text-left" for="regform[8]">Company address</label>
              <div class="col-xs-12 col-md-10">
                <textarea name="regform[8]" id="regform[8]" class="form-control" placeholder="Address" onblur="validate('regform[8]',/^.{1,250}$/,250)"></textarea>
                <div class="genericerror">Invalid company address</div>
                <div class="lengtherror">Maximum length 250</div>

              </div>
            </div>
            <div class="form-group" id="compnr3">
              <label class="col-xs-12 col-md-2 control-label text-left" for="regform[9]">Industry</label>
              <div class="col-xs-12 col-md-10">
                <input name="regform[9]" id="regform[9]" type="text" placeholder="Industry" onblur="validate('regform[9]',/^.{1,50}$/,50)">
                <div class="genericerror">Invalid company industry</div>
                <div class="lengtherror">Maximum length 50</div>
              </div>
            </div>
            <div class="form-group" id="compnr4">
              <label class="col-xs-12 col-md-2 control-label text-left" for="regform[10]">Company Key</label>
              <div class="col-xs-12 col-md-10">
                <input name="regform[10]" id="regform[10]" type="text" onfocus="document.getElementById('keyhint').style.display='block';" onblur="document.getElementById('keyhint').style.display='none';validate('regform[10]',/^.{1,8}$/,8);">
                <div class="genericerror">Invalid company key</div>
                <div class="lengtherror">Maximum length 8</div>                
                <div id="keyhint" style="display: none;">This key can be used to register other Employees</div>
              </div>
            </div>
            <div class="col-xs-12 col-md-10 col-md-offset-2">
              <button type="submit" class="btn btn-info">Submit</button>
            </div>
          </fieldset>
      </form>
    </div>
    <?php require_once 'footer.php'; ?>
  </body>

  </html>