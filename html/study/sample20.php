<?php
$age = 'あいうえお';

$age = mb_convert_kana($age, 'n', 'UTF-8');
if(is_numeric($age)) {
    echo($age　.'歳');
} 
else{
    echo ('※ 年齢が数字ではありません');
}
