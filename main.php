<!-- Session check -->
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

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <link
      rel="stylesheet"
      href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css"
    />
    <link rel="stylesheet" href="main.css" />
    <link
  rel="stylesheet"
  href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
/>


    <title>Make.DisCount</title>
  </head>
  <body>
    <div class="page_wrapper">
      <aside class="sidebar">
        <div class="logo-wrapper">
          <a href="#" class="logo">Make.DisCount</a>
        </div>
        <input
          class="searchbar"
          type="text"
          placeholder="Search stores"
          id="searchInput"
        />
        <div id="searchResults" class="search-results"></div>
      </aside>
      <aside class="sidebarStoreDetails">
      <!-- Store details and products panel -->  
        <div class="sidebar-container">
          <div id="storeDetails" class="store-details">
            <i class="fa-solid fa-xmark"></i>
            <button class="submitOffer">Submit an Offer</button>
            <h2 id="storeName">Store Name</h2>
            
            <ul id="productList" class="product-list">
              <!-- Products will be added here dynamically -->
            </ul>
          </div>
        </div>
      </aside>

      <div class="avatar-container">
        <div class="user-menu">
          <i class="fa-solid fa-user-ninja" id="avatar-icon"></i>
        
          <div class="dropdown-content">
            <p class="username">
              <?php echo $varDumpOutput; ?>
            </p>
            <button href="#" id="test">Edit Profile</button>
            <button href="#">Dashboard</button>
            <form action="./login-register/logout.php" method="post">  
              <button type="submit" name="logout">Log Out</button>
            </form>
          </div>
        </div>
      </div>

      <div class="map-area" id="map"></div>

      

    </div>
  </body>
  <script
    src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"
    integrity="sha256-WBkoXOwTeyKclOHuWtc+i2uENFpDZ9YPdf5Hf+D7ewM="
    crossorigin=""
  ></script>

  <script src="./map/map.js"></script>
  <script src="./map/show_markers.js"></script>
  <script src="./main-interface/random_functions.js"></script>
</html>
