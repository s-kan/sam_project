<?php
// SSH tunnel configuration
$ssh_host = '164.125.254.141';
$ssh_port = 4001;
$ssh_user = 'shlee';
$ssh_pass = 'korea2020';

// Database configuration
$db_host = 'localhost';
$db_port = 3306;
$db_user = 'root';
$db_pass = 'password';
$db_name = 'sam';

// Create SSH tunnel
$connection = ssh2_connect($ssh_host, $ssh_port);
if (ssh2_auth_password($connection, $ssh_user, $ssh_pass)) {
    echo "SSH connection established.\n";
    $tunnel = ssh2_tunnel($connection, $db_host, $db_port);
    if ($tunnel) {
        echo "SSH tunnel created.\n";

        // Database connection
        $conn = mysqli_connect('127.0.0.1', $db_user, $db_pass, $db_name, $db_port);
        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }
        echo "Database connection established.\n";

        // Get form data
        $name = $_POST['name'];
        $number = $_POST['number'];
        $phone = $_POST['phone'];
        $date = $_POST['date'];
        $time = $_POST['time'];

        // Insert data into database
        $sql = "INSERT INTO reservations (name, number, phone, date, time)
                VALUES ('$name', '$number', '$phone', '$date', '$time')";

        if (mysqli_query($conn, $sql)) {
            echo "Reservation submitted successfully";
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }

        // Close connections
        mysqli_close($conn);
        ssh2_exec($connection, 'exit');
        echo "SSH tunnel closed.\n";
    } else {
        die("SSH tunnel creation failed.");
    }
} else {
    die("SSH authentication failed.");
}
?>
