<!-- Session check -->
<?php
  session_start();

  if(isset($_SESSION['user_id'])) {
    header('Location: http://webproject2023.ddns.net/index.php');
  }

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="login-signup.css" />
    <title>MakeDisCount</title>

    <!-- Αjax function to check user credentials -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
      $(document).ready(function() {
        
        $(".form_button").click(function(e) {
          e.preventDefault(); 

          // Get the user input values
          var username = $("input[id='username']").val();
          var email = $("input[id='email']").val();
          var password = $("input[id='password']").val();
          var password_conf = $("input[id='password_conf']").val();
          var login_type = "register"

          // Create a data object to send with the request
          var data = {
            username: username,
            email: email,
            password: password,
            password_conf: password_conf,
            functionality: login_type
          };
          
          let ajaxString = JSON.stringify(data);
          // Send an AJAX POST request to the server
          $.ajax({
            type: "POST",
            url: "./user_login_functions.php", // Replace with your login endpoint
            data: { data: ajaxString },
            success: function(response) {
              if (response == 201) {
                alert("Registration was successful!");
                alert("Please login");
                window.location.href = "./login.php";
              } else {
                alert(response);
              }
            },
            error: function(xhr, status, error) {
              alert("An error occurred while processing your request.");
              console.error(xhr.responseText);
            }
          });
        });
      });
    </script>
  </head>

  <body>
    <section>
      <div class="page_wrapper">
        <div class="logo">
          <!-- <img src="#" alt="MakeDisCountLogo" /> -->
          Make.DisCount
        </div>
        <div class="form_container">
          <div class="form_title">Sign Up</div>
          <div class="form_content">
            <form  action="" method="post">
              <div class="form_data" id="username">
                <div class="form_label">Username</div>
                <div class="form_input">
                  <input type="text" id="username" placeholder="Enter Username" />
                </div>
              </div>
              <div class="form_data" id="email">
                <div class="form_label">Email</div>
                <div class="form_input">
                  <input type="text" id="email" placeholder="Enter Email" />
                </div>
              </div>
              <div class="form_data" id="password">
                <div class="form_label">Password</div>
                <div class="form_input">
                  <input type="password" id="password" 
                    title="Password must be at least 8 characters long, contain at least one uppercase letter, one number, and one special character." 
                    placeholder="Enter Password" />
                </div>
              <div class="form_data" id="password_conf">
                <div class="form_label">Confirm Password</div>
                <div class="form_input">
                  <input type="password" id="password_conf" 
                    title="Password must match the password above." 
                  placeholder="Confrim your Password" />
                </div>
                <div class="form_btn_container">
                  <button class="form_button">Sign Up</button>
                </div>
                <div class="form_redirect">
                  <p>
                    Already have an account? <br />
                    <a href="./login.php">Login Here!</a>
                  </p>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </section>
  </body>
</html>


