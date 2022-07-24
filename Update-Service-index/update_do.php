<?php
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

// functionの呼びだし
$db = dbconnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);
    $field = filter_input(INPUT_POST, 'field', FILTER_SANITIZE_STRING);
    $course = filter_input(INPUT_POST, 'course', FILTER_SANITIZE_STRING);

    $day = filter_input(INPUT_POST, 'day', FILTER_SANITIZE_STRING);


    $Expectation = filter_input(INPUT_POST, 'Expectation', FILTER_SANITIZE_STRING);

    $Understanding = filter_input(INPUT_POST, 'Understanding', FILTER_SANITIZE_STRING);

    $Communication = filter_input(INPUT_POST, 'Communication', FILTER_SANITIZE_STRING);
    $good = filter_input(INPUT_POST, 'good', FILTER_SANITIZE_STRING);
    $bad = filter_input(INPUT_POST, 'bad', FILTER_SANITIZE_STRING);

    $trouble = filter_input(INPUT_POST, 'trouble', FILTER_SANITIZE_STRING);
    $Comprehensive = filter_input(INPUT_POST, 'Comprehensive', FILTER_SANITIZE_STRING);

    $link = filter_input(INPUT_POST, 'link', FILTER_SANITIZE_STRING);

    $update_id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_STRING);



    $stmt = $db->prepare('update keizi set message=?, field=?, course=?, days=?, Expectation=?, Understanding=?, Communication=?, good=?, bad=?, trouble=?, Comprehensive=?, link=? where id=?');
    if (!$stmt) {
        die($db->error);
    }

    $stmt->bind_param('ssssssssssssi', $message, $field, $course, $day, $Expectation, $Understanding, $Communication, $good, $bad, $trouble, $Comprehensive, $link, $update_id);
    $success = $stmt->execute();
    if (!$success) {
        die($db->error);
    }

    // データベースの重複登録を防ぐ　POSTの内容を消す
    header('Location: ../Service-index/home.php');
}
