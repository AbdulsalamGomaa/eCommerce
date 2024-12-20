<?php

// allow the guests and restrict the users
if(!empty($_SESSION['user'])) {

    header("location:home.php");die;
}