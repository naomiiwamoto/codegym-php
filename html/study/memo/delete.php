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
        $statement = $db->prepare('DELETE FROM memos WHERE id=?');
        $statement->execute(array($id));
    }
    ?>
    <pre>
        <P>メモを削除しました</P>
    </pre>
    <p><a href="index.php">戻る</a></p>

</main>
