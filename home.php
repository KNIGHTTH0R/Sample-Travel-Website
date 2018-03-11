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

?>
  <!DOCTYPE html>
  <html>

  <head>
    <title>Home</title>
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
  </head>

  <body>
    <?php require_once 'navbar.php'; ?>
    <div class="container">
      <div class="row">
      <div class="infobox postit-yellow col-xs-12 col-md-6">
        <div class="pin"></div>
        <div id="myCarousel-1" class="carousel slide" data-ride="carousel">
          <!-- Indicators -->
          <ol class="carousel-indicators">
            <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
            <li data-target="#myCarousel" data-slide-to="1"></li>
            <li data-target="#myCarousel" data-slide-to="2"></li>
            <li data-target="#myCarousel" data-slide-to="3"></li>
            <li data-target="#myCarousel" data-slide-to="4"></li>
          </ol>
          <!-- Wrapper for slides -->
          <div class="carousel-inner" role="listbox">
            <div class="item active">
              <img src="images/page1.jpg" alt="Image1">
            </div>
            <div class="item">
              <img src="images/page2.jpg" alt="Image2">
            </div>
            <div class="item">
              <img src="images/page3.jpg" alt="Image3">
            </div>
            <div class="item">
              <img src="images/page4.jpg" alt="Image4">
            </div>
            <div class="item">
              <img src="images/page5.jpg" alt="Image5">
            </div>
          </div>
          <a class="left carousel-control" href="#myCarousel-1" role="button" data-slide="prev">
            <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
          </a>
          <a class="right carousel-control" href="#myCarousel-1" role="button" data-slide="next">
            <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
          </a>
        </div>
      </div>
      <div class="infobox postit-yellow boxminheight font-override col-xs-12 col-md-5 col-md-offset-1">
        <div class="pin"></div>
        <h1>About us</h1>
        <div id="myCarousel-2" class="carousel slide" data-ride="carousel">
          <!-- Indicators -->
          <!-- Wrapper for slides -->
          <div class="carousel-inner" role="listbox">
            <div class="item active">
              <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. In euismod leo eget bibendum varius. In elementum, tellus sed consectetur ornare, felis leo dignissim eros, a vestibulum velit est non est. Praesent egestas, ex vitae convallis dapibus, enim nisl maximus nisl, et dignissim lectus ante ut quam. Donec augue lacus, commodo amet. </p>
            </div>
            <div class="item">
              <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus nec orci nec odio maximus pretium a vel mi. Sed euismod ornare enim at pretium. Maecenas lacinia quam mauris, in mollis orci gravida nec. Proin commodo enim et libero pharetra feugiat a in mauris. Donec sodales felis nunc, maximus condimentum turpis sodales ut. Vivamus eget ullamcorper ipsum. Curabitur pellentesque lacus vitae fringilla ullamcorper. Aliquam posuere dolor tellus, vulputate turpis duis.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
      <div class="infobox postit-yellow col-xs-12 col-md-3">
        <div class="pin"></div><div style="text-align: center;">
        <a href="manageforms.php?book=conference"><img src="conference.jpg" alt="" class="img-thumbnail img-circle"></a>
        <h1>Conferences</h1>
        <a href="manageforms.php?book=conference"><button type="button" class="btn btn-default btn-lg">Book now!</button></a>
      </div></div>
      <div class="infobox postit-yellow col-xs-12 col-md-3 col-md-offset-1">
        <div class="pin"></div><div style="text-align: center;">
        <a href="manageforms.php?book=stay"><img src="stay.jpg" alt="" class="img-thumbnail img-circle"></a>
        <h1>Hotel stays</h1>
        <a href="manageforms.php?book=stay"><button type="button" class="btn btn-default btn-lg">Book now!</button></a>
      </div></div>
      <div class="infobox postit-yellow col-xs-12 col-md-3 col-md-offset-1">
        <div class="pin"></div><div style="text-align: center;">
        <a href="manageforms.php?book=car"><img src="car.jpg" alt="" class="img-thumbnail img-circle"></a>
        <h1>Cars</h1>
        <a href="manageforms.php?book=car"><button type="button" class="btn btn-default btn-lg">Book now!</button></a>
      </div></div>
    </div>
    <?php require_once 'footer.php'; ?>
  </body>

  </html>