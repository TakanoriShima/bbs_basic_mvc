<?php
    // Controller //
    
    // 外部ファイルの読み込み
    require_once 'filters/post_filter.php';
    require_once 'models/Message.php';

    // フォームからの入力値を取得
    $name = $_POST['name'];
    $title = $_POST['title'];
    $body = $_POST['body'];
    $image = $_FILES['image']['name'];

    // 新しいMessageインスタンスを生成
    $message = new Message($name, $title, $body, $image);

    // 入力チェック
    $errors = $message->validate();

    // 入力エラーが1つもなければ
    if(count($errors) === 0){
        
        // 画像ファイルの物理的アップロードと新しい画像ファイル名の取得
        $image = Message::upload();
        
        // 画像ファイル名の更新
        $message->image = $image;
        
        // Modelを使ってデータベースにデータを1件保存
        $flash_message = $message->save();
        
        // セッションにフラッシュメッセージを保存        
        $_SESSION['flash_message'] = $flash_message;
        
        // リダイレクト
        header('Location: index.php');
        exit;
        
    }else{
        // セッションにMessageインスタンス情報をセット
        $_SESSION['message'] = $message;
        // セッションにエラー配列をセット
        $_SESSION['errors'] = $errors;
        // リダイレクト
        header('Location: create.php');
        exit;
    }