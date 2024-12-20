<?php 
$title = "Profile";
include_once "layouts/header.php";
include_once "app/middleware/auth.php";
include_once "app/models/User.php";
include_once "app/requests/Validation.php";
$userObject = new User;
$userObject->setEmail($_SESSION['user']->email);
// if post "update_data".
if(isset($_POST['update_data'])) { 
    # Validation
    $errors = [];
    // Validation on first name field
    $fnameValidation = new Validation('First name',$_POST['first_name']);
    $fnameValidationResult = $fnameValidation->required();
    if(!empty($fnameValidationResult)) {
        $errors['errors']['first_name']['required'] = $fnameValidationResult;
    }
    // Validation on last name field
    $lnameValidation = new Validation('Last name',$_POST['last_name']);
    $lnameValidationResult = $lnameValidation->required();
    if(!empty($lnameValidationResult)) {
        $errors['errors']['last_name']['required'] = $lnameValidationResult;
    }
    // Validation on phone field
    $phoneValidation = new Validation('Phone',$_POST['phone']);
    $phoneValidationResult = $phoneValidation->required();
    if(!empty($phoneValidationResult)) {
        $errors['errors']['phone']['required'] = $phoneValidationResult;
    }
    // if no errors 
    # update user data in database
    $userObject->setFirst_name($_POST['first_name']);
    $userObject->setLast_name($_POST['last_name']);
    $userObject->setPhone($_POST['phone']);
    $userObject->setGender($_POST['gender']);
    if($_FILES['image']['error'] == 0) {
        // image exists
        # Validation on image size
        $maxUploadSize = 10**6;
        $maxUploadSizeByBytes = $maxUploadSize/(10**6);
        if($_FILES['image']['size'] > $maxUploadSize) {
            $errors['image-size'] = "Max upload size of image is: $maxUploadSizeByBytes";
        }
        # Validation on image extension
        $extension = pathinfo($_FILES['image']['name'],PATHINFO_EXTENSION);
        $availableExtensions = ['jpg','png','jpeg'];
        if(!in_array($extension,$availableExtensions)) {
            $errors['image-extension'] = "Allowed extensions of image are: (" . implode(", ",$availableExtensions) . ')';
        }
        if(empty($errors)) {
            // Save the image in server storage
            $imageName = uniqid() . "." . $extension; // wf82fe5ef5f5.jpg
            $imagePath = "assets/img/users/$imageName";
            move_uploaded_file($_FILES['image']['tmp_name'],$imagePath);
            // set image
            $userObject->setImage($imageName);
            $_SESSION['user']->image = $imageName;
        }
    }
    if(empty($errors)) {
        $result = $userObject->update();
        $_SESSION['user']->first_name = $_POST['first_name'];
        $_SESSION['user']->last_name = $_POST['last_name'];
        $_SESSION['user']->phone = $_POST['phone'];
        $_SESSION['user']->gender = $_POST['gender'];
        if($result) {
            $success = "<div class='alert alert-success text-center'>Your information has been updated successfully</div>";
        }else {
            $errors['some-wrong'] = "Something went wrong, Try again later";
        }
    }
}
$result = $userObject->getUserByEmail();
$user = $result->fetchObject();
include_once "layouts/nav.php";
include_once "layouts/breadcrumb.php";
// if post "update_password".
if(isset($_POST['update_password'])) {
    $key = "show";
    // Validation 
    # validation on old password => required, regex, correct in database
    $oldPasswordValidation = new Validation('Old password',$_POST['old_password']);
    $oldPasswordRequiredResult = $oldPasswordValidation->required();
    if(empty($oldPasswordRequiredResult)) {
        $passwordPattern = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,20}$/";
        $oldPasswordRegexResult = $oldPasswordValidation->regex($passwordPattern);
        if(empty($oldPasswordRegexResult)) {
            $hashOldPassword = sha1($_POST['old_password']);
            if($user->password != $hashOldPassword){
                $errorsPassword['old-password']['not-correct'] = "Password is not correct";
            }
        }else {
            $errorsPassword['old-password']['regex'] = $oldPasswordRegexResult;
        }
    }else {
        $errorsPassword['old-password']['required'] = $oldPasswordRequiredResult;
    }
    # validation on new password => required, regex, confirmed
    $newPasswordValidation = new Validation('New password',$_POST['new_password']);
    $newPasswordRequiredResult = $newPasswordValidation->required();
    if(empty($newPasswordRequiredResult)) {
        $passwordPattern = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,20}$/";
        $newPasswordRegexResult = $newPasswordValidation->regex($passwordPattern);
        if(empty($newPasswordRegexResult)) {
            if($_POST['old_password'] != $_POST['new_password']) {
                $newPasswordConfirmResult = $newPasswordValidation->confirmed($_POST['password_confirmation']);
                if(!empty($newPasswordConfirmResult)) {
                    $errorsPassword['new-password']['confirm'] = $newPasswordConfirmResult;
                }
            }else {
                $errorsPassword['new-password']['matching'] = "New password can't match the old password";
            }
        }else {
            $errorsPassword['new-password']['regex'] = $newPasswordRegexResult;
        }
    }else {
        $errorsPassword['new-password']['required'] = $newPasswordRequiredResult;
    }
    # validation on password confirm => required
    $passwordConfirmValidation = new Validation('Password confirm',$_POST['password_confirmation']);
    $passwordConfirmRequiredResult = $passwordConfirmValidation->required();
    if(!empty($passwordConfirmRequiredResult)) {
        $errorsPassword['password-confirm']['required'] = $passwordConfirmRequiredResult;
    }

    if(empty($errorsPassword)) {
        // update password in database
        $userObject->setPassword($_POST['new_password']);
        $updateResult = $userObject->updateOldPassword();
        $_SESSION['user']->password = sha1($_POST['new_password']);
        if($updateResult) {
            $successPassword = "<div class='alert alert-success text-center'>Password has been updated successfully</div>";
        }else {
            $errorsPassword['some-wrong'] = "Something went wrong, Try again later";
        }
    }
}
?>
<!-- my account start -->
<div class="checkout-area pb-80 pt-100">
    <div class="container">
        <div class="row">
            <div class="ml-auto mr-auto col-lg-9">
                <div class="checkout-wrapper">
                    <div id="faq" class="panel-group">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h5 class="panel-title"><span>1</span> <a data-toggle="collapse" data-parent="#faq"
                                href="#my-account-1">Edit your account information </a></h5>
                            </div>
                            <div id="my-account-1" class="panel-collapse collapse <?= isset($key) ? "" : "show" ?>">
                                <div class="panel-body">
                                    <div class="billing-information-wrapper">
                                        <div class="account-info-wrapper">
                                            <h4>My Account Information</h4>
                                            <h5>Your Personal Details</h5>
                                        </div>
                                        <?php 
                                            if(isset($success)) {echo $success;}
                                            if(!empty($errors)) {
                                                foreach($errors AS $key => $value) {
                                                    echo "<div class='alert alert-danger text-center'>$value</div>";
                                                }
                                            }
                                        ?>
                                        <form method="post" enctype="multipart/form-data">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="col-4 offset-4">
                                                        <img src="assets/img/users/<?= $user->image ?>" alt="" id="image" class="w-75 rounded-circle" style="cursor: pointer">
                                                        <input type="file" name="image" id="file" class="d-none">
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 col-md-6 mt-5">
                                                    <div class="billing-info">
                                                        <label>First Name</label>
                                                        <input type="text" name="first_name" value="<?= $user->first_name ?>">
                                                        <?php 
                                                            if(!empty($errors['errors']['first_name'])) {
                                                                foreach($errors['errors']['first_name'] AS $key => $value) {
                                                                    echo "<div class='alert alert-danger'>$value</div>";
                                                                }
                                                            }
                                                        ?>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 col-md-6 mt-5">
                                                    <div class="billing-info">
                                                        <label>Last Name</label>
                                                        <input type="text" name="last_name" value="<?= $user->last_name ?>">
                                                        <?php 
                                                            if(!empty($errors['errors']['last_name'])) {
                                                                foreach($errors['errors']['last_name'] AS $key => $value) {
                                                                    echo "<div class='alert alert-danger'>$value</div>";
                                                                }
                                                            }
                                                        ?>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 col-md-6">
                                                    <div class="billing-info">
                                                        <label>phone</label>
                                                        <input type="number" name="phone" value="<?= $user->phone ?>">
                                                        <?php 
                                                            if(!empty($errors['errors']['phone'])) {
                                                                foreach($errors['errors']['phone'] AS $key => $value) {
                                                                    echo "<div class='alert alert-danger'>$value</div>";
                                                                }
                                                            }
                                                        ?>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 col-md-6">
                                                    <div class="form-group">
                                                        <label for="gender">Gender</label>
                                                        <select name="gender" id="gender" class="form-control">
                                                            <option <?= $user->gender == "1" ? "selected" : "" ?> value="1">Male</option>
                                                            <option <?= $user->gender == "0" ? "selected" : "" ?> value="0">female</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="billing-back-btn">
                                                <div class="billing-btn">
                                                    <button type="submit" name="update_data">Update data</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h5 class="panel-title"><span>2</span> <a data-toggle="collapse" data-parent="#faq"
                                href="#my-account-2">Change your password </a></h5>
                            </div>
                            <div id="my-account-2" class="panel-collapse collapse <?= isset($key) ? "$key" : "" ?>">
                                <div class="panel-body">
                                    <div class="billing-information-wrapper">
                                        <div class="account-info-wrapper">
                                            <h4>Change Password</h4>
                                            <h5>Your Password</h5>
                                        </div>
                                        <?php 
                                            if(isset($successPassword)) {echo $successPassword;}
                                            if(!empty($errors)) {
                                                foreach($errorsPassword AS $key => $value) {
                                                    echo "<div class='alert alert-danger text-center'>$value</div>";
                                                }
                                            }
                                        ?>
                                        <form method="post">
                                            <div class="row">
                                                <div class="col-lg-12 col-md-12">
                                                    <div class="billing-info">
                                                        <label>Old password</label>
                                                        <input type="password" name="old_password">
                                                        <?php 
                                                            if(!empty($errorsPassword['old-password'])) {
                                                                foreach($errorsPassword['old-password'] AS $key => $value) {
                                                                    echo "<div class='alert alert-danger'>$value</div>";
                                                                }
                                                            }
                                                        ?>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12 col-md-12">
                                                    <div class="billing-info">
                                                        <label>New password</label>
                                                        <input type="password" name="new_password">
                                                    </div>
                                                    <?php 
                                                        if(!empty($errorsPassword['new-password'])) {
                                                            foreach($errorsPassword['new-password'] AS $key => $value) {
                                                                echo "<div class='alert alert-danger'>$value</div>";
                                                            }
                                                        }
                                                    ?>
                                                </div>
                                                <div class="col-lg-12 col-md-12">
                                                    <div class="billing-info">
                                                        <label>Password confirm</label>
                                                        <input type="password" name="password_confirmation">
                                                    </div>
                                                    <?php 
                                                        if(!empty($errorsPassword['password-confirm'])) {
                                                            foreach($errorsPassword['password-confirm'] AS $key => $value) {
                                                                echo "<div class='alert alert-danger'>$value</div>";
                                                            }
                                                        }
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="billing-back-btn">
                                                <div class="billing-btn">
                                                    <button type="submit" name="update_password">Update password</button>
                                                </div>
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
</div>

<!-- my account end -->
<?php 
include_once "layouts/footer.php";
include_once "layouts/footer-scripts.php";
?>

