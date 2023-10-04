<!-- Session check -->
<?php
  session_start();

  if(!isset($_SESSION['user_id'])) {
    header('Location: http://webproject2023.ddns.net/index.php');
  }

?>

<!DOCTYPE HTML>
<html>

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="profile.css" />
    <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
    />
    <title>Make Dis.Count User Profile</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<script>
      $(document).ready(function() {
        
        $("#change_username_button").click(function(e) {
          e.preventDefault(); 

          // Get the user input values
          var current_username = $("input[id='currentUsername']").val();
          var new_username = $("input[id='newUsername']").val();
          var confirm_username = $("input[id='confirmNewUsername']").val();
          var function_type = "username_change"

          var data = {
            current_username: current_username,
            new_username: new_username,
            confirm_username: confirm_username,
            functionality: function_type
          };
          
          let ajaxString = JSON.stringify(data);

          // Send an AJAX POST request to the server
          $.ajax({
            type: "POST",
            url: "./profile_functions.php", 
            data: { data: ajaxString },
            success: function(response) {
              if (response == 200) {
                alert("Username changed successfully!");
              } else {
                alert(response);
              }
            },
            error: function(xhr, status, error) {
              // Handle any errors that occur during the AJAX request
              alert("An error occurred while processing your request.");
              console.error(xhr.responseText);
            }
          });
        });

        $("#change_password_button").click(function(e) {
          e.preventDefault(); 

          // Get the user input values
          var current_password = $("input[id='currentPassword']").val();
          var new_password = $("input[id='newPassword']").val();
          var confirm_password = $("input[id='confirmNewPassword']").val();
          var function_type = "password_change"

          var data = {
            current_password: current_password,
            new_password: new_password,
            confirm_password: confirm_password,
            functionality: function_type
          };
          
          let ajaxString = JSON.stringify(data);

          // Send an AJAX POST request to the server
          $.ajax({
            type: "POST",
            url: "./profile_functions.php", 
            data: { data: ajaxString },
            success: function(response) {
              if (response == 200) {
                alert("Password changed successfully!");
              } else {
                alert(response);
              }
            },
            error: function(xhr, status, error) {
              // Handle any errors that occur during the AJAX request
              alert("An error occurred while processing your request.");
              console.error(xhr.responseText);
            }
          });
        });
      });
</script>

<script>
    $(document).ready(function () {
    // Function to compare dates, considering null dates as the latest
    function compareDates(a, b) {
        if (!a.date_submitted) return 1;
        if (!b.date_submitted) return -1;
        return new Date(a.date_submitted) - new Date(b.date_submitted);
    }

    // Function to populate the User History table
    function populateUserHistoryTable(data) {
        var tableBody = document.getElementById("userHistoryTableBody");

        // Clear existing table rows
        tableBody.innerHTML = "";

        // Check if data is an array before sorting
        if (Array.isArray(data)) {
            // Sort the data by date
            data.sort(compareDates);

            // Loop through the sorted JSON data and create table rows
            for (var i = 0; i < data.length; i++) {
                var row = document.createElement("tr");

                var actionCell = document.createElement("td");
                actionCell.textContent = data[i].action;

                var productCell = document.createElement("td");
                productCell.textContent = data[i].product_name;

                var marketCell = document.createElement("td");
                marketCell.textContent = data[i].market_name;

                var dateCell = document.createElement("td");
                dateCell.textContent = data[i].date_submitted;

                row.appendChild(actionCell);
                row.appendChild(productCell);
                row.appendChild(marketCell);
                row.appendChild(dateCell);

                tableBody.appendChild(row);
            }
        } else {
            // Handle the case where data is not an array
            console.error("Data is not an array:", data);
            // You may want to display an error message or handle it differently
        }
    }

    // Get the user email from the session
    var userEmail = "<?php echo $_SESSION['user_email']; ?>";

    // Construct the AJAX URL with the user email
    var ajaxUrl = "http://webproject2023.ddns.net/main-interface/get-profile-info.php?userEmail=" + userEmail;

    // Make an AJAX request to retrieve JSON data
    $.ajax({
        type: "GET",
        url: ajaxUrl,
        dataType: "json",
        success: function (data) {
            // Call the function to populate and sort the User History table
            populateUserHistoryTable(data);
        },
        error: function (xhr, status, error) {
            console.error("An error occurred while retrieving data.");
            console.error(xhr.responseText);
        },
    });
});

</script>


<body>
    <header>
        <a class="go-back" href="../index.php">
          <i class="fas fa-arrow-left"></i> Go back to map
        </a>
        <div class="logo-container">
          <a href="../index.php" class="logo">Make.DisCount</a>
        </div>
    </header>

    <div id="container">
        <form id="usernameForm">
            <h2>Change Username</h2>
            <label for="currentUsername">Current Username:</label>
            <input type="text" id="currentUsername" name="currentUsername" required><br><br>

            <label for="newUsername">New Username:</label>
            <input type="text" id="newUsername" name="newUsername" required><br><br>

            <label for="confirmNewUsername">Confirm New Username:</label>
            <input type="text" id="confirmNewUsername" name="confirmNewUsername" required><br><br>

            <div class="form_btn_container">
                  <button id="change_username_button" class="change_username_button">Change Username</button>
            </div>
        </form>

        <form id="passwordForm">
            <h2>Change Password</h2>
            <label for="currentPassword">Current Password:</label>
            <input type="password" id="currentPassword" name="currentPassword" required><br><br>

            <label for="newPassword">New Password:</label>
            <input type="password" id="newPassword" name="newPassword" required><br><br>

            <label for="confirmNewPassword">Confirm New Password:</label>
            <input type="password" id="confirmNewPassword" name="confirmNewPassword" required><br><br>

            <div class="form_btn_container">
                  <button id="change_password_button" class="change_password_button">Change Password</button>
            </div>
        </form>

        <hr>

        <h2>User History</h2>
        <table id="likeDislikeHistory">
            <thead>
                <tr>
                    <th>Action</th>
                    <th>Product</th>
                    <th>Market</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody id="userHistoryTableBody">
                <!-- Like/dislike history rows will be filled dynamically using JavaScript -->
            </tbody>
        </table>
        <table id="profileInfo">
            <tr>
                <th>Total Score</th>
                <td><!-- Display total score here --></td>
            </tr>
            <tr>
                <th>Current Month's Score</th>
                <td><!-- Display current month's score here --></td>
            </tr>
            <tr>
                <th>Tokens Received (Last Month)</th>
                <td><!-- Display tokens received last month here --></td>
            </tr>
            <tr>
                <th>Total Tokens Received (Since Registration)</th>
                <td><!-- Display total tokens received here --></td>
            </tr>
        </table>
    </div>
</body>

</html>
