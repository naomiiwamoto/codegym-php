<?php
session_start();
require('dbconnect.php');

if (isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()) {
    // ログインしている
    $_SESSION['time'] = time();

    $members = $db->prepare('SELECT * FROM members WHERE id=?');
    $members->execute(array($_SESSION['id']));
    $member = $members->fetch();
} else {
    // ログインしていない
    header('Location: login.php');
    exit();
}

// 投稿を記録する
if (!empty($_POST)) {
    if ($_POST['message'] != '') {
        $message = $db->prepare('INSERT INTO posts SET member_id=?, message=?, reply_post_id=?, created=NOW()');
        $message->execute(array(
            $member['id'],
            $_POST['message'],
            $_POST['reply_post_id']
        ));

        header('Location: index.php');
        exit();
    }
}

// 投稿を取得する
$page = $_REQUEST['page'];
if ($page == '') {
    $page = 1;
}
$page = max($page, 1);

// 最終ページを取得する
$counts = $db->query('SELECT COUNT(*) AS cnt FROM posts');
$cnt = $counts->fetch();
$maxPage = ceil($cnt['cnt'] / 5);
$page = min($page, $maxPage);

$start = ($page - 1) * 5;
$start = max(0, $start);

$posts = $db->prepare('SELECT m.name, m.picture, p.* FROM members m, posts p WHERE m.id=p.member_id ORDER BY p.created DESC LIMIT ?, 5');
$posts->bindParam(1, $start, PDO::PARAM_INT);
$posts->execute();

// 返信の場合
if (isset($_REQUEST['res'])) {
    $response = $db->prepare('SELECT m.name, m.picture, p.* FROM members m, posts p WHERE m.id=p.member_id AND p.id=? ORDER BY p.created DESC');
    $response->execute(array($_REQUEST['res']));

    $table = $response->fetch();
    $message = '@' . $table['name'] . ' ' . $table['message'];
}

// 本文内のURLにリンクを設定します
function makeLink($value)
{
    return mb_ereg_replace("(https?)(://[[:alnum:]\+\$\;\?\.%,!#~*/:@&=_-]+)", '<a href="\1\2">\1\2</a>', $value);
}

// htmlspecialcharsのショートカット
function h($value)
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

//いいね機能
function getfavpost($post_id, $db)
{
    $count = $db->prepare("SELECT COUNT(*) AS cnt FROM favorites WHERE member_id=? AND post_id=?");
    $count->execute(array(
        $_SESSION['id'],
        $post_id
    ));
    $record = $count->fetch();
    if ($record['cnt'] > 0) {
        $del = $db->prepare('DELETE FROM favorites WHERE member_id=? AND post_id=? ');
        $del->execute(array(
            $_SESSION['id'],
            $post_id
        ));
    } else {
        $iine = $db->prepare("INSERT INTO favorites SET member_id=?,post_id=?, created=NOW()");
        $iine->execute(array(
            $_SESSION['id'],
            $post_id
        ));
    }
    return;
}

///いいね件数を取得
function FavCount($post_id, $db)
{
    $post = $db->prepare('SELECT COUNT(*) AS cnt FROM favorites WHERE post_id=?');
    $post->execute(array($post_id));
    $posts = $post->fetch();

    return $posts['cnt'];
}

//いいねが元投稿かリツイート投稿か確認
function myFavCountpost($post_id, $db)
{
    if (originalpost($post_id, $db) === true) { //元投稿
        if (getFavCount($post_id, $db) > 0) { //favoritesに自分のidとpostidで確認 
            return true;
        }
    } else { //リツイート投稿
        $id = getRetweetPostIdById($post_id, $db);
        $count = $db->prepare("SELECT COUNT(*) AS cnt FROM favorites WHERE member_id=? AND post_id=?");  //変数に格納　favoritesの中身を検索
        $count->execute(array(
            $_SESSION['id'],
            $id
        )); //探す実行
        $record = $count->fetch();
        if ($record['cnt'] > 0) {
            return true;
        }
    }
    return false;
}

//いいねfavoritesの中身を検索
function getFavCount($post_id, $db)
{
    $count = $db->prepare("SELECT COUNT(*) AS cnt FROM favorites WHERE member_id=? AND post_id=?");  //変数に格納　favoritesの中身を検索
    $count->execute(array(
        $_SESSION['id'],
        $post_id
    )); //探す実行
    $record = $count->fetch();
    return $record['cnt'];
}

//元投稿かどうかを確認する
function originalpost($post_id, $db)
{
    $data = $db->prepare("SELECT * FROM posts WHERE id=?");
    $data->execute(array($post_id));
    $data1 = $data->fetch(); //結果

    if ($data1['retweet_post_id'] === '0') {
        return true;
    } else {
        return false;
    }
}

