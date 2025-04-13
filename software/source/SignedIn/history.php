<?php
    session_start();
    if (empty($_SESSION['username'])):
        header('location: ../');
    endif;

    $user = $_SESSION['username'];
    $email = $_SESSION['email'];
    $role = $_SESSION['role'];
    $result = "";

    require_once("scripts/query.php");
    require_once("scripts/table.php");

    $filteredRows = [];
    $line = "";
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
            <h1>Historical Data</h1>
            <div class="stats-container">
                <!-- Data Filters -->
                <div class="stat-box">Date range selector and filters
                    <form name="historyfilters" method="POST">
                        <label for="start">Start Date:</label>
                        <input type="datetime-local" name="start" value="<?= isset($_POST['start']) ? $_POST['start'] : '' ?>"><br>
                        <label for="end">End Date:</label>
                        <input type="datetime-local" name="end" value="<?= isset($_POST['end']) ? $_POST['end'] : '' ?>">
                        <br>
                        <label for="sensor">Sensor ID:</label>
                        <input type="number" name="sensor" value="<?= isset($_POST['sensor']) ? $_POST['sensor'] : '1' ?>">
                        <label for="line">Line Number:</label>
                        <input type="number" name="line" value="<?= isset($_POST['line']) ? $_POST['line'] : '4' ?>">
                        <br>
                        Status:
                        <input type="checkbox" id="green" name="green" value="G" <?= !isset($_POST['apply']) || isset($_POST['green']) ? 'checked' : '' ?>>
                        <label for="green">Green</label>
                        <input type="checkbox" id="amber" name="amber" value="A" <?= !isset($_POST['apply']) || isset($_POST['amber']) ? 'checked' : '' ?>>
                        <label for="amber">Amber</label>
                        <input type="checkbox" id="red" name="red" value="R" <?= !isset($_POST['apply']) || isset($_POST['red']) ? 'checked' : '' ?>>
                        <label for="red">Red</label>
                        <br>
                        <button type="submit" name="apply">Apply Filters</button>
                        <button type="reset" name="reset">Reset Filters</button>
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
                        /* if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apply'])) {
                            historyfilters($_POST);
                        } */
                        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apply'])) {

                            if (!empty($_POST['line']) && !empty($_POST['sensor'])) {
                                $sensor = "r0" . $_POST['sensor'];
                                $line = "line" . $_POST['line'];
                            }

                            // Process Status Checkboxes (Green, Amber, Red)
                            $statuses = [];
                            if (isset($_POST['green'])) $statuses[] = 'G';  // Green
                            if (isset($_POST['amber'])) $statuses[] = 'A';  // Amber
                            if (isset($_POST['red'])) $statuses[] = 'R';    // Red

                            require_once('../scripts/dbconnect.php');

                            $sql = "SELECT timestamp, $sensor FROM $line ORDER BY timestamp DESC";
                            $params = [];
                            $types = "";

                            if (!empty($_POST['start']) && !empty($_POST['end'])) {
                                $startDate = date('Y-m-d H:i:s', strtotime($_POST['start']));
                                $endDate = date('Y-m-d H:i:s', strtotime($_POST['end']));

                                $sql .= " WHERE timestamp BETWEEN ? AND ?";
                                $params = [$startDate, $endDate];
                                $types = "ss";
                            }

                            $sql .= " LIMIT 100"; // optional: adjust limit

                            $stmt = $conn->prepare($sql);
                            if (!empty($params)) {
                                $stmt->bind_param($types, ...$params);
                            }
                            $stmt->execute();

                            try {
                                //$result = $conn->query($sql);
                                $result = $stmt->get_result();

                                // Filter Results Based on Selected Statuses
                                //$filteredRows = [];

                                while ($row = $result->fetch_assoc()) {
                                    $value = $row[$sensor];

                                    // Filter Based on Status Selection (Green, Amber, Red)
                                    $include = false;

                                    if (in_array('G', $statuses) && $value <= 250) {
                                        $include = true; // Green
                                    }
                                    if (in_array('A', $statuses) && $value > 250 && $value <= 375) {
                                        $include = true; // Amber
                                    }
                                    if (in_array('R', $statuses) && $value > 375) {
                                        $include = true; // Red
                                    }

                                    // If the value matches any selected filter, add to the filtered result
                                    if ($include) {
                                        $row['status'] = ($value <= 250) ? 'Green' : (($value <= 375) ? 'Amber' : 'Red');
                                        $filteredRows[] = $row;
                                    }
                                }
                            }
                            catch (Exception $e) {
                                $result = "";
                            }
                        }
                    ?>
                    <h2>Sensor Table</h2>
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
                            <?php
                                historyrow($filteredRows, $line);
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
