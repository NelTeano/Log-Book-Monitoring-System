<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: login.html');
    exit();
}

require 'dbconn1.php'; 

$config = new Config();
$conn = $config->conn;

$query = "SELECT * FROM book";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Logs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">

</head>
<body>
        <div class='container-fluid'>
            <div class="row">
                <div class="col-2">
                    <nav class='container text-bg-dark p-3'  style="display: flex; justify-content: flex-between; flex-direction: column; min-height: 1000px; height: 100%;">
                        <div>
                            <div class='col'>
                                <div class="col-md-12"> <img src="./default.jpg" id="loginavatar" style="width:130px;height:130px;display: block;margin: 0 auto;border: 0 px solid;border-radius: 100%;margin: 3em auto 1.5em;object-fit: cover;" class="img-circle img-responsive"> </div>
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
                    <h1 style='text-transform: capitalize; margin-bottom: 50px;'>Log Book</h1>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>Time-In</th>
                                    <th>Time-Out</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="bookData">
                                <?php if ($result && $result->num_rows > 0): ?>
                                    <?php while ($row = $result->fetch_assoc()): ?>
                                        <tr data-id="<?php echo $row['person_id']; ?>">
                                            <td><?php echo htmlspecialchars($row['person_id']); ?></td>
                                            <td><?php echo htmlspecialchars($row['first_name']); ?></td>
                                            <td><?php echo htmlspecialchars($row['last_name']); ?></td>
                                            <td><?php echo htmlspecialchars($row['time_in']); ?></td>
                                            <td><?php echo htmlspecialchars($row['time_out']); ?></td>
                                            <td>
                                                <button class="btn btn-warning btn-sm edit-btn" data-bs-toggle="modal" data-bs-target="#editModal">Edit</button>
                                                <button class="btn btn-danger btn-sm delete-btn">Delete</button>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center">No data available</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                        <div style='display:flex; justify-content: right; align-items: center; gap: 10px;'>
                            <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addModal">Add Record</button>
                        </div>
                    </div>
            
                    <!-- Edit Modal -->
                    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form id="editForm">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editModalLabel">Edit Book Record</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" id="editPersonId" name="person_id">
                                        <div class="mb-3">
                                            <label for="editFirstName" class="form-label">First Name</label>
                                            <input type="text" class="form-control" id="editFirstName" name="first_name" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="editLastName" class="form-label">Last Name</label>
                                            <input type="text" class="form-control" id="editLastName" name="last_name" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="editTimeIn" class="form-label">Time-In</label>
                                            <input type="datetime-local" class="form-control" id="editTimeIn" name="time_in" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="editTimeOut" class="form-label">Time-Out</label>
                                            <input type="datetime-local" class="form-control" id="editTimeOut" name="time_out" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Save Changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
            
                    <!-- Add Modal -->
                    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form id="addForm">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="addModalLabel">Add New Record</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="addFirstName" class="form-label">First Name</label>
                                            <input type="text" class="form-control" id="addFirstName" name="first_name" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="addLastName" class="form-label">Last Name</label>
                                            <input type="text" class="form-control" id="addLastName" name="last_name" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="addTimeIn" class="form-label">Time-In</label>
                                            <input type="datetime-local" class="form-control" id="addTimeIn" name="time_in" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="addTimeOut" class="form-label">Time-Out</label>
                                            <input type="datetime-local" class="form-control" id="addTimeOut" name="time_out" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Add Record</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Edit button handler
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', event => {
                const row = event.target.closest('tr');
                const id = row.dataset.id;
                const firstName = row.children[1].textContent.trim();
                const lastName = row.children[2].textContent.trim();
                const timeIn = row.children[3].textContent.trim();
                const timeOut = row.children[4].textContent.trim();

                document.getElementById('editPersonId').value = id;
                document.getElementById('editFirstName').value = firstName;
                document.getElementById('editLastName').value = lastName;
                document.getElementById('editTimeIn').value = new Date(timeIn).toISOString().slice(0, 16);
                document.getElementById('editTimeOut').value = new Date(timeOut).toISOString().slice(0, 16);
            });
        });

        // Add form submission
        document.getElementById('addForm').addEventListener('submit', event => {
            event.preventDefault();
            const formData = new FormData(event.target);
            fetch('add_book.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                alert(data);
                location.reload();
            })
            .catch(err => console.error(err));
        });

        // Edit form submission
        document.getElementById('editForm').addEventListener('submit', event => {
            event.preventDefault();
            const formData = new FormData(event.target);
            fetch('edit_book.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                alert(data);
                location.reload();
            })
            .catch(err => console.error(err));
        });

        // Delete button handler
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', event => {
                const row = event.target.closest('tr');
                const id = row.dataset.id;

                if (confirm('Are you sure you want to delete this record?')) {
                    fetch('delete_book.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ person_id: id })
                    })
                    .then(response => response.text())
                    .then(data => {
                        alert(data);
                        location.reload();
                    })
                    .catch(err => console.error(err));
                }
            });
        });
    </script>
</body>
</html>
