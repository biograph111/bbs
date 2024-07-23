<!-- header -->
<?php require_once __DIR__ . '/../common/php/layout/header.php'; ?>
<?php if (!empty($_SESSION['error'])) : ?>
    <h2><?php echo $_SESSION['error']; ?></h2>
<?php unset($_SESSION['error']);
endif; ?>
<a href="/bbs/index.php">トップページへ戻る</a>
<!-- footer -->
<?php require_once __DIR__ . '/../common/php/layout/footer.php'; ?>