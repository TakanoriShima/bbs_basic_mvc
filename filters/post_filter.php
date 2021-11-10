<?php
    
    // フォームからのPOST通信の時だけアクセスできるようにするfilter
    
    // セッション開始
    session_start();
    
    $token = $_POST['_token'];
    
    // GET通信もしくはtokenが正しく取得できない場合は不正アクセスと判定
    if($_SERVER['REQUEST_METHOD'] === 'GET' || $token !== session_id()){
        // 空のエラー配列を準備
        $errors = array();
        $errors[] = '不正アクセスです';
        // セッションにエラーをセット
        $_SESSION['errors'] = $errors;
        
        // 画面遷移
        header('Location: index.php');
        exit;
        
    }