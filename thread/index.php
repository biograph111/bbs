<?php
require_once __DIR__ . '/../common/php/common.php';
session_check();
login_check();

$total_page = total_page('threads', 5);
$pages = current_page('threads', 5);
$auth = authority_check();

// ユーザーID
$users_id = array_column($pages, 'user_id');
// ユーザーIDからハンドルネームの取得
$handle_names = users_id($users_id);
$count = count($handle_names);

// 配列にハンドルネームを追加
for ($ii = 0; $ii < $count; $ii++) {
    $pages[$ii]['handle_name'] = $handle_names[$ii];
}
?>
<!-- header -->
<?php require_once __DIR__ . '/../common/php/layout/header.php'; ?>
<h2>スレッド一覧</h2>
<ul>
    <?php foreach ($pages as $page) : ?>
        <?php $thread_name = $page['name'];
        $updated_at = date_fmt($page['updated_at']);
        $thread_id = $page['id'];
        $deleted = $page['deleted_at'];
        $handle_name = $page['handle_name']; ?>
        <li>
            <span></span>
            <?php if (is_null($deleted)) : ?>
                <h2><a href="page.php?id=<?php echo $thread_id; ?>"><?php echo $thread_name; ?></a></h2>
            <?php else : ?>
                <h2><?php echo $thread_name . 'は削除されました。'; ?></h2>
            <?php endif; ?>
            <span>作成者</span>
            <span><?php echo $handle_name; ?></span>
            <span>更新日時</span>
            <span><?php echo $updated_at; ?></span>
            <?php if ($auth) : ?>
                <?php if (is_null($deleted)) : ?>
                    <form action="delete.php" method="post">
                        <input type="hidden" name="id" value="<?php echo $thread_id; ?>">
                        <button class="token" type="submit">削除</button>
                    </form>
                <?php else : ?>
                    <form action="restore.php" method="post">
                        <input type="hidden" name="id" value="<?php echo $thread_id; ?>">
                        <button class="token" onclick="btn()" type="submit">復元</button>
                    </form>
                <?php endif; ?>
            <?php endif; ?>
        </li>
    <?php endforeach; ?>
</ul>
<!-- ページネーション -->
<?php $ii = 1;
while ($total_page > $ii) : ?>
    <span><a href="?page=<?php echo $ii; ?>"><?php echo $ii++; ?></a></span>
<?php endwhile; ?>
<h2>新規スレッドの作成</h2>
<?php echo input_error_message(); ?>
<form action="add.php" method="post" enctype="multipart/form-data">
    <label>
        <dt>スレッドタイトル:</dt>
        <dd><input type="text" name="name" placeholder="必須"></dd>
    </label>
    <dt>ハンドルネーム:</dt>
    <dd><?php echo $_SESSION['handle_name']; ?></dd>
    <label>
        <dt>本文:</dt>
        <dd><textarea name="comment" cols="30" rows="10" placeholder="必須"></textarea></dd>
    </label>
    <input type="hidden" name="max_file_size" value="1000000">
    <label>
        <dt>画像(任意):</dt>
        <dd><input type="file" name="image" size="40"></dd>
    </label>
    <label>
        <dt>パスワード:</dt>
        <dd><input type="password" name="password" placeholder="パスワードを入力"></dd>
    </label>
    <input type="hidden" name="token" value="<?php echo token_create() ?>">
    <input type="submit" value="スレッドを作成する">
</form>
<p><a href="/bbs/index.php">トップページに戻る</a></p>
<!-- footer -->
<?php require_once __DIR__ . '/../common/php/layout/footer.php'; ?>