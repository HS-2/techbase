<?php

function h($text) {
  return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');// htmlspecialchars() 関数を使用してエスケープ（サニタイズ）処理（HTMLの特殊文字を無効化）します。
}
//DB接続設定
 $dsn="DBname";
 $user="username";
 $password="password";
 $pdo=new PDO($dsn,$user,$password,array(PDO::ATTR_ERRMODE =>PDO::ERRMODE_WARNING));
    //テーブル作成  
    $sql= "CREATE TABLE  IF NOT EXISTS mission5_1(
     id INT AUTO_INCREMENT PRIMARY KEY,  
     name char(32),
     comment TEXT,
     date datetime,
     password char(30)
     )";
     $res=$pdo ->query($sql);
  $deta0=null;
  $deta1=null;
  $deta2=null;
//新規入力
    if(!empty($_POST["name"]) && !empty($_POST["comment"]) && !empty($_POST["pass"])&& $_POST["submit"]=="送信" && empty($_POST["editnumber"])){
    //もし[名前]かつ[コメント]かつ[パスワード]が空じゃなかったらかつ送信ボタンが押されたらかつ編集番号が空だったら
    echo "送信しました。";
    $name=h($_POST["name"]);
    $comment=h($_POST["comment"]);
    $password=h($_POST["pass"]);
    
    //テーブルにINSERTを行ってデータを入力５
    $sql = $pdo -> prepare("INSERT INTO mission5_1 (name, comment,date,password) VALUES (:name,:comment,now(),:password)");
	$sql -> bindParam(':name', $name, PDO::PARAM_STR);
	$sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
	$sql -> bindParam(':password', $password, PDO::PARAM_STR);
	$sql -> execute();
	
//投稿削除
     } elseif(!empty($_POST["delete"] )&& !empty($_POST["delpass"])&&$_POST["submit"]=="削除"){ 
         //もし[削除番号]かつ[削除パスワード]が空じゃないかつ削除ボタンが押されたら 
     $delpass=h($_POST["delpass"]);
         $del=$_POST["delete"];
         
    //パスワード認証
	 $id=$del;
     // SELECT文を変数に格納
       $sql = "SELECT * FROM mission5_1 where id = :id";
    // SQLステートメントを実行し、結果を変数に格納
    $stmt = $pdo->prepare($sql);
    // プレースホルダと変数をバインド
	$stmt -> bindParam(":id",$id, PDO::PARAM_INT);
	$stmt -> execute(); //実行
	// データを取得
	$rec = $stmt->fetch(PDO::FETCH_ASSOC);
	$pass= $rec['password'];
    if($delpass==$pass){ 
    //入力したデータをdeleteによって削除する
   	$sql = 'delete from mission5_1 where id= :id';
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':id', $id, PDO::PARAM_INT);
	$stmt->execute();
        }else{
            echo "パスワードが間違っています。";
        }
	    
//編集番号指定
    }elseif(!empty($_POST["edit"])&& !empty($_POST["edipass"])&&$_POST["submit"]=="編集"){
        //もし[編集番号]かつ[編集パスワード]が空じゃないかつ編集ボタンが押されたら
        $edi=$_POST["edit"];
        $edipass=h($_POST["edipass"]);
        
    //パスワード認証 
	 $id=$edi;
     // SELECT文を変数に格納
    $sql = "SELECT * FROM mission5_1 where id = :id";
    // SQLステートメントを実行し、結果を変数に格納
    $stmt = $pdo->prepare($sql);
    // プレースホルダと変数をバインド
	$stmt -> bindParam(":id",$id, PDO::PARAM_INT);
	$stmt -> execute(); //実行
	// データを取得
	$rec = $stmt->fetch(PDO::FETCH_ASSOC);
	$pass= $rec['password'];
    if($edipass==$pass){    //パスワード分岐 
     //入力したデータをselectによって表示する
    $sql = 'SELECT * FROM mission5_1 WHERE id=:id ';
    $stmt = $pdo->prepare($sql);                  // ←差し替えるパラメータを含めて記述したSQLを準備し、
    $stmt->bindParam(':id', $id, PDO::PARAM_INT); // ←その差し替えるパラメータの値を指定してから、
    $stmt->execute();                             // ←SQLを実行する。
    $results = $stmt->fetchAll(); 
	foreach ($results as $row){
		//$rowの中にはテーブルのカラム名が入る
		$deta0= $row['id'];
		$deta1= $row['name'];
		$deta2= $row['comment'];
	}}else{
	    echo"パスワードが間違っています。";
	}
	
	    
//編集内容更新
    }else if(!empty($_POST["name"]) && !empty($_POST["comment"])&& !empty($_POST["editnumber"])&& !empty($_POST["pass"]) && $_POST["submit"]=="送信") {
        //もし[内容]と[コメント]と[編集番号(隠し)]と[パスワード]が空じゃないかつ送信ボタンが押されたら。
    $name=h($_POST["name"]);
    $comment=h($_POST["comment"]);
    $edinum=$_POST["editnumber"];
    $repass=h($_POST["pass"]);
     
    //パスワード認証 
      $id=$edinum;
	// SELECT文を変数に格納
    $sql = "SELECT * FROM mission5_1 where id = :id";
    // SQLステートメントを実行し、結果を変数に格納
    $stmt = $pdo->prepare($sql);
    // プレースホルダと変数をバインド
	$stmt -> bindParam(":id",$id, PDO::PARAM_INT);
	$stmt -> execute(); //実行
	// データを取得
	$rec = $stmt->fetch(PDO::FETCH_ASSOC);
	$pass= $rec['password'];
    if($repass==$pass){  //パスワード分岐
   	$sql = 'update mission5_1 set name=:name,comment=:comment, date=now() where id=:id';
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':name', $name, PDO::PARAM_STR);
	$stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
	$stmt->bindParam(':id', $id, PDO::PARAM_INT);
	$stmt->execute();
        
    }else{
        echo "パスワードが間違っています。";
    }}
        
 ?>
 
 <!DOCTYPE html> 
<html lang="ja">
<head>
    <meta charset="UTF-8">
</head>
<p>【 投稿フォーム 】</p>
<body>
    <form method="post" action"">
        <input type="text" name="name" placeholder="名前" value="<?php echo $deta1; ?>"><br>
        <input type="text" name="comment" placeholder="コメント"value="<?php echo $deta2; ?>"><br>
        <input type="password" name="pass" placeholder="パスワード">
        <input type="submit" name="submit" value="送信"><br>
        <input type=hidden name="editnumber" value="<?php echo $deta0; ?>"><br>
          </form>
 <p>【 削除フォーム 】</p>
    <form method="post" action"">        
    <input type="text" name="delete" placeholder="削除対象番号"><br>
    <input type="password" name="delpass"placeholder="パスワード" >
    <input type="submit" name="submit" value="削除"><br><br>
    </form>
    
<p>【 編集フォーム 】</p>    
    <form method="post" action"">        
    <input type="text" name="edit" placeholder="編集対象番号"><br>
    <input type="password" name="edipass" placeholder="パスワード">
    <input type="submit" name="submit" value="編集"><br>
    </form>
<p>【 投稿一覧 】</p>
</body>
</html>
<?php
//入力したデータをselectによって表示する
$sql = 'SELECT * FROM mission5_1';
	$stmt = $pdo->query($sql);
	$results = $stmt->fetchAll();
	foreach ($results as $row){
		//$rowの中にはテーブルのカラム名が入る
		echo $row['id'].',';
		echo $row['name'].',';
		echo $row['comment'].',';
		echo $row['date'].'<br>';
		//echo $row['password'];
	echo "<hr>";
	}
?>
