<?php
require_once __DIR__ . '/../common/php/common.php';
session_check();
login_check();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ダイレクトリンクのチェック
    if (!isset($_POST['logout'])) {
        error_message('不正なリクエスト。');
        exit;
    }

    // ログアウト処理
    $_SESSION = array();
    session_destroy();
    header('location:/bbs/logout/logout_done.php');
    exit;
}
?>

<!-- header -->
<?php require_once __DIR__ . '/../common/php/layout/header.php'; ?>
<?php if (isset($_SESSION['login_id'])) : ?>
    <h2>ログアウト</h2>
    <p>ログアウトしますか？</p>
    <form action="" method="post">
        <input type="submit" name="logout" value="ログアウトする">
    </form>
    <a href="/bbs/index.php">戻る</a>
<?php endif ?>
<!-- footer -->
<?php require_once __DIR__ . '/../common/php/layout/footer.php'; ?>