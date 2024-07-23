<?php
session_s();

// ユーザー新規登録用
const USER_AUTHORITY = 10;

// エスケープ処理
function h($string)
{
    if (is_array($string)) {
        return array_map("h", $string);
    } else {
        return htmlspecialchars($string, ENT_QUOTES, 'utf-8');
    }
}

// ワンタイムトークン作成
function token_create()
{
    $token = bin2hex(random_bytes(20));
    $_SESSION['token'] = $token;
    return $token;
}

// ワンタイムトークンチェック
function token_check()
{
    $token = $_POST['token'];
    if (!isset($_SESSION) || $token !== $_SESSION['token']) {
        error_message('不正なリクエスト。');
        exit;
    }
    $_SESSION['token'] = ''; // セッショントークン初期化
    return;
}

// データベース接続
function db_connect()
{
    $user = 'root';
    $pass = '';

    try {
        $dbh = new PDO('mysql:host=localhost; port=3306; dbname=bbs; charset=utf8', $user, $pass);
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $dbh;
    } catch (PDOException $e) {
        $dbh->rollBack();
        error_message('エラーの発生サポートにお問い合わせください。');
        exit;
    }
}

// ログインチェック
function login_check()
{
    if (!empty($_SESSION["is_login"])) {
        return true;
    } else {
        return false;
    }
}

// ログイン後のページ遷移
function logged_in()
{
    if (isset($_SESSION['login_id'])) {
        return header('location:/bbs/account/my_page.php');;
    }
    return;
}

// セッションの有効期限チェック
function session_check()
{
    if (isset($_SESSION['login_id'])) {
        return;
    }
    return error_message('セッションが切れています。再度ログインしてください。');
}

// セッションのスタートチェック
function session_s()
{
    if (session_status() === PHP_SESSION_NONE) {
        return session_start();
    }
    return;
}

// 不正、その他エラーのメッセージ表示とページ遷移
function error_message($string)
{
    $_SESSION['error'] = $string;
    return header('location:/bbs/user/error.php');
}

// 入力エラーのメッセージとページ遷移
function input_error($string)
{
    $_SESSION['input_error'] = $string;
    return header('location:' . $_SERVER['HTTP_REFERER']);
}

// 入力エラーのメッセージ表示
function input_error_message()
{
    if (!empty($_SESSION['input_error'])) {
        echo $_SESSION['input_error'];
        $_SESSION['input_error'] = '';
    }
    return;
}

//ログインユーザーのauthorityチェック
function authority_check()
{
    if (!empty($_SESSION['login_id'])) {
        $dbh = db_connect();
        $sql = <<<EOM
        SELECT `authority_id`
        FROM `users`
        WHERE `login_id` = :login_id;
    EOM;
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':login_id', $_SESSION['login_id'], PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result['authority_id'] < USER_AUTHORITY) {
            $dbh = null;
            return true;
        } else {
            $dbh = null;
            return false;
        }
        return;
    }
}

//ログインユーザーのauthorityチェックとページ遷移
function authority_check_redirect()
{
    $user_authority = authority_check();
    if (!$user_authority) {
        header('location:/bbs/index.php');
        exit;
    }
}

//入力パスワードのチェック
function password_check($pass)
{
    $dbh = db_connect();
    $sql = <<<EOM
        SELECT *
        FROM `users`
        WHERE `login_id` = :login_id
    EOM;
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':login_id', $_SESSION['login_id'], PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if (password_verify($pass, $result['password'])) {
        $dbh = null;
        return true;
    } else {
        $dbh = null;
        return false;
    }
}

// $num件毎のページネーション数の取得
function total_page($table, $num)
{
    $dbh = db_connect();
    $sql = <<<EOM
        SELECT COUNT(id)
        FROM {$table}
        WHERE `deleted_at` IS NULL;
        EOM;
    $stmt = $dbh->query($sql);
    $result = $stmt->fetchColumn();
    $dbh = null;
    if ($result) {
        return ceil($result / $num);
    }
    return;
}

//現在のページから最大$num件取得
function current_page($table, $num)
{
    if (isset($_GET['page'])) {
        $page = (int) $_GET['page'];
    } else {
        $page = 1;
    }

    if ($page <= 1) {
        $start_page = 0;
    } else {
        $start_page = ($page * $num) - $num;
    }

    $dbh = db_connect();
    $sql = <<<EOM
        SELECT
        `id`,
        `user_id`,
        `name`,
        `updated_at`,
        `deleted_at`
        FROM {$table}
        ORDER BY `updated_at` DESC
        LIMIT {$start_page}, $num;
        EOM;
    $stmt = $dbh->query($sql);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

// 日時のフォーマット
function date_fmt($date)
{
    if (is_array($date)) {
        return array_map("date_fmt", $date);
    }
    $date_format = date_create_from_format('Y-m-d H:i:s', $date);
    $result = $date_format->format('Y年m月d日H時i分s秒');
    return $result;
}

//ユーザーIDからハンドルネームの取得
function users_id($id)
{
    if (is_array($id)) {
        return array_map("users_id", $id);
    } else {
        $dbh = db_connect();
        $sql = <<<EOM
            SELECT `handle_name`
            FROM `users`
            WHERE `id` = :id
        EOM;
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchColumn();
        return $result;
    }
}
