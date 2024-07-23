<?php
require_once __DIR__ . '/../common/php/common.php';
// セッションの有効期限チェック
session_check();
login_check();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ワンタイムトークンチェック
    token_check();

    // 入力チェック
    if (empty($_POST['current_password'])) {
        input_error('現在のパスワードを入力してください。');
        exit;
    } elseif (empty($_POST['bind_password'])) {
        input_error('新しいパスワードを入力してください。');
        exit;
    }

    $pass = h($_POST['current_password']);
    $match_pass = password_check($pass);

    // 値の更新
    if ($match_pass) {
        $pass = h(password_hash($_POST['bind_password'], PASSWORD_DEFAULT));
        $dbh = db_connect();
        $sql = <<<EOM
            UPDATE `users`
            SET `password` = :pass
            WHERE `login_id` = :login_id
        EOM;
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':pass', $pass, PDO::PARAM_STR);
        $stmt->bindParam(':login_id', $_SESSION['login_id'], PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!is_bool($result)) {
            error_message('エラーの発生サポートにお問い合わせください。');
        } else {
            $_SESSION['edit_done'] = true;
            header('location:done.php');
        }
    }
}
?>

<!-- header -->
<?php require_once __DIR__ . '/../common/php/layout/header.php'; ?>
<h2>パスワードの変更</h2>
<form action="" method="post">
    <p><?php input_error_message(); ?></p>
    <label>現在のパスワード:
        <input type="password" name="current_password" placeholder="パスワードを入力"></label>
    <label>新しいパスワード:
        <input type="password" name="bind_password" placeholder="パスワードを入力"></label>
    <input type="submit" value="変更する">
    <input type="hidden" name="token" value="<?php echo h(token_create()); ?>">
</form>
<p><a href="/bbs/index.php">トップページに戻る</a></p>
<!-- footer -->
<?php require_once __DIR__ . '/../common/php/layout/footer.php'; ?>