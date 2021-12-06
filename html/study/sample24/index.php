<?php

$value = '変数に保存した値です';
setcookie('save_message', 'Cookieに保存した値です', time() + 60 * 60 * 24 * 14);

?>

<!doctype html>
<!---DOCTYPE宣言の下に置き、html文書であることを定義するタグ-->
<html lang="ja">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"> <!-- 表示画面の指定サイズなど--->

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../../css/style.css">

</head>

<body>
    <main>
        <h2>Cookieに値を保存する</h2>
        <pre>
Cookieに値を保存しました。次のページに移動してみましょう。
&raquo; <a href="page02.php">Page02へ</a> <!--ー移動先のURLを指定--->
</pre>
    </main>
</body>

</html>
