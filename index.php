<!-- Session check -->
<?php
  session_start();

  if(!isset($_SESSION['user_id'])) {
    header("Location: http://webproject2023.ddns.net/login-register/login.php");
  }
  $username = $_SESSION['username'];
  $admin = $_SESSION['is_admin'];
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
  <div id="overlay" class="overlay"></div>
    <div class="page_wrapper">
      <aside class="sidebar">
        <div class="logo-wrapper">
          <a href="index.php" class="logo">Make.DisCount</a>
        </div>
        <p>Search by: </p>
        <div class="search-options">
          <button class="search-mode-button active" id="searchByMarketName">Market Name</button>
          <button class="search-mode-button" id="searchByProductCategory">Product Category</button>
        </div>
        <div class="search-inputs">
          <input
            class="searchbar"
            type="text"
            placeholder="Search stores"
            id="searchInput"
          />
          <select id="categorySearchDropdown" class="hidden">
            <option value="" disabled selected>Choose a Product Category</option>
            <!-- Options for product category will be added dynamically -->
          </select>
        </div>
        <div id="searchResults" class="search-results"></div>
      </aside>
      <aside class="sidebarStoreDetails">
      <!-- Store details and products panel -->  
        <div class="sidebar-container">
          <div id="storeDetails" class="store-details">
            <i class="fa-solid fa-xmark"></i>
            <button id="submitOffer" onclick="openPopup()">Submit an Offer</button>
            <h2 id="storeName">Store Name</h2>
            
            <ul id="productList" class="product-list">
              <!-- Products will be added here dynamically -->
            </ul>
          </div>
        </div>
      </aside>

      <!-- Popup container -->
      <div id="offerPopup" class="popup">
        <div class="popup-content">
          <span class="popup-close" onclick="closePopup()">&times;</span>
          <h3>Submit an Offer</h3>
          <form>
            <label for="categoryDropdown">Select Category:</label>
            <select id="categoryDropdown">
              <option value="" disabled selected>Choose a Product Category</option>
            </select>

            <label for="subcategoryDropdown">Select Subcategory:</label>
            <select id="subcategoryDropdown">
              <option value="" disabled selected>Choose a Product Subcategory</option>
            </select>

            <label for="productDropdown">Select Product:</label>
            <select id="productDropdown"></select>
                <!-- <option value="" disabled selected>Choose a Product</option> -->
                <!-- Options will be added dynamically using JavaScript -->
            </select>
            <label for="searchProduct"><br>Or <br><br> Search Product Directly here:</label>
            <input type="text" id="searchProduct" name="searchProduct" oninput="searchProducts(this.value)">
            <div id="suggestionList">
              <!-- Suggestions will be displayed here -->
            </div> 

            <label for="offerPrice"><br>Offer Price (in €):</label>
            <input type="number" id="offerPrice" name="offerPrice" required>
            <button type="button" onclick="submitOffer()">Submit</button>
          </form>
        </div>
      </div>

      <div class="avatar-container">
        <div class="user-menu">
          <i class="fa-solid fa-user-ninja" id="avatar-icon"></i>
        
          <div class="dropdown-content">
            <p class="username">
              <?php echo $varDumpOutput; ?>
              <?php if ($admin) { echo " (Admin)"; } ?>
            </p>
            <a href="main-interface/profile.php" id="test">Edit Profile</a>
            <?php if ($admin) { echo '<a href="main-interface/admin.css">Admin Page</a>'; } ?> <!-- available only to admin users -->
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
  <script src="./map/marker_icons.js"></script>
  <script src="./map/show_markers.js"></script>
  <script src="./main-interface/popup_function.js"></script>
  <script src="./main-interface/random_functions.js"></script>
</html>
