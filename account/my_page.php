<?php
require_once __DIR__ . '/../common/php/common.php';
// セッションの有効期限チェック
session_check();
login_check();
?>

<!-- header -->
<?php require_once __DIR__ . '/../common/php/layout/header.php'; ?>
<h2 class="main_title fade_in">マイページ</h2>
<p class="my_config"><a href="/bbs/account/setting.php">アカウントの設定</a></p>
<p class="my_config"><a href="/bbs/thread/index.php">スレッドの入口</a></p>
<div class="back_ground"></div>
<!-- footer -->
<?php require_once __DIR__ . '/../common/php/layout/footer.php'; ?>