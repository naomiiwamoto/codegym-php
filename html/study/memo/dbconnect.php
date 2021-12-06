<?php
try {
    $db = new PDO('mysql:dbname=mydb;host=mysql;charset=utf8', 'root', 'root'); //データーベースに接続
} catch (PDOException $e) {
    echo 'DB接速エラー：' . $e->getMessage();
}
