<?php
    session_start();
    if (empty($_SESSION['username'])):
        header('location: ../');
    endif;

    $user = $_SESSION['username'];
    $email = $_SESSION['email'];
    $role = $_SESSION['role'];
    $result = "";
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Management</title>
        <link rel="stylesheet" href="../styles/cmn.css">
        <link rel="stylesheet" href="styles/admin.css">
        <link rel="stylesheet" href="styles/table.css">
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
                        echo '<li><a href="admin.php">Manage</a></li>';
                    }
                ?>
                <li><a href="../">Sign Out</a></li>
            </ul>
            <div class="menu-icon">
                <i class="fa-solid fa-bars" onclick="toggleMenu()"></i>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="dashboard-container">
            <h1>Management</h1>
            <div class="stats-container">
                <div class="stat-box">Date range selector and filters
                    <form method="POST">
                        <label for="start">Start Date:</label>
                        <input type="datetime-local" name="start" value="<?= isset($_POST['start']) ? $_POST['start'] : '' ?>">
                        <label for="end">End Date:</label>
                        <input type="datetime-local" name="end" value="<?= isset($_POST['end']) ? $_POST['end'] : '' ?>">
                        <br>
                        <label for="sensor">Sensor ID:</label>
                        <input type="number" name="sensor" value="<?= isset($_POST['sensor']) ? $_POST['sensor'] : '' ?>">
                        <label for="line">Line Number:</label>
                        <input type="number" name="line" value="<?= isset($_POST['line']) ? $_POST['line'] : '' ?>">
                        <br>
                        <label>Request Type:
                            <select name="role">
                                <option value="add">Add</option>
                                <option value="update">Update</option>
                                <option value="forgot">Forgot</option>
                            </select>
                        </label>
                        <br>
                        <button type="submit" name="apply">Apply</button>
                        <button type="reset" name="reset">Reset</button>
                    </form>
                </div>

                <div class="table-box">
                    <?php
                        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apply'])) {
                            require_once('../scripts/dbconnect.php');

                            $sql = "SELECT * FROM requests";
                            $sql .= " LIMIT 100"; // optional: adjust limit

                            try {
                                $result = $conn->query($sql);
                            }
                            catch (Exception $e) {
                                $result = "";
                            }
                        }
                    ?>
                    <h2>User Management Table</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Request ID</th>
                                <th>Type</th>
                                <th>Email Address</th>
                                <th>Username</th>
                                <th>Role</th>
                                <th>Timestamp</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result && $result->num_rows > 0): ?>
                                <?php while($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['id']) ?></td>
                                        <td><?= htmlspecialchars($row['type']) ?></td>
                                        <td><?= htmlspecialchars($row['email']) ?></td>
                                        <td><?= htmlspecialchars($row['user_name']) ?></td>
                                        <td><?= htmlspecialchars($row['role']) ?></td>
                                        <td><?= htmlspecialchars($row['requested_at']) ?></td>
                                        <td>
                                            <form method="post">
                                                <input type="hidden" name="user_id" value="<?= htmlspecialchars($row['id']) ?>">
                                                <button type="submit" name="accept">Accept</button>
                                                <button type="submit" name="deny">Deny</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="6">No Requests Found.</td></tr>
                            <?php endif; ?>
                            <?php
                                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                                    if (isset($_POST['accept'])) {
                                        $userId = $_POST['user_id'];
                                        require_once("scripts/add.php");
                                        adduser(htmlspecialchars($userId));
                                    }

                                    if (isset($_POST['deny'])) {
                                        $userId = $_POST['user_id'];
                                        // Do something else
                                        echo "Denied user with ID: " . htmlspecialchars($userId);
                                    }
                                }
                            ?>
                        </tbody>
                    </table>
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
