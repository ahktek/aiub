<?php
include "../../config/db.php";
include "../../config/helpers.php";

if(!isset($_SESSION['user_id']))
{
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch current user data from database
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$userData = $stmt->get_result()->fetch_assoc();

$name = $userData['name'];
$email = $userData['email'];
$phone = $userData['phone'];
$addresses = json_decode($userData['shipping_addresses'], true) ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - E-Commerce Store</title>
    <link rel="stylesheet" href="../../public/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .dropdown {
            position: relative;
            display: inline-block;
        }
        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background-color: white;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 100;
            border-radius: 8px;
            margin-top: 10px;
        }
        .dropdown-content a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            font-size: 14px;
        }
        .dropdown-content a:hover {background-color: #f1f5f9; border-radius: 8px;}
        .show {display: block;}
        
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .alert-success { background: #dcfce7; color: #10b981; }
        .alert-error { background: #fee2e2; color: #ef4444; }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-logo">
                <i class="fas fa-shopping-bag"></i> E-Commerce Store
            </div>
            <nav>
                <a href="#" class="nav-item">
                    <i class="fas fa-th-large"></i> Dashboard
                </a>
                <a href="#" class="nav-item">
                    <i class="fas fa-shopping-cart"></i> My Orders
                </a>
                <a href="#" class="nav-item active">
                    <i class="fas fa-user"></i> My Profile
                </a>
                <a href="#" class="nav-item">
                    <i class="fas fa-map-marker-alt"></i> Addresses
                </a>
                <a href="#" class="nav-item">
                    <i class="fas fa-lock"></i> Change Password
                </a>
                <a href="../../controllers/AuthController.php?logout=1" class="nav-item" style="color: #ef4444;">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <i class="far fa-bell" style="font-size: 20px; color: var(--text-muted); cursor: pointer; margin-right: 20px;"></i>
                <div class="dropdown">
                    <div class="user-profile" onclick="toggleDropdown()" style="cursor: pointer;">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($name); ?>&background=2563eb&color=fff" class="user-avatar" alt="User">
                        <span><?php echo htmlspecialchars($name); ?></span>
                        <i class="fas fa-chevron-down" style="font-size: 12px;"></i>
                    </div>
                    <div id="myDropdown" class="dropdown-content">
                        <a href="#"><i class="fas fa-user"></i> My Profile</a>
                        <a href="#"><i class="fas fa-cog"></i> Settings</a>
                        <hr style="border: 0.5px solid #eee;">
                        <a href="../../controllers/AuthController.php?logout=1" style="color: #ef4444;"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </div>
                </div>
            </div>

            <div style="margin-bottom: 30px;">
                <h1 style="font-size: 24px;">My Profile</h1>
                <p style="color: var(--text-muted);">Manage your personal information and account settings</p>
            </div>

            <?php if(isset($_GET['success'])): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($_GET['success']); ?></div>
            <?php endif; ?>
            <?php if(isset($_GET['error'])): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($_GET['error']); ?></div>
            <?php endif; ?>

            <div class="grid-2">
                <!-- Personal Information -->
                <div class="card">
                    <div class="card-title">Personal Information</div>
                    <form method="POST" action="../../controllers/UserController.php">
                        <div class="form-group">
                            <label for="name">Full Name</label>
                            <input type="text" id="name" name="name" class="form-control" style="padding-left: 12px;" value="<?php echo htmlspecialchars($name); ?>">
                        </div>
                        <div class="grid-2">
                            <div class="form-group">
                                <label for="email">Email Address</label>
                                <input type="email" id="email" name="email" class="form-control" style="padding-left: 12px;" value="<?php echo htmlspecialchars($email); ?>">
                            </div>
                            <div class="form-group">
                                <label for="phone">Phone Number</label>
                                <input type="text" id="phone" name="phone" class="form-control" style="padding-left: 12px;" value="<?php echo htmlspecialchars($phone); ?>">
                            </div>
                        </div>
                        <button type="submit" name="update_profile" class="btn btn-blue" style="width: auto; padding: 10px 25px;">Update Profile</button>
                    </form>
                </div>

                <!-- Saved Addresses -->
                <div class="card">
                    <div class="card-title">
                        Saved Addresses
                        <button class="btn btn-blue btn-small" style="width: auto;" onclick="showAddAddress()">+ Add Address</button>
                    </div>
                    
                    <div id="addAddressForm" style="display:none; margin-bottom: 20px; border: 1px solid #eee; padding: 15px; border-radius: 8px;">
                        <form method="POST" action="../../controllers/UserController.php">
                            <div class="form-group">
                                <label>New Address</label>
                                <textarea name="address" class="form-control" style="padding-left: 12px; height: 60px;" placeholder="Enter address..."></textarea>
                            </div>
                            <button type="submit" name="add_address" class="btn btn-blue btn-small">Save Address</button>
                            <button type="button" class="btn btn-outline btn-small" onclick="showAddAddress()">Cancel</button>
                        </form>
                    </div>

                    <?php if(empty($addresses)): ?>
                        <p style="color: var(--text-muted); font-size: 14px;">No saved addresses found.</p>
                    <?php else: ?>
                        <?php foreach($addresses as $index => $addr): ?>
                            <div class="address-item">
                                <div class="address-info">
                                    <span style="background: #dbeafe; color: var(--primary-blue); font-size: 10px; padding: 2px 8px; border-radius: 4px; font-weight: 600;">Address <?php echo $index + 1; ?></span>
                                    <p style="margin-top: 8px; font-size: 14px;"><?php echo nl2br(htmlspecialchars($addr)); ?></p>
                                </div>
                                <div class="address-actions">
                                    <button class="btn btn-outline-blue btn-small"><i class="far fa-edit"></i> Edit</button>
                                    <a href="../../controllers/UserController.php?delete_address=<?php echo $index; ?>" class="btn btn-outline-red btn-small" style="text-decoration:none; display:inline-block; line-height:24px;" onclick="return confirm('Delete this address?')"><i class="far fa-trash-alt"></i> Delete</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Change Password -->
            <div class="card">
                <div class="card-title">Change Password</div>
                <form method="POST" action="../../controllers/UserController.php">
                    <div class="grid-2" style="grid-template-columns: 1fr 1fr 1fr; align-items: flex-end;">
                        <div class="form-group">
                            <label for="current_password">Current Password</label>
                            <div class="input-icon-wrapper">
                                <i class="fas fa-lock" style="pointer-events:none;"></i>
                                <input type="password" id="current_password" name="current_password" class="form-control" placeholder="Enter current password">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="new_password">New Password</label>
                            <div class="input-icon-wrapper">
                                <i class="fas fa-lock" style="pointer-events:none;"></i>
                                <input type="password" id="new_password" name="new_password" class="form-control" placeholder="Enter new password">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="confirm_password">Confirm New Password</label>
                            <div class="input-icon-wrapper">
                                <i class="fas fa-lock" style="pointer-events:none;"></i>
                                <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Confirm new password">
                            </div>
                        </div>
                    </div>
                    <div style="display: flex; justify-content: flex-end;">
                        <button type="submit" name="change_password" class="btn btn-blue" style="width: auto; padding: 10px 25px;">Change Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleDropdown() {
            document.getElementById("myDropdown").classList.toggle("show");
        }

        function showAddAddress() {
            var x = document.getElementById("addAddressForm");
            if (x.style.display === "none") {
                x.style.display = "block";
            } else {
                x.style.display = "none";
            }
        }

        window.onclick = function(event) {
            if (!event.target.closest('.dropdown')) {
                var dropdowns = document.getElementsByClassName("dropdown-content");
                for (var i = 0; i < dropdowns.length; i++) {
                    var openDropdown = dropdowns[i];
                    if (openDropdown.classList.contains('show')) {
                        openDropdown.classList.remove('show');
                    }
                }
            }
        }
    </script>
</body>
</html>
