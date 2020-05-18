<?php
ini_set('display_errors',1);
require_once '../tools/dbAccess.php';
require_once '../tools/sqlCreate.php';
require_once '../tools/toolbox.php';


//Ajax以外からのアクセスを遮断
$request = isset($_SERVER['HTTP_X_REQUESTED_WITH'])
? strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) : '';
if($request !== 'xmlhttprequest') exit;

$searchId = $_POST["searchId"];

//資材情報検索処理コントローラー
//searchIDの値を元に処理を割り振る
if($searchId == "register"){
    registerQuantity();
}

//直の資材の情報を保存する
function registerQuantity(){

    $productId = null;
    if (!empty($_POST["productId"]))  $productId = $_POST["productId"];

    $machineId = null;
    if (!empty($_POST["machineId"]))  $machineId = $_POST["machineId"];

    $count = null;
    if (!empty($_POST["count"]))  $count = $_POST["count"];

    $productDate = null;
    if(!empty($_POST["productDate"]))  $productDate = $_POST["productDate"];

    $zenDate = null;
    if(!empty($_POST["zenday"]))  $zenDate = $_POST["zenday"];

    $tyoku = null;
    if(!empty($_POST["tyoku"]))  $tyoku = $_POST["tyoku"];


    //
    $zentyoku = array();
    if (!empty($_POST["zentyoku"]))  $zentyoku = $_POST["zentyoku"];

    $niage = array();
    if (!empty($_POST["niage"]))  $niage = $_POST["niage"];

    $nisage = array();
    if (!empty($_POST["nisage"]))  $nisage = $_POST["nisage"];

    $genbazan = array();
    if (!empty($_POST["genbazan"]))  $genbazan = $_POST["genbazan"];

    $siyosu = array();
    if (!empty($_POST["siyosu"]))  $siyosu = $_POST["siyosu"];

    //dbAcsessをインスタンス化
    $Pdo = new dbAccsess();

    //SQLクラスをインスタンス化
    $Sql = new sqlCreate();

    //それぞれのカウント数を取得する
    $dbCount = array();
    $dbCount[0] = quantitiyCountGet($productId,$zenDate,$tyoku,"zentyoku",$Pdo);
    $dbCount[1] = quantitiyCountGet($productId,$productDate,$tyoku,"niage",$Pdo);
    $dbCount[2] = quantitiyCountGet($productId,$productDate,$tyoku,"nisage",$Pdo);
    $dbCount[3] = quantitiyCountGet($productId,$productDate,$tyoku,"genbazan",$Pdo);
    $dbCount[4] = quantitiyCountGet($productId,$productDate,$tyoku,"siyosu",$Pdo);



    //dbcount[]の回数分ループする
    for($i=0;$i<=count($dbCount);$i++){

        $koumoku = array();

        switch ($i){
            case 0: //前直
                $koumoku = $zentyoku;
                break;
            case 1:
                $koumoku = $niage;
                break;
            case 2:
                $koumoku = $nisage;
                break;
            case 3:
                $koumoku = $genbazan;
                break;
            case 4:
                $koumoku = $siyosu;
                break;
        }
        //それぞれの項目を保存していく
        for($y=1;$y<=$count;$y++){
            //dbの件数より少ない場合、update文を使用して保存する
            if($i<=$dbCount[$i]){
                $Sql->quantitiyUpdate($productId,$koumoku[$y][1],$koumoku[$y][2],$machineId,$productDate,$tyoku,$Pdo);
            }

            //dbの件数より多い場合、insert文を使用して保存する
            if($i>$dbCount[$i]){
                $Sql->quantitiyInsert($productId,$koumoku[$y][1],$koumoku[$y][2],$machineId,$productDate,$tyoku,$Pdo);
            }


        }
    }

}
