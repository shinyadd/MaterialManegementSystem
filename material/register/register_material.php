<?php
ini_set('display_errors',1);
require_once '../../tools/dbAccess.php';
require_once '../../tools/sqlCreate.php';
require_once '../../tools/toolbox.php';
//Ajax以外からのアクセスを遮断
$request = isset($_SERVER['HTTP_X_REQUESTED_WITH'])
? strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) : '';
if($request !== 'xmlhttprequest') exit;

//入力された値を取得する
$value = array();
$value["materialNo"]   = $_POST["materialNo"];
$value["materialName"] = $_POST["materialName"];
$value["length"]       = $_POST["length"];
$value["unit"]         = $_POST["unit"];

//dbAcsessをインスタンス化
$Pdo = new dbAccsess();

//SQLクラスをインスタンス化
$Sql = new sqlCreate();

//INSERT文を作成する
/*$sqls = "INSERT INTO MATERIAL (INSERT_DATE,UPDETE_DATE,STAT,MATERIAL_NAME,MATERIAL_NUMBER,LENGTH,UNIT)".
    " VALUES(NOW(),".
    "NOW(),".
    "1".
    '"'.$value['materialName'].'"'.",".
    '"'.$value['materialNo'].'"'.",".
    $value['length'].",".
    $value['unit'].
    ");";

   $Pdo-> dbDataOperation($sqls);*/




//SQLを実行
$flg = $Sql->materialInsert($value, $Pdo);

//データベースより取得したデータを一行ずつ表示する
if($flg == true){
    echo "<center>更新しました</center>";
}else if($flg == false){
    echo "更新に失敗しました。";
}
?>
