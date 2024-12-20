<?php
$title = "register";
include_once "layouts/header.php";
include_once "layouts/nav.php";
include_once "layouts/breadcrumb.php";
include_once "app/requests/Validation.php";
include_once "app/models/User.php";
include_once __DIR__."\app\services\mail.php";

if($_POST) {
    // Validation rules
    # first_name => required,string
    # last_name => required,string
    # gender => required,['1','0']
    # email => required,regular expression,unique
    # phone => required,regular expression,unique
    # password => required,regular expression, = password_confirmation

    // Validation on email
    $success = [];
    $emailValidation = new Validation("email",$_POST['email']);
    $emailRequiredResult = $emailValidation->required();
    $emailPattern = "/^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/";
    if(empty($emailRequiredResult)) {
        $emailRegexResult = $emailValidation->regex($emailPattern);
        if(empty($emailRegexResult)) {
            $emailUniqueResult = $emailValidation->unique('users');
            if(empty($emailUniqueResult)) {
                // no email validation errors
                $success['email'] = "email";
            }
        }
    }

    // Validation on phone
    $phoneValidation = new Validation("phone",$_POST['phone']);
    $phoneRequiredResult = $phoneValidation->required();
    $phonePattern = "/^01[0-2,5]{1}[0-9]{8}$/";
    if(empty($phoneRequiredResult)) {
        $phoneRegexResult = $phoneValidation->regex($phonePattern);
        if(empty($phoneRegexResult)) {
            $phoneUniqueResult = $phoneValidation->unique('users');
            if(empty($phoneUniqueResult)) {
                // no phone validation errors
                $success['phone'] = "phone";
            }
        }
    }

    // Validation on password
    $passwordValidation = new Validation("password",$_POST['password']);
    $passwordRequiredResult = $passwordValidation->required();
    if(empty($passwordRequiredResult)) {
        $passwordPattern = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,20}$/";
        $passwordRegexResult = $passwordValidation->regex($passwordPattern);
        if(empty($passwordRegexResult)) {
            // Validation on password confirmed field
            $passwordConfirmedResult = $passwordValidation->confirmed($_POST['password_confirmation']);
            if(empty($passwordConfirmedResult)) {
                // no password validation errors
                $success['password'] = "password";
            }
        }
    }

    if(isset($success['email']) && isset($success['phone']) && isset($success['password'])) {

        // insert user
        $user = new user;
        $user->setFirst_name($_POST['first_name']);
        $user->setLast_name($_POST['last_name']);
        $user->setEmail($_POST['email']);
        $user->setPhone($_POST['phone']);
        $user->setGender($_POST['gender']);
        $user->setPassword($_POST['password']); // Password with hashing from setPassword() function //
        $code = rand(10000,99999); // Generate code //
        $user->setCode($code);
        $result = $user->create();
        // Result if true or false
        if($result) {
            // send mail with code to user
            # mail to: => $_POST['email']
            # mail from: => abdulsalammoahmed618@gamail.com
            # mail subject: => verification code
            # mail body: => Hello => name , Your verification code is: => 123456 <br> Thank you
            $subject = "Verification code";
            $body = "Hello {$_POST['first_name']} {$_POST['last_name']} <br> :Your verification code is <br>$code <br> .Thank you";
            $mail = new mail($_POST['email'],$subject,$body);
            $mailResult = $mail->send();
            if($mailResult) {
                // store email in session
                $_SESSION['user_email'] = $_POST['email'];
                $_SESSION['success'] = 'success';
                // header to verification code page
                header('location:verification_code.php?page=register');die;
            }else {

                $error = "<div class='alert alert-danger text-center'>Something went wrong, try again later</div>";
            }

        }else {

            $error = "<div class='alert alert-danger'>Something went wrong, try again later</div>";
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
                        <a class="active" data-toggle="tab" href="#lg2">
                            <h4> register </h4>
                        </a>
                    </div>
                    <div class="tab-content">
                        <div id="lg2" class="tab-pane active">
                            <div class="login-form-container">
                                <div class="login-register-form">
                                    <form action="" method="post">
                                        <?= (isset($error)) ? $error : "" ?>
                                        <input type="text" name="first_name" placeholder="First name" value="<?= (isset($_POST['first_name'])) ? $_POST['first_name'] : "" ?>">
                                        <input type="text" name="last_name" placeholder="Last name" value="<?= (isset($_POST['last_name'])) ? $_POST['last_name'] : "" ?>">

                                        <input type="password" name="password" placeholder="Password">
                                        <?= empty($passwordRequiredResult) ? "" : "<div class='alert alert-danger'>$passwordRequiredResult</div>"; ?>
                                        <?= empty($passwordRegexResult) ? "" : "<div class='alert alert-danger'>$passwordRegexResult, Minimum eight and maximum 20 characters, at least one uppercase letter, one lowercase letter, one number and one special character</div>" ?>
                                        <input type="password" name="password_confirmation" placeholder="Password confirmation">
                                        <?= empty($passwordRequiredConfirmedValidation) ? "" : "<div class='alert alert-danger'>Password confirmation is required</div>"; ?>
                                        <?= empty($passwordConfirmedResult) ? "" : "<div class='alert alert-danger'>$passwordConfirmedResult</div>"; ?>
                                        <input type="email" name="email" placeholder="Email" value="<?= (isset($_POST['email'])) ? $_POST['email'] : "" ?>">
                                        <?= empty($emailRequiredResult) ? "" : "<div class='alert alert-danger'>$emailRequiredResult</div>"; ?>
                                        <?= empty($emailRegexResult) ? "" : "<div class='alert alert-danger'>$emailRegexResult</div>"; ?>
                                        <?= empty($emailUniqueResult) ? "" : "<div class='alert alert-danger'>$emailUniqueResult</div>"; ?>
                                        <input type="number" name="phone" placeholder="Phone" value="<?= (isset($_POST['phone'])) ? $_POST['phone'] : "" ?>">
                                        <?= empty($phoneRequiredResult) ? "" : "<div class='alert alert-danger'>$phoneRequiredResult</div>"; ?>
                                        <?= empty($phoneRegexResult) ? "" : "<div class='alert alert-danger'>$phoneRegexResult</div>"; ?>
                                        <?= empty($phoneUniqueResult) ? "" : "<div class='alert alert-danger'>$phoneUniqueResult</div>"; ?>
                                        <select name="gender" class="form-control" id="">
                                            <option <?= (isset($_POST['gender']) && $_POST['gender'] == "1") ? "selected" : "" ?> value="1">Male</option>
                                            <option <?= (isset($_POST['gender']) && $_POST['gender'] == "0") ? "selected" : "" ?> value="0">Female</option>
                                        </select>
                                        <div class="button-box mt-5">
                                            <button type="submit"><span>Register</span></button>
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
include_once "layouts/footer.php";
include_once "layouts/footer-scripts.php";
?>