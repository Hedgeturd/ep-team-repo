<?php
    session_start();
    if (empty($_SESSION['username'])):
        header('location: ../');
    endif;

    $user = $_SESSION['username'];
    $email = $_SESSION['email'];
    $role = $_SESSION['role'];
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Account Settings</title>
        <link rel="stylesheet" href="../styles/cmn.css">
        <link rel="stylesheet" href="styles/settings.css">
        <script src="https://kit.fontawesome.com/7135b02251.js" crossorigin="anonymous"></script>
    </head>

    <body>
        <!-- Navigation Bar -->
        <nav>
            <div class="logo">
                <img src="../images/Logo.png" alt="Logo" class="logo-img">
            </div>
            <ul id="menuList">
                <li><a href="./">Home</a></li>
                <li><a href="history.php">Historical Data</a></li>
                <li><a href="alert.php">Alerts</a></li>
                <li><a href="settings.php">Settings</a></li>
                <?php
                    if ($role == "admin") {
                        echo '<li><a href="admin.html">Manage</a></li>';
                    }
                ?>
                <li><a href="../">Sign Out</a></li>
            </ul>
            <div class="menu-icon">
                <i class="fa-solid fa-bars" onclick="toggleMenu()"></i>
            </div>
        </nav>

        <!-- Main Content -->
        <div style="padding: 20px">
            <!-- <div>Welcome, <?php echo htmlspecialchars($username); ?></div> -->
            <h1>Account Credentials</h1>

            <div class="dashboard-container">
                <div class="stat-box">
                    <!--Detail Input Box-->
                    <form action="scripts/update.php" method="POST">
                        <label>Username: <input type="text" name="username" value="<?php echo htmlspecialchars($user);?>" required></label><br>
                        <label>Email: <input type="email" name="email" value="<?php echo htmlspecialchars($email);?>" required></label><br>
                        <!-- <label>Password: <input type="password" name="pass" required></label><br> -->

                        <br>

                        <label>Role:
                            <select name="role">
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
                            </select>
                        </label>
                        <button class="m_button" type="submit">Update</button>
                    </form>

                    <br>
                    <br>

                    <div>
                        <a href="../forgot.html">Change Password</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer>
            <p>&copy; 2025 Rakusen's - Real-Time Dashboard</p>
            <div class="dark-toggle">
                <button id="darkModeToggle" title="Toggle Dark Mode">
                    🌙
                </button>
            </div>
        </footer>

        <script>
            let menuList = document.getElementById("menuList");
            menuList.style.maxHeight = "0px";

            function toggleMenu(){
                if(menuList.style.maxHeight === "0px") {
                    menuList.style.maxHeight = "300px";
                } else {
                    menuList.style.maxHeight = "0px";
                }
            }
            //darkmode
                window.onload = function () {
                    let menuList = document.getElementById("menuList");
                    menuList.style.maxHeight = "0px";

                    function toggleMenu() {
                        if (menuList.style.maxHeight === "0px") {
                            menuList.style.maxHeight = "300px";
                        } else {
                            menuList.style.maxHeight = "0px";
                        }
                    }

                    // Expose toggleMenu to global scope
                    window.toggleMenu = toggleMenu;

                    const toggleButton = document.getElementById("darkModeToggle");
                    const body = document.body;

                    // Apply saved preference
                    if (localStorage.getItem("darkMode") === "enabled") {
                        body.classList.add("dark-mode");
                    }

                    if (toggleButton) {
                        toggleButton.addEventListener("click", () => {
                            body.classList.toggle("dark-mode");
                            localStorage.setItem("darkMode", body.classList.contains("dark-mode") ? "enabled" : "disabled");
                        });
                    }
                };
        </script>
    </body>
</html>

