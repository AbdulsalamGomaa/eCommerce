<?php 
$title = "Verification code";
include_once "layouts/header.php";
include_once "app/middleware/guest.php";
if(empty($_SESSION['user_email'])) {
    header('location:login.php');die;
}
include_once "app/requests/Validation.php";
include_once "app/models/User.php";
// if get has query string
// validation on query string
$availablePages = ["register","forget"];
if($_GET) {
    // Check if key exists
    if(isset($_GET['page'])) {
        // Check if value is correct
        if(!in_array($_GET['page'],$availablePages)) {
            header("location:layouts/errors/404.php");die;
        }
    }else {
        header("location:layouts/errors/404.php");die;
    }
}else {
    header("location:layouts/errors/404.php");die;
}
if($_POST) {
    // code => post
    // email => session
    // validation
    // code => required , integer, digits = 5 , min = 10000 , max = 99999
    $verifyCode = new Validation("code",$_POST['code']);
    $codeRequiredResult = $verifyCode->required();
    if(empty($codeRequiredResult)) {        
        // code is not empty and exists
        $codeIntegerResult = $verifyCode->integers();
        if(empty($codeIntegerResult)) {
            // code is exists and integer
            $codeDigitsResult = $verifyCode->digits();
            if(empty($codeDigitsResult)) {
                // if no errors
                if(isset($_SESSION['success'])) {
                    $user = new user;
                    $user->setCode($_POST['code']);
                    $user->setEmail($_SESSION['user_email']);
                    $result = $user->verifyOfCode();
                    if($result) {
                        // correct code
                        // update email verified at and status
                        $user->setStatus(1);
                        date_default_timezone_set("Africa/Cairo");
                        $user->setEmail_verified_at(date("Y-m-d H:i:s"));
                        $updateResult = $user->makeUserVerified();
                        if($updateResult) {
                            if($_GET['page'] == 'register') { // if user coming from register page
                                // delete sessions
                                unset($_SESSION['user_email']);
                                unset($_SESSION['success']);
                                // header to login
                                header("location:login.php");die;

                            }elseif($_GET['page'] == 'forget') { // if user coming from forget password page
                                // header to reset password page
                                unset($_SESSION['success']);
                                header("location:reset_password.php");die;
                            }
                        }else {

                            $error = "<div class='alert alert-danger'>Something went wrong, try again later</div>";
                        }
                    }else {

                        $error = "<div class='alert alert-danger'>Code is wrong</div>";
                    }
                }

                // Verifying the account of user again after the first time
                if(isset($_SESSION['code'])) {
                    if($_SESSION['code'] == $_POST['code']) {
                        $userVerifyAgain = new user;
                        $userVerifyAgain->setCode($_SESSION['code']);
                        $userVerifyAgain->setEmail($_SESSION['user_email']);
                        $resultUpdateCode = $userVerifyAgain->updateOldCode();
                        if($resultUpdateCode) {
                            // code updated successfully
                            // update email verified at and status
                            $userVerifyAgain->setStatus(1);
                            date_default_timezone_set("Africa/Cairo");
                            $userVerifyAgain->setEmail_verified_at(date("Y-m-d H:i:s"));
                            $updateOfVerifyAgain = $userVerifyAgain->makeUserVerified();
                            if($updateOfVerifyAgain) {
                                unset($_SESSION['user_email']);
                                unset($_SESSION['code']);
                                // header to login
                                header("location:login.php");die;
                            }
                        }else {

                            $error = "<div class='alert alert-danger'>Something went wrong, try again later</div>";
                        }

                    }else {

                        $error = "<div class='alert alert-danger'>Code is wrong</div>";
                    }
                }
            }
        }
    }
}


?>

<div class="login-register-area ptb-100">
    <div class="container">
        <div class="row">
            <div class="col-lg-7 col-md-12 ml-auto mr-auto">
                <div class="login-register-wrapper">
                    <div class="login-register-tab-list nav">
                        <a class="active" data-toggle="tab" href="#lg1">
                            <h4> <?= $title ?> </h4>
                        </a>
                    </div>
                    <div class="tab-content">
                        <div id="lg1" class="tab-pane active">
                            <div class="login-form-container">
                                <div class="login-register-form">
                                    <form method="post">
                                        <input type="number" name="code" min="10000" max="99999"
                                            placeholder="Enter your verification code">
                                        <?= empty($codeRequiredResult) ? "" : "<div class='alert alert-danger'>$codeRequiredResult</div>" ?>
                                        <?= empty($codeIntegerResult) ? "" : "<div class='alert alert-danger'>$codeIntegerResult</div>" ?>
                                        <?= empty($codeDigitsResult) ? "" : "<div class='alert alert-danger'>$codeDigitsResult</div>" ?>
                                        <?= isset($error) ? $error : "" ?>
                                        <div class="button-box">
                                            <button type="submit"><span>Verify</span></button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>