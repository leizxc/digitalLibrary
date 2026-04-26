<?php
include("connection.php");
$con = connection();

$id = $_GET['id'] ?? null;
$title = "";
$content = "";
$file = "";

if($id){
    $query = "SELECT title, softcopy_file FROM books WHERE id='$id'";
    $result = mysqli_query($con, $query);
    $row = mysqli_fetch_assoc($result);

    if($row){
        $title = $row['title'];
        if(!empty($row['softcopy_file'])){
            $file = "uploads/softcopies/" . $row['softcopy_file'];
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

            if(file_exists($file)){
                if($ext === "txt"){
                    // Wattpad-style: basahin ang text file
                    $content = nl2br(htmlspecialchars(file_get_contents($file)));
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?php echo htmlspecialchars($title); ?></title>
  <link rel="stylesheet" href="assets/style/read.css">
</head>
<body>
  <div class="reader-container">
    <h2><?php echo htmlspecialchars($title); ?></h2>
    <?php if(!empty($file) && file_exists($file)){ 
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        if($ext === "pdf"){ ?>
          <!-- PDF viewer -->
          <iframe src="<?php echo $file; ?>" width="100%" height="600px"></iframe>
        <?php } elseif($ext === "txt"){ ?>
          <!-- Wattpad-style text reader -->
          <div class="story-text">
            <?php echo $content; ?>
          </div>
        <?php } else { ?>
          <!-- Download link for non-PDF/TXT -->
          <div class="no-file">
            This file type (<?php echo $ext; ?>) cannot be viewed directly.<br>
            <a href="<?php echo $file; ?>" download>Download Soft Copy</a>
          </div>
        <?php } ?>
    <?php } else { ?>
      <div class="no-file">No soft copy available for this book.</div>
    <?php } ?>
  </div>
</body>
</html>
