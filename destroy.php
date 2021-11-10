<?php
    // Controller
    
    // 外部ファイルの読み込み
    require_once 'filters/post_filter.php';
    require_once 'models/Message.php';

    // フォームから飛んできたidを取得
    $id = $_POST['id'];
    
    // id値から注目してるMessageインスタンスを取得
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
        
        // データベースからデータ削除
        $flash_message = Message::destroy($id);
        
        // フラッシュメッセージのセット
        $_SESSION['flash_message'] = $flash_message;
        
        // リダイレクト
        header('Location: index.php');
        exit;
        // view の表示
        include_once 'views/show_view.php';
    }
       

    

    
    // そのような投稿が存在すれば
    if($message !== false){
        

    }else{
        // エラーメッセージのセット
        $_SESSION['error'] = '存在しない投稿です';
        // リダイレクト
        header('Location: index.php');
        exit;
    }

    