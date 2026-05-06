<?php
session_start();
include("connection.php");
$con = connection();

// query para sa reservations
$query = "SELECT r.reservation_id, b.title, p.fname, p.lname, r.date_reserved, r.status 
          FROM reservations r 
          INNER JOIN books b ON r.book_id = b.id 
          INNER JOIN profile p ON r.account_id = p.account_id";
$result = mysqli_query($con, $query);

// query para sa books
$books = mysqli_query($con, "SELECT * FROM books");

//total books 
$totalbooks = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS total FROM books"))['total'];

//active reservations
$activereservations = mysqli_fetch_assoc(mysqli_query($con,"SELECT COUNT(*) AS total FROM reservations WHERE status = 'Pending'"))['total'];

//overdue books
$overduebooks = mysqli_fetch_assoc(mysqli_query($con,"SELECT COUNT(*) AS total
                                                      FROM reservations
                                                      WHERE due_date < CURDATE() 
                                                      AND borrow_status = 'Borrowed'"))['total'];

//total users 
$totaluser = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS total FROM profile"))['total'];


// recent activity
$recent = mysqli_query($con, "SELECT b.title, p.fname, r.status FROM reservations r INNER JOIN books b ON r.book_id = b.id INNER JOIN profile p ON r.account_id = p.account_id ORDER BY r.date_reserved DESC LIMIT 10");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Librarian Panel</title>
  <link rel="stylesheet" href="./assets/style2.css">
</head>
<body>
<div class="dashboard-container">
  <!-- Sidebar -->
  <div class="sidebar">
    <h2>Librarian Panel</h2>
    <ul>
      <li><a href="#dashboard" onclick="showSection('dashboard')">Dashboard</a></li>
      <li><a href="#add-book" onclick="showSection('add-book')">Add Book</a></li>
      <li><a href="#manage-books" onclick="showSection('manage-books')">Manage Books</a></li>
      <li><a href="#manage-reservations" onclick="showSection('manage-reservations')">Reservations</a></li>
      <li><a href="#borrowed" onclick="showSection('borrowed')">Borrowed Books</a></li>
      <li><a href="#reports" onclick="showSection('reports')">Reports</a></li>
      <li><a href="index.php">Log out</a></li>
    </ul>
  </div>

  <!-- Main Content -->
  <div class="main-content">

    <!-- Dashboard Section -->
    <section id="dashboard" class="active">
      <h2>Welcome Librarian</h2>
      
    <div class="cards">
      
    <div class="card">
        <h3>Total Books</h3>
        <p><?php echo $totalbooks; ?></p>
      </div>
    
    <div class="card">
      <h3>Active Reservations</h3>
      <p><?php echo $activereservations; ?></p>
    </div>

    <div class="card">
      <h3>Overdue Books</h3>
      <p><?php echo $overduebooks; ?></p>
    </div>

    <div class="card">
      <h3>Registered users</h3>
      <p><?php echo $totaluser; ?></p>
    </div>

    </div>

    <br>

    <h3>Recent Activity</h3>
    <table border="1" cellpadding = "5">
      <tr>
        <th>Book</th>
        <th>User</th>
        <th>Status</th>
      </tr>
      <?php if(mysqli_num_rows($recent) > 0){?>
      <?php while($r = mysqli_fetch_assoc($recent)){?>
      <tr>
        <td><?php echo $r['title']; ?></td>
        <td><?php echo $r['fname']; ?></td>
        <td><?php echo $r['status']; ?></td>
      </tr>
     <?php } ?>
     <?php } else { ?>
    <tr>
      <td colspan="3"> No recent activity</td>
    </tr>
    <?php } ?>
    </table>

    </section>

    <!-- Add Book Section -->
    <section id="add-book">
      <h2>Add Book</h2>
      <form action="./add_book.php" method="POST" enctype="multipart/form-data">
        <input type="text" name="title" placeholder="Book Title" required>
        <input type="text" name="author" placeholder="Author" required>
        <input type="file" name="image" accept="image/*" required>
        <label>Softcopy 
          <input type="file" name="softcopy_file" accept=".pdf,.doc,.docx,.epub,.txt">
        </label>
        <select name="status">
          <option value="Available">Available</option>
          <option value="Not Available">Borrowed</option>
        </select>
        <select name="bookstat">
          <option value="Softcopy">Soft copy</option>
          <option value="Hardcopy">Hardcopy</option>
          <option value="Softcopy/Hardcopy">Softcopy/Hardcopy</option>
        </select>
        <button type="submit">Add Book</button>
      </form>
    </section>

    <!-- Manage Books Section -->
    <section id="manage-books">
      <h2>Manage Books</h2>
      <table>
        <tr>
          <th>ID</th>
          <th>Title</th>
          <th>Author</th>
          <th>Status</th>
          <th>Type</th>
          <th>Image</th>
          <th>Soft Copy</th>
          <th>Update</th>
        </tr>
        <?php while($row = mysqli_fetch_assoc($books)){ ?>
        <tr>
          <td><?php echo $row['id']; ?></td>
          <td><?php echo htmlspecialchars($row['title']); ?></td>
          <td><?php echo htmlspecialchars($row['author']); ?></td>
          <td><?php echo $row['status']; ?></td>
          <td><?php echo $row['bookstat']; ?></td>
          <td>
            <?php if(!empty($row['image'])): ?>
              <img src="uploads/<?php echo $row['image']; ?>" width="60">
            <?php endif; ?>
          </td>
       <td>
          <?php if(!empty($row['softcopy_file'])): 
            $ext = strtolower(pathinfo($row['softcopy_file'], PATHINFO_EXTENSION));
            if($ext === "pdf"){ ?>
              <a href="read.php?id=<?php echo $row['id']; ?>" target="_blank">View PDF</a>
            <?php } elseif($ext === "txt") { ?>
              <a href="read.php?id=<?php echo $row['id']; ?>" target="_blank">Read Text</a>
            <?php } else { ?>
              <a href="uploads/softcopies/<?php echo $row['softcopy_file']; ?>" download>Download File</a>
            <?php } ?>
          <?php else: ?>
            No soft copy
          <?php endif; ?>
               </td>

              <td>
                <?php if(strtolower($row['bookstat']) !== "softcopy"){ ?>
                  <form method="POST" action="update_status.php">
                  <input type="hidden" name="book_id" value="<?php echo $row['id']; ?>">
                  <select name="status">
                  <option value="Available" <?php if($row['status']=="Available") echo "selected"; ?>>Available</option>
                  <option value="Not Available" <?php if($row['status']=="Not Available") echo "selected"; ?>>Borrowed</option>
                  </select>
                  <button type="submit">Update</button>
                  </form>
                  <?php } else { ?>
                  <span>Softcopy — status fixed</span>
                  <?php } ?>
                </td>
                </tr>
        <?php } ?>
      </table>
    </section>

    <!-- Reservations Section -->
    <section id="manage-reservations">
      <h2>Manage Reservations</h2>
      <table>
        <tr>
          <th>Reservation ID</th>
          <th>Book Title</th>
          <th>Reserved By</th>
          <th>Date Reserved</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($result)){ ?>
        <tr>
          <td><?php echo $row['reservation_id']; ?></td>
          <td><?php echo $row['title']; ?></td>
          <td><?php echo $row['fname'].' '.$row['lname']; ?></td>
          <td><?php echo $row['date_reserved']; ?></td>
          <td><?php echo $row['status']; ?></td>
          <td>
            <?php if($row['status'] == 'Pending'){ ?>
              <form method="POST" action="approve_reservation.php">
                <input type="hidden" name="reservation_id" value="<?php echo $row['reservation_id']; ?>">
                <button type="submit" name="action" value="approve">Approve</button>
                <button type="submit" name="action" value="decline">Decline</button>
              </form>
            <?php } else { ?>
              <span>No action available</span>
            <?php } ?>
          </td>
        </tr>
        <?php } ?>
      </table>
    </section>

    <!-- Borrowed Books Section -->
    <section id="borrowed">
  <h2>Borrowed Books</h2>

  <!-- Step 1: Role Selection -->
  <form method="GET" action="librarian.php#borrowed">
    <label>Select Role:</label>
    <select name="role" onchange="this.form.submit()">
      <option value="">-- Select Role --</option>
      <option value="Student" <?php if(isset($_GET['role']) && $_GET['role']=='Student') echo 'selected'; ?>>Student</option>
      <option value="Teacher" <?php if(isset($_GET['role']) && $_GET['role']=='Teacher') echo 'selected'; ?>>Teacher</option>
    </select>
  </form>

  <!-- Step 2: User Selection (only if role chosen) -->
  <?php if(isset($_GET['role']) && !empty($_GET['role'])){ ?>
    <form method="GET" action="librarian.php#borrowed">
      <input type="hidden" name="role" value="<?php echo $_GET['role']; ?>">
      <label>Select User:</label>
      <select name="account_id" onchange="this.form.submit()">
        <option value="">-- Select User --</option>
        <?php
        $role = $_GET['role'];
        $users = mysqli_query($con, "SELECT account_id, fname, lname FROM profile WHERE role='$role'");
        while($u = mysqli_fetch_assoc($users)){
          echo '<option value="'.$u['account_id'].'"';
          if(isset($_GET['account_id']) && $_GET['account_id']==$u['account_id']) echo ' selected';
          echo '>'.$u['fname'].' '.$u['lname'].'</option>';
        }
        ?>
      </select>
    </form>
  <?php } ?>

  <!-- Step 3: Borrowed Books Table -->
  <?php
if(isset($_GET['account_id']) && !empty($_GET['account_id'])){
  $aid = $_GET['account_id'];

$borrowed = mysqli_query($con, "
  SELECT b.title, r.date_borrowed, r.due_date, r.status, r.borrow_status, p.role
  FROM reservations r
  INNER JOIN books b ON r.book_id = b.id
  INNER JOIN profile p ON r.account_id = p.account_id
  WHERE r.account_id = '$aid'
  AND r.borrow_status = 'Borrowed'
");
?>
<table border="1" cellpadding="5">
  <tr>
    <th>Book Title</th>
    <th>Date Borrowed</th>
    <th>Due Date</th>
    <th>Status</th>
    <th>Role</th>
    <th>Penalty</th>
  </tr>

<?php
$totalPenalty = 0;

if(mysqli_num_rows($borrowed) > 0){
  while($row = mysqli_fetch_assoc($borrowed)){
    $penalty = 0;

    if($row['role'] === 'student'){ // lowercase sa DB mo
      $today = new DateTime();
      $due = new DateTime($row['due_date']);

      if($today > $due){
        $interval = $due->diff($today);
        $daysLate = $interval->days;
        $penalty = $daysLate * 2;
        $totalPenalty += $penalty;
      }
    }
?>
<tr>
  <td><?php echo $row['title']; ?></td>
  <td><?php echo $row['date_borrowed']; ?></td>
  <td><?php echo $row['due_date']; ?></td>
  <td><?php echo $row['status']; ?></td>
  <td><?php echo $row['role']; ?></td>
  <td><?php echo ($row['role'] === 'teacher') ? 'No penalty' : '₱'.$penalty; ?></td>
</tr>

<?php
  }
} else {
  echo "<tr><td colspan='6'>No borrowed books found</td></tr>";
}
?>
</table>

<p><strong>Total Penalty:</strong> ₱<?php echo $totalPenalty; ?></p>

<?php } ?>
</section>
    <!-- Reports Section -->
    <section id="reports">
      <h2>Reports</h2>
     
      <form method="POST" action="librarian.php#reports">
        <label>Report Type:</label>
        <select name="report_type">
          <option value="Borrowed Books">borrowed Books</option>
          <option value="Overdue Books">Overdue Books</option>
          <option value="Returned Books">Returned Books</option>
        </select>
        <button type="submit" name="generate_report">Generate</button>
      </form>
      
    <?php 
    if(isset($_POST['generate_report'])){
      $type = $_POST ['report_type'];
      $date = date('Y-m-d');
      $prepared = "Librarian";

      //insert report card para makita ng admin
      mysqli_query($con,"INSERT INTO reports(report_type, generated_date, prepared_by)
                         VALUES ('$type', '$date', '$prepared')");
      echo "<p> Report '$type' generated and sent to Admin. </p>";
    }

    //show reports generated by this librarian
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
    }
    ?>

    </section>
  </div>
</div>

<script>
  function showSection(sectionId){
    const sections = document.querySelectorAll('section');
    sections.forEach(sec => sec.classList.remove('active'));
    const target = document.getElementById(sectionId);
    if(target){ target.classList.add('active'); 
    location.hash = sectionId;
    }
  }

  //autoload tamang section pag refresh
  window.onload = function(){
    let hash = window.location.hash.replace('#', '');

    if(hash){
      showSection(hash);
    } else{
      showSection('dashboard');
    }
  }

  //dropuser
function updateUserDropdown(){
  const roleSelect = document.getElementById('roleSelect');
  const userSelect = document.getElementById('userSelect');
  const userBtn = document.getElementById('userBtn');

  if(roleSelect.value === ""){
    userSelect.disabled = true;
    userBtn.disabled = true;
  } else {
    userSelect.disabled = false;
    userBtn.disabled = false;
  }
}
</script>
</body>
</html>