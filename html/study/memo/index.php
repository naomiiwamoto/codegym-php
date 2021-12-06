<?php require('dbconnect.php'); ?>

<!doctype html>
<html lang="ja">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/style.css">

    <title>よくわかるPHPの教科書</title>
</head>

<body>
    <header>
        <h1 class="font-weight-normal">よくわかるPHPの教科書</h1>
    </header>

    <main>
        <h2>Practice</h2>
        <pre>
<?php
try {
    $db = new PDO('mysql:dbname=mydb;host=mysql;charset=utf8', 'root', 'root');
} catch (PDOException $e) {
    echo 'DB接速エラー：' . $e->getMessage();
}

if (isset($_REQUEST['page']) && is_numeric($_REQUEST['page'])) {
    $page = $_REQUEST['page'];
} else {
    $page = 1;
}
$page = $_REQUEST['page'];
$start = 5 * ($page - 1);

$memos = $db->prepare('SELECT * FROM memos ORDER BY id LIMIT ?,5');
$memos->bindParam(1, $start, PDO::PARAM_INT);
$memos->execute();
?>
<article>
    <?php while ($memo = $memos->fetch()) : ?>
        <p><a href="memo.php?id=<?php print($memo['id']); ?>"><?php print(mb_substr($memo['memo'], 0, 50)); ?></a></p>
        <time><?php print($memo['created_at']); ?></time>
        <hr>
        <?php endwhile; ?>

        <?php if ($page >= 2) : ?>
        <a href="index.php?page=<?php print($page - 1); ?>"><?php print($page - 1); ?> ページ目へ</a>
        <?php endif; ?>
        |
        <?php
        $conuts = $db->query('SELECT COUNT(*) AS cnt FROM memos');
        $count = $conuts->fetch();
        $max_page = ceil($count['cnt'] / 5);
        if ($page < $max_page) :
        ?>
        <a href="index.php?page=<?php print($page + 1); ?>"><?php print($page + 1); ?> ページ目へ</a>
        <?php endif; ?>
</article>



</pre>
    </main>
</body>

</html>
