<?php
session_start(); //初期化処理
$_SESSION['session_message'] = '値をセッションに保存しました'; //PHPでセッションを使うときの関数
?>
<!doctype html>
<html lang="ja">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!--画面サイズ表示-->

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../../css/style.css">


    <main>
        <h2>セッションに値を保存する</h2>
        <pre>
セッションに値を保存しました。次のページに移動してみましょう。
&raquo; <a href="page02.php">Page02へ</a>
</pre>
    </main>
    </body>

</html>
