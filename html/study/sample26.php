<?php
$email = 'master@h20-space.com'; ///メールアドレスを代入

mb_language('japanese');
mb_internal_encoding('UTF-8'); //日本語のメールに対応　日本語を指定

$form = 'noreply@example.com'; //差出人・件名・本文を指定可能

$subject = 'よくわかるPHPの教科書';
$body = 'このメールは、『よくわかるPHPの教科書』から送信してます';
$success = mb_send_mail($email, $subject, $body, '$form :' . $form); //送信・戻り値を利用してsuccessに変数に一旦保管

?>

<!doctype html><!-- hTml開始の合図-->
<html lang="ja">　
<!--日本語を指定-->

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">　
    <!--日本語を指定　、文字化け防止-->
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!--画面表示サイズ-->

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../css/style.css">

    <main>
        <h2>電子メールを送信する</h2>
        <pre>
<?php if ($success) : ?>
電子メールを送信しました。メールボックスを確認してみてください。<!--送信が成功したかどうかをif構文で確認--->
<?php else : ?>
電子メールの送信に失敗しました。Webサーバーの設定などをご確認ください。
<?php endif; ?>
</pre>
    </main>
    </body>

</html>
