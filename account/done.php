<?php
require_once __DIR__ . '/../common/php/common.php';

$name_changed = false;
$changed = false;
$deleted = false;

if (!empty($_SESSION['name_changed'])) {
    $name_changed = true;
    unset($_SESSION['name_changed']);
} elseif (!empty($_SESSION['edit_done'])) {
    $changed = true;
    unset($_SESSION['edit_done']);
} elseif (!empty($_SESSION['account_delete'])) {
    $deleted = session_destroy();
}
?>

<!-- header -->
<?php require_once __DIR__ . '/../common/php/layout/header.php'; ?>
<?php if ($name_changed) : ?>
    <h2>氏名を変更しました。</h2>
<?php elseif ($changed) : ?>
    <h2>パスワードを変更しました。</h2>
<?php elseif ($deleted) : ?>
    <h2>アカウントを削除しました。</h2>
<?php endif; ?>
<p><a href="/bbs/index.php">トップページに戻る</a></p>
<!-- footer -->
<?php require_once __DIR__ . '/../common/php/layout/footer.php'; ?>