<?php
require_once __DIR__ . '/../common/php/common.php';

// ログイン後のページ遷移
logged_in();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ワンタイムトークンチェック
    token_check();

    $_SESSION["is_login"] = false;

    // ログイン内容の取得
    $login = h($_POST);

    // 次回からのユーザーIDの省略
    if (isset($_POST['login_in'])) {
        setcookie('login_id', h($_POST['login_id']), time() + 60 * 60 * 60);
    } else {
        setcookie('login_id', '', time() - 60 * 60 * 60);
        unset($_COOKIE['login_id']);
    }

    // 入力チェック
    if (empty($login['login_id'])) {
        input_error('IDを入力してください。');
        exit;
    } elseif (empty($login['password'])) {
        input_error('パスワードを入力してください。');
        exit;
    }

    // データベース接続
    $dbh = db_connect();

    $sql = <<<EOM
        SELECT *
        FROM `users`
        WHERE `login_id` = :login_id AND deleted_at IS NULL;
    EOM;
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':login_id', $login['login_id'], PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // ID、パスワードチェック
    if (is_bool($result)) {
        input_error('IDまたはパスワードが違います。');
        exit;
    } elseif (password_verify($login['password'], $result['password']) && $result['login_id'] === $login['login_id']) {
        $_SESSION['user_id'] = $result['id'];
        $_SESSION['login_id'] = $result['login_id'];
        $_SESSION['handle_name'] = $result['handle_name'];
        $_SESSION['is_login'] = true;
        header('Location:/bbs/login/login_done.php');
        exit;
    } else {
        input_error('IDまたはパスワードが違います。');
        exit;
    }
}
?>

<!-- header -->
<?php require_once __DIR__ . '/../common/php/layout/header.php'; ?>
<h2>ログイン</h2>
<?php echo input_error_message(); ?>
<form action="" method="post">
    <label>ユーザーID:
        <input type="text" name="login_id" placeholder="IDを入力" value="<?php if (isset($_COOKIE['login_id'])) echo $_COOKIE['login_id']; ?>"></label>
    <label>パスワード:
        <input type="password" name="password" placeholder="パスワードを入力"></label>
    <label>
        <input type="checkbox" name="ed" checked>次回からユーザーIDの省略
    </label>
    <input type="hidden" name="token" value="<?php echo h(token_create()) ?>">
    <input type="submit" value="ログイン">
</form>
<!-- footer -->
<?php require_once __DIR__ . '/../common/php/layout/footer.php'; ?>