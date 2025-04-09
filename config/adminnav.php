<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modern Admin Panel</title>
    <style>

        nav {
            height:75px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 1rem 2rem;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;

        }

        nav ul {
            display: flex;
            list-style: none;
        }

        nav h2 {
            color: #2c3e50;
            font-size: 1.8rem;
            font-weight: 600;
            transform: translateX(-50%);
        }

        nav a {
            text-decoration: none;
            color: #34495e;
            font-weight: 500;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        nav a::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: #3498db;
            transition: width 0.3s ease;
        }

        nav a:hover {
            color: #3498db;
            background: rgba(52, 152, 219, 0.1);
        }

        nav a:hover::after {
            width: 100%;
        }
    </style>
</head>
<body>
    <nav>
        <ul>
            <li><a href="admin.html">Home</a></li>
        </ul>
        <h2>Admin Panel</h2>
        <ul>
            <li><a href="../Admin/movies.php">Movies</a></li>
            <li><a href="../Admin/users.php">Users</a></li>
            <li><a href="../Admin/comments.php">Comments</a></li>
            <li><a href="../../../config/logout.php">Logout</a></li>
        </ul>
    </nav>
</body>
</html>