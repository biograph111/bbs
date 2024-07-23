<?php
require_once __DIR__ . '/../common/php/common.php';

$auth = USER_AUTHORITY;

if ($_SESSION['register']) {
    $register = h($_SESSION['register']);
    $pass = password_hash($register['password'], PASSWORD_DEFAULT);

    // データベース接続
    $dbh = db_connect();

    // テーブルに値を登録
    $dbh->beginTransaction();
    $sql = <<<EOM
            INSERT INTO `users` (login_id, first_name, last_name, handle_name, `password`, `authority_id`, created_at, updated_at)
            VALUES (:login_id, :first_name, :last_name, :handle_name, :pass, :authority_id, :created_at, :updated_at);
            EOM;
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':login_id', $register['login_id'], PDO::PARAM_STR);
    $stmt->bindParam(':first_name', $register['first_name'], PDO::PARAM_STR);
    $stmt->bindParam(':last_name', $register['last_name'], PDO::PARAM_STR);
    $stmt->bindParam(':handle_name', $register['handle_name'], PDO::PARAM_STR);
    $stmt->bindParam(':pass', $pass, PDO::PARAM_STR);
    $stmt->bindParam(':authority_id', $auth, PDO::PARAM_INT);
    $stmt->bindParam(':created_at', $register['created_at'], PDO::PARAM_STR);
    $stmt->bindParam(':updated_at', $register['updated_at'], PDO::PARAM_STR);
    $stmt->execute();
    $dbh->commit();

    $_SESSION['login_id'] = $register['login_id'];
    $_SESSION['is_login'] = true;
    $_SESSION['register'] = '';

    $sql = <<<EOM
    SELECT `id`
    FROM `users`
    WHERE `login_id` = :login_id
    EOM;
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam('login_id', $_SESSION['login_id'], PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!empty($result)) {
        $_SESSION['user_id'] = $result['id'];
        $_SESSION['login_id'] = $register['login_id'];
        $_SESSION['handle_name'] = $register['handle_name'];
        $_SESSION['is_login'] = true;
    }
}
?>

<!-- header -->
<?php require_once __DIR__ . '/../common/php/layout/header.php'; ?>
<p>登録が完了しました。</p>
<a href="/bbs/index.php">トップページへ戻る</a>
<!-- footer -->
<?php require_once __DIR__ . '/../common/php/layout/footer.php'; ?>