<?php
session_start(); //初期化処理
?>

<!doctype html>　　
<!---HTML始めるよの合図--->
<html lang="ja">　
<!---日本語を指定--->

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">　
    <!---文字化け防止--->
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!---画面サイズのしてい--->

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../../css/style.css">


    <main>
        <h2>セッションに値を保存する</h2>
        <pre>
セッションの値： <?php print($_SESSION['session_message']); ?> <!---セッション変数を画面に表示。」セッションに保存された内容はwebブラウザにを閉じなければずっと保存されている--->
<?php session_unset(); ?><!---セッション内容を消去　消去の仕方は何パターンかあるのでその時にあった消去の仕方を見つける--->
</pre>
    </main>
    </body>

</html>
