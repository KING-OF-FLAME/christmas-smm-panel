<?php require_once 'config/config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Christmas Spin & Win Gift</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

    <div class="snow-container"></div>

    <div class="container">
        
        <?php if(isLoggedIn()): ?>
        <?php 
            // 1. Fetch User Data
            $nav_user = getUserData($pdo, $_SESSION['user_id']); 

            // 2. Safety Check (Logout if user deleted)
            if ($nav_user === false) {
                session_destroy();
                echo "<script>window.location.href = '" . BASE_URL . "login.php';</script>";
                exit;
            }
        ?>
        <nav class="navbar">
            <div class="logo">
                <a href="index.php" style="color:var(--gold); text-decoration:none; font-family:'Mountains of Christmas'; font-size:1.5rem;">
                    <i class="fa-solid fa-sleigh"></i> Santa Gift
                </a>
            </div>
            
            <div style="display:flex; align-items:center;">
                <div class="coin-display">
                    <i class="fa-solid fa-coins"></i> 
                    <span id="user-coins"><?php echo $nav_user['coins']; ?></span>
                </div>

                <div class="coin-display" style="background: var(--christmas-green); margin-left: 8px;">
                    <i class="fa-solid fa-arrows-rotate"></i> 
                    <span id="nav-spins"><?php echo $nav_user['spins_left']; ?></span>
                </div>
                
                <div class="nav-links">
                    <a href="dashboard.php">Dashboard</a>
                    <a href="logout.php">Logout</a>
                </div>
            </div>
        </nav>
        <?php endif; ?>