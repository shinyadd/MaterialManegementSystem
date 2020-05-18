<?php
class dbAccsess{
    // データベースユーザ
    const USER = 'CMK';
    const PASSWORD = 'rootroot';
    // 利用するデータベース
    const DB_NAME = 'MaterialManagementSystem';
    // MySQLサーバ
    const HOST = 'localhost:8889';
    const UTF = "utf8";

    //データベースに接続する
    function dbConect(){
        //MySQLサーバー情報を代入する
        $host = self::HOST;
        $dbName = self::DB_NAME;
        $utf = self::UTF;
        //データベースユーザを代入
        $user = self::USER;
        $pass = self::PASSWORD;
        // MySQLのDSN文字列
        $dsn = "mysql:dbname=".$dbName.";host=".$host.";charset=".$utf;

        try{
            $pdo = new PDO($dsn,$user,$pass);
            // プリペアドステートメントのエミュレーションを無効にする
            $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

        }catch(Exception $e){
            echo 'error' .$e->getMesseage;
            die();
        }
        // 例外がスローされる設定にする
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        //$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        return $pdo;
    }

    //SELECT文を実行する
    function dbSelect($sql){
        //データベースに接続する
        $pdo=$this->dbConect();
        // プリペアドステートメントを作る
        $stm = $pdo->prepare($sql);
        // SQL文を実行する
        $stm->execute();
        // 結果の取得（連想配列で返す）
        $result = $stm->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    }

    //INSERT文、UPDETE文、DELETE文を実行する
    function dbDataOperation($sql){
        $flg = true;
        try {
            //データベースに接続する
            $pdo=$this->dbConect();
            // プリペアドステートメントを作る
            $stm = $pdo->prepare($sql);
            // SQL文を実行する
            $stm->execute();

        } catch (PDOException $Exception) {
            //元の状態に戻す
            $pdo->rollBack();
            $flg = false;
        }

        return $flg;
    }

}
?>