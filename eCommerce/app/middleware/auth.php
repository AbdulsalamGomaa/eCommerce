<?php

// allow the users and restrict the guests
if(empty($_SESSION['user'])) {

    header("location:login.php");die;
}