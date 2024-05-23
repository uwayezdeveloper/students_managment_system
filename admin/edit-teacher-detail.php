<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');
if (strlen($_SESSION['sturecmsaid']==0)) {
  header('location:logout.php');
} else {
  if(isset($_POST['submit'])) {
    $teachername = $_POST['teachername'];
    $teacheremail = $_POST['teacheremail'];
    $gender = $_POST['gender'];
    $dob = $_POST['dob'];
    $mobileNumber = $_POST['mobileNumber'];
    $altconnum = $_POST['altconnum'];
    $address = $_POST['address'];
    $eid = $_GET['editid'];

    $sql = "UPDATE tbluser SET Name=:teachername, Email=:teacheremail, Gender=:gender, DOB=:dob, MobileNumber=:mobileNumber, Address=:address WHERE ID=:eid";
    $query = $dbh->prepare($sql);
    $query->bindParam(':teachername', $teachername, PDO::PARAM_STR);
    $query->bindParam(':teacheremail', $teacheremail, PDO::PARAM_STR);
    $query->bindParam(':gender', $gender, PDO::PARAM_STR);
    $query->bindParam(':dob', $dob, PDO::PARAM_STR);
    $query->bindParam(':mobileNumber', $mobileNumber, PDO::PARAM_STR);
    $query->bindParam(':address', $address, PDO::PARAM_STR);
    $query->bindParam(':eid', $eid, PDO::PARAM_STR);
    $query->execute();
    echo '<script>alert("Teacher details have been updated")</script>';
  }
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Student  Information System || Update Teachers</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="vendors/simple-line-icons/css/simple-line-icons.css">
    <link rel="stylesheet" href="vendors/flag-icon-css/css/flag-icon.min.css">
    <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <link rel="stylesheet" href="vendors/select2/select2.min.css">
    <link rel="stylesheet" href="vendors/select2-bootstrap-theme/select2-bootstrap.min.css">
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <!-- endinject -->
    <!-- Layout styles -->
    <link rel="stylesheet" href="css/style.css" />
  </head>
  <body>
    <div class="container-scroller">
      <!-- partial:partials/_navbar.html -->
      <?php include_once('includes/header.php'); ?>
      <!-- partial -->
      <div class="container-fluid page-body-wrapper">
        <!-- partial:partials/_sidebar.html -->
        <?php include_once('includes/sidebar.php'); ?>
        <!-- partial -->
        <div class="main-panel">
          <div class="content-wrapper">
            <div class="page-header">
              <h3 class="page-title"> Update Teachers </h3>
              <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                  <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                  <li class="breadcrumb-item active" aria-current="page"> Update Teachers</li>
                </ol>
              </nav>
            </div>
            <div class="row">
              <div class="col-12 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <h4 class="card-title" style="text-align: center;">Update Teachers</h4>
                    <form class="forms-sample" method="post" enctype="multipart/form-data">
                      <?php
                      $eid = $_GET['editid'];
                      $sql = "SELECT * FROM tbluser WHERE ID=:eid";
                      $query = $dbh->prepare($sql);
                      $query->bindParam(':eid', $eid, PDO::PARAM_STR);
                      $query->execute();
                      $results = $query->fetchAll(PDO::FETCH_OBJ);
                      $cnt = 1;
                      if($query->rowCount() > 0) {
                        foreach($results as $row) { 
                      ?>
                      <div class="form-group">
                        <label for="teachername">Teacher Name</label>
                        <input type="text" name="teachername" value="<?php echo htmlentities($row->Name); ?>" class="form-control" required='true'>
                      </div>
                      <div class="form-group">
                        <label for="teacheremail">Teacher Email</label>
                        <input type="email" name="teacheremail" value="<?php echo htmlentities($row->Email); ?>" class="form-control" required='true'>
                      </div>
                      <div class="form-group">
                        <label for="gender">Gender</label>
                        <select name="gender" class="form-control" required='true'>
                          <option value="<?php echo htmlentities($row->Gender); ?>"><?php echo htmlentities($row->Gender); ?></option>
                          <option value="Male">Male</option>
                          <option value="Female">Female</option>
                        </select>
                      </div>
                      <div class="form-group">
                        <label for="dob">Date of Birth</label>
                        <input type="date" name="dob" value="<?php echo htmlentities($row->DOB); ?>" class="form-control" required='true'>
                      </div>
                      <div class="form-group">
                        <label for="mobileNumber">Mobile Number</label>
                        <input type="text" name="mobileNumber" value="<?php echo htmlentities($row->MobileNumber); ?>" class="form-control" required='true' maxlength="10" pattern="[0-9]+">
                      </div>
                      <div class="form-group">
                        <label for="address">Address</label>
                        <textarea name="address" class="form-control" required='true'><?php echo htmlentities($row->Address); ?></textarea>
                      </div>
                      <h3>Login details</h3>
                      <div class="form-group">
                        <label for="username">User Name</label>
                        <input type="text" name="username" value="<?php echo htmlentities($row->UserName); ?>" class="form-control" readonly='true'>
                      </div>
                      <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" name="password" value="<?php echo htmlentities($row->Password); ?>" class="form-control" readonly='true'>
                      </div>
                      <?php $cnt = $cnt + 1; } } ?>
                      <button type="submit" class="btn btn-primary mr-2" name="submit">Update</button>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- content-wrapper ends -->
          <!-- partial:partials/_footer.html -->
          <?php include_once('includes/footer.php'); ?>
          <!-- partial -->
        </div>
        <!-- main-panel ends -->
      </div>
      <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->
    <!-- plugins:js -->
    <script src="vendors/js/vendor.bundle.base.js"></script>
    <!-- endinject -->
    <!-- Plugin js for this page -->
    <script src="vendors/select2/select2.min.js"></script>
    <script src="vendors/typeahead.js/typeahead.bundle.min.js"></script>
    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="js/off-canvas.js"></script>
    <script src="js/misc.js"></script>
    <!-- endinject -->
    <!-- Custom js for this page -->
    <script src="js/typeahead.js"></script>
    <script src="js/select2.js"></script>
    <!-- End custom js for this page -->
  </body>
</html>
<?php } ?>
