<?php
require_once __DIR__ . '/../common/php/common.php';
session_check();
login_check();
$token = token_create();
const DELETED_MESSAGE = "この投稿には問題のある表現が含まれているため、表示できません。";

$auth = authority_check();
$thread_id = (int)h($_GET['id']);
$finfo = new finfo(FILEINFO_MIME_TYPE);
// スレッドタイトル、作成日時取得
$dbh = db_connect();
$sql = <<<EOM
    SELECT
        `name`,
        `created_at`
    FROM
        `threads`
    WHERE `deleted_at` IS NULL
        AND `id` = :thread_id
    EOM;
$stmt = $dbh->prepare($sql);
$stmt->bindParam(':thread_id', $thread_id, PDO::PARAM_INT);
$stmt->execute();
$thread = $stmt->fetch(PDO::FETCH_ASSOC);

// スレッド削除済みのページ遷移
if (!$thread) {
    header('location:' . $_SERVER['HTTP_REFERER']);
    exit;
}

//スレッドタイトル
$thread_name = $thread['name'];
// 日時のフォーマット変換
$created_date_thread = date_fmt($thread['created_at']);

// コメント、日時、ハンドルネーム取得
$sql = <<<EOM
    SELECT
        m.id as id,
        m.comment as comment,
        m.image_name as image_name,
        m.extension as extension,
        m.created_at as created_at,
        m.deleted_at as deleted_at,
        u.handle_name as handle_name
    FROM
        message as m
    LEFT OUTER JOIN
        users as u
    ON
        m.user_id = u.id
    WHERE
        m.thread_id = :thread_id
    ORDER BY
        m.created_at ASC;
EOM;

$stmt = $dbh->prepare($sql);
$stmt->bindParam(':thread_id', $thread_id, PDO::PARAM_INT);
$stmt->execute();
$msgs = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!-- header -->
<?php require_once __DIR__ . '/../common/php/layout/header.php'; ?>
<h2><?php echo $thread_name; ?></h2>
<?php foreach ($msgs as $msg) : ?>
<?php
        $comment = "";
        $image_name = "";
        $file_path = "";
        $comment = is_null($msg['deleted_at']) ? $msg['comment'] : DELETED_MESSAGE;
        $id = $msg['id'];
        $handle_name = $msg['handle_name'];
        $image_name = $msg['image_name'];
        $extension = $msg['extension'];
        $created_date_msg = date_fmt($msg['created_at']);
        $name_and_date = $handle_name . ' ' . $created_date_msg;
        $deleted_at = $msg['deleted_at'];
        if ($image_name && $extension) {
            $file_path = "/../bbs/storage/image/" . $image_name . "." . $extension;
        }
        ?>
<article>
    <p><?php echo $name_and_date; ?></p>
    <?php if (is_null($deleted_at) && $file_path) : ?>
    <img class="image" src="<?php echo $file_path; ?>" alt="<?php echo $image_name; ?>">
    <?php endif; ?>
    <p><?php echo $comment; ?></p>
    <?php if ($auth) : ?>
    <?php if (is_null($deleted_at)) : ?>
    <form action="/../bbs/comment/delete.php" method="post">
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <input type="hidden" name="token" value="<?php echo $token; ?>">
        <button type="submit">削除</button>
    </form>
    <?php else : ?>
    <form action="/../bbs/comment/restore.php" method="post">
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <input type="hidden" name="token" value="<?php echo $token; ?>">
        <button type="submit">復元</button>
    </form>
    <?php endif; ?>
    <?php endif; ?>
</article>
<?php endforeach; ?>
<h2>コメントの投稿</h2>
<?php input_error_message(); ?>
<?php if (isset($_SESSION['msg_done'])) {
        echo $_SESSION['msg_done'];
        unset($_SESSION['msg_done']);
    } ?>
<form action="/../bbs/comment/add.php" method="post" name="comment" enctype="multipart/form-data">
    <label>
        <dt>スレッドタイトル:</dt>
        <dd><?php echo $thread_name; ?></dd>
    </label>
    <dt>ハンドルネーム:</dt>
    <dd><?php echo $_SESSION['handle_name']; ?></dd>
    <label>
        <dt>本文:</dt>
        <dd><textarea name="comment" cols="30" rows="10" placeholder="必須"></textarea></dd>
    </label>
    <label>
        <dt>画像(任意):</dt>
        <dd><input type="file" name="image" size="40"></dd>
    </label>
    <input type="hidden" name="thread_id" value="<?php echo $thread_id; ?>">
    <input type="hidden" name="token" value="<?php echo $token; ?>">
    <input type="submit" value="コメントを投稿する">
</form>
<p><a href="index.php">スレッド一覧に戻る</a></p>
<p><a href="/bbs/index.php">トップページに戻る</a></p>
<!-- footer -->
<?php require_once __DIR__ . '/../common/php/layout/footer.php'; ?>