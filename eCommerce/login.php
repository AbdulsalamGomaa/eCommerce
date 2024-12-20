<?php 
$title = "login";
include_once "layouts/header.php";
include_once "app/middleware/guest.php";
include_once "layouts/nav.php";
include_once "layouts/breadcrumb.php";
?>

<div class="login-register-area ptb-100">
    <div class="container">
        <div class="row">
            <div class="col-lg-7 col-md-12 ml-auto mr-auto">
                <div class="login-register-wrapper">
                    <div class="login-register-tab-list nav">
                        <a class="active" data-toggle="tab" href="#lg1">
                            <h4> login </h4>
                        </a>
                    </div>
                    <div class="tab-content">
                        <div id="lg1" class="tab-pane active">
                            <div class="login-form-container">
                                <div class="login-register-form">
                                    <form action="app/post/post_of_login.php" method="post">
                                        <input type="email" name="email" placeholder="Email">
                                        <?php 
                                            if(!empty($_SESSION['errors']['email'])) {

                                                foreach($_SESSION['errors']['email'] AS $key => $value) {
                                                    echo "<div class='alert alert-danger'>$value</div>";
                                                }
                                            }
                                        ?>
                                        <input type="password" name="password" placeholder="Password">
                                        <?php 
                                            if(!empty($_SESSION['errors']['password'])) {

                                                foreach($_SESSION['errors']['password'] AS $key => $value) {
                                                    echo "<div class='alert alert-danger'>$value</div>";
                                                }
                                            }
                                        ?>
                                        <div class="button-box">
                                            <div class="login-toggle-btn">
                                                <input type="checkbox" name="remember_me" id="remember_me">
                                                <label for="remember_me">Remember me</label>
                                                <a href="forget_password.php">Forgot Password?</a>
                                            </div>
                                            <button type="submit" name="login"><span>Login</span></button>
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
unset($_SESSION['errors']);
?>