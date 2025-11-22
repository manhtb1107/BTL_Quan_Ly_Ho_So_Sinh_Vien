<?php

function getDbConnection() {
    $servername = "localhost";
    $username = "root";
    $password = "manhduy1107@";
    $dbname = "qlhssv";
    $port = 3306;

    // Tạo kết nối
    $conn = mysqli_connect($servername, $username, $password, $dbname, $port);

    // Kiểm tra kết nối
    if (!$conn) {
        die("Kết nối database thất bại: " . mysqli_connect_error());
    }
    // Thiết lập charset cho kết nối (quan trọng để hiển thị tiếng Việt đúng)
    // Use utf8mb4 to fully support all Unicode characters including emoji and full Vietnamese repertoire
    mysqli_set_charset($conn, "utf8mb4");
    return $conn;
}

?>