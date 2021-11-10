<?php
    // Controller //
    
    // 外部ファイルの読み込み   
    require_once 'models/Message.php';
    
    // セッション開始
    session_start();
    
    // セッションからMessageインスタンスを取得、削除
    $message = $_SESSION['message'];
    $_SESSION['message'] = null;
    
    // セッションにMessageインスタンスが保存されていなければ
    if($message === null){
        // 空のインスタンスを作成
        $message = new Message();
    }
    
    // セッションからエラーメッセージを取得、削除
    $errors = $_SESSION['errors'];
    $_SESSION['errors'] = null;
    
    // CSRF対策(セッションidを取得)
    $token = session_id();
    
    // view の表示
    include_once 'views/create_view.php';
