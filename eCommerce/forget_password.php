<?php 
$title = "Forget password";
include_once "layouts/header.php";
include_once "app/middleware/guest.php";
include_once "app/requests/Validation.php";
include_once "app/models/User.php";
include_once "app/services/mail.php";

if($_POST) {
    // validation
    # email => required, regex
    $errors = [];
    $emailValidation = new Validation('email',$_POST['email']);
    $emailRequiredResult = $emailValidation->required();
    if(empty($emailRequiredResult)) {
        
        $emailPattern = "/^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/";
        $emailRegexResult = $emailValidation->regex($emailPattern);
        if(!empty($emailRegexResult)) {
            $errors['email_regex'] = $emailRegexResult;
        }
    }else {
        $errors['email_required'] = $emailRequiredResult;
    }
    if(empty($errors)) {
        // search on email in database
        $userObject = new user;
        $userObject->setEmail($_POST['email']);
        $result = $userObject->getUserByEmail();
        if($result) { # if email is exist => generate code, send code, header to verification code page
            $user = $result->fetchObject(); // fetch the returned row of user to data
            // generate code
            $code = rand(10000,99999);
            $userObject->setCode($code);
            $updateResult = $userObject->updateOldCode();
            if($updateResult) {
                // send code
                $subject = "Forget password";
                $body = "Hello {$user->first_name} {$user->last_name} <br> :Your forget password is <br>$code <br> .Thank you";
                $mail = new mail($_POST['email'],$subject,$body);
                $mailResult = $mail->send();
                if($mailResult) {
                    // store email in session
                    $_SESSION['user_email'] = $_POST['email'];
                    $_SESSION['success'] = 'success';
                    // header to verification code page
                    header('location:verification_code.php?page=forget');die;
                }else {
                    $errors['some_wrong'] = "Something went wrong, try again later";
                }
            }else {
                $errors['some_wrong'] = "Something went wrong, try again later";
            }
        }else {
            # if email not exists => This email is not exists
            $errors['email_wrong'] = "Wrong email, this email doesn't exist";
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
                            <h4> Verification email </h4>
                        </a>
                    </div>
                    <div class="tab-content">
                        <div id="lg1" class="tab-pane active">
                            <div class="login-form-container">
                                <div class="login-register-form">
                                    <form method="post">
                                        <input type="email" name="email" placeholder="Enter your email address">
                                        <?php 
                                            if(!empty($errors)) {
                                                foreach($errors AS $key => $value) {
                                                
                                                    echo "<div class='alert alert-danger'>$value</div>";
                                                }
                                            }
                                        ?>
                                        <div class="button-box">
                                            <button type="submit"><span>Verify Email</span></button>
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