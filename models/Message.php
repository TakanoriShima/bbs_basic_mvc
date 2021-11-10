<?php
    // Message Model //
    // 外部ファイルの読みこみ
    require_once 'models/Model.php';
    
    // 一件分の投稿データを格納するクラス
    class Message extends Model{
        
        // プロパティ
        public $id; // id
        public $name; // 投稿者名
        public $title; // タイトル
        public $body; // 本文
        public $image; // 画像ファイル名
        public $created_at; // 投稿日時
        
        // コンストラクタ
        public function __construct($name='', $title='', $body='', $image=''){
            $this->name = $name;
            $this->title = $title;
            $this->body = $body;
            $this->image = $image;
        }
        
        // 入力チェック
        public function validate(){
            // エラー配列を空で作成
            $errors = array();
            
            // 名前が入力されていなければ
            if($this->name === ''){
                $errors[] = '名前を入力してください';
            }
            // タイトルが入力されていなければ
            if($this->title === ''){
                $errors[] = 'タイトルを入力してください';
            }
            // 本文が入力されていなければ
            if($this->body === ''){
                $errors[] = '本文を入力してください';
            }
            // 画像が選択されていなければ
            if($this->image === ''){
                $errors[] = '画像を選択してください';
            }
            
            // エラー配列を返す
            return $errors;
        }
        
        // 全投稿情報を取得するメソッド
        public static function all(){
            try {
                // データースに接続
                $pdo = self::get_connection();
                // SQL文の実行
                $stmt = $pdo->query('SELECT * FROM messages ORDER BY id DESC');
                // フェッチの結果を、Messageクラスのインスタンスにマッピングする
                $stmt->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'Message');
                // 全投稿データを Messageクラスの配列として抜き出す
                $messages = $stmt->fetchAll();
                // データベースとの接続を切る
                self::close_connection($pdo, $stmt);
                // Messageクラスのインスタンス配列を返す
                return $messages;
            } catch (PDOException $e) {
                return 'PDO exception: ' . $e->getMessage();
            }
        }
        
        // id値から1件分の投稿データを抜き出すメソッド
        public static function find($id){
            try {
                // データベースに接続
                $pdo = self::get_connection();
                // SQL文の準備
                $stmt = $pdo->prepare('SELECT * FROM messages WHERE id=:id');
                // バインド処理
                $stmt->bindValue(':id', $id, PDO::PARAM_INT);
                // 実行
                $stmt->execute();
                // フェッチの結果を、Messageクラスのインスタンスにマッピングする
                $stmt->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'Message');
                // 投稿データをMessageクラスのインスタンスとして抜き出す
                $message = $stmt->fetch();
                // データベースとの接続を切る
                self::close_connection($pdo, $stmt);
                // Messageクラスのインスタンスを返す
                return $message;
            } catch (PDOException $e) {
                return null;
            }
        }
        
        // 投稿データを1件登録もしくは更新するメソッド
        public function save(){
            try {
                // データベースと接続
                $pdo = self::get_connection();
                // 新規投稿の場合
                if($this->id === null){
                    // SQL文の準備
                    $stmt = $pdo->prepare('INSERT INTO messages(name, title, body, image) VALUES (:name, :title, :body, :image)');
                    // バインド処理
                    $stmt->bindValue(':name', $this->name, PDO::PARAM_STR);
                    $stmt->bindValue(':title', $this->title, PDO::PARAM_STR);
                    $stmt->bindValue(':body', $this->body, PDO::PARAM_STR);
                    $stmt->bindValue(':image', $this->image, PDO::PARAM_STR);
                    // 実行
                    $stmt->execute();
                    // データベースとの接続を切る
                    self::close_connection($pdo, $stmt);
                    // flash_message を返す
                    return "投稿が成功しました。";
                }else{ // 更新の場合
                    // SQL文の準備
                    $stmt = $pdo->prepare('UPDATE messages SET name=:name, title=:title, body=:body, image=:image WHERE id=:id');
                    // バインド処理
                    $stmt->bindValue(':name', $this->name, PDO::PARAM_STR);
                    $stmt->bindValue(':title', $this->title, PDO::PARAM_STR);
                    $stmt->bindValue(':body', $this->body, PDO::PARAM_STR);
                    $stmt->bindValue(':image', $this->image, PDO::PARAM_STR);
                    $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
                    // 実行
                    $stmt->execute();
                    // データベースとの接続を切る
                    self::close_connection($pdo, $stmt);
                    
                    // flash_message を返す
                    return 'id: ' . $this->id . 'の投稿情報を更新しました';
                }
                
            } catch (PDOException $e) {
                return 'PDO exception: ' . $e->getMessage();
            }
        }
        
        // 投稿データを1件削除するメソッド
        public static function destroy($id){
            try {
                // データベースと接続
                $pdo = self::get_connection();
                // 画像ファイル名を取得
                $image = self::find($id)->image;
                // SQL文を準備
                $stmt = $pdo->prepare('DELETE FROM messages WHERE id=:id');
                // バインド処理
                $stmt->bindValue(':id', $id, PDO::PARAM_INT);
                // 実行
                $stmt->execute();
                // データベースとの接続を切る
                self::close_connection($pdo, $stmt);
                
                // 画像の物理的削除
                unlink('upload/' . $image);
                // flash_messageを返す
                return 'id: ' . $id . 'の投稿を削除しました。';
                
            } catch (PDOException $e) {
                return 'PDO exception: ' . $e->getMessage();
            }
        }
        
        // ファイルをアップロードするメソッド
        public static function upload(){
            // ファイル名をランダムに生成（ユニーク化）
            $image = uniqid(mt_rand(), true); 
            // アップロードされたファイルの拡張子を取得
            $image .= '.' . substr(strrchr($_FILES['image']['name'], '.'), 1);
            // 画像のフルパスを設定
            $file = 'upload/' . $image;
            // uploadディレクトリにファイル保存
            move_uploaded_file($_FILES['image']['tmp_name'], $file);
            // 新しく作成された画像名を返す
            return $image;
        }
    }