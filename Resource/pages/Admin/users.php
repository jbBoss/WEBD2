<?php
require_once '../../../config/connect.php'; // Ensure database connection file is included


try {
    $query = "SELECT * FROM user_table
    ORDER BY user_id DESC";
    $statement = $db->prepare($query);
    $statement->execute();
    $users_data = $statement->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../Styles/users.css">
    <title>User list</title>
</head>
<body>
<?php include '../../../config/adminnav.php'; ?>
<br>
<main>

    <div>
        <ol>
                <div class="user-container">
                <?php foreach ($users_data as $user): ?>

        <div class="user-card">
            <div class="user-info">
                <div class="user-info-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3Zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/>
                    </svg>
                </div>
                <div class="user-info-text">
                    <div class="user-label">Username</div>
                    <div class="user-value"><?= $user['user_name'] ?></div>
                </div>
            </div>

            <div class="user-info">
                <div class="user-info-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0Zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4Zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10Z"/>
                    </svg>
                </div>
                <div class="user-info-text">
                    <div class="user-label">Full Name</div>
                    <div class="user-value"><?= $user['user_fname'] ?></div>
                </div>
            </div>

            <div class="user-info">
                <div class="user-info-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M.05 3.555A2 2 0 0 1 2 2h12a2 2 0 0 1 1.95 1.555L8 8.414.05 3.555ZM0 4.697v7.104l5.803-3.558L0 4.697ZM6.761 8.83l-6.57 4.027A2 2 0 0 0 2 14h12a2 2 0 0 0 1.808-1.144l-6.57-4.027L8 9.586l-1.239-.757Zm3.436-.586L16 11.801V4.697l-5.803 3.546Z"/>
                    </svg>
                </div>
                <div class="user-info-text">
                    <div class="user-label">Email</div>
                    <div class="user-value"><?= $user['user_gmail'] ?></div>
                </div>
            </div>
            
        </div>
        <?php endforeach; ?>

    </div>
        </ol>
    </div>
</main>
</body>
