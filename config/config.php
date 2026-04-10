<?php
/**
 * Mở kết nối đến CSDL sử dụng PDO
 */
function pdo_get_connection(){
    $local_config = array();
    $local_config_path = __DIR__ . DIRECTORY_SEPARATOR . 'config.local.php';
    if (file_exists($local_config_path)) {
        $loaded = require $local_config_path;
        if (is_array($loaded)) {
            $local_config = $loaded;
        }
    }

    $host = $local_config['db_host'] ?? getenv('DB_HOST') ?: '127.0.0.1';
    $port = (int)($local_config['db_port'] ?? getenv('DB_PORT') ?: 3306);
    $dbname = $local_config['db_name'] ?? getenv('DB_NAME') ?: 'duan1_traicay';
    $username = $local_config['db_user'] ?? getenv('DB_USER') ?: 'root';
    $password = $local_config['db_pass'] ?? getenv('DB_PASS') ?: '';

    $dburl = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8";

    $has_local_override = array_key_exists('db_pass', $local_config) || getenv('DB_PASS') !== false;
    $password_candidates = array($password);
    if (!$has_local_override) {
        // Fallback cho môi trường local nhiều máy/dev khác nhau.
        $password_candidates = array_values(array_unique(array($password, 'root', '123456', 'mysql')));
    }

    $last_exception = null;
    foreach ($password_candidates as $candidate_password) {
        try {
            $conn = new PDO($dburl, $username, $candidate_password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        } catch (PDOException $e) {
            $last_exception = $e;
        }
    }

    if ($last_exception instanceof PDOException) {
        $msg = $last_exception->getMessage();
        $is_refused = (stripos($msg, '2002') !== false
            || stripos($msg, 'actively refused') !== false
            || stripos($msg, 'Connection refused') !== false
            || stripos($msg, 'could not be made') !== false);
        if ($is_refused) {
            throw new PDOException(
                'Không kết nối được MySQL/MariaDB (chưa chạy hoặc sai cổng). '
                . 'Mở XAMPP Control Panel → bấm Start ở dòng MySQL → tải lại trang. '
                . 'Nếu đổi cổng MySQL, chỉnh db_port trong file config/config.local.php. '
                . '[Chi tiết: ' . $msg . ']',
                (int) $last_exception->getCode(),
                $last_exception
            );
        }
        throw $last_exception;
    }

    throw new PDOException('Không thể kết nối CSDL.');
}