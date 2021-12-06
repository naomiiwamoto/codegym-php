<?php
$success = file_put_contents('./news_data/news.txt', '2016-07-17 ホームページをリニューアルしました');
if ($success) {
    echo 'ファイルへの書き込みが完了しました。';
} else {
    echo '書き込みに失敗しました。フォルダの権限などを確認してください。';
}
