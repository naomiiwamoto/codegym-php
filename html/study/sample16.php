<?php
$file = file_get_contents('https://h2o-space.com/feed/json');
$json = json_decode($fie);

foreach ($jeon->$items as $item) :
?>
    ・<a href="<?php print($item->url); ?>"><?php print($item->title); ?></a>
<?php
endforeach;
?>
//表示されない
