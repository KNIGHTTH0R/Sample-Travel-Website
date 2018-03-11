<?php
if (isset($_SESSION) === false) {
    session_start();
}

header('Content-Type: text/html; charset=utf-8');

if (isset($_SESSION['username']) === true) {
    unset($_SESSION['username']);
} if (isset($_SESSION['compkey']) === true) {
    unset($_SESSION['compkey']);
}

header('Location: home.php');
