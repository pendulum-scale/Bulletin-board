<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>Bulletin-board</title>
</head>
<body>
    <?php
   
    //データベース設定
    $dsn = 'databasename';
    $user = 'username';
    $password = 'password';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    //データベース設定終わり
    $sql = "CREATE TABLE IF NOT EXISTS tbtest3"
    ." ("
    . "id INT AUTO_INCREMENT PRIMARY KEY,"
    . "name char(32),"
    . "comment TEXT,"
    . "date TEXT,"
    . "password TEXT"
    .");";
    $stmt = $pdo->query($sql);
        
        //日付データの格納
        $date = date("Y/m/d H:i:s"); 
        
        //misson3-1 削除フォームと編集フォームがともに空であるとき
        if(empty($_POST["delete"])&&empty($_POST["edit_n"]))
            {
                //名前とコメントどちらかに入力があるとき
                if((!empty($_POST["name"])||!empty($_POST["comment"]))&&!empty($_POST["password1"])&&!empty($_POST["password2"])&&empty($_POST["password3"]))
                {
                    $name = $_POST["name"];
                    $comment = $_POST["comment"];
                    $password1 = $_POST["password1"];
                    $sql = $pdo -> prepare("INSERT INTO tbtest3 (name, comment , date , password) VALUES (:name, :comment, :date, :password )");
                    $sql -> bindParam(':name', $name, PDO::PARAM_STR);
                    $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
                    $sql -> bindParam(':date', $date, PDO::PARAM_STR);
                    $sql -> bindParam(':password', $password1, PDO::PARAM_STR);
                    $sql -> execute();
                    $password1="";
                }
            }
   
        //編集フォームとパスワードにだけ入力があるとき
        if(!empty($_POST["edit_n"])&&empty($_POST["delete"])&&!empty($_POST["password3"]))
        {
            $sql = 'SELECT * FROM tbtest3';
            $stmt = $pdo->query($sql);
            $results = $stmt->fetchAll();
            $edit_n=$_POST["edit_n"];
            foreach ($results as $row){
                if($row['id']==$edit_n&&$row['password']==$_POST["password3"])
                {
                    $name = $_POST["name"];
                    $comment = $_POST["comment"];
                    if(empty($_POST["name"])&&empty($_POST["comment"]))
                    {
                        $name = $row["name"];
                        $comment = $row["comment"];
                    }
                }
                else if($row['id']==$edit_n&&$row['password']!=$_POST["password3"]) 
                {
                    $edit_n="";
                    $name = "";
                    $comment = "";
                }    
            }
        }
    if ((empty($_POST["password1"])&&empty($_POST["password2"])&&empty($_POST["password3"]))
    ||(!empty($_POST["delete"])&&empty($_POST["password2"]))
    ||(!empty($_POST["edit_n"])&&empty($_POST["password3"]))
    ||(!empty($_POST["password1"])&&!empty($_POST["password3"])))
        {
        $name="";
        $comment="";
        $edit_n="";        
        }
        
        
    ?>
    <form action="" method="post">
        <input type="text" name="name" value="<?php if(!empty($_POST["edit_n"])){echo $name;}?>" placeholder="名前">
        <input type="text" name="comment" value="<?php if(!empty($_POST["edit_n"])){echo $comment;}?>" placeholder="コメント">
        <input type="text" name="password1" placeholder="パスワード">
        <input type="submit" name="submit"><br> 
        <input type="text" name="delete" placeholder="削除番号">
        <input type="text" name="password2" placeholder="パスワード">
        <input type="submit" name="submit_d" value="削除"><br>
        <input type="text" name="edit_n" value="<?php if(!empty($_POST['edit_n'])){echo $edit_n;}?>"placeholder="編集対象番号">
        <input type="text" name="password3" placeholder="パスワード">
        <input type="submit" name="edit" value="編集">
    </form>
    <?php
        //misson3-3 削除フォームだけに入力があるとき
        if(!empty($_POST["delete"])&&empty($_POST["edit_n"])&&!empty($_POST["password2"])&&empty($_POST["password1"]))
            {
                $id = $_POST["delete"];
                $password=$_POST["password2"];  
                $sql = 'delete from tbtest3 where id=:id AND password=:password';
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->bindParam(':password', $password, PDO::PARAM_STR);
                $stmt->execute();
                $sql = 'SELECT * FROM tbtest3';
                $stmt = $pdo->query($sql);
                $results = $stmt->fetchAll();
                $sql = 'DROP TABLE tbtest3';
                $stmt = $pdo->query($sql);
                $sql = "CREATE TABLE IF NOT EXISTS tbtest3"
                ." ("
                . "id INT AUTO_INCREMENT PRIMARY KEY,"
                . "name char(32),"
                . "comment TEXT,"
                . "date TEXT,"
                . "password TEXT"
                .");";
                $stmt = $pdo->query($sql);
                $id=1;
                foreach ($results as $row){
                $name=$row['name'];
                $comment=$row['comment'];
                $date=$row['date'];
                $password=$row['password'];   
                $sql = $pdo -> prepare("INSERT INTO tbtest3 (id,name, comment , date , password) VALUES (:id,:name, :comment, :date, :password )");
                $sql -> bindParam(':name', $name, PDO::PARAM_STR);
                $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
                $sql -> bindParam(':date', $date, PDO::PARAM_STR);
                $sql -> bindParam(':password', $password, PDO::PARAM_STR);
                $sql ->bindParam(':id', $id, PDO::PARAM_INT);
                $sql -> execute();
                $id=$id+1;
                    }
            }
            
    
            
        //編集フォームから取得した、名前とコメントを編集して書き込む      
        if(!empty($_POST["edit_n"])&&empty($_POST["delete"])&&(!empty($_POST["name"])||!empty($_POST["comment"]))&&!empty($_POST["password3"])&&empty($_POST["password1"]))
            {
                $sql = 'SELECT * FROM tbtest3';
                $stmt = $pdo->query($sql);
                $results = $stmt->fetchAll();
                $idmax=count($results);
                $id = $_POST["edit_n"]; //変更する投稿番号
                if($idmax>=$id){
                $name = $_POST["name"];
                $comment = $_POST["comment"]; //変更したい名前、変更したいコメントは自分で決めること
                $password = $_POST["password3"];
                $sql = 'UPDATE tbtest3 SET name=:name,comment=:comment,date=:date, password=:password WHERE id=:id';
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
                $stmt->bindParam(':date', $date, PDO::PARAM_STR);
                $stmt->bindParam(':password', $password, PDO::PARAM_STR);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
                }
                else{
                $name=$row['name'];
                $comment=$row['comment'];
                $date=$row['date'];
                $password=$row['password'];   
                $sql = $pdo -> prepare("INSERT INTO tbtest3 (id,name, comment , date , password) VALUES (:id,:name, :comment, :date, :password )");
                $sql -> bindParam(':name', $name, PDO::PARAM_STR);
                $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
                $sql -> bindParam(':date', $date, PDO::PARAM_STR);
                $sql -> bindParam(':password', $password, PDO::PARAM_STR);
                $sql ->bindParam(':id', $id, PDO::PARAM_INT);
                $sql -> execute();
                $name="";
                $comment="";
                $date="";
                $password="";
                }
            }
    
    if (empty($_POST["name"])&&empty($_POST["comment"])) 
        {
        echo "データが入力されていません。文字列を入力して「送信」をクリックしてください<br>";
        }
    else if(!empty($_POST["password1"])&&empty($_POST["edit_n"])&&empty($_POST["delete"])&&empty($_POST["password2"])){
        echo $_POST["name"].$_POST["comment"]."を受け付けました<br>";
        }
    if ((empty($_POST["password1"])&&empty($_POST["password2"])&&empty($_POST["password3"]))
    ||((!empty($_POST["name"])||!empty($_POST["comment"]))&&empty($_POST["password1"]))
    ||(!empty($_POST["delete"])&&empty($_POST["password2"]))
    ||(!empty($_POST["edit_n"])&&empty($_POST["password3"])))
        {
        echo "パスワードが入力されていません。<br>";
        }
    if ((!empty($_POST["name"])||!empty($_POST["comment"]))&&!empty($_POST["password1"])&&!empty($_POST["password3"])) 
        {
        echo "入力と編集は同時に行えません<br>";
        }
    if ((!empty($_POST["name"])||!empty($_POST["comment"]))&&!empty($_POST["password1"])&&!empty($_POST["password2"])) 
        {
        echo "入力と削除は同時に行えません<br>";
        }
    
    //データベースの表示
    $sql = 'SELECT * FROM tbtest3';
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
    foreach ($results as $row){
        //$rowの中にはテーブルのカラム名が入る
        echo $row['id'].',';
        echo $row['name'].',';
        echo $row['comment'].',';
        echo $row['date'].',';
        echo $row['password'].'<br>';
        echo "<hr>";
    } 
    
                
    ?>
</body>
</html>