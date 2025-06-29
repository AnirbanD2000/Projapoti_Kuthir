<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require_once('../db.php');

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Check if approval_status column exists, if not add it
$result = $conn->query("SHOW COLUMNS FROM biodata_images LIKE 'approval_status'");
if ($result->num_rows == 0) {
    // Add the column
    $conn->query("ALTER TABLE biodata_images ADD COLUMN approval_status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending'");
    // Update existing records to approved
    $conn->query("UPDATE biodata_images SET approval_status = 'approved'");
}

// Fetch all biodata entries
$sql = "SELECT * FROM biodata_images ORDER BY id DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Projapoti Kuthir</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background-color: #15283c;
        }
        .sidebar {
    min-height: 100%;
    background: #343a40;
    color: white;
    padding: 10px 0 30px;
}
        .sidebar .nav-link {
            color: rgba(255,255,255,.8);
        }
        .sidebar .nav-link:hover {
            color: white;
        }
        .main-content {
    padding: 20px;
    background: #15283c;
}
.main-content div h2, .main-content div span {
    color: #fff;
}
        .biodata-card {
            margin-bottom: 20px;
        }
        .biodata-card img {
            width: 100%;
            height: 300px;
            object-fit: cover;
            object-position: top;
        }
        .dash-logo img {
    max-width: 100%;
    height: 50px;
}
@media (max-width: 991.98px) {
    .main-content div {
    flex-direction: column;
}
.sidebar {
    min-height: auto;
    background: #343a40;
    color: white;
    padding: 10px 0 30px;
}
}
@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}
    
.biodata-card {
    transition: all 0.3s ease;
}
    
