<?php
require_once __DIR__ . '/../common/php/common.php';
// セッションの有効期限チェック
session_check();
login_check();
?>

<!-- header -->
<?php require_once __DIR__ . '/../common/php/layout/header.php'; ?>
<h2 class="main_title fade_in">アカウントの設定</h2>
<p><a href="/bbs/account/name_edit.php">氏名の変更</a></p>
<p><a href="/bbs/account/edit.php">パスワードの変更</a></p>
<p><a href="/bbs/account/delete.php">アカウントの削除</a></p>
<p><a href="/bbs/thread/index.php">スレッドの入口</a></p>
<div class="back_ground"></div>
<!-- footer -->
<?php require_once __DIR__ . '/../common/php/layout/footer.php'; ?>