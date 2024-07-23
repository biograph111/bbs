<?php
require_once __DIR__ . '/../common/php/common.php';
// 画像ファイルのチェック関数
function image_check()
{
    if ($_FILES['image'] !== null) {
        // ファイルアップロードエラーのチェック
        if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            $msg = [
                UPLOAD_ERR_INI_SIZE => 'php.iniのupload_max_filesizeを超えています。',
                UPLOAD_ERR_PARTIAL => 'ファイルが一部しかアップロードされていません。',
                UPLOAD_ERR_NO_FILE => 'ファイルがアップロードされませんでした。',
                UPLOAD_ERR_NO_TMP_DIR => '一時保存ファイルが存在しません。',
                UPLOAD_ERR_CANT_WRITE => 'ディスクへの書き込みが失敗しました。',
                UPLOAD_ERR_EXTENSION => '拡張モジュールによってアップロードが中断されました。'
            ];
            $err_msg = $msg[$_FILES['image']['error']];
            input_error($err_msg);
            exit;
            // 拡張子チェック
        } elseif (!in_array(
            strtolower(pathinfo($_FILES['image']['name'])['extension']),
            ['gif', 'jpg', 'jpeg', 'png']
        )) {
            $err_msg = '画像以外のファイルはアップロードできません。';
            input_error($err_msg);
            exit;
            // ファイルの内容が画像であるかチェック
        } elseif (!in_array(
            finfo_file(
                finfo_open(FILEINFO_MIME_TYPE),
                $_FILES['image']['tmp_name']
            ),
            ['image/gif', 'image/jpg', 'image/jpeg', 'image/png']
        )) {
            $err_msg = 'ファイルの内容が画像ではありません。';
            input_error($err_msg);
            exit;
        } else {
            return true;
        }
    }
    return;
}
