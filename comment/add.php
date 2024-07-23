<?php
require_once __DIR__ . '/../common/php/common.php';
require_once __DIR__ . '/../image/check.php';
require_once __DIR__ . '/../image/rename.php';
require_once __DIR__ . '/../image/save.php';
session_check();
login_check();
token_check();

$new_comment = h($_POST);

date_default_timezone_set('Asia/Tokyo');
$date = date('Y/m/d H:i:s');

// 入力チェック
if (!$new_comment['comment']) {
    input_error('本文が入力されていません。');
}

// 画像ファイルチェック
$file_check = false;
$file_check = image_check();
$image_name = null;
$extension = null;
if ($file_check) {
    image_rename();
    image_save();
    // 画像ファイル名の分割
    $image_name = pathinfo($_FILES["image"]["name"], PATHINFO_FILENAME);
    $extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
}

// コメントの登録とスレッドの更新日時の変更
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dbh = db_connect();
    $dbh->beginTransaction();
    $sql = <<<EOM
        INSERT INTO `message`
        (`user_id`,
        `thread_id`,
        `comment`,
        `image_name`,
        `extension`,
        `created_at`,
        `updated_at`)
        VALUE
        (:user_id,
        :thread_id,
        :comment,
        :image_name,
        :extension,
        :created_at,
        :updated_at);
        EOM;
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->bindParam(':thread_id', $new_comment['thread_id'], PDO::PARAM_INT);
    $stmt->bindParam(':comment', $new_comment['comment'], PDO::PARAM_STR);
    $stmt->bindParam(':image_name', $image_name, PDO::PARAM_INT);
    $stmt->bindParam(':extension', $extension, PDO::PARAM_STR);
    $stmt->bindParam(':created_at', $date, PDO::PARAM_STR);
    $stmt->bindParam(':updated_at', $date, PDO::PARAM_STR);
    $stmt->execute();

    $sql = <<<EOM
        UPDATE `threads`
        SET `updated_at` = :updated_at
        WHERE id = :thread_id
        EOM;
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':updated_at', $date, PDO::PARAM_STR);
    $stmt->bindParam(':thread_id', $new_comment['thread_id'], PDO::PARAM_INT);
    $stmt->execute();
    $dbh->commit();
    $_SESSION['msg_done'] = 'コメントを投稿しました。';
    header('location:/bbs/thread/page.php?id=' . $new_comment['thread_id']);
    exit;
}
