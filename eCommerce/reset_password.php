<?php 
$title = "Reset password";
include_once "layouts/header.php";
include_once "app/middleware/guest.php";
if(empty($_SESSION['user_email'])) {
    header('location:login.php');die;
}
include_once "app/requests/Validation.php";
include_once "app/models/User.php";
if($_POST) {
    // validation
    # password => required , regex
    # password confirmed => required , confirmed
    // Validation on password field
    $errors = [];
    $passwordValidation = new Validation('password',$_POST['password']);
    $passwordRequiredResult = $passwordValidation->required();
    if(empty($passwordRequiredResult)) {
        $passwordPattern = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,20}$/";
        $passwordRegexResult = $passwordValidation->regex($passwordPattern);
        if(empty($passwordRegexResult)) {
            // Validation on password confirm field if confirmed with password field
            $passwordConfirmedResult = $passwordValidation->confirmed($_POST['password_confirmation']);
            if(!empty($passwordConfirmedResult)) {
                $errors['password']['confirm'] = $passwordConfirmedResult;
            }
        }else {
            $errors['password']['regex'] = "$passwordRegexResult, Minimum eight and maximum 20 characters, at least one uppercase letter, one lowercase letter, one number and one special character";
        }
    }else {
        $errors['password']['required'] = $passwordRequiredResult;
    }

    // Validation on password confirm field that it required
    $ConfirmPasswordValidation = new Validation('Password confirm',$_POST['password_confirmation']);
    $confirmPasswordRequiredResult = $ConfirmPasswordValidation->required();
    if(!empty($confirmPasswordRequiredResult)) {
        $errors['password_confirmation']['required'] = $confirmPasswordRequiredResult;
    }

    if(empty($errors)) {
        // update the old password to the new password in database
        $userObject = new User;
        $userObject->setEmail($_SESSION['user_email']);
        $userObject->setPassword($_POST['password']);
        $result = $userObject->updateOldPassword();
        if($result) {
            unset($_SESSION['user_email']);
            // set success message
            $success = "Your password has been updated successfully";
            // header to login page
            header("refresh: 3; url=login.php");
        }else {
            $errors['some-wrong']['error'] = "Something went wrong, Try again later";
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
                                    <?php if(isset($success)) {
                                        echo "<div class='alert alert-success text-center'>$success</div>";
                                    } ?>
                                    <form method="post">
                                        <input type="password" name="password" placeholder="Password">
                                        <?php
                                            if(!empty($errors['password'])) {
                                                foreach($errors['password'] AS $key => $value) {
                                                    echo "<div class='alert alert-danger'>$value</div>";
                                                }
                                            }
                                        ?>
                                        <input type="password" name="password_confirmation"
                                            placeholder="Password confirm">
                                        <?php
                                            if(!empty($errors['password_confirmation'])) {
                                                foreach($errors['password_confirmation'] AS $key => $value) {
                                                    echo "<div class='alert alert-danger'>$value</div>";
                                                }
                                                if(!empty($errors['some-wrong'])) {
                                                    foreach($errors['some-wrong'] AS $key => $value) {
                                                        echo "<div class='alert alert-danger'>$value</div>";
                                                    } 
                                                }
                                            }
                                        ?>
                                        <div class="button-box">
                                            <button type="submit" name="reset_password"><span>Reset</span></button>
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

<?php 
include_once "layouts/footer-scripts.php";
?>