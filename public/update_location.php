<?php
require('../includes/db.php');
session_start();

if (isset($_SESSION['user_id']) && isset($_POST['lat']) && isset($_POST['lng'])) {
    $user_id = $_SESSION['user_id'];
    $lat = mysqli_real_escape_string($conn, $_POST['lat']);
    $lng = mysqli_real_escape_string($conn, $_POST['lng']);

    $sql = "UPDATE users SET latitude = '$lat', longitude = '$lng' WHERE id = '$user_id'";
    mysqli_query($conn, $sql);
}
?>
