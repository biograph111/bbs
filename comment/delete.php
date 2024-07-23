<?php
require_once __DIR__ . '/../common/php/common.php';
session_check();
login_check();
authority_check_redirect();

$comment = h($_POST);

date_default_timezone_set('Asia/Tokyo');
$date = date('Y/m/d H:i:s');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dbh = db_connect();
    $sql = <<<EOM
        UPDATE `message`
        SET `deleted_at` = :deleted_at
        WHERE `id` = :id
        EOM;
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':deleted_at', $date, PDO::PARAM_STR);
    $stmt->bindParam(':id', $comment['id'], PDO::PARAM_INT);
    $stmt->execute();
    header('location:' . $_SERVER['HTTP_REFERER']);
    exit;
} else {
    header('location:/../bbs/index.php');
    exit;
}
