<?php
require_once __DIR__ . '/../common/php/common.php';
// 画像ファイルのリネーム
function image_rename()
{
    $dbh = db_connect();
    $sql = <<<EOM
    SELECT image_name
    FROM message
    ORDER BY image_name DESC
    LIMIT 1
    EOM;
    $stmt = $dbh->query($sql);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    // 画像ファイル名の変更
    setlocale(LC_ALL, 'ja_JP.UTF-8');
    if (!$result) {
        $_FILES["image"]["name"] = "1." . pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
        return;
    } else {
        $_FILES["image"]["name"] = ++$result["image_name"] . "." . pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
        return;
    }
}
