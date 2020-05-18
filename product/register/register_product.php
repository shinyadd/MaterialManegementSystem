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
$value["productNo"]   = $_POST["productlNo"];
$value["productName"] = $_POST["productName"];
$value["length"]      = $_POST["length"];
$value["machineId"]   = $_POST["machineId"];

//PRODUCT_NUMBERは数値型しか入力できないので未入力の場合NULLを代入する
if(!isset($value["productNo"])) $value["productNo"] = "null";

//dbAcsessをインスタンス化
$Pdo = new dbAccsess();

//SQLクラスをインスタンス化
$Sql = new sqlCreate();

//INSERT文を作成する
$sqls = "INSERT INTO PRODUCT (INSERT_DATE,UPDATE_DATE,STAT,PRODUCT_NAME,PRODUCT_NUMBER,PRODUCT_LENGTH,MACHINE_ID)".
" VALUES(NOW(),".
"NOW(),".
"1,".
'"'.$value['productName'].'"'.",".
$value['productNo'].",".
$value['length'].",".
$value['machineId'].
");";

$Pdo-> dbDataOperation($sqls);


?>