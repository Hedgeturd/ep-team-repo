<?php
    session_start();
    if (empty($_SESSION['username'])):
        header('location: ../');
    endif;

    $user = $_SESSION['username'];
    $email = $_SESSION['email'];
    $role = $_SESSION['role'];

    if ($role != "admin") {
        header('location: ../');
    }

    $reqresult = $usrresult = "";

    // This is run on start up for the table to fill
    require_once('scripts/dbconnect.php');
    require_once("scripts/query.php");
    require_once("scripts/table.php");

    $reqsql = "SELECT * FROM requests LIMIT 10";
    $usrsql = "SELECT * FROM users LIMIT 10";

    try {
        $reqresult = $conn->query($reqsql);
        $usrresult = $conn->query($usrsql);
    }
    catch (Exception $e) {
        $reqresult = $usrresult = "";
    }
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
                <div class="stat-box">Request Selector and Filters
                    <form method="POST">
                        <label for="req_start">Start Date:</label>
                        <input type="datetime-local" name="req_start" value="<?= isset($_POST['req_start']) ? $_POST['req_start'] : '' ?>"><br>
                        <label for="req_end">End Date:</label>
                        <input type="datetime-local" name="req_end" value="<?= isset($_POST['req_end']) ? $_POST['req_end'] : '' ?>">
                        <br>
                        <label for="req_id">Request ID:</label>
                        <input type="number" name="req_id" value="<?= isset($_POST['req_id']) ? $_POST['req_id'] : '' ?>">
                        <br>
                        <label for="req_email">Email:</label>
                        <input type="email" name="req_email" value="<?= isset($_POST['req_email']) ? $_POST['req_email'] : '' ?>">
                        <br>
                        <label>Request Type:
                            <select name="req_role">
                                <option value="add">Add</option>
                                <option value="update">Update</option>
                                <option value="forgot">Forgot</option>
                            </select>
                        </label>
                        <br>
                        <label>Role Type:
                            <select name="req_type">
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
                            </select>
                        </label>
                        <br>
                        <button type="submit" name="apply">Apply Filters</button>
                        <button type="reset" name="reset">Reset Filters</button>
                    </form>
                </div>

                <div class="table-box">
                    <?php
                        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apply'])) {
                            $filteredResults = requestfilters($_POST);
                        }
                    ?>
                    <h2>Request Manager</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Request ID</th>
                                <th>Type</th>
                                <th>Email Address</th>
                                <th>Username</th>
                                <th>Role</th>
                                <th>Requested At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (!empty($filteredResults)) {
                                foreach ($filteredResults as $row) {
                                    echo "<tr>";
                                        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['type']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['user_name']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['role']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['requested_at']) . "</td>";
                                        echo "<td>
                                            <form method='post'>
                                                <input type='hidden' name='req_id' value=" . htmlspecialchars($row['id']) . ">
                                                <button type='submit' name='accept'>Accept</button>
                                                <button type='submit' name='deny'>Deny</button>
                                            </form>
                                        </td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='6'>No Requests Found.</td></tr>";
                            } ?>
                            <?php
                                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                                    if (isset($_POST['accept'])) {
                                        $reqId = $_POST['req_id'];
                                        require_once("scripts/add.php");
                                        adduser(htmlspecialchars($reqId));
                                    }

                                    if (isset($_POST['deny'])) {
                                        $reqId = $_POST['req_id'];
                                        require_once("scripts/delete.php");
                                        delreq(htmlspecialchars($reqId));
                                    }
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="stats-container">
                <div class="stat-box">User Selector and Filters
                    <form method="POST">
                        <label for="usr_start">Start Date:</label>
                        <input type="datetime-local" name="usr_start" value="<?= isset($_POST['usr_start']) ? $_POST['usr_start'] : '' ?>"><br>
                        <label for="usr_end">End Date:</label>
                        <input type="datetime-local" name="usr_end" value="<?= isset($_POST['usr_end']) ? $_POST['usr_end'] : '' ?>">
                        <br>
                        <label for="usr_id">User ID:</label>
                        <input type="number" name="usr_id" value="<?= isset($_POST['usr_id']) ? $_POST['usr_id'] : '' ?>">
                        <br>
                        <label for="usr_email">Email:</label>
                        <input type="email" name="usr_email" value="<?= isset($_POST['usr_email']) ? $_POST['usr_email'] : '' ?>">
                        <br>
                        <label>Role Type:
                            <select name="usr_type">
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
                            </select>
                        </label>
                        <br>
                        <button type="submit" name="apply">Apply Filters</button>
                        <button type="reset" name="reset">Reset Filters</button>
                    </form>
                </div>

                <div class="table-box">
                    <?php
                        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apply'])) {
                            require_once('../scripts/dbconnect.php');

                            $usrsql = "SELECT * FROM users";
                            $usrsql .= " LIMIT 10"; // optional: adjust limit

                            try {
                                $usrresult = $conn->query($usrsql);
                            }
                            catch (Exception $e) {
                                $usrresult = "";
                            }
                        }
                    ?>
                    <h2>User Manager</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>User ID</th>
                                <th>Email Address</th>
                                <th>Username</th>
                                <th>Role</th>
                                <th>Created At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($usrresult && $usrresult->num_rows > 0): ?>
                                <?php while($row = $usrresult->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['id']) ?></td>
                                        <td><?= htmlspecialchars($row['email']) ?></td>
                                        <td><?= htmlspecialchars($row['user_name']) ?></td>
                                        <td><?= htmlspecialchars($row['role']) ?></td>
                                        <td><?= htmlspecialchars($row['created_at']) ?></td>
                                        <td>
                                            <form method="post">
                                                <input type="hidden" name="user_id" value="<?= htmlspecialchars($row['id']) ?>">
                                                <button type="submit" name="delete">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="5">No Users Found.</td></tr>
                            <?php endif; ?>
                                <?php
                                    if ($_SERVER["REQUEST_METHOD"] == "POST") {
                                        if (isset($_POST['delete'])) {
                                            $userId = $_POST['user_id'];
                                            require_once("scripts/delete.php");
                                            deluser(htmlspecialchars($userId));
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
