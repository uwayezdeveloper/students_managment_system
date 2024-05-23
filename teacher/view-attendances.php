<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');

if (strlen($_SESSION['sturecmsaid']) == 0) {
    header('location:logout.php');
} else {
    if (isset($_POST['submit'])) {
        $classID = $_POST['class'];
        $courseID = $_POST['course'];

        // Fetch class name
        $sql_class = "SELECT ClassName, Section FROM tblclass WHERE ID = :classID";
        $query_class = $dbh->prepare($sql_class);
        $query_class->bindParam(':classID', $classID, PDO::PARAM_STR);
        $query_class->execute();
        $class = $query_class->fetch(PDO::FETCH_ASSOC);

        // Fetch course name
        $sql_course = "SELECT CourseName FROM tblcourse WHERE ID = :courseID";
        $query_course = $dbh->prepare($sql_course);
        $query_course->bindParam(':courseID', $courseID, PDO::PARAM_STR);
        $query_course->execute();
        $course = $query_course->fetch(PDO::FETCH_ASSOC);

        $_SESSION['classID'] = $classID;
        $_SESSION['className'] = $class['ClassName'] . " " . $class['Section'];
        $_SESSION['courseID'] = $courseID;
        $_SESSION['courseName'] = $course['CourseName'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Student  Information System|| Manage Students</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="vendors/simple-line-icons/css/simple-line-icons.css">
    <link rel="stylesheet" href="vendors/flag-icon-css/css/flag-icon.min.css">
    <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="./vendors/daterangepicker/daterangepicker.css">
    <link rel="stylesheet" href="./vendors/chartist/chartist.min.css">
    <link rel="stylesheet" href="./css/style.css">
    <!-- End layout styles -->
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
                        <h3 class="page-title"> View Attendance </h3>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page"> Manage Students</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="row">
                        <!-- Add Class and Course Selection -->
                        <div class="col-md-12">
                        <form method="post" action="">
                            <div class="form-group">
                                <label for="class">Select Class:</label>
                                <select class="form-control" id="class" name="class">
                                    <?php
                                    // Fetch classes from the database
                                    $sql_class = "SELECT * FROM tblclass";
                                    $query_class = $dbh->prepare($sql_class);
                                    $query_class->execute();
                                    $classes = $query_class->fetchAll(PDO::FETCH_ASSOC);
                                    echo "<option selected disabled>Select Class</option>";
                                    foreach ($classes as $class) {
                                        echo "<option value='" . $class['ID'] . "'>" . $class['ClassName'] . " " . $class['Section'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="course">Select Course:</label>
                                <select class="form-control" id="course" name="course">
                                    <?php
                                    // Fetch courses from the database
                                    $sql_course = "SELECT * FROM tblcourse";
                                    $query_course = $dbh->prepare($sql_course);
                                    $query_course->execute();
                                    $courses = $query_course->fetchAll(PDO::FETCH_ASSOC);
                                    echo "<option selected disabled>Select Course</option>";
                                    foreach ($courses as $course) {
                                        echo "<option value='" . $course['ID'] . "'>" . $course['CourseName'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary" name="submit">Search</button>
                        </form>

                        </div>
                        <!-- End Class and Course Selection -->

                        <!-- Display Students -->
                        <div class="col-md-12 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                        <div class="d-sm-flex align-items-center mb-4">
                                            <h4 class="card-title mb-sm-0">View Students Attendance 
                                            <?php 
                                            if (isset($_SESSION['className'])) {
                                                echo " - Class: " . $_SESSION['className'];
                                            }
                                            if (isset($_SESSION['courseName'])) {
                                                echo " - Course: " . $_SESSION['courseName'];
                                            } 
                                            ?> 
                                            </h4>
                                            <a href="#" class="text-dark ml-auto mb-3 mb-sm-0">View all Students</a>
                                        </div>
                                        <div class="table-responsive border rounded p-1">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th class="font-weight-bold">#</th>
                                                        <th class="font-weight-bold">	AttendanceDate</th>
                                                        <th class="font-weight-bold">Class</th>
                                                        <th class="font-weight-bold">Student Name</th>
                                                        <th class="font-weight-bold">Attendance</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $selected_class = $_SESSION['classID'];
                                                    $selected_course = $_SESSION['courseID'];
                                                    // Assuming $date is already set
                                                    $date = date('Y-m-d'); // Or any specific date you want to check
                                                    if ($selected_class != '' && $selected_course != '') {
                                                        // Fetch students enrolled in the class
                                                        $sql_students = "SELECT *, tblstudent.ID as studentID FROM tblstudent,tblattendance WHERE StudentClass = :class group by tblattendance.ID";
                                                        $query_students = $dbh->prepare($sql_students);
                                                        $query_students->bindParam(':class', $selected_class, PDO::PARAM_STR);
                                                        $query_students->execute();
                                                        $students = $query_students->fetchAll(PDO::FETCH_ASSOC);
                                                        $i = 0;
                                                        foreach ($students as $key => $student) {
                                                            $i++;
                                                            $studentID = $student['studentID'];
                                                            echo "<tr>";
                                                            echo "<td>" . ($key + 1) . "</td>";
                                                            echo "<td>" .$student['AttendanceDate'] . "</td>";
                                                            echo "<td>" .$_SESSION['className'] . "</td>";
                                                            echo "<td>" . $student['StudentName'] . "</td>";
                                                    
                                                            // Check if an attendance record exists for the student on the given date
                                                            $sql_attendance = "SELECT IsPresent FROM tblattendance WHERE StudentID = :studentID AND ClassID = :classID AND AttendanceDate = :date";
                                                            $query_attendance = $dbh->prepare($sql_attendance);
                                                            $query_attendance->bindParam(':studentID', $studentID, PDO::PARAM_INT);
                                                            $query_attendance->bindParam(':classID', $selected_class, PDO::PARAM_INT);
                                                            $query_attendance->bindParam(':date', $date, PDO::PARAM_STR);
                                                            $query_attendance->execute();
                                                            $attendance_result = $query_attendance->fetch(PDO::FETCH_ASSOC);                            
                                                            if ($attendance_result) {
                                                                // Display attendance status based on existing attendance record
                                                                $present = ($attendance_result['IsPresent'] == 1 ? 'Present' : '');
                                                                $absent = ($attendance_result['IsPresent'] == 0 ? 'Absent' : '');
                                                                if ($present) {
                                                                    echo "<td>" .$present."</td>";
                                                                } else if ($absent) {
                                                                    echo "<td>" .$absent. "</td>";
                                                                }
                                                            } else {
                                                                // Display "No Attendance Taken" if no record exists
                                                                echo "<td>No Attendance Taken</td>";
                                                                echo "</tr>";
                                                            }
                                                        }
                                                        echo "<td><input type='hidden' name='num' value='" . $i . "'></td>";
                                                    } else {
                                                        echo "<tr><td colspan='7'>No Class and Course selected</td></tr>";
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                       
                                </div>
                            </div>
                        </div>
                        <!-- End Display Students -->
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
    <script src="./vendors/chart.js/Chart.min.js"></script>
    <script src="./vendors/moment/moment.min.js"></script>
    <script src="./vendors/daterangepicker/daterangepicker.js"></script>
    <script src="./vendors/chartist/chartist.min.js"></script>
    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="./js/off-canvas.js"></script>
    <script src="./js/misc.js"></script>
    <!-- endinject -->
    <!-- Custom js for this page -->
    <script src="./js/dashboard.js"></script>
    <!-- End custom js for this page -->
</body>
</html>
