<!-- header -->
<?php require_once __DIR__ . '/common/php/layout/header.php'; ?>

<?php if (!isset($_SESSION['login_id'])) : ?>
    <h2 class="main_title fade_in">テスト掲示板</h2>
    <p class="main_text fade_in">Welcome To.</p>
    <div class="back_ground"></div>
<?php else : ?>
    <h2 class="main_title fade_in">テスト掲示板</h2>
    <p class="link_text"><a href="/bbs/thread/index.php">スレッドの入口</a></p>
<?php endif; ?>

<!-- footer -->
<?php require_once __DIR__ . '/common/php/layout/footer.php'; ?>