//自分のリツイートか確認する
function isMyretweetpost($post_id, $db)
{
    if (originalpost($post_id, $db) === true) { //元投稿
        if (retweetCount($post_id, $db) > 0) {
            return true;
        }
    } else { //リツイート投稿
        $id = getRetweetPostIdById($post_id, $db);
        $SameOriginalPost = $db->prepare('SELECT COUNT(*) as cnt FROM posts WHERE member_id=? AND retweet_post_id=?');
        $SameOriginalPost->execute(array(
            $_SESSION['id'],
            $id
        ));
        $OriginalPost = $SameOriginalPost->fetch();
        if ($OriginalPost['cnt'] > 0) {
            return true;
        }
    }
    return false;
}

//元投稿からrtを探す
function retweetCount($post_id, $db)
{
    $rt = $db->prepare("SELECT COUNT(*) AS cnt FROM posts WHERE member_id=? AND retweet_post_id=?");
    $rt->execute(array(
        $_SESSION['id'],
        $post_id
    ));
    $rtw = $rt->fetch(); //結果
    return $rtw['cnt'];
}

//idとmember_idの比較
function rtpost($post_id, $db)
{
    $data2 = $db->prepare("SELECT COUNT(*) AS cnt FROM posts WHERE id=? AND member_id=? AND retweet_post_id>0");
    $data2->execute(array(
        $post_id,
        $_SESSION['id']

    ));
    $data3 = $data2->fetch(); //結果
    return $data3['cnt'];
}

//リツイート件数
function rtCount($post_id, $db)
{
    $count = $db->prepare('SELECT COUNT(*) AS cnt FROM posts WHERE retweet_post_id=? ');
    $count->execute(array(
        $post_id,
    ));
    $rtcount = $count->fetch();


    return $rtcount['cnt'];
}

//idを元にretweet_post_idを取得
function getRetweetPostIdById($id, $db)
{

    $posts = $db->prepare('SELECT retweet_post_id FROM posts WHERE id=? ');
    $posts->execute(
        array($id)
    );
    $posts1 = $posts->fetch();

    return $posts1['retweet_post_id'];
}

