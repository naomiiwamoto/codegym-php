<table>
    <!--テーブル表を作る。（HTML）-->
    <h1>一行ごとにテーブルセルの色を変える</h1>
    <?php
    for ($i = 1; $i <= 10; $i++) { //1〜10以下まで一個ずつ表示
        if ($i % 2) { //2回に一回処理
            echo ('<tr style="background-color: #ccc">'); //( <tr background-colorで背景色の指定 >)HTML
        } else {
            echo ('<tr>'); //横方向のセルをまとめる( <tr>tdやthなどの要素</tr>)
        }
        echo ('<td>' . $i . '行目</td>'); //データをいれるセルを作る(<td>データ</td>HTML)
        echo ('</tr>');
    }
    ?>
</table>

<h1>2018年9月のカレンダー</h1>
<?php
$week = ['金', '土', '日', '月', '火', '水', '木'];
for ($i = 1; $i <= 30; $i++) {
    echo ($i . '日（' . $week[$i % 7] . ')<br />');
}
?>
