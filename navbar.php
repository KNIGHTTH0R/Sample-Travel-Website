<!-- <a href="https://www.freepik.com/free-photos-vectors/background">Background image created by Yingyang - Freepik.com</a> -->
<!-- <a href="https://www.freepik.com/free-photos-vectors/background">Background image created by Kues - Freepik.com</a> -->
<?php
if (isset($_SESSION) === false) {
    session_start();
}
?>
<nav class="navbar navbar-default">
	<?php if ((isset($_SESSION) === true) && (isset($_SESSION['username']) === true)) { ?>
	<div class="navbar-header">
		<button type="button" data-target="#navbarCollapse" data-toggle="collapse" class="navbar-toggle">
			<span class="sr-only">Toggle navigation</span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		</button>
		<a href="home.php"><img src="logo2.jpg"></a>
	</div>
	<div id="navbarCollapse" class="collapse navbar-collapse">
		<ul class="nav navbar-nav">
			<li <?php if($_SERVER['PHP_SELF'] === "/tj/home.php") { echo 'class="active"';}?>><a href="home.php">Home</a></li>
			<li <?php if($_SERVER['PHP_SELF'] === "/tj/manageforms.php") { echo 'class="active"';}?>><a href="manageforms.php">Forms</a></li>
			<li <?php if($_SERVER['PHP_SELF'] === "/tj/review.php") { echo 'class="active"';}?>><a href="review.php">Review</a></li>
			<li <?php if($_SERVER['PHP_SELF'] === "/tj/logoutform.php") { echo 'class="active"';}?>><a href="logoutform.php">Logout</a></li>
		</ul>
	</div>
	<?php } else { ?>
	<div class="navbar-header">
		<button type="button" data-target="#navbarCollapse" data-toggle="collapse" class="navbar-toggle">
			<span class="sr-only">Toggle navigation</span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		</button>
		<a href="home.php"><img src="logo2.jpg"></a>
	</div>
	<div id="navbarCollapse" class="collapse navbar-collapse">
		<ul class="nav navbar-nav">
			<li <?php if($_SERVER['PHP_SELF'] === "/tj/home.php") { echo 'class="active"';}?>><a href="home.php">Home</a></li>
			<li <?php if($_SERVER['PHP_SELF'] === "/tj/register.php") { echo 'class="active"';}?>><a href="register.php">Register</a></li>
			<li <?php if($_SERVER['PHP_SELF'] === "/tj/login.php") { echo 'class="active"';}?>><a href="login.php">Login</a></li>
		</ul>
	</div>
	<?php } ?>
</nav>