//押されたpostidを元にpostsのidと比較して一致したidのmessageと元情報を取り出す
function getRetweetPost($id, $db)
{
    $rtid = getRetweetPostIdById($id, $db);
    $rtposts = $db->prepare('SELECT posts.retweet_post_id, posts.id, posts.message, posts.member_id, members.name, members.picture, members.id 
    FROM posts 
    INNER JOIN members 
    ON posts.member_id = members.id AND posts.id=?');

    $rtposts->execute(array($rtid));
    $getrtposts = $rtposts->fetch();

    return $getrtposts;
}

$id =  getRetweetPostIdById($_GET['okini'], $db); { //いいねボタンがおされた時の流れ
    if ($_GET['okini'])
        if (originalpost($_GET['okini'], $db) === true) { //リツイート投稿か元投稿か比較　
            getfavpost($_GET['okini'], $db);
        } else {
            getfavpost($id, $db);
        }
    header('Location: index.php');
    exit();
}

//実際にrtボタンが押された時の流れ 
if ($_GET['rt']) { //リツートボタンを押されたか判断して $data1 = originalpost($_GET['rt'], $db); //idを検索して、どの行か確認する必要がある
    if (originalpost($_GET['rt'], $db)) { //元投稿か調べる
        if (retweetCount($_GET['rt'], $db) > 0) { //元投稿の場合は$メンバーidとリツイートポストidで検索をする　
            //カウントで０より大きい場合は、すでにリツイートされていて、自分のリツイートが存在する。//削除できる
            $del = $db->prepare('DELETE FROM posts WHERE member_id=? AND retweet_post_id=? '); //一致した場合は削除できる
            $del->execute(array(
                $_SESSION['id'],
                $_GET['rt']
            ));
        } else {
            $enter = $db->prepare("INSERT INTO posts SET member_id=?,retweet_post_id=?, created=NOW()"); // データがない場合は情報を導入
            $enter->execute(array(
                $_SESSION['id'],
                $_GET['rt']
            ));
        }
    } else { //リツイート投稿の場合は   getRetweetPostIdById($_GET['rt'], $db)
        $rtid = getRetweetPostIdById($_GET['rt'], $db); //idを元にretweet_post_idを取得
        if (retweetCount($rtid, $db) > 0) { //リツイート投稿のidを元投稿のidに変換した値がretweet_post_idにはいる。また$member_idとretweet_post_idを検索をする　
            //カウントで０より大きい場合は、すでにリツイートされていて、自分のリツイートが存在する。//削除できる
            $del = $db->prepare('DELETE FROM posts WHERE member_id=? AND retweet_post_id=? '); //一致した場合は削除できる
            $del->execute(array(
                $_SESSION['id'],
                $rtid
            ));
        } else {
            $enter = $db->prepare("INSERT INTO posts SET member_id=?,retweet_post_id=?, created=NOW()"); // データがない場合は(自分以外の場合は)情報を導入
            $enter->execute(array(
                $_SESSION['id'],
                $rtid
            ));
        }
    }

    header('Location: index.php');
    exit();
}

?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ひとこと掲示板</title>

    <link rel="stylesheet" href="style.css" />
</head>

<body>
    <div id="wrap">
        <div id="head">
            <h1>ひとこと掲示板</h1>
        </div>
        <div id="content">
            <div style="text-align: right"><a href="logout.php">ログアウト</a></div>
            <form action="" method="post">
                <dl>
                    <dt><?php echo h($member['name']); ?>さん、メッセージをどうぞ</dt>
                    <dd>
                        <textarea name="message" cols="50" rows="5"><?php echo h($message); ?></textarea>
                        <input type="hidden" name="reply_post_id" value="<?php echo h($_REQUEST['res']); ?>" />
                    </dd>
                </dl>
                <div>
                    <p>
                        <input type="submit" value="投稿する" />
                    </p>
                </div>
            </form>

            <?php
            foreach ($posts as $post) :
            ?>
                <?php if (originalpost($post['id'], $db) === true) { ?>
                    <div class="msg">
                        <img src="member_picture/<?php echo h($post['picture']); ?>" width="48" height="48" alt="<?php echo h($post['name']); ?>" />
                        <p><?php echo makeLink(h($post['message'])); ?><span class="name">（<?php echo h($post['name']); ?>）</span>[<a href="index.php?res=<?php echo h($post['id']); ?>">Re</a>]</p>

                    <?php } else { //リツイート投稿  
                    $rtposts = getRetweetPost($post['id'], $db) ?>
                        <div class="msg"><?php echo h($post['name']), "さんがリツイートしました"; ?>
                            <img src="member_picture/<?php echo h($rtposts['picture']); ?>" width="48" height="48" alt="<?php echo h($rtpostss['name']); ?>" />
                            <p><?php echo ($rtposts['message']); ?><span class="rtname">（<?php echo h($rtposts['name']); ?>）</span>[<a href="index.php?res=<?php echo h($post['id']); ?>">Re</a>]</p>
                        <?php } ?>
                        <p class="day">
                            <!-- リツイート機能　-->
                            <a href="index.php?rt=<?php echo $post['id']; ?>">
                                <span class="retweet">
                                    <?php
                                    if (
                                        isMyretweetpost($post['id'], $db, $id) === true
                                    ) {
                                    ?>
                                        <img methottype="button" class="retweet-image" src="images/retweet-solid-blue.svg" name="rt" method="get">
                                    <?php } else { ?>
                                        <img methottype="button" class="retweet-image" src="images/retweet-solid-gray.svg" name="rt" method="get">
                                    <?php } ?>
                            </a>
                            <?php if (originalpost($post['id'], $db) === true) { //リツイート件数の表示
                            ?>
                                <span style="color:gray;"></a><?php echo rtCount($post['id'], $db); //元投稿
                                                                ?></span>
                            <?php } else { ?>
                                <span style="color:gray;"></a><?php echo rtCount(getRetweetPostIdById($post['id'], $db), $db); //リツイート投稿
                                                                ?></span>
                            <?php } ?>

                            </span>
                            <!-- いいね機能　-->
                            <a href="index.php?okini=<?php echo $post['id']; ?>">
                                <span class="favorite">

                                    <?php
                                    if (myFavCountpost($post['id'], $db) === true) {
                                    ?>
                                        <img methottype="button" class="favorite-image" src="images/heart-solid-red.svg" name="red" method="get">
                                    <?php } else { ?>
                                        <img methottype="button" class="favorite-image" src="images/heart-solid-gray.svg" name="okini" method="get">
                                    <?php }
                                    ?>
                            </a>
                            <!-- いいね件数の表示 -->
                            <?php if (originalpost($post['id'], $db) === true) { ?>
                                <span style="color:gray;"> <?php echo FavCount($post['id'], $db); ?></span>
                            <?php } else { ?>
                                <span style="color:gray;"> <?php echo FavCount(getRetweetPostIdById($post['id'], $db), $db); ?></span>
                            <?php } ?>
                            </span>
                            <a href="view.php?id=<?php echo h($post['id']); ?>"><?php echo h($post['created']); ?></a>
                            <?php
                            if ($post['reply_post_id'] > 0) :
                            ?><a href="view.php?id=<?php echo h($post['reply_post_id']); ?>">
                                    返信元のメッセージ</a>
                            <?php
                            endif;
                            ?>
                            <?php
                            if ($_SESSION['id'] == $post['member_id']) :
                            ?>
                                [<a href="delete.php?id=<?php echo h($post['id']); ?>" style="color: #F33;">削除</a>]
                            <?php
                            endif;
                            ?>
                        </p>
                        </div>
                    <?php
                endforeach;
                    ?>
                    <ul class="paging">
                        <?php
                        if ($page > 1) {
                        ?>
                            <li><a href="index.php?page=<?php print($page - 1); ?>">前のページへ</a></li>
                        <?php
                        } else {
                        ?>
                            <li>前のページへ</li>
                        <?php
                        }
                        ?>
                        <?php
                        if ($page < $maxPage) {
                        ?>
                            <li><a href="index.php?page=<?php print($page + 1); ?>">次のページへ</a></li>
                        <?php
                        } else {
                        ?>
                            <li>次のページへ</li>
                        <?php
                        }
                        ?>
                    </ul>
                    </div>
        </div>
</body>

</html>
