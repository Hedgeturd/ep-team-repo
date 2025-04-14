<?php
    session_start();
    if (empty($_SESSION['username'])):
        header('location: ../');
    endif;

    $user = $_SESSION['username'];
    $email = $_SESSION['email'];
    $role = $_SESSION['role'];

    require_once('../scripts/dbconnect.php');
    require_once("scripts/table.php");

    $line = "line4";

    $sqline = "SELECT r01, r02, r03, timestamp FROM $line LIMIT 1";
    $resultline = $conn->query($sqline);
?>

<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/html">
    <head>
        <script src="https://cdn.plot.ly/plotly-latest.min.js"></script>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Rakusen's Dashboard</title>
        <link rel="stylesheet" href="../styles/cmn.css">
        <link rel="stylesheet" href="styles/dash.css">
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
            <div>Welcome, <?php echo htmlspecialchars($user); ?>!</div>
            <br>
            <h1>Dashboard</h1>
            <div class="stats-container">
                <div class="stat-box">Total Sensors Active <br> [Live Count]</div>
                <div class="stat-box">Alerts Triggered <br> [Active Alerts]</div>
            </div>
            <div class="content-area">
                <!-- <div class="graph-box">Real-Time Temperature Graph <br> (Line graph - Sensor data updating in real-time)</div> -->
                <div class="table-box-plot">
                    <div id="myPlot" style="width:100%;max-width:700px;height:400px;"></div>
                        <script>
                            const trace = {
                              x: [1, 2, 3, 4, 5],
                              y: [10, 15, 13, 17, 22],
                              type: 'scatter', // or 'bar', 'pie', 'line' (alias for scatter with mode: lines)
                              mode: 'lines+markers',
                              marker: { color: 'red' }
                            };

                            const layout = {
                              title: 'Real-Time Temperature Graph',
                              yaxis: { title: 'Heat (in Degrees)' },
                              xaxis: { title: 'Time' },
                              plot_bgcolor: 'rgba(0,0,0,0)',  // Transparent plot area
                              paper_bgcolor: 'rgba(0,0,0,0)'
                            };

                            Plotly.newPlot('myPlot', [trace], layout);
                        </script>
                </div>
                <div class="table-box">
                    <h2>Sensor Table (Sortable and Searchable)</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Sensor ID</th>
                                <th>Location</th>
                                <th>Current Temp</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                dashrow($resultline, $line);
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
