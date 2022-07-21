<?php
// sessionスタート
session_start();

// requireでfunctionを呼び込む
require('../db.php');

//--------------------------------------------------------------------------------------------------------------------------

// ログインしている場合
if (isset($_SESSION['id'])) {
    $id = $_SESSION['user_id'];
    $name = $_SESSION['user_name'];
} else {
    // ログインしていない場合、ログインページへ戻す
    header('Location: ../Login/login.php');
    exit();
}

//---------------------------------------------------------------------------------------------------------------------------

// functionの呼びだし
$db = dbconnection();



// メッセージの投稿
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);
    $field = filter_input(INPUT_POST, 'field', FILTER_SANITIZE_STRING);
    $course = filter_input(INPUT_POST, 'course', FILTER_SANITIZE_STRING);
    $Expectation = filter_input(INPUT_POST, 'Expectation', FILTER_SANITIZE_STRING);
    $Communication = filter_input(INPUT_POST, 'Communication', FILTER_SANITIZE_STRING);
    $good = filter_input(INPUT_POST, 'good', FILTER_SANITIZE_STRING);
    $bad = filter_input(INPUT_POST, 'bad', FILTER_SANITIZE_STRING);

    $stmt = $db->prepare('INSERT INTO keizi (message, field, course, Expectation, Communication, good, bad, member_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
    if (!$stmt) {
        die($db->error);
    }

    $stmt->bind_param('sssssssi', $message, $field, $course, $Expectation, $Communication, $good, $bad, $id);
    $success = $stmt->execute();
    if (!$success) {
        die($db->error);
    }

    // データベースの重複登録を防ぐ　POSTの内容を消す
    header('Location: home.php');
}
?>
<?php
/* 最大ページ数を求める */
$counts = $db->query('select count(*) as cnt from keizi');
$count = $counts->fetch_assoc();
$max_page = floor(($count['cnt'] + 1) / 5 + 1);

//--------------------------------------------------------------------------------------------------------------------------

// データの呼び出し
$stmt = $db->prepare('select p.id, p.member_id, p.message, p.field, p.course, p.Expectation, p.Communication, p.good, p.bad, p.created, m.name, m.picture, m.status, m.course, m.School_year from keizi p, members m where m.id=p.member_id order by id desc limit ?, 5');

// 最大ページ数を求める
$page = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_NUMBER_INT);
$page = ($page ?: 1);
$start = ($page - 1) * 5;
$stmt->bind_param('i', $start);
$success = $stmt->execute();

//　結果を変数におく
$stmt->bind_result($id, $member_id, $message, $field, $course1, $Expectation, $Communication, $good, $bad, $created, $name, $picture, $status, $course, $School_year);


?>
<!-------------------------------------------------------------------------------------------------------------------------->
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Css/service-home.css">

    <link rel="icon" href="../img/名称未設定-3.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />

    <title>Real intention</title>
</head>

