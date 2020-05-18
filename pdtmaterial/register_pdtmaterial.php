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
if($searchId == "search"){
    searchMaterialInfo();
}else if($searchId == "register"){
    registerpdtMaterial();
}

//入力された情報を元に検索を実行し、検索結果を返す
function searchMaterialInfo(){
    $select = array();
    $select[0] = "MATERIAL_ID";
    $select[1] = "MATERIAL_NUMBER";
    $select[2] = "MATERIAL_NAME";
    $select[3] = "LENGTH";

    $where = array();
    if (!empty($_POST["materialNo"]))  $where["materialNo"]   = $_POST["materialNo"];
    if (!empty($_POST["materialName"]))$where["materialName"] = $_POST["materialName"];
    if (!empty($_POST["length"]))      $where["length"]       = $_POST["length"];

    //dbAcsessをインスタンス化
    $Pdo = new dbAccsess();

    //SQLクラスをインスタンス化
    $Sql = new sqlCreate();

    //toolboxクラスをインスタンス化
    $Toolbox = new toolbox();

    $result = $Sql->materialSearch($select ,$where, $Pdo);

    $count = 1;
    //データベースより取得したデータを一行ずつ表示する
    if(count($result) == 0){
        echo "<center>検索した結果、0件でした。</center>";
    }else if(count($result) != 0){
        echo "<h2>ー検索結果ー</h2>";
        echo "<table class='table table'>";
        echo "<thead>";
        echo "<tr>";
        echo '<th class="th-result" scope="col"></th>';
        echo '<th class="th-result" scope="col">資材番号</th>';
        echo '<th class="th-result" scope="col">資材名</th>';
        echo '<th class="th-result" sscope="col">長さ</th>';
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";

        foreach ($result as $row) {
            echo '<tr id="tr'.$count.'"  onclick="sentaku('.$count.')" onmouseover="thinOrengeBackground(this);" onmouseout="modalWhiteBackground(this);">';
            echo '<td class="td-result" >'.$count.'<input type="hidden" id="id'.$count.'" value="'.$Toolbox->es($row["MATERIAL_ID"]).'"></td>';
            echo '<td class="td-result" id="number'.$count.'">'. $Toolbox->es($row["MATERIAL_NUMBER"]). "</td>";
            echo '<td class="td-result" id="name'.$count.'">'. $Toolbox->es($row["MATERIAL_NAME"])."</td>";
            echo '<td class="td-result" id="length'.$count.'">'.$Toolbox->es($row["LENGTH"])."</td>";
            echo "</tr>";

            $count++;
        }
        echo "</tbody>";
        echo "</table>";
        echo '<input type="hidden" id="sentaku_mtid" value="">';
        echo '<input type="hidden" id="sentaku_mtname" value="">';
        echo '<input type="hidden" id="modal_count" value="'.($count-1).'">';
    }
}

function registerpdtMaterial(){

    $productId = null;
    if (!empty($_POST["productId"]))  $productId = $_POST["productId"];

    $machineId = null;
    if (!empty($_POST["machineId"]))  $machineId = $_POST["machineId"];


    $materialTypeId = array();
    if (!empty($_POST["materialTypeId"]))  $materialTypeId = $_POST["materialTypeId"];

    $materialId = array();
    if (!empty($_POST["materialId"]))  $materialId = $_POST["materialId"];

    $count = null;
    if (!empty($_POST["count"]))  $count = $_POST["count"];

    //dbAcsessをインスタンス化
    $Pdo = new dbAccsess();

    //SQLクラスをインスタンス化
    $Sql = new sqlCreate();

    //DBの製品資材情報の件数を取得する
    $dbPdtmaterialCount = $Sql->pdtmaterialCountGet($productId, $Pdo);

    //製品資材情報を保存していく
    for($i=1;$i<=$count;$i++){
        //dbの件数より少ない場合、update文を使用して保存する
        if($i<=$dbPdtmaterialCount){
            $Sql->pdtmaterialUpdate($productId, $machineId, $materialTypeId, $materialId, $i, $Pdo);
        }

        //dbの件数より多い場合、insert文を使用して保存する
        if($i>$dbPdtmaterialCount){
            $Sql->pdtmaterialInsert($productId, $machineId, $materialTypeId, $materialId, $i, $Pdo);
        }
    }

    //dbPdtmaterialが$pdtmaterialInfo（画面入力）より多ければ、多い分を削除する
    for($i=1;$i<=$dbPdtmaterialCount;$i++){

        if($i>$count){
            $Sql->pdtmaterialDelete($productId, $machineId , $i , $Pdo);
        }
    }
}