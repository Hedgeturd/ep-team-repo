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
        <title>Historical Data</title>
        <link rel="stylesheet" href="../styles/cmn.css">
        <link rel="stylesheet" href="styles/history.css">
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
        <div class="dashboard-container">
            <h1>Historical Data</h1>
            <div class="stats-container">
                <div class="stat-box">Date range selector and filters
                    <form method="POST">
                        <label for="start">Start Date:</label>
                        <input type="date" name="start">
                        <label for="end">End Date:</label>
                        <input type="date" name="end">
                        <br>
                        <label for="sensor">Sensor ID:</label>
                        <input type="number" name="sensor">
                        <label for="line">Line Number:</label>
                        <input type="number" name="line">
                        <br>
                        Status:
                        <input type="checkbox" id="green" name="green" value="G">
                        <label for="green">Green</label>
                        <input type="checkbox" id="amber" name="amber" value="A">
                        <label for="amber">Amber</label>
                        <input type="checkbox" id="red" name="red" value="R">
                        <label for="red">Red</label>
                        <br>
                        <button type="submit" name="apply">Apply</button>
                        <button type="reset" name="reset">Reset</button>
                    </form>
                </div>
                <div class="stat-box">Historical Temperature Graph
                    <br> (Line graph - temperature
                    <br> trends over time)
                </div>
            </div>
            <div class="content-area">

                <div class="table-box">
                    <?php
                        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apply'])) {
                            $startDate = $_POST['start'] ?? null;
                            $endDate = $_POST['end'] ?? null;
                            $sensor = "r0" . $_POST['sensor'] ?? null;
                            $line = "line" . $_POST['line'] ?? null;
                            $status = $_POST['status'] ?? [];

                            // Example usage

                            // Now you can use these variables however you want (e.g. DB query, filtering, etc.)

                            if (empty($_POST['line']) || empty($_POST['sensor'])) {
                                //
                            }
                            else {
                                require_once('../scripts/dbconnect.php');
                                $sql = "SELECT timestamp, $sensor FROM $line LIMIT 8";
                                try {
                                    $result = $conn->query($sql);
                                }
                                catch (Exception $e) {
                                    //
                                }
                            }
                        }
                    ?>
                    <h2>Sensor Table (Sortable and Searchable)</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Sensor ID</th>
                                <th>Location</th>
                                <th>Current Temp</th>
                                <th>Status</th>
                                <th>Timestamp</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result && $result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <?php for ($i = 1; $i <= 8; $i++):
                                    $col = 'r0' . $i;
                                    if (!isset($row[$col])) continue;
                                    $value = $row[$col];
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars($col) ?></td>
                                    <td><?= htmlspecialchars($line) ?></td>
                                    <td><?= htmlspecialchars($value) . "°C"?></td>
                                    <td>
                                        <?php
                                            if ($value <= 250) {
                                                echo "<span class='status green'>Green</span>";
                                            } elseif ($value >= 251 && $value <= 375) {
                                                echo "<span class='status amber'>Amber</span>";
                                            } elseif ($value >= 376) {
                                                echo "<span class='status red'>Red</span>";
                                            }
                                        ?>
                                    </td>
                                    <td><?= htmlspecialchars($row["timestamp"])?></td>
                                </tr>
                                <?php endfor; ?>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="4">No users found.</td></tr>
                        <?php endif; ?>
                            <!-- <tr>
                                <td>001</td>
                                <td>Line 4</td>
                                <td>120°C</td>
                                <td><span class="status green">Green</span></td>
                                <td>2025-02-10</td>
                            </tr>
                            <tr>
                                <td>002</td>
                                <td>Line 5</td>
                                <td>180°C</td>
                                <td><span class="status red">Red</span></td>
                                <td>2025-02-11</td>
                            </tr>
                            <tr>
                                <td>003</td>
                                <td>Line 4</td>
                                <td>150°C</td>
                                <td><span class="status amber">Amber</span></td>
                                <td>2025-02-12</td>
                            </tr> -->
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
