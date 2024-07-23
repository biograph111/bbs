<?php
require_once __DIR__ . '/../common/php/common.php';
// セッションの有効期限チェック
session_check();
login_check();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // セッションの有効期限チェック
    session_check();

    // ワンタイムトークンチェック
    token_check();

    if (!isset($_POST['delete'])) {
        error_message('不正なリクエスト。');
        exit;
    }

    // 入力チェック
    if (empty($_POST['password'])) {
        input_error('パスワードが入力されていません。');
        exit;
    }

    $pass = h($_POST['password']);

    $match_pass = password_check($pass);

    if ($match_pass) {
        date_default_timezone_set('Asia/Tokyo');
        $deleted_at = date('Y/m/d H:i:s');
        // データベース接続
        $dbh = db_connect();
        $sql = <<<EOM
        UPDATE `users`
        SET `deleted_at` = :deleted_at
        WHERE `login_id` = :login_id
    EOM;
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':login_id', $_SESSION['login_id'], PDO::PARAM_STR);
        $stmt->bindParam(':deleted_at', $deleted_at, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (is_bool($result)) {
            $_SESSION['account_delete'] = true;
            header('location:done.php');
            exit;
        } else {
            error_message('エラーの発生サポートにお問い合わせください。');
            exit;
        }
    } else {
        input_error('パスワードが違います。');
        exit;
    }
}
?>

<!-- header -->
<?php require_once __DIR__ . '/../common/php/layout/header.php'; ?>
<h2>アカウントを削除しますか？</h2>
<p><?php input_error_message(); ?></p>
<form action="" method="post">
    <label>パスワード:
        <input type="password" name="password" placeholder="パスワードを入力">
    </label>
    <input type="submit" name="delete" value="はい">
    <input type="button" onclick="location.href='my_page.php'" value="いいえ">
    <input type="hidden" name="token" value="<?php echo h(token_create()); ?>">
</form>
<p><a href="/bbs/index.php">トップページに戻る</a></p>
<!-- footer -->
<?php require_once __DIR__ . '/../common/php/layout/footer.php'; ?>