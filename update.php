<?php 
    // Controller
    
    // 外部ファイルの読み込み
    require_once 'filters/post_filter.php';
    require_once 'models/Message.php';
    
    // フォームからの指定されたid値を取得
    $id = $_POST['id'];
    
    // 注目してるメッセージインスタンスを取得
    $message = Message::find($id);

    // そのような投稿が存在すれば
    if($message !== false){
        // 入力データの取得
        $name = $_POST['name'];
        $title = $_POST['title'];
        $body = $_POST['body'];
        $image = $_FILES['image']['name'];
        
        // インスタンス情報の更新
        $message->name = $name;
        $message->title = $title;
        $message->body = $body;
        
        // 現状の画像ファイル名を取得（画像の物理削除のため）
        $pre_image = $message->image;
        
        // 画像が選択されていればMessageインスタンスの画像ファイル名を上書き
        if($image !== ''){
            $message->image = $image;
        }

        // 入力チェック
        $errors = $message->validate();
        
        // 入力エラーが1つもなければ
        if(count($errors) === 0){
            
            // 画像が新しく選択されたならば
            if($image !== ''){
                // 以前の画像ファイルの物理削除
                unlink('upload/' . $pre_image);
                // 新規画像の物理的アップロード
                $image = Message::upload();
                // インスタンスの画像ファイル名の更新
                $message->image = $image;
            }
            
            // データベースの更新
            $flash_message = $message->save();
            
            // セッションにフラッシュメッセージを保存        
            $_SESSION['flash_message'] = $flash_message;
            
            // リダイレクト
            header('Location: show.php?id=' . $id);
            exit;
            
        }else{
            // セッションにエラー配列をセット
            $_SESSION['errors'] = $errors;
            // リダイレクト
            header('Location: edit.php?id=' . $id);
            exit;
        }
        
    }else{
        // セッションにエラーメッセージをセット
        $_SESSION['error'] = '存在しない投稿です';
        // リダイレクト
        header('Location: index.php');
        exit;
    }
