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
        <div class="card">Total Books: 1,250</div>
        <div class="card">Active Reservations: 45</div>
        <div class="card">Overdue Books: 12</div>
        <div class="card">Registered Users: 320</div>
      </div>
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
      <p>Dito lalabas ang borrowed books list.</p>
    </section>

    <!-- Reports Section -->
    <section id="reports">
      <h2>Reports</h2>
      <p>Dito lalabas ang reports ng library.</p>
    </section>
  </div>
</div>

<script>
  function showSection(sectionId){
    const sections = document.querySelectorAll('section');
    sections.forEach(sec => sec.classList.remove('active'));
    const target = document.getElementById(sectionId);
    if(target){ target.classList.add('active'); }
  }
</script>
</body>
</html>
