<?php
  session_start();

  if(!isset($_SESSION['user_id'])) {
    header("Location: http://webproject2023.ddns.net/login-register/login.php");
  }
  $username = $_SESSION['username'];
  ob_start();

  print_r($username);
  $varDumpOutput = ob_get_clean(); // Get the var_dump output and store it in a variable
?>
<!DOCTYPE HTML>
<html>

<head>
    <meta charset="UTF-8">
    <title>Make Dis.Count User Profile</title>
    <link rel="stylesheet" href="admin.css" />
    <link
  rel="stylesheet"
  href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
/>
    
</head>

<body>
    <header>
        <a href="#" class="logo">Make.DisCount (Admin)</a>
    </header>

    <div id="container">
        <div class="upload-section">
            <p class="upload-label">Upload here to update the products database:</p>
            <input type="file" id="productFile" accept=".csv">
            <label for="productFile" class="upload-button">Choose File</label>
        </div>

        <div class="upload-section">
            <p class="upload-label">Upload here to update the markets database:</p>
            <input type="file" id="marketFile" accept=".csv">
            <label for="marketFile" class="upload-button">Choose File</label>
        </div>
        <div class="avatar-container">
        <div class="user-menu">
          <i class="fa-solid fa-user-ninja" id="avatar-icon"></i>
        
          <div class="dropdown-content">
            <p class="username">
              <?php echo $varDumpOutput;?>
            </p>
            <button href="main-interface/profile.php" id="test">Edit Profile</button>
            <button href="#">Dashboard</button>
            <form action="./login-register/logout.php" method="post">  
              <button type="submit" name="logout">Log Out</button>
            </form>
          </div>
        </div>
      </div>
    </div>
</body>

</html>
