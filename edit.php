<?php
    // Controller //
    
    // 外部ファイルの読み込み
    require_once 'models/Message.php';
    
    // セッション開始
    session_start();
    
    // 注目している投稿のIDを取得
    $id = $_GET['id'];
    
    // 指定されたid値からMessageインスタンスを取得
    $message = Message::find($id);
    


    // Messageインスタンスが存在しなければ
    if($message === false){
        // 空のエラー配列を作成
        $errors = array();
        $errors[] = 'そのような投稿は存在しません';
        // セッションにエラーメッセージを保存
        $_SESSION['errors'] = $errors;
        // リダイレクト
        header('Location: index.php');
        exit;
    }else{
        
        // セッションからエラーメッセージの取得、削除
        $errors = $_SESSION['errors'];
        $_SESSION['errors'] = null;
        
        // CSRF対策
        $token = session_id();
        
        // view の表示
        include_once 'views/edit_view.php';
    }