.biodata-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.3);
}
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0 sidebar">
                <div class="p-3 dash-logo">
                    <img src="../images/logo.jpg" alt="Logo" class="img-fluid mb-3">
                    <h4>Admin Panel</h4>
                </div>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php">
                            <i class="fas fa-home me-2"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="profile.php">
                            <i class="fas fa-user me-2"></i>Profile
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Biodata Management</h2>
                    <span>Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
                </div>

                <!-- Pending Requests Section -->
                <div class="card mb-4" style="background: #2c3e50; border: 1px solid #34495e;">
                    <div class="card-header" style="background: #e74c3c; color: white;">
                        <h5 class="mb-0">
                            <i class="fas fa-clock me-2"></i>Pending Approval Requests
                            <span id="pendingCount" class="badge bg-light text-dark ms-2">0</span>
                        </h5>
                    </div>
                    <div class="card-body" id="pendingRequestsContainer">
                        <div id="pendingRequestsList">
                            <p class="text-muted">Loading pending requests...</p>
                        </div>
                    </div>
                </div>

                <!-- Approved Biodata Cards -->
                <div class="card" style="background: #2c3e50; border: 1px solid #34495e;">
                    <div class="card-header" style="background: #27ae60; color: white;">
                        <h5 class="mb-0">
                            <i class="fas fa-check-circle me-2"></i>Approved Biodata
                            <span id="approvedCount" class="badge bg-light text-dark ms-2">0</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div id="approvedBiodataContainer">
                            <div class="row" id="approvedBiodataList">
                                <?php 
                                // Only show approved biodata
                                $approved_sql = "SELECT * FROM biodata_images WHERE approval_status = 'approved' ORDER BY id DESC";
                                $approved_result = $conn->query($approved_sql);
                                $approved_count = 0;
                                while($row = $approved_result->fetch_assoc()): 
                                    $approved_count++;
                                ?>
                                <div class="col-md-4" data-biodata-id="<?php echo $row['id']; ?>">
                                    <div class="card biodata-card">
                                        <img src="../<?php echo htmlspecialchars($row['image_path']); ?>" class="card-img-top" alt="Biodata Image">
                                        <div class="card-body">
                                            <h5 class="card-title"><?php echo htmlspecialchars($row['name']); ?></h5>
                                            <p class="card-text">
                                                Age: <?php echo htmlspecialchars($row['age']); ?><br>
                                                Type: <?php echo htmlspecialchars($row['type']); ?><br>
                                                Address: <?php echo htmlspecialchars($row['address']); ?>
                                            </p>
                                            <button class="btn btn-primary btn-sm" onclick="openEditModal(<?php echo $row['id']; ?>)">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <button class="btn btn-danger btn-sm" onclick="deleteBiodata(<?php echo $row['id']; ?>)">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Biodata Modal -->
    <div class="modal fade" id="editBiodataModal" tabindex="-1" aria-labelledby="editBiodataModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <form id="editBiodataForm" enctype="multipart/form-data">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="editBiodataModalLabel">Edit Biodata</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <input type="hidden" name="id" id="editBiodataId">
              <div class="mb-3">
                <label for="editImage" class="form-label">Image</label>
                <input type="file" class="form-control" id="editImage" name="image">
                <img id="currentBiodataImage" src="" alt="Current Image" style="width:100px; margin-top:10px;">
              </div>
              <div class="mb-3">
                <label for="editName" class="form-label">Name</label>
                <input type="text" class="form-control" id="editName" name="name" required>
              </div>
              <div class="mb-3">
                <label for="editAge" class="form-label">Age</label>
                <input type="text" class="form-control" id="editAge" name="age" required>
              </div>
              <div class="mb-3">
                <label for="editType" class="form-label">Type</label>
                <select class="form-select" id="editType" name="type" required>
                  <option value="men">Men</option>
                  <option value="women">Women</option>
                </select>
              </div>
              <div class="mb-3">
                <label for="editAddress" class="form-label">Address</label>
                <input type="text" class="form-control" id="editAddress" name="address" required>
              </div>
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-success">Save Changes</button>
            </div>
          </div>
        </form>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Load pending requests on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadPendingRequests();
        updateApprovedCount();
        // Check for new requests every 5 seconds
        setInterval(loadPendingRequests, 5000);
    });

    // Function to update approved count
    function updateApprovedCount() {
        const approvedCards = document.querySelectorAll('#approvedBiodataList .col-md-4');
        const countElement = document.getElementById('approvedCount');
        countElement.textContent = approvedCards.length;
    }

    // Function to add approved biodata card to the dashboard
    function addApprovedBiodataCard(biodata) {
        const approvedList = document.getElementById('approvedBiodataList');
        
        // Create the card HTML
        const cardHtml = `
            <div class="col-md-4" data-biodata-id="${biodata.id}" style="opacity: 0; transition: opacity 0.5s ease-in-out;">
                <div class="card biodata-card">
                    <img src="../${biodata.image_path}" class="card-img-top" alt="Biodata Image">
                    <div class="card-body">
                        <h5 class="card-title">${biodata.name}</h5>
                        <p class="card-text">
                            Age: ${biodata.age}<br>
                            Type: ${biodata.type}<br>
                            Address: ${biodata.address}
                        </p>
                        <button class="btn btn-primary btn-sm" onclick="openEditModal(${biodata.id})">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="deleteBiodata(${biodata.id})">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        // Add the card to the beginning of the list (newest first)
        approvedList.insertAdjacentHTML('afterbegin', cardHtml);
        
        // Fade in the card
        const newCard = approvedList.querySelector(`[data-biodata-id="${biodata.id}"]`);
        setTimeout(() => {
            newCard.style.opacity = '1';
        }, 100);
        
        // Update the count
        updateApprovedCount();
    }

    // Function to load pending requests
    function loadPendingRequests() {
        console.log('Loading pending requests...');
        fetch('get_pending_biodata.php')
            .then(response => {
                console.log('Response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Pending requests data:', data);
                if (data.success) {
                    displayPendingRequests(data.pending_biodata);
                    updatePendingCount(data.count);
                } else {
                    console.error('Error loading pending requests:', data.message);
                    // Show error in the pending requests container
                    document.getElementById('pendingRequestsList').innerHTML = 
                        '<p class="text-danger">Error loading pending requests: ' + data.message + '</p>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('pendingRequestsList').innerHTML = 
                    '<p class="text-danger">Error loading pending requests: ' + error.message + '</p>';
            });
    }

    // Function to display pending requests
    function displayPendingRequests(pendingBiodata) {
        const container = document.getElementById('pendingRequestsList');
        
        if (pendingBiodata.length === 0) {
            container.innerHTML = '<p class="text-muted">No pending requests</p>';
            return;
        }

        let html = '<div class="row">';
        pendingBiodata.forEach(biodata => {
            html += `
                <div class="col-md-4 mb-3">
                    <div class="card" style="background: #34495e; border: 1px solid #2c3e50;">
                        <img src="../${biodata.image_path}" class="card-img-top" alt="Biodata Image" style="height: 200px; object-fit: cover;">
                        <div class="card-body">
                            <h6 class="card-title text-white">${biodata.name}</h6>
                            <p class="card-text text-light" style="font-size: 0.9rem;">
                                Age: ${biodata.age}<br>
                                Type: ${biodata.type}<br>
                                Address: ${biodata.address}
                            </p>
                            <div class="d-flex gap-2">
                                <button class="btn btn-success btn-sm" onclick="approveBiodata(${biodata.id})">
                                    <i class="fas fa-check"></i> Approve
                                </button>
                                <button class="btn btn-danger btn-sm" onclick="rejectBiodata(${biodata.id})">
                                    <i class="fas fa-times"></i> Reject
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        html += '</div>';
        container.innerHTML = html;
    }

    // Function to update pending count
    function updatePendingCount(count) {
        const countElement = document.getElementById('pendingCount');
        countElement.textContent = count;
        
        // Add notification if count > 0
        if (count > 0) {
            countElement.style.animation = 'pulse 1s infinite';
        } else {
            countElement.style.animation = 'none';
        }
    }

    // Function to approve biodata
    function approveBiodata(biodataId) {
        if (confirm('Are you sure you want to approve this biodata?')) {
            const formData = new FormData();
            formData.append('biodata_id', biodataId);
            formData.append('action', 'approve');

            fetch('approve_biodata.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove from pending list
                    loadPendingRequests();
                    
                    // Fetch the approved biodata details and add to dashboard
                    fetch('get_biodata.php?id=' + biodataId)
                        .then(response => response.json())
                        .then(biodataData => {
                            if (biodataData.success) {
                                // Add the approved card to the dashboard
                                addApprovedBiodataCard(biodataData.data);
                                showNotification('Biodata approved successfully! Card added to approved section.', 'success');
                            } else {
                                showNotification('Biodata approved but could not load details for dashboard: ' + biodataData.message, 'warning');
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching approved biodata details:', error);
                            showNotification('Biodata approved but could not load details for dashboard.', 'warning');
                        });
                } else {
                    showNotification('Error approving biodata: ' + data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error approving biodata', 'error');
            });
        }
    }

    // Function to reject biodata
    function rejectBiodata(biodataId) {
        if (confirm('Are you sure you want to reject this biodata?')) {
            const formData = new FormData();
            formData.append('biodata_id', biodataId);
            formData.append('action', 'reject');

            fetch('approve_biodata.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove from pending list
                    loadPendingRequests();
                    // Show success message
                    showNotification('Biodata rejected successfully!', 'success');
                } else {
                    showNotification('Error rejecting biodata: ' + data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error rejecting biodata', 'error');
            });
        }
    }

    // Function to show notifications
    function showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show position-fixed`;
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        notification.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(notification);
        
        // Auto remove after 3 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 3000);
    }

    function deleteBiodata(id) {
        if (confirm('Are you sure you want to delete this biodata?')) {
            fetch('../delete_image.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'image_id=' + id
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove the card from the dashboard
                    const cardElement = document.querySelector(`[data-biodata-id="${id}"]`);
                    if (cardElement) {
                        cardElement.style.opacity = '0';
                        setTimeout(() => {
                            cardElement.remove();
                            updateApprovedCount();
                        }, 500);
                    }
                    showNotification('Biodata deleted successfully!', 'success');
                } else {
                    showNotification('Error deleting biodata: ' + data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error deleting biodata', 'error');
            });
        }
    }

    function openEditModal(id) {
        // Fetch biodata details and populate modal
        fetch('get_biodata.php?id=' + id)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const biodata = data.data;
                    document.getElementById('editBiodataId').value = biodata.id;
                    document.getElementById('editName').value = biodata.name;
                    document.getElementById('editAge').value = biodata.age;
                    document.getElementById('editType').value = biodata.type;
                    document.getElementById('editAddress').value = biodata.address;
                    document.getElementById('currentBiodataImage').src = '../' + biodata.image_path;
                    
                    const modal = new bootstrap.Modal(document.getElementById('editBiodataModal'));
                    modal.show();
                } else {
                    alert('Error loading biodata details: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error loading biodata details');
            });
    }

    // Handle edit form submission
    document.getElementById('editBiodataForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const biodataId = formData.get('id');
        
        fetch('update_biodata.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update the card in the dashboard
                const cardElement = document.querySelector(`[data-biodata-id="${biodataId}"]`);
                if (cardElement) {
                    // Update the card content
                    const cardTitle = cardElement.querySelector('.card-title');
                    const cardText = cardElement.querySelector('.card-text');
                    
                    if (cardTitle) cardTitle.textContent = formData.get('name');
                    if (cardText) {
                        cardText.innerHTML = `
                            Age: ${formData.get('age')}<br>
                            Type: ${formData.get('type')}<br>
                            Address: ${formData.get('address')}
                        `;
                    }
                    
                    // Update the image if a new one was uploaded
                    const fileInput = document.getElementById('editImage');
                    if (fileInput.files.length > 0) {
                        const cardImage = cardElement.querySelector('.card-img-top');
                        if (cardImage) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                cardImage.src = e.target.result;
                            };
                            reader.readAsDataURL(fileInput.files[0]);
                        }
                    }
                }
                
                // Close the modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('editBiodataModal'));
                modal.hide();
                
                showNotification('Biodata updated successfully!', 'success');
            } else {
                showNotification('Error updating biodata: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error updating biodata', 'error');
        });
    });

    // Initialize approved count on page load
    const approvedCount = <?php echo $approved_count; ?>;
    document.getElementById('approvedCount').textContent = approvedCount;
    </script>
</body>
</html> 