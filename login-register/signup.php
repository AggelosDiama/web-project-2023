<?php

    if (isset($_POST['register'])){

        $email = $_POST["email"];
        $username = $_POST["username"];
        $password = $_POST["password"];
        $hashed_pwd = password_hash($password, PASSWORD_DEFAULT);


        include "database_config.php";

        $sql = "SELECT email FROM users WHERE email='$email'";
        $result1 = $conn->query($sql);
        $sql = "SELECT username FROM users WHERE username='$username'";
        $result2 = $conn->query($sql);

        if ($result1->num_rows > 0) {
            echo "
                    <script>alert('Email already exists.');</script>
                ";
        }
        elseif($result2->num_rows > 0) {
            echo "
                    <script>alert('User already exists.');</script>
                ";
        }
        else{

            $sql = "INSERT INTO users (email, username, user_password)
            VALUES ('$email', '$username', '$hashed_pwd');";

            if ($conn->query($sql) === TRUE) {
                $last_id = $conn->insert_id;
                echo "
                    <script>alert('New user created successfully');</script>
                ";
                header("sign_in.php");
            }
            else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
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
          <div class="form_title">Sign Up</div>
          <div class="form_content">
            <form action="main.html" method="#">
              <div class="form_data" id="email">
                <div class="form_label">Username</div>
                <div class="form_input">
                  <input type="text" placeholder="Enter Username" />
                </div>
              </div>
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
              <div class="form_data" id="password">
                <div class="form_label">Confirm Password</div>
                <div class="form_input">
                  <input type="password" placeholder="Confrim your Password" />
                </div>
                <div class="form_btn_container">
                  <button class="form_button">Sign Up</button>
                </div>
                <div class="form_redirect">
                  <p>
                    Already have an account? <br />
                    <a href="login.html">Login Here!</a>
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


<script> 

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

function startJavascript() {
    let email = $("#email").val();
    let username = $("#username").val();
    let password = $("#password").val();
    let secondPassword = $("#secondPassword").val();

    if (!validateEmail(email)) {
        alert('Please enter a valid email.');
    }
    else if(!validateUsername(username)) {
        alert('Please enter a valid password.');
    }
    else if(!validatePassword(password)) {
        alert('Please enter a valid password.');
    }
    else if(secondPassword != password) {
        alert('Password do not match.');
    }
    else{
        document.registerForm.submit();
        $.ajax(
            {
                url:'sign_up.php',
                method: 'POST',
                data: {
                    register: 1,
                    ajaxEmail: email,
                    ajaxUsername: username,
                    ajaxPassword: password
                },
                success: function (response) {
                    console.log("Ajax call succeded");
                    document.registerForm.submit();
                }
            }
        );
    }
}
</script>

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
        let email = $("#email").val();
        let username = $("#username").val();
        let password = $("#password").val();
        let secondPassword = $("#secondPassword").val();

        if (!validateEmail(email)) {
            alert('Please enter a valid email.');
        }
        else if(!validateUsername(username)) {
            alert('Please enter a valid password.');
        }
        else if(!validatePassword(password)) {
            alert('Please enter a valid password.');
        }
        else if(secondPassword != password) {
            alert('Password do not match.');
        }
        else{
            $.ajax(
                {
                    url:'sign_up.php',
                    method: 'POST',
                    data: {
                        register: 1,
                        ajaxEmail: email,
                        ajaxUsername: username,
                        ajaxPassword: password
                    },
                    success: function (response) {
                        console.log("Ajax call succeded");
                        document.registerForm.submit();
                    }
                }
            );
        }
    }
});

</script>