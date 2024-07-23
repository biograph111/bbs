<?php
require_once __DIR__ . '/../common.php';
// セッションのスタートチェック
session_s();

// ログインチェック
$is_login = login_check();

$login_user_authority = authority_check();
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Delicious+Handrawn&family=Source+Sans+Pro:wght@200&family=Zen+Kurenaido&display=swap" rel="stylesheet">
    <title>テスト掲示板</title>
    <link rel="stylesheet" href="/bbs/common/css/reset.css">
    <link rel="stylesheet" href="/bbs/common/css/style.css">
</head>

<body>
    <header>
        <nav class="nav_wrapper">
            <h1 class="nav_list"><a class="nav_link" href="/bbs/index.php">テスト掲示板</a></h1>
            <div class="nav_list">
                <?php if ($is_login) : ?>
                <ul class="nav_link_wrapper">
                    <li>
                        <a class="nav_link" href="/bbs/account/my_page.php">マイページ</a>
                    </li>
                    <li>
                        <a class="nav_link" href="/bbs/logout/index.php">ログアウト</a>
                    </li>
                    <li>
                        <?php if ($login_user_authority) : ?>
                        <a class="nav_link" href="/bbs/user/index.php">ユーザー管理</a>
                        <?php endif; ?>
                    </li>
                </ul>
                <?php else : ?>
                <ul class="nav_link_wrapper">
                    <li>
                        <a class="nav_link" href="/bbs/login/index.php">ログイン</a>
                    </li>
                    <li>
                        <a class="nav_link" href="/bbs/user/register.php">新規登録</a>
                    </li>
                </ul>
                <?php endif; ?>
            </div>
        </nav>
    </header>
    <main class="main">