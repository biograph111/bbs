<?php
require_once __DIR__ . '/../common/php/common.php';
session_check();
login_check();
authority_check_redirect();

if (!$_SERVER['PHP_SELF'] && empty($_POST['id'])) {
    error_message('不正なリクエスト。');
}

if (!empty($_POST['id'])) {
    $_SESSION['id'] = h($_POST['id']);
}

//ユーザー情報の取得
$dbh = db_connect();
$sql = <<<EOM
    SELECT
    users.id as id,
    login_id,
    first_name,
    last_name,
    handle_name,
    name as authority_name
    FROM users
    LEFT JOIN authorities
    USING(authority_id)
    WHERE users.id = :id;
    EOM;
$stmt = $dbh->prepare($sql);
$stmt->bindParam('id', $_SESSION['id'], PDO::PARAM_INT);
$stmt->execute();
global $result, $th_list;
$result = $stmt->fetch(PDO::FETCH_ASSOC);
$th_list = [
    'ログインID',
    '氏名',
    'ハンドルネーム',
    '権限'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['edit'])) {
    // ワンタイムトークンチェック
    token_check();

    if (!isset($_POST['edit'])) {
        error_message('不正なリクエスト。');
        exit;
    }

    // ユーザー権限の変更
    $edit = $_POST['edit'];
    $authority = match ($edit) {
        'admin' => (int) 0,
        'user' => (int) 10,
    };

    $sql = <<<EOM
        UPDATE `users`
        SET `authority_id` = :authority_id
        WHERE `id` = :id
    EOM;
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':id', $_SESSION['id'], PDO::PARAM_INT);
    $stmt->bindParam(':authority_id', $authority, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if (is_bool($result)) {
        $_SESSION['account_edit'] = true;
        header('location:' . $_SERVER['PHP_SELF']);
        exit;
    } else {
        error_message('エラーの発生サポートにお問い合わせください。');
        exit;
    }
}

?>

<!-- header -->
<?php require_once __DIR__ . '/../common/php/layout/header.php'; ?>
<p><?php input_error_message(); ?></p>
<?php if (!empty($_SESSION['account_edit'])) : ?>
    <p><?php echo 'ID' . $_SESSION['id'] . 'のアカウントの権限を' . $result['authority_name'] . 'に変更しました。'; ?></p>
<?php unset($_SESSION['account_edit']);
endif; ?>
<table border="1">
    <thead>
        <tr>
            <?php foreach ($th_list as $item) : ?>
                <th><?php echo $item ?></th>
            <?php endforeach; ?>
            <th>権限の変更</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $id = $result['id'];
        $login_id = $result['login_id'];
        $first_name = $result['first_name'];
        $last_name = $result['last_name'];
        $handle_name = $result['handle_name'];
        $authority_name = $result['authority_name'];
        $full_name = $last_name . "　" . $first_name;
        ?>
        <tr>
            <td><?php echo $login_id; ?></td>
            <td><?php echo $full_name; ?></td>
            <td><?php echo $handle_name; ?></td>
            <td><?php echo $authority_name; ?></td>
            <form action="" method="post">
                <td>
                    <select name="edit" onchange="submit(this.form)">
                        <option hidden>選択してください</option>
                        <option value="admin">管理者に変更</option>
                        <option value="user">ユーザーに変更</option>
                    </select>
                </td>
                <input type="hidden" name="token" value="<?php echo h(token_create()); ?>">
            </form>
        </tr>
    </tbody>
</table>
<!-- footer -->
<?php require_once __DIR__ . '/../common/php/layout/footer.php'; ?>