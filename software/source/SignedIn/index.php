<?php
session_start();

if (empty($_SESSION['username'])) {
    header('location: ../');
    exit();
}

$user = $_SESSION['username'];
$email = $_SESSION['email'] ?? '';
$role = $_SESSION['role'] ?? '';
?>



<!DOCTYPE html>
<html lang="en">
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
<nav>
    <div class="logo">
        <img src="../images/Logo.png" alt="Logo" class="logo-img">
    </div>
    <ul id="menuList">
        <li><a href="./">Home</a></li>
        <li><a href="history.php">Historical Data</a></li>
        <li><a href="alert.php">Alerts</a></li>
        <li><a href="settings.php">Settings</a></li>
        <?php if ($role == "admin") echo '<li><a href="admin.html">Manage</a></li>'; ?>
        <li><a href="../">Sign Out</a></li>
    </ul>
    <div class="menu-icon">
        <i class="fa-solid fa-bars" onclick="toggleMenu()"></i>
    </div>
</nav>

<div class="dashboard-container">
    <div>Welcome, <?= htmlspecialchars($user); ?>!</div>
    <br>
    <h1>Dashboard</h1>

    <div class="stats-container">
        <div class="stat-box">Total Sensors Active <br> [8]</div>
        <div class="stat-box">Alerts Triggered <br> [Active Alerts]</div>
    </div>

    <div class="content-area">
        
        <div class="table-box-plot">
            <div id="line4Plot" style="width:100%;max-width:700px;height:400px;margin-bottom:40px;"></div>
            <div id="line5Plot" style="width:100%;max-width:700px;height:400px;"></div>
        </div>

        <div class="table-box">
            <h2>Sensor Table (Sortable and Searchable)</h2>
            <table>
                <thead>
                    <tr>
                        <th>Sensor ID</th>
                        <th>Location</th>
                        <th>Current Temp (°C)</th>
                        <th>Status</th>
                        <th>Last Recorded</th>
                    </tr>
                </thead>
                <tbody id="sensor-table-body">
                    <!-- Filled dynamically by JS every 30 seconds -->
                </tbody>

            </table>
        </div>
    </div>
</div>

<footer>
    <p>&copy; 2025 Rakusen's - Real-Time Dashboard</p>
    <div class="dark-toggle">
        <button id="darkModeToggle" title="Toggle Dark Mode">🌙</button>
    </div>
</footer>

<script>
window.onload = function () {
    const sensorLabels = ['r01', 'r02', 'r03', 'r04'];
    let line4Data = {}, line5Data = {};

    sensorLabels.forEach(id => {
        line4Data[id] = { x: [], y: [], temp: 100 };
        line5Data[id] = { x: [], y: [], temp: 100 };
    });

    function generateTemperature(prev) {
        return parseFloat((prev + (Math.random() - 0.5) * 4).toFixed(2));
    }

    function getCurrentTime() {
        return new Date().toLocaleTimeString();
    }

    function initPlot(id, title) {
        Plotly.newPlot(id, [], {
            title,
            xaxis: { title: 'Time' },
            yaxis: { title: 'Temperature (°C)' },
            plot_bgcolor: 'rgba(0,0,0,0)',
            paper_bgcolor: 'rgba(0,0,0,0)'
        });
    }

    function updateLineSensors(lineLabel, dataMap, chartId) {
        const time = getCurrentTime();
        const traces = [];

        sensorLabels.forEach(sensorId => {
            const temp = generateTemperature(dataMap[sensorId].temp);
            dataMap[sensorId].temp = temp;
            dataMap[sensorId].x.push(time);
            dataMap[sensorId].y.push(temp);
            if (dataMap[sensorId].x.length > 20) {
                dataMap[sensorId].x.shift();
                dataMap[sensorId].y.shift();
            }

            traces.push({
                x: dataMap[sensorId].x,
                y: dataMap[sensorId].y,
                name: sensorId,
                type: 'scatter',
                mode: 'lines+markers'
            });

            fetch('save_sensor.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `sensor=${encodeURIComponent(sensorId)}&temperature=${temp}&location=${encodeURIComponent(lineLabel)}`
            });
        });

        Plotly.newPlot(chartId, traces, {
            title: `${lineLabel} - Real-Time Temperature`,
            xaxis: { title: 'Time' },
            yaxis: { title: 'Temperature (°C)' },
            plot_bgcolor: 'rgba(0,0,0,0)',
            paper_bgcolor: 'rgba(0,0,0,0)'
        });
    }

    function updateAllPlots() {
        updateLineSensors('Line 4', line4Data, 'line4Plot');
        updateLineSensors('Line 5', line5Data, 'line5Plot');
    }

    initPlot('line4Plot', 'Line 4 - Real-Time Temperature');
    initPlot('line5Plot', 'Line 5 - Real-Time Temperature');

    updateAllPlots();
    setInterval(updateAllPlots, 30000);
};
</script>

<script>
    let menuList = document.getElementById("menuList");
    menuList.style.maxHeight = "0px";

    function toggleMenu(){
        menuList.style.maxHeight = menuList.style.maxHeight === "0px" ? "300px" : "0px";
    }

    const toggleButton = document.getElementById("darkModeToggle");
    const body = document.body;

    if (localStorage.getItem("darkMode") === "enabled") {
        body.classList.add("dark-mode");
    }

    toggleButton.addEventListener("click", () => {
        body.classList.toggle("dark-mode");
        localStorage.setItem("darkMode", body.classList.contains("dark-mode") ? "enabled" : "disabled");
    });
</script>
<script>
function fetchSensorTable() {
    fetch('get_sensors.php')
        .then(res => res.text())
        .then(html => {
            document.getElementById('sensor-table-body').innerHTML = html;
        })
        .catch(err => {
            console.error('Error updating sensor table:', err);
        });
}

// Initial fetch and every 30 seconds
fetchSensorTable();
setInterval(fetchSensorTable, 30000);
</script>
</body>
</html>
