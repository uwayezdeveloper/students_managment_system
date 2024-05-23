<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');

if(isset($_POST['submit'])) {
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    //echo $email." ".$mobile;
    $newpassword = md5($_POST['newpassword']);
    $sql = "SELECT Email FROM tbluser WHERE Email=:email AND MobileNumber=:mobile";
    $query = $dbh->prepare($sql);
    $query->bindParam(':email', $email, PDO::PARAM_STR);
    $query->bindParam(':mobile', $mobile, PDO::PARAM_STR);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_OBJ);
    var_dump($results);
    if($query->rowCount() > 0) {
        $con = "UPDATE tbluser SET Password=:newpassword WHERE Email=:email AND MobileNumber=:mobile";
        $chngpwd1 = $dbh->prepare($con);
        $chngpwd1->bindParam(':email', $email, PDO::PARAM_STR);
        $chngpwd1->bindParam(':mobile', $mobile, PDO::PARAM_STR);
        $chngpwd1->bindParam(':newpassword', $newpassword, PDO::PARAM_STR);
        $chngpwd1->execute();
        echo "<script>alert('Your Password has been successfully changed');</script>";
    } else {
       echo "<script>alert('Email ID or Mobile number is invalid');</script>"; 
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Student  Information System || Forgot Password</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="vendors/simple-line-icons/css/simple-line-icons.css">
    <link rel="stylesheet" href="vendors/flag-icon-css/css/flag-icon.min.css">
    <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
    <!-- endinject -->
    <!-- inject:css -->
    <link rel="stylesheet" href="css/style.css">
    <!-- endinject -->
    <script type="text/javascript">
    function valid() {
        if(document.chngpwd.newpassword.value != document.chngpwd.confirmpassword.value) {
            alert("New Password and Confirm Password fields do not match!");
            document.chngpwd.confirmpassword.focus();
            return false;
        }
        return true;
    }
    </script>
</head>
<body>
<div class="container-scroller">
    <div class="container-fluid page-body-wrapper full-page-wrapper">
        <div class="content-wrapper d-flex align-items-center auth">
            <div class="row flex-grow">
                <div class="col-lg-4 mx-auto">
                    <div class="auth-form-light text-left p-5">
                        <div class="brand-logo">
                            <img src="images/logo.svg">
                        </div>
                        <h4>RECOVER PASSWORD</h4>
                        <h6 class="font-weight-light">Enter your email address and mobile number to reset password!</h6>
                        <form class="pt-3" method="post" name="chngpwd" onSubmit="return valid();">
                            <div class="form-group">
                                <input type="email" class="form-control form-control-lg" placeholder="Email Address" required="true" name="email">
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control form-control-lg" name="mobile" placeholder="Mobile Number" required="true" maxlength="10" pattern="[0-9]+">
                            </div>
                            <div class="form-group">
                                <input class="form-control form-control-lg" type="password" name="newpassword" placeholder="New Password" required="true"/>
                            </div>
                            <div class="form-group">
                                <input class="form-control form-control-lg" type="password" name="confirmpassword" placeholder="Confirm Password" required="true" />
                            </div>
                            <div class="mt-3">
                                <button class="btn btn-success btn-block loginbtn" name="submit" type="submit">Reset</button>
                            </div>
                            <div class="my-2 d-flex justify-content-between align-items-center">
                                <a href="login.php" class="auth-link text-black">Sign In</a>
                            </div>
                            <div class="mb-2">
                                <a href="../index.php" class="btn btn-block btn-facebook auth-form-btn">
                                    <i class="icon-social-home mr-2"></i>Back Home
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- content-wrapper ends -->
    </div>
    <!-- page-body-wrapper ends -->
</div>
<!-- container-scroller -->
<!-- plugins:js -->
<script src="vendors/js/vendor.bundle.base.js"></script>
<!-- endinject -->
<!-- inject:js -->
<script src="js/off-canvas.js"></script>
<script src="js/misc.js"></script>
<!-- endinject -->
</body>
</html>
