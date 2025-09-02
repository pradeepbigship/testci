<?php
// PHP Scripting starts here
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "1234";
$dbname = "logistics_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// --- Fetching data from the database ---

// Query for total shipments
$sql_total = "SELECT COUNT(*) AS total FROM shipments";
$result_total = $conn->query($sql_total);
$row_total = $result_total->fetch_assoc();
$total_shipments = $row_total['total'];

// Query for in-transit shipments
$sql_transit = "SELECT COUNT(*) AS total FROM shipments WHERE status = 'in_transit'";
$result_transit = $conn->query($sql_transit);
$row_transit = $result_transit->fetch_assoc();
$in_transit_shipments = $row_transit['total'];

// Query for delivered shipments
$sql_delivered = "SELECT COUNT(*) AS total FROM shipments WHERE status = 'delivered'";
$result_delivered = $conn->query($sql_delivered);
$row_delivered = $result_delivered->fetch_assoc();
$delivered_shipments = $row_delivered['total'];

// Query for pending shipments
$sql_pending = "SELECT COUNT(*) AS total FROM shipments WHERE status = 'pending'";
$result_pending = $conn->query($sql_pending);
$row_pending = $result_pending->fetch_assoc();
$pending_shipments = $row_pending['total'];

// Query for recent shipments for the table
$sql_recent = "SELECT * FROM shipments ORDER BY created_at DESC LIMIT 5";
$result_recent = $conn->query($sql_recent);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logistics Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f4f7f9;
        }
        .navbar {
            background-color: #ffffff;
            border-bottom: 1px solid #e0e0e0;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
            transition: transform 0.2s;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .card-icon {
            font-size: 2.5rem;
            color: #007bff;
        }
        .main-content {
            padding-top: 2rem;
            padding-bottom: 2rem;
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold text-primary" href="#">Logistics Hub</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Shipments</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Vehicles</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Reports</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container main-content">
        <h1 class="mb-4">Dashboard</h1>

        <div class="row g-4">
            <div class="col-md-3">
                <div class="card p-3 text-center">
                    <div class="d-flex justify-content-center align-items-center mb-2">
                        <i class="fas fa-box card-icon"></i>
                    </div>
                    <h5 class="card-title mt-2">Total Shipments</h5>
                    <p class="card-text fw-bold fs-3 text-primary"><?php echo $total_shipments; ?></p>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card p-3 text-center">
                    <div class="d-flex justify-content-center align-items-center mb-2">
                        <i class="fas fa-truck-fast card-icon"></i>
                    </div>
                    <h5 class="card-title mt-2">In Transit</h5>
                    <p class="card-text fw-bold fs-3 text-warning"><?php echo $in_transit_shipments; ?></p>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card p-3 text-center">
                    <div class="d-flex justify-content-center align-items-center mb-2">
                        <i class="fas fa-clipboard-check card-icon"></i>
                    </div>
                    <h5 class="card-title mt-2">Delivered</h5>
                    <p class="card-text fw-bold fs-3 text-success"><?php echo $delivered_shipments; ?></p>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card p-3 text-center">
                    <div class="d-flex justify-content-center align-items-center mb-2">
                        <i class="fas fa-hourglass-half card-icon"></i>
                    </div>
                    <h5 class="card-title mt-2">Pending</h5>
                    <p class="card-text fw-bold fs-3 text-danger"><?php echo $pending_shipments; ?></p>
                </div>
            </div>
        </div>

        <div class="row mt-5">
            <div class="col-12">
                <div class="card p-4">
                    <h4 class="mb-3">Recent Shipments</h4>
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th scope="col">Tracking ID</th>
                                <th scope="col">Status</th>
                                <th scope="col">Origin</th>
                                <th scope="col">Destination</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if ($result_recent->num_rows > 0) {
                                // Loop through the results and display each row
                                while($row = $result_recent->fetch_assoc()) {
                                    $status_class = '';
                                    switch ($row['status']) {
                                        case 'in_transit':
                                            $status_class = 'bg-warning';
                                            break;
                                        case 'delivered':
                                            $status_class = 'bg-success';
                                            break;
                                        case 'pending':
                                            $status_class = 'bg-danger';
                                            break;
                                        default:
                                            $status_class = 'bg-secondary';
                                    }
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row['tracking_id']) . "</td>";
                                    echo "<td><span class='badge " . $status_class . "'>" . htmlspecialchars(ucwords(str_replace('_', ' ', $row['status']))) . "</span></td>";
                                    echo "<td>" . htmlspecialchars($row['origin']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['destination']) . "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='4'>No recent shipments found.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>