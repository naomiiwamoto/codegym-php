<?php
$doc = file_get_contents('./news_data/news.txt'); //ファイルの読み込み
$doc .= "<br />2016-07-17 ニュースを追加しました。"; //内容の追加
file_put_contents('./news_data/news.txt', $doc); //書き込み

readfile('news_data/news.txt'); //読み込みをして表示
