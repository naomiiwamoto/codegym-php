<?php require('dbconnect.php'); ?>
<!doctype html>
<html lang="ja">

<head>
    <meta charset="utf-8">
</head>
<main>
    <h2>practice</h2>
    <?php

    if (isset($_REQUEST['id']) && is_numeric($_REQUEST['id'])) {
        $id = $_REQUEST['id'];

        $memos = $db->prepare('SELECT * FROM memos WHERE id=?');
        $memos->execute($array($id));
        $memo = $memos->fetch();
    }
    ?>


    <form action="update_do.php" method="post">
        <input type="hidden" name='id' value="<?php print($id); ?>">
        <textarea name="memo" cols="50" rows="10">
        </textarea>
        <br>
        <button type="submit">登録する</button>
    </form>



</main>
