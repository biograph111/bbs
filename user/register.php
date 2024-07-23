<?php require_once __DIR__ . '/../common/php/common.php';

// ログイン後のページ遷移
logged_in();

// 入力済みの項目チェック
if (isset($_SESSION['register'])) {
    $register = $_SESSION['register'];
}
?>

<!-- header -->
<?php require_once __DIR__ . '/../common/php/layout/header.php'; ?>
<?php
    if (isset($_SESSION['err_message'])) {
        echo $_SESSION['err_message'];
        $_SESSION['err_message'] = '';
    }
    ?>
<h2>新規登録</h2>
<form action="register_check.php" method="post">
    <label>ユーザーID:
        <input type="text" name="login_id" placeholder="IDを入力(必須)" value="<?php if (isset($register)) echo $register['login_id']; ?>"></label>
    <label>パスワード:
        <input type="password" name="password" placeholder="パスワードを入力(必須)"></label>
    <label>氏:
        <input type="text" name="last_name" placeholder="名前を入力(任意)" value="<?php if (isset($register)) echo $register['last_name']; ?>"></label>
    <label>名:
        <input type="text" name="first_name" placeholder="名前を入力(任意)" value="<?php if (isset($register)) echo $register['first_name']; ?>"></label>
    <label>ハンドルネーム:
        <input type="text" name="handle_name" placeholder="ハンドルネームを入力(必須)" value="<?php if (isset($register)) echo $register['handle_name']; ?>"></label>
    <input type="hidden" name="token" value="<?php echo h(token_create()); ?>">
    <input type="submit" value="新規登録">
</form>
<!-- footer -->
<?php require_once __DIR__ . '/../common/php/layout/footer.php'; ?>