<body>
    <!--　ヘッダーエリア　-->
    <div class="header">
        <div class="header-nav">
            <img src="../img/名称未設定-3.png" alt="" width="80" height="80">
            <a href="../Home-index/home.php">
                <h1>Real intentioN</h1>
            </a>
        </div>

        <ul>
            <li><a href="../Home-index/home.php"><i class="fa-solid fa-house"></i><span>Home</span></a></li>
            <li><a href="../Home-index/myprofile.php?id=<?php echo htmlspecialchars($id); ?>"><i class=" fa fa-user"></i><span>Profile</span></a></li>
            <li><a href="../Service-index/home.php"><i class="fa fa-briefcase"></i><span>Service</span></a></li>
            <li><a href="#"><i class="fa-solid fa-file-signature"></i><span>Contact</span></a></li>

        </ul>
    </div>




    <!---------------------------------------------------------------------------------------------------------------------->

    <!-- メインエリア -->
    <div class="container">


        <div class="main-contents">

            <div class="search-box">
                <label class="open" for="pop-up"><i class="fa-solid fa-pen-clip"></i><a href="./post.php">投稿する</a></label>


            </div>



            <?php
            while ($stmt->fetch()) :
            ?>
                <div class="post">

                    <!-- 写真の表示 -->
                    <?php if ($picture) : ?>

                        <a href="../Home-index/myprofile.php?id=<?php echo htmlspecialchars($member_id); ?>">
                            <img src="../member_picture/<?php echo htmlspecialchars($picture); ?>" alt="" width="100" height="100">
                        </a>
                    <?php endif; ?>

                    <!-- ユーザーが写真を登録していない場合はデフォルトの画像を表示 -->
                    <?php if (!$picture) : ?>
                        <a href="../Home-index/myprofile.php?id=<?php echo htmlspecialchars($member_id); ?>">
                            <img src="../img/default.png" alt="" width="100" height="100">
                        </a>
                    <?php endif; ?>


                    <li>

                        <p>
                            <!-- ユーザー情報の表示 -->
                            <span class="user_name"><?php echo htmlspecialchars($name); ?></span>
                            <span><?php echo ('@user' . $member_id); ?></span>
                        </p>

                        <p class="koube">
                            <span class="a"><?php echo $status; ?></span>
                            <span class="b"><?php echo $course; ?></span>
                            <span class="c"><?php echo $School_year; ?></span>
                        </p>


                        <!-- メッセージの表示 -->
                        <p class="start">
                            <label>企業名：<?php echo htmlspecialchars($message); ?></label>
                        </p>

                        <p class="newline">
                            <label>参加した分野：<?php echo htmlspecialchars($field); ?></label>
                        </p>

                        <p class="newline">
                            <label>参加したカリキュラム：<?php echo htmlspecialchars($course1); ?></label>
                        </p>

                        <p class="newline">
                            <label>体験内容の満足度：<?php echo htmlspecialchars($Expectation); ?></label>
                        </p>

                        <p class="newline">
                            <label>社員とのコミュニーケーション：<?php echo htmlspecialchars($Communication); ?></label>
                        </p>

                        <p class="newline">
                            <label>良かった所：<?php echo htmlspecialchars($good); ?></label>
                        </p>

                        <p class="end">
                            <label>良くなかった所：<?php echo htmlspecialchars($bad); ?></label>
                        </p>

                        <!-- 投稿時間の表示 -->
                        <p>
                            <small><?php echo htmlspecialchars($created); ?></small>
                        </p>

                        <p>
                            <!-- 自分の投稿であれば削除できる -->
                            <?php if ($_SESSION['user_id'] === $member_id) : ?>
                                [<a href="../Delete_community/delete.php?id=<?php echo htmlspecialchars($id); ?>" class="a" style="color: red;">削除</a>]
                            <?php endif; ?>

                            <!-- 自分の投稿であれば編集ができる -->
                            <?php if ($_SESSION['user_id'] === $member_id) : ?>
                                [<a href="../Update_community/update.php?id=<?php echo htmlspecialchars($id); ?>" class="a" style="color: blue;">編集</a>]
                            <?php endif; ?>


                            [<a href="reply.php?id=<?php echo htmlspecialchars($id); ?>" class="a" style="color: green;">返信</a>]

                        </p>

                    </li>

                </div>
            <?php endwhile; ?>



            <!-------------------------------------------------------------------------------------------------------------->

            <div class="btn1">
                <?php if ($page > 1) : ?>
                    <button><a href="?page=<?php echo $page - 1; ?>">&lt;&lt;<?php echo $page - 1; ?></a></button>
                <?php endif; ?>

                <?php if ($page < $max_page) : ?>
                    <button><a href="?page=<?php echo $page + 1; ?>"><?php echo $page + 1; ?>&gt;&gt;</a></button>
                <?php endif; ?>
            </div>

        </div>

        <!------------------------------------------------------------------------------------------------------------------>


        <div class="side-contents">
            <div class="search">
                <form method="post" action="search.php" class="search">
                    <input type="text" size="25" placeholder="　　メッセージを検索" name="search_name" required>
                    <button><i class="fa fa-search"></i></button>
                </form>
            </div>




            <!-- カレンダーの表示 -->
            <div class="calendar">
                <iframe src="https://calendar.google.com/calendar/embed?src=ja.japanese%23holiday%40group.v.calendar.google.com&ctz=Asia%2FTokyo" style="border: 0" frameborder="0" scrolling="no"></iframe>
            </div>


            <div class="site-content">
                <div class="site">
                    <a href="#"><img src="../img/ダウンロード.png" alt=""></a>
                    <a href="#"><img src="../img/log_main.png" alt=""></a>
                </div>

                <div class="site">
                    <a href="#"><img src="../img/ダウンロード.png" alt=""></a>
                    <a href="#"><img src="../img/log_main.png" alt=""></a>
                </div>
            </div>

            <div class="btn_arrow">
                <a href="../Login/logout1.php">ログアウト</a>
            </div>
        </div>
    </div>


    <!---------------------------------------------------------------------------------------------------------------------->

    <!-- フッターエリア -->
    <footer>
        <p>ご意見ご要望お待ちしています。気軽にお問い合わせください!</p>
        <p>&copy;hayate-studio</p>
    </footer>



</body>

</html>