<?php
require_once __DIR__ . '/../common/php/common.php';
require_once __DIR__ . '/../common/php/common.php';
require_once __DIR__ . '/../image/check.php';
require_once __DIR__ . '/../image/rename.php';
require_once __DIR__ . '/../image/save.php';
session_check();
login_check();
token_check();

$new_thread = h($_POST);
date_default_timezone_set('Asia/Tokyo');
$date = date('Y/m/d H:i:s');
$key = array_search('', $new_thread);

// 入力チェック
if ($key) {
    $result = match ($key) {
        'name' => 'スレッドタイトルが入力されていません。',
        'comment' => '本文が入力されていません。',
        'password' => 'パスワードが入力されていません。'
    };
    input_error($result);
    exit;
}

$match_pass = password_check($new_thread['password']);

if ($match_pass) {
} else {
    input_error('パスワードが違います。');
    exit;
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

// スレッドの重複チェック
$dbh = db_connect();
$dbh->beginTransaction();
$sql = <<<EOM
    SELECT `name`
    FROM `threads`
    EOM;
$stmt = $dbh->query($sql);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

$same_name = in_array($new_thread['name'], array_column($result, 'name'));

if ($same_name) {
    input_error('同名のスレッドタイトルが存在します。');
    exit;
}
//スレッドの作成
$sql = <<<EOM
    INSERT INTO `threads`
    (user_id,
    name,
    created_at,
    updated_at)
    VALUES
    (:user_id,
    :name,
    :created_at,
    :updated_at)
    EOM;
$stmt = $dbh->prepare($sql);
$stmt->bindParam('user_id', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->bindParam('name', $new_thread['name'], PDO::PARAM_STR);
$stmt->bindParam('created_at', $date, PDO::PARAM_STR);
$stmt->bindParam('updated_at', $date, PDO::PARAM_STR);
$stmt->execute();

// スレッドIDの取得
$sql = <<<EOM
        SELECT `id`
        FROM `threads`
        WHERE `name` = :name;
    EOM;
$stmt = $dbh->prepare($sql);
$stmt->bindParam('name', $new_thread['name'], PDO::PARAM_STR);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);

// コメントの登録
$sql = <<<EOM
    INSERT INTO `message`
    (user_id,
    thread_id,
    comment,
    image_name,
    extension,
    created_at,
    updated_at)
    VALUES
    (:user_id,
    :thread_id,
    :comment,
    :image_name,
    :extension,
    :created_at,
    :updated_at)
    EOM;
$stmt = $dbh->prepare($sql);
$stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->bindParam(':thread_id', $result['id'], PDO::PARAM_INT);
$stmt->bindParam(':comment', $new_thread['comment'], PDO::PARAM_STR);
$stmt->bindParam(':image_name', $image_name, PDO::PARAM_INT);
$stmt->bindParam(':extension', $extension, PDO::PARAM_STR);
$stmt->bindParam(':created_at', $date, PDO::PARAM_STR);
$stmt->bindParam(':updated_at', $date, PDO::PARAM_STR);
$stmt->execute();
$commit = $dbh->commit();

// 作成したスレッドへリダイレクト
if ($commit) {
    header('location:page.php?id=' . $result['id']);
    exit;
}
