<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');

// Check if user is logged in
if (strlen($_SESSION['sturecmsaid']) == 0) {
    header('location:logout.php');
} else {
    // Handle form submission for class and course selection
    if (isset($_POST['submit'])) {
        $_SESSION['class'] = $_POST['class'];
        $_SESSION['course'] = $_POST['course'];
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
        $_SESSION['className1'] = $class['ClassName'] . " " . $class['Section'];
        $_SESSION['courseName1'] = $course['CourseName'];
    }

    // Handle form submission for grades
    if (isset($_POST['submit12'])) {
        $CourseID = $_SESSION['course'];
        $num = $_POST['num'];
    
        for ($i = 1; $i <= $num; $i++) {
            $name_grade = "GRADE" . $i;
            $Grade = $_POST[$name_grade];
            $name_id = "ID" . $i;
            $StudentID = $_POST[$name_id];
    
            // Check if the grade is not empty
            if (!empty($Grade)) {
                // Check if the grade already exists for the student in the course
                $sql_check = "SELECT * FROM tblgrade WHERE StudentID = :studentID AND CourseID = :courseID";
                $query_check = $dbh->prepare($sql_check);
                $query_check->bindParam(':studentID', $StudentID, PDO::PARAM_INT);
                $query_check->bindParam(':courseID', $CourseID, PDO::PARAM_INT);
                $query_check->execute();
                $result = $query_check->fetch(PDO::FETCH_ASSOC);
    
                if ($result) {
                    // Update the grade
                    $sql_update = "UPDATE tblgrade SET Grade = :grade WHERE StudentID = :studentID AND CourseID = :courseID";
                    $query_update = $dbh->prepare($sql_update);
                    $query_update->bindParam(':grade', $Grade, PDO::PARAM_STR);
                    $query_update->bindParam(':studentID', $StudentID, PDO::PARAM_INT);
                    $query_update->bindParam(':courseID', $CourseID, PDO::PARAM_INT);
                    $query_update->execute();
                } else {
                    // Insert the grade
                    $sql_insert = "INSERT INTO tblgrade (StudentID, CourseID, Grade) VALUES (:studentID, :courseID, :grade)";
                    $query_insert = $dbh->prepare($sql_insert);
                    $query_insert->bindParam(':studentID', $StudentID, PDO::PARAM_INT);
                    $query_insert->bindParam(':courseID', $CourseID, PDO::PARAM_INT);
                    $query_insert->bindParam(':grade', $Grade, PDO::PARAM_STR);
                    $query_insert->execute();
                }
            }
        }
        echo "<script>alert('Grades updated successfully');</script>";
        echo "<script>window.location.href='view_grade.php'</script>";
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Student  Information System || Manage Students</title>
    <!-- Include your CSS and JS files here -->
    <link rel="stylesheet" href="vendors/simple-line-icons/css/simple-line-icons.css">
    <link rel="stylesheet" href="vendors/flag-icon-css/css/flag-icon.min.css">
    <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="./vendors/daterangepicker/daterangepicker.css">
    <link rel="stylesheet" href="./vendors/chartist/chartist.min.css">
    <link rel="stylesheet" href="./css/style.css">
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
                        <h3 class="page-title"> Manage Students </h3>
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
                                        $classes = $dbh->query("SELECT * FROM tblclass")->fetchAll(PDO::FETCH_ASSOC);
                                        echo "<option selected disabled>Select Class</option>";
                                        foreach ($classes as $class) {
                                            echo "<option value='{$class['ID']}'>{$class['ClassName']} {$class['Section']}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="course">Select Course:</label>
                                    <select class="form-control" id="course" name="course">
                                        <?php 
                                        $courses = $dbh->query("SELECT * FROM tblcourse")->fetchAll(PDO::FETCH_ASSOC);
                                        echo "<option selected disabled>Select Course</option>";
                                        foreach ($courses as $course) {
                                            echo "<option value='{$course['ID']}'>{$course['CourseName']}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary" name="submit">View Grades</button>
                            </form>
                        </div>
                        <!-- End Class and Course Selection -->

                        <!-- Display Students -->
                        <div class="col-md-12 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-sm-flex align-items-center mb-4">
                                        <h4 class="card-title mb-sm-0">Manage Students</h4>
                                    </div>
                                    <div class="table-responsive border rounded p-1">
                                        <form action="" method="post">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th class="font-weight-bold">S.No</th>
                                                        <th class="font-weight-bold">Student ID</th>
                                                        <th class="font-weight-bold">Student Class</th>
                                                        <th class="font-weight-bold">Student Name</th>
                                                        <th class="font-weight-bold">Grade</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    // Fetch and display students from the database
                                                    if (isset($_SESSION['class']) && isset($_SESSION['course'])) {
                                                        // Fetch students enrolled in the class
                                                        $selected_class = $_SESSION['class'];
                                                        $sql_students = "SELECT tblstudent.*, tblclass.ClassName, tblclass.Section 
                                                                        FROM tblstudent 
                                                                        JOIN tblclass ON tblstudent.StudentClass = tblclass.ID 
                                                                        WHERE StudentClass = :class";
                                                        $query_students = $dbh->prepare($sql_students);
                                                        $query_students->bindParam(':class', $selected_class, PDO::PARAM_STR);
                                                        $query_students->execute();
                                                        $students = $query_students->fetchAll(PDO::FETCH_ASSOC);

                                                        if (count($students) > 0) {
                                                        // Display the hidden field for the course ID
                                                        echo "<input type='hidden' name='CourseID' value='" . $_SESSION['course'] . "'>";

                                                        // Fetch students enrolled in the class
                                                        $selected_class = $_SESSION['class'];
                                                        $sql_students = "SELECT tblstudent.*, tblclass.ClassName, tblclass.Section 
                                                                         FROM tblstudent 
                                                                         JOIN tblclass ON tblstudent.StudentClass = tblclass.ID 
                                                                         WHERE StudentClass = :class";
                                                        $query_students = $dbh->prepare($sql_students);
                                                        $query_students->bindParam(':class', $selected_class, PDO::PARAM_STR);
                                                        $query_students->execute();
                                                        $students = $query_students->fetchAll(PDO::FETCH_ASSOC);

                                                        $i = 0;
                                                        foreach ($students as $key => $student) {
                                                            $i++;
                                                            echo "<tr>";
                                                            echo "<td>" . ($key + 1) . "</td>";
                                                            echo "<td>" . $student['ID'] . "</td>";
                                                            echo "<td>" . $student['ClassName'] . " " . $student['Section'] . "</td>";
                                                            echo "<td>" . $student['StudentName'] . "</td>";
                                                        

                                                            // Check if a grade exists for the student in the course
                                                            $studentID = $student['ID'];
                                                            $courseID = $_SESSION['course'];
                                                            $sql_grade = "SELECT Grade FROM tblgrade WHERE StudentID = :studentID AND CourseID = :courseID";
                                                            $query_grade = $dbh->prepare($sql_grade);
                                                            $query_grade->bindParam(':studentID', $studentID, PDO::PARAM_INT);
                                                            $query_grade->bindParam(':courseID', $courseID, PDO::PARAM_INT);
                                                            $query_grade->execute();
                                                            $grade_result = $query_grade->fetch(PDO::FETCH_ASSOC);

                                                            // Display input field based on grade existence
                                                            $name_grade = "GRADE" . $i;
                                                            if ($grade_result) {
                                                                echo "<td><select name='$name_grade' class='form-control'>";
                                                                // Check if the grade is 'A', if so, make it unavailable
                                                                echo "<option value='A'" . ($grade_result['Grade'] == 'A' ? ' selected' : '') . ">A</option>";
                                                                echo "<option value='B'" . ($grade_result['Grade'] == 'B' ? ' selected' : '') . ">B</option>";
                                                                echo "<option value='C'" . ($grade_result['Grade'] == 'C' ? ' selected' : '') . ">C</option>";
                                                                echo "<option value='D'" . ($grade_result['Grade'] == 'D' ? ' selected' : '') . ">D</option>";
                                                                echo "<option value='E'" . ($grade_result['Grade'] == 'E' ? ' selected' : '') . ">E</option>";
                                                                echo "</select></td>";
                                                            } else {
                                                                echo "<td><select name='$name_grade' class='form-control'>";
                                                                echo "<option value='B'>B</option>"; // Make grade 'B' the default available option
                                                                echo "<option value='C'>C</option>";
                                                                echo "<option value='D'>D</option>";
                                                                echo "<option value='E'>E</option>";
                                                                echo "</select></td>";
                                                            }


                                                            $name_id = "ID" . $i;
                                                            echo "<td><input type='hidden' name='$name_id' value='" . $student['ID'] . "'></td>";
                                                            echo "</tr>";
                                                        }
                                                        echo "<input type='hidden' name='num' value='" . $i . "'>";
                                                    }else{
                                                        echo "<tr><td>No students found in this class.</td></tr>";
                                                    }
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                            <button type="submit" name="submit12" class="btn btn-primary">Save Grade</button>
                                        </form>
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
    <script src="js/off-canvas.js"></script>
    <script src="js/misc.js"></script>
    <!-- endinject -->
    <!-- Custom js for this page -->
    <script src="./js/dashboard.js"></script>
</body>
</html>
