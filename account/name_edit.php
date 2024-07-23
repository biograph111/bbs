<?php
require_once __DIR__ . '/../common/php/common.php';
// セッションの有効期限チェック
session_check();
login_check();

// 氏名の取得
$dbh = db_connect();
$sql = <<<EOM
    SELECT `last_name`,
    `first_name`
    FROM `users`
    WHERE `login_id` = :login_id
EOM;
$stmt = $dbh->prepare($sql);
$stmt->bindParam(':login_id', $_SESSION['login_id'], PDO::PARAM_STR);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);
$name = $result['last_name'] . ' ' . $result['first_name'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ワンタイムトークンチェック
    token_check();

    // 入力チェック
    if (empty($_POST['last_name']) || empty($_POST['first_name'])) {
        input_error('名前を入力してください。');
        exit;
    }

    $last_name = h($_POST['last_name']);
    $first_name = h($_POST['first_name']);

    $sql = <<<EOM
            UPDATE `users`
            SET `last_name` = :last_name,
                `first_name` = :first_name
            WHERE `login_id` = :login_id
        EOM;
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':last_name', $last_name, PDO::PARAM_STR);
    $stmt->bindParam(':first_name', $first_name, PDO::PARAM_STR);
    $stmt->bindParam(':login_id', $_SESSION['login_id'], PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!is_bool($result)) {
        error_message('エラーの発生サポートにお問い合わせください。');
    } else {
        $_SESSION['name_changed'] = true;
        header('location:done.php');
    }
}
?>

<!-- header -->
<?php require_once __DIR__ . '/../common/php/layout/header.php'; ?>
<h2>氏名の変更</h2>
<form action="" method="post">
    <p><?php input_error_message(); ?></p>
    <?php echo $name; ?>
    <label>氏:
        <input type="text" name="last_name" placeholder="名前を入力"></label>
    <label>名:
        <input type="text" name="first_name" placeholder="名前を入力"></label>
    <input type="submit" value="変更する">
    <input type="hidden" name="token" value="<?php echo h(token_create()); ?>">
</form>
<p><a href="/bbs/index.php">トップページに戻る</a></p>
<!-- footer -->
<?php require_once __DIR__ . '/../common/php/layout/footer.php'; ?>