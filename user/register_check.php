<?php
require_once __DIR__ . '/../common/php/common.php';

// ワンタイムトークンチェック
token_check();

// 登録内容の取得
$register = h($_POST);
date_default_timezone_set('Asia/Tokyo');
$register += array(
    'created_at' => date('Y/m/d H:i:s'),
    'updated_at' => date('Y/m/d H:i:s')
);

// 登録の確認用
$can_register = false;

// 前のページに戻る用
$ref = $_SERVER['HTTP_REFERER'];

// 入力チェック
if (empty($register['login_id'])) {
    $_SESSION['err_message'] = 'IDを入力してください。';
    header("location:$ref");
    exit;
} elseif (empty($register['password'])) {
    $_SESSION['err_message'] = 'パスワードを入力してください。';
    header("location:$ref");
    exit;
} elseif (empty($register['handle_name'])) {
    $_SESSION['err_message'] = 'ハンドルネームを入力してください。';
    header("location:$ref");
    exit;
}

// データベース接続
$dbh = db_connect();
$sql = <<<EOM
    SELECT *
    FROM `users`
    WHERE `login_id` = :login_id
    OR `handle_name` = :handle_name;
EOM;
$stmt = $dbh->prepare($sql);
$stmt->bindParam(':login_id', $register['login_id'], PDO::PARAM_STR);
$stmt->bindParam(':handle_name', $register['handle_name'], PDO::PARAM_STR);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);

// ユーザーIDとハンドルネームの重複チェック
if (is_bool($result)) {
    $can_register = true;
    $_SESSION['register'] = $register;
} elseif ($result['login_id'] === $register['login_id']) {
    $_SESSION['err_message'] = $register['login_id'] . 'はすでに登録されているIDです';
    header("location:$ref");
    exit;
} elseif ($result['handle_name'] === $register['handle_name']) {
    $_SESSION['err_message'] = $register['handle_name'] . 'はすでに登録されているハンドルネームです';
    header("location:$ref");
    exit;
}
?>

<!-- header -->
<?php require_once __DIR__ . '/../common/php/layout/header.php'; ?>
<div>
    <h2>登録をしますか？</h2>
    <?php if ($can_register) : ?>
        <button type="button" onclick="location.href='/bbs/user/register_done.php'">登録する</button>
        <input type="hidden" name="token" value="<?php echo h(token_create()); ?>">
    <?php endif; ?>
    <button type="button" onclick="location.href='<?= $ref ?>'">戻る</button>
</div>
<!-- footer -->
<?php require_once __DIR__ . '/../common/php/layout/footer.php'; ?>