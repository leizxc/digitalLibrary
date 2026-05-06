<?php
session_start();
include("connection.php");
$con = connection();

// --- ADD LIBRARIAN ---
// --- ADD LIBRARIAN ---
if(isset($_POST['add_librarian'])){
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = 'Librarian';
    $status = 'Approved';

    // hash password bago i-save
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // check kung existing email
    $check = mysqli_query($con,"SELECT * FROM account WHERE email='$email'");
    if(mysqli_num_rows($check) > 0){
        $_SESSION['msg'] = "<p style='color:red;'>Email already exists!</p>";
    } else {
        mysqli_query($con, "INSERT INTO account (fname, mname, lname, email, password, role, status) 
                            VALUES ('$fname', '', '$lname', '$email', '$passwordHash', '$role', '$status')");
        $_SESSION['msg'] = "<p style='color:green;'>New librarian account created successfully!</p>";
    }

    header("Location: admin.php#librarian-user-management");
    exit();
}

// --- DELETE LIBRARIAN ---
if(isset($_POST['delete_librarian'])){
    $id = $_POST['delete_id'];
    // burahin muna lahat ng profile rows na naka-link sa account_id
    mysqli_query($con, "DELETE FROM profile WHERE account_id='$id'");
    // saka burahin yung account row
    mysqli_query($con, "DELETE FROM account WHERE id='$id'");
    $_SESSION['msg'] = "<p style='color:red;'>Librarian deleted successfully!</p>";
    header("Location: admin.php#librarian-user-management");
    exit();
}
//teacher verification
if(isset($_POST['approve_account'])){
    $id = $_POST['approve_id'];
    mysqli_query($con, "UPDATE account SET status='Approved' WHERE id='$id'");
    $_SESSION['msg'] = "Teacher account approved successfully!";
    header("Location: admin.php");
    exit();
}

$pendingTeachers = mysqli_query($con, "
    SELECT a.*, tv.file_path 
    FROM account a 
    LEFT JOIN teacher_verification tv ON a.id = tv.account_id 
    WHERE a.role='teacher' AND a.status='Pending'
");
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="assets/style2.css">
</head>

<body>

<div class="dashboard-container">

    <!-- SIDEBAR -->
    <div class="sidebar">
        <h2>Admin Panel</h2>
        <ul>
            <li><a href="#dashboard" onclick="showSection('dashboard')">Dashboard</a></li>
            <li><a href="#librarian-user-management" onclick="showSection('librarian-user-management')">Librarian User Management</a></li>
            <li><a href="#teacher-verification" onclick="showSection('teacher-verification')">Teacher Verification</a></li>
            <li><a href="#reports" onclick="showSection('reports')">Reports</a></li>
            <li><a href="index.php">Log out</a></li>
        </ul>
    </div>

    <!-- MAIN CONTENT -->
    <div class="main-content">

        <!-- DASHBOARD -->
        <section id="dashboard">
            <header><h1>Welcome, Admin</h1></header>

            <div class="cards">
                <?php
                $books = mysqli_num_rows(mysqli_query($con,"SELECT * FROM books"));
                $librarians = mysqli_num_rows(mysqli_query($con,"SELECT * FROM profile WHERE role = 'Librarian'"));
                $users = mysqli_num_rows(mysqli_query($con,"SELECT * FROM profile WHERE role IN ('Student','Teacher')"));
                $reports = mysqli_num_rows(mysqli_query($con,"SELECT * FROM reports"));
                ?>

                <div class="card"><h3>Total Books</h3><p><?php echo $books; ?></p></div>
                <div class="card"><h3>Total Librarians</h3><p><?php echo $librarians; ?></p></div>
                <div class="card"><h3>Total Users</h3><p><?php echo $users; ?></p></div>
                <div class="card"><h3>Reports</h3><p><?php echo $reports; ?></p></div>
            </div>
        </section>

        <!-- LIBRARIAN MANAGEMENT -->
        <section id="librarian-user-management">
            <header><h1>Librarian Management</h1></header>

            <?php
            if(isset($_SESSION['msg'])){
                echo $_SESSION['msg'];
                unset($_SESSION['msg']);
            }
            ?>

            <div class="card form-card">
                <h3>Add New Librarian</h3>
                <form method="POST" action="admin.php#librarian-user-management" autocomplete="off">
                    <input type="text" name="fname" placeholder="First Name" required autocomplete="off">
                    <input type="text" name="lname" placeholder="Last Name" required autocomplete="off">
                    <input type="email" name="email" placeholder="Email" required autocomplete="off">
                    <input type="password" name="password" placeholder="Password" required autocomplete="new-password">
                    <button type="submit" name="add_librarian">Add Librarian</button>
                </form>
            </div>

            <?php
            $res = mysqli_query($con, "SELECT * FROM account WHERE role='Librarian'");
            echo "<div class='managed-books'><table>";
            echo "<tr><th>Name</th><th>Email</th><th>Status</th><th>Actions</th></tr>";
            while($row = mysqli_fetch_assoc($res)){
                $name = trim($row['fname'] . ' ' . $row['lname']);
                $status = isset($row['status']) ? $row['status'] : '';
                echo "<tr>";
                echo "<td>{$name}</td>";
                echo "<td>{$row['email']}</td>";
                echo "<td>{$status}</td>";
                echo "<td>";
                echo "<form method='POST' style='display:inline;'>";
                echo "<input type='hidden' name='delete_id' value='{$row['id']}'>";
                echo "<button type='submit' name='delete_librarian' class='btn btn-danger'>Delete</button>";
                echo "</form>";
                echo "</td>";
                echo "</tr>";
            }
            echo "</table></div>";

            ?>
        </section>

        <!-- TEACHER VERIFICATION -->
        <!-- TEACHER VERIFICATION -->
<section id="teacher-verification">
  <header><h1>Teacher Verification</h1></header>
  <?php
  if(mysqli_num_rows($pendingTeachers) > 0){
      echo "<div class='managed-books'><table>";
      echo "<tr><th>Name</th><th>Email</th><th>Teacher ID</th><th>Status</th><th>Actions</th></tr>";
      while($row = mysqli_fetch_assoc($pendingTeachers)){
          $name = trim($row['fname'].' '.$row['lname']);
          echo "<tr>
                  <td>{$name}</td>
                  <td>{$row['email']}</td>
                  <td>";
          if(!empty($row['file_path'])){
              // thumbnail + zoom on click
              echo "<a href='{$row['file_path']}' target='_blank'>
                      <img src='{$row['file_path']}' alt='Teacher ID' style='width:80px;height:auto;border:1px solid #ccc;border-radius:4px;cursor:pointer;' />
                    </a>";
          } else {
              echo "No ID uploaded";
          }
          echo "</td>
                <td>{$row['status']}</td>
                <td>
                  <form method='POST'>
                    <input type='hidden' name='approve_id' value='{$row['id']}'>
                    <button type='submit' name='approve_account'>Approve</button>
                  </form>
                </td>
              </tr>";
      }
      echo "</table></div>";
  } else {
      echo "<p>No pending teacher accounts.</p>";
  }
  ?>
</section>



        <!-- REPORTS -->
        <section id="reports">
            <header><h1>Reports</h1></header>
            <?php
            $res = mysqli_query($con, "SELECT * FROM reports ORDER BY generated_date DESC");
            if(mysqli_num_rows($res) > 0){
                echo "<div class='managed-books'><table>";
                echo "<tr><th>Report Type</th><th>Date</th><th>Prepared By</th></tr>";
                while($row = mysqli_fetch_assoc($res)){
                    echo "<tr>
                            <td>{$row['report_type']}</td>
                            <td>{$row['generated_date']}</td>
                            <td>{$row['prepared_by']}</td>
                          </tr>";
                }
                echo "</table></div>";
            } else {
                echo "<p>No reports yet.</p>";
            }
            ?>
        </section>

    </div> <!-- END MAIN CONTENT -->

</div> <!-- END DASHBOARD CONTAINER -->

<script>
function showSection(sectionId){
    const sections = document.querySelectorAll('section');
    sections.forEach(sec => sec.classList.remove('active'));
    const target = document.getElementById(sectionId);
    if(target){
        target.classList.add('active');
        location.hash = sectionId;
    }
}
window.onload = function(){
    let hash = window.location.hash.replace('#','');
    if(hash){
        showSection(hash);
    } else {
        showSection('dashboard');
        location.hash = 'dashboard';
    }
}
</script>

</body>
</html>
