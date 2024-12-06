<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: login.html');
    exit();
}

require 'dbconn1.php'; 

$config = new Config();
$conn = $config->conn;

// Fetch user details including the image
$username = $_SESSION['username'];
$query = "SELECT image FROM users WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $username);
$stmt->execute();
$stmt->bind_result($userImage);
$stmt->fetch();
$stmt->close();

// Default image if no profile image is uploaded
if (empty($userImage)) {
    $userImage = './default.jpg';
}

// Define the number of records per page
$records_per_page = 8;

// Get the current page or set a default
if (isset($_GET['page']) && is_numeric($_GET['page'])) {
    $current_page = (int) $_GET['page'];
} else {
    $current_page = 1;
}

// Calculate the starting record of the current page
$start_from = ($current_page - 1) * $records_per_page;

// Query to get the total number of records
$total_query = "SELECT COUNT(*) AS total FROM history";
$total_result = $conn->query($total_query);
$total_row = $total_result->fetch_assoc();
$total_records = $total_row['total'];

// Query to fetch the records for the current page
$query = "SELECT * FROM history LIMIT $start_from, $records_per_page";
$result = $conn->query($query);

// Calculate the total number of pages
$total_pages = ceil($total_records / $records_per_page);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History Logs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
</head>
<body>
    <div class='container-fluid'>
        <div class="row">
            <div class="col-2">
                    <nav class='container text-bg-dark p-3'  style="display: flex; justify-content: flex-between; flex-direction: column; min-height: 1000px; height: 100%;">
                        <div>
                            <div class="col-md-12"> 
                                <img src="<?php echo htmlspecialchars($userImage); ?>" id="loginavatar" 
                                    style="width:130px; height:130px; display:block; margin:0 auto; border:0px solid; border-radius:100%; margin:3em auto 1.5em; object-fit:cover;" 
                                    class="img-circle img-responsive"
                                > 
                            </div>
                            <div class='col text-center'>
                                <h1 style='text-transform: capitalize;'><?php echo htmlspecialchars($_SESSION['username']); ?></h1>
                            </div>
                            <div class='col text-left' style='display: flex; flex-direction:column; margin-top:80px; gap:40px;'>
                                <a class='text-white fs-5 text-decoration-none' href="main_page.php"><span style='margin-right:20px;'><i class="bi bi-book"></i></span>Book Logs</a>
                                <a class='text-white fs-5 text-decoration-none' href="history.php"><span style='margin-right:20px;'><i class="bi bi-clock-history"></i></span>History Logs</a>
                            </div>
                        </div>    
                        <div class='col' style='display: flex; justify-content: center;  align-items: center; margin-top:200px; width: 100%;'>
                            <a href="logout.php" class="btn btn-danger w-75">Logout</a>
                        </div>
                    </nav>
                </div>
            <div class="col-10">
                <div class="container mt-5">
                    <h1 style='text-transform: capitalize; margin-bottom: 50px;'>History Logs</h1>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Person ID</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Time-In</th>
                                <th>Time-Out</th>
                            </tr>
                        </thead>
                        <tbody id="historyData">
                            <?php if ($result && $result->num_rows > 0): ?>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['person_id']); ?></td>
                                        <td><?php echo htmlspecialchars($row['first_name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['last_name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['time_in']); ?></td>
                                        <td><?php echo htmlspecialchars($row['time_out']); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center">No history available</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    <nav aria-label="Page navigation example">
                        <ul class="pagination">
                            <?php if ($current_page > 1): ?>
                                <li class="page-item"><a class="page-link" href="?page=<?php echo $current_page - 1; ?>">Previous</a></li>
                            <?php endif; ?>
                            
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?php echo ($i == $current_page) ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php if ($current_page < $total_pages): ?>
                                <li class="page-item"><a class="page-link" href="?page=<?php echo $current_page + 1; ?>">Next</a></li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
