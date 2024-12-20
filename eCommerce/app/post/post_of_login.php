<?php
session_start();
if(!isset($_POST['login'])) {
    
    header("location:../../layouts/errors/404.php");die;
}

include_once "../requests/Validation.php";
include_once "../models/User.php";
include_once "../services/mail.php";
// Validation
# email => required , regex
$emailValidation = new Validation('email',$_POST['email']);
$emailRequiredResult = $emailValidation->required();
if(empty($emailRequiredResult)) {
    
    $emailPattern = "/^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/";
    $emailRegexResult = $emailValidation->regex($emailPattern);
    if(!empty($emailRegexResult)) {
        $_SESSION['errors']['email']['regex'] = $emailRegexResult;
    }
}else {
    $_SESSION['errors']['email']['required'] = $emailRequiredResult;
}
# password => required , regex
$passwordValidation = new Validation('password',$_POST['password']);
$passwordRequiredResult = $passwordValidation->required();
if(empty($passwordRequiredResult)) {

    $passwordPattern = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,20}$/";
    $passwordRegexResult = $passwordValidation->regex($passwordPattern);
    if(!empty($passwordRegexResult)) {
        $_SESSION['errors']['password']['regex'] = $passwordRegexResult;
    }
}else {
    $_SESSION['errors']['password']['required'] = $passwordRequiredResult;
}

// if no errors
if(empty($_SESSION['errors'])) {
    # search in database
    $userObject = new User;
    $userObject->setEmail($_POST['email']);
    $userObject->setPassword($_POST['password']);
    $result = $userObject->login();
    if($result) {
        // if the returned result is correct
        $user = $result->fetchObject();
        if($user->status == 1) {
            // user is verified in database
            // header to home page
            if($_POST['remember_me']) {
                
                // Set cookie for the person who selected remember me
                setcookie('remember_me',$_POST['email'],time() + (24*60*60) * 30 * 12,'/');
            }
            $_SESSION['user'] = $user;
            header("location:../../home.php");die;
            
        }elseif($user->status == 0) {
            // user is not verified in database
            // header to Verification code page
            $code = rand(10000,99999); // Generate code //
            $userObject->setCode($code);
            $subject = "Verification code";
            $body = "Hello {$user->first_name} {$user->last_name} <br> :Your verification code is <br>$code <br> .Thank you";
            $mail = new mail($_POST['email'],$subject,$body);
            $mailResult = $mail->send();
            if($mailResult) {
                // store email And code in session
                $_SESSION['user_email'] = $_POST['email'];
                $_SESSION['code'] = $code;
                // header to verification code page
                header("location:../../verification_code.php");die;
            }
        }else {
            // 2 Blocked member
            $_SESSION['errors']['password']['blocked'] = "Sorry, Your account has been blocked";
        }
        // if wrong email or password
    }else {
        # display error message
        $_SESSION['errors']['password']['wrong'] = "Wrong email or password";
    }
}
header("location:../../login.php");die;




