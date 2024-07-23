<?php
// 画像をディレクトリに保存
function image_save()
{
    $save = "../storage/image/" . basename($_FILES['image']['name']);
    if (move_uploaded_file($_FILES['image']['tmp_name'], $save)) {
        return true;
    } else {
        return false;
    }
}
