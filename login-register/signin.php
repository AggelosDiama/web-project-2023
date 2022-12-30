<?php

    if(isset($_POST['login'])) {

        $email = $_POST['email'];
        $password = $_POST['password'];

        include "database_config.php";


        $sql = "SELECT * FROM users WHERE email='$email'";
        $result = $conn->query($sql);
            
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $hashed_pwd = $row['user_password'];
            
            if(password_verify($password ,$hashed_pwd)){
              $conn->close();
              echo("Login succeded.");
              session_start();
              $_SESSION["username"] = $row['username'];
              $_SESSION["email"] = $row['email'];
              $_SESSION["is_admin"] = $row['is_admin'];
              $_SESSION["user_id"] = $row['userid'];
              if($_SESSION["is_admin"] == 1){
                header("Location: main_page/admin_page/admin.php");
                exit;
              }
              header("Location: main_page/index.php");
              exit;
            }
            else{
              echo "
              <script>alert('Password is incorrect.');</script>
              ";
            }  
        }
        else {
          echo "
          <script>alert('Email does not exist.');</script>
      ";
        }
        
        $conn->close();

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
  </head>
  <body>
    <section>
      <div class="page_wrapper">
        <div class="logo">
          <!-- <img src="#" alt="MakeDisCountLogo" /> -->
          Make.DisCount
        </div>
        <div class="form_container">
          <div class="form_title">Login</div>
          <div class="form_content">
            <form action="main.html" method="#">
              <div class="form_data" id="email">
                <div class="form_label">Email</div>
                <div class="form_input">
                  <input type="text" placeholder="Enter Email" />
                </div>
              </div>
              <div class="form_data" id="password">
                <div class="form_label">Password</div>
                <div class="form_input">
                  <input type="password" placeholder="Enter Password" />
                </div>
                <div class="form_btn_container">
                  <button class="form_button">Login</button>
                </div>
                <div class="form_redirect">
                  <p>
                    Don't you have an account? <br />
                    <a href="signup.html">Sign Up Here!</a>
                  </p>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </section>

  <script type="text/javascript">

    function validateEmail(email) {
      const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
      return re.test(String(email).toLowerCase());
    }


    function validateUsername(username) {
      const re = /^[a-zA-Z0-9-' ]*$/;
      return re.test(String(username).toLowerCase());
    }


    function validatePassword(password) {
      const re = /^[a-zA-Z0-9-' ]*$/;
      return re.test(String(password).toLowerCase());
    }



    $(document).ready(function () {
      function startAjax(){

            alert('Skata');
            
            if (!validateEmail(email)) {
                alert('Please enter a valid email.');
            }
            else if(!validatePassword(password)) {
                  alert('Please enter a valid password.');
            }
            else{
                $.ajax(
                    {
                        url: 'sign_in.php',
                        method: 'POST',
                        data: {
                            login: 1,
                            ajaxEmail: $("#email").val(),
                            ajaxPassword: $("#password").val()
                        },
                        success: function (response) {
                            console.log("Ajax call succeded");
                            document.loginForm.submit();
                        }
                    }
                );
            }
      }
    });

  </script>

  </body>
</html>
