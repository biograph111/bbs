<?php
require_once __DIR__ . '/../common/php/common.php';

authority_check_redirect();

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
    WHERE deleted_at IS NULL;
EOM;
$stmt = $dbh->query($sql);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
$th_list = [
    'ログインID',
    '氏名',
    'ハンドルネーム',
    '権限'
];
?>

<!-- header -->
<?php require_once __DIR__ . '/../common/php/layout/header.php'; ?>
<?php if (!empty($_SESSION['account_delete'])) : ?>
<p><?php echo 'ID' . $_SESSION['id'] . 'のアカウントを削除しました。'; ?></p>
<?php unset($_SESSION['id']);
        $_SESSION['account_delete'] = false;
    endif; ?>
<table border="1">
    <thead>
        <tr>
            <?php foreach ($th_list as $item) : ?>
            <th><?php echo $item ?></th>
            <?php endforeach; ?>
            <th colspan="2">ユーザー管理</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($results as $result) : ?>
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
            <?php if ($_SESSION['login_id'] === $login_id) : ?>
            <td colspan="2">ログイン中</td>
            <?php else : ?>
            <form>
                <td>
                    <button type="submit" formaction="edit.php" formmethod="post" name="id" value="<?php echo $id; ?>">編集</button>
                </td>
                <td>
                    <button type="submit" formaction="delete.php" formmethod="post" name="id" value="<?php echo $id; ?>">削除</button>
                </td>
            </form>
            <?php endif; ?>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<!-- footer -->
<?php require_once __DIR__ . '/../common/php/layout/footer.php'; ?>