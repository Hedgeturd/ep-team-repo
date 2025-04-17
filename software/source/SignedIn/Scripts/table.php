<?php
    function dashrow($resultline, $line) {
        if ($resultline && $resultline->num_rows > 0) {
            while($row = $resultline->fetch_assoc()) {
                for ($i = 1; $i <= 8; $i++) {
                    $col = 'r0' . $i;
                    if (!isset($row[$col])) continue;
                    $value = $row[$col];

                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($col) . "</td>";
                    echo "<td>" . htmlspecialchars($line) . "</td>";
                    echo "<td>" . htmlspecialchars($value) . "°C</td>";
                    echo "<td>";

                    if ($value <= 250) {
                        echo "<span class='status green'>Green</span>";
                    } elseif ($value >= 251 && $value <= 375) {
                        echo "<span class='status amber'>Amber</span>";
                    } elseif ($value >= 376) {
                        echo "<span class='status red'>Red</span>";
                    }

                    echo "</td></tr>";
                }
            }
        } else {
            echo "<tr><td colspan='4'>No users found.</td></tr>";
        }
    }

    function historyrow($filteredRows, $line) {
        if (!empty($filteredRows)) {
            foreach ($filteredRows as $row) {
                for ($i = 1; $i <= 8; $i++) {
                    $col = 'r0' . $i;
                    if (!isset($row[$col])) continue;
                    $value = $row[$col];

                    echo "<tr>";
                        echo "<td>" . htmlspecialchars($col) . "</td>";
                        echo "<td>" . htmlspecialchars($line) . "</td>";
                        echo "<td>" . htmlspecialchars($value) . "°C</td>";
                        echo "<td>";
                            if ($value <= 250) {
                                echo "<span class='status green'>Green</span>";
                            } elseif ($value >= 251 && $value <= 375) {
                                echo "<span class='status amber'>Amber</span>";
                            } elseif ($value >= 376) {
                                echo "<span class='status red'>Red</span>";
                            }
                        echo "</td>";
                        echo "<td>" . htmlspecialchars($row["timestamp"]) . "</td>";
                    echo "</tr>";
                }
            }
        } else {
            echo "<tr><td colspan='5'>No Data Found.</td></tr>";
        }
    }

    function alertrow($filteredRows) { //, $line) {
        if (!empty($filteredRows)) {
            foreach ($filteredRows as $row) {
                $view = "Unread";

                echo "<tr>";
                    echo "<td>" . htmlspecialchars($row["id"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row['location']) . "</td>";
                    echo "<td>" . htmlspecialchars($row["sensor_id"]) . "</td>";
                    echo "<td>" . htmlspecialchars($view) . "</td>";
                    echo "<td>" . htmlspecialchars($row["recorded_at"]) . "</td>";
                    echo "<td>";
                        if ($row["flag"] == "green") {
                            echo "<span class='status green'>LOW</span>";
                        } elseif ($row["flag"] == "yellow") {
                            echo "<span class='status amber'>MEDIUM</span>";
                        } elseif ($row["flag"] == "red") {
                            echo "<span class='status red'>HIGH</span>";
                        }
                    echo "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='6'>No Data Found.</td></tr>";
        }
    }

    function userrow() {
        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['type']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['user_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['role']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['requested_at']) . "</td>";
                    echo "<td>";
                        echo "<form method='post'>";
                            echo "<input type='hidden' name='user_id' value='" . htmlspecialchars($row['id']) . "'>";
                            echo "<button type='submit' name='accept'>Accept</button>";
                            echo "<button type='submit' name='deny'>Deny</button>";
                        echo "</form>";
                    echo "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='6'>No Requests Found.</td></tr>";
        }
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
    }
?>