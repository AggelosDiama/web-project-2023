<!DOCTYPE HTML>
<html>

<head>
    <meta charset="UTF-8">

    <link rel="stylesheet" href="profile.css" />
    <title>Make Dis.Count User Profile</title>
</head>

<body>
    <header>
        <a href="#" class="logo">Make.DisCount</a>
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

            <input type="submit" value="Submit">
        </form>

        <form id="passwordForm">
            <h2>Change Password</h2>
            <label for="currentPassword">Current Password:</label>
            <input type="password" id="currentPassword" name="currentPassword" required><br><br>

            <label for="newPassword">New Password:</label>
            <input type="password" id="newPassword" name="newPassword" required><br><br>

            <label for="confirmNewPassword">Confirm New Password:</label>
            <input type="password" id="confirmNewPassword" name="confirmNewPassword" required><br><br>

            <input type="submit" value="Submit">
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
            <tbody>
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

    <script>
        // JavaScript code to fetch and populate the table with data
        fetch('fetch_data.php') // Replace with the actual PHP file to fetch data
            .then(response => response.json())
            .then(data => {
                const tableBody = document.querySelector('#dataTable tbody');
                data.forEach(item => {
                    const row = document.createElement('tr');
                    row.innerHTML = `<td>${item.id}</td><td>${item.name}</td><td>${item.email}</td>`;
                    tableBody.appendChild(row);
                });
            })
            .catch(error => {
                console.error('Error fetching data:', error);
            });
    </script>
</body>

</html>
