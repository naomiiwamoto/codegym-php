<?php
$xmlTree = simplexml_load_file('https://h2o-space.com/feed');

foreach ($xmlTree->channel->item as $item) :
?>
    ・<a href="<?php print($item->link); ?>"><?php print($item->title); ?></a>
<?php
endforeach;
?>

//表示されない
