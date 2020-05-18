<?php
ini_set('display_errors',1);
require_once '../../tools/dbAccess.php';
require_once '../../tools/sqlCreate.php';
require_once '../../tools/toolbox.php';


//Ajax以外からのアクセスを遮断
$request = isset($_SERVER['HTTP_X_REQUESTED_WITH'])
? strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) : '';
if($request !== 'xmlhttprequest') exit;

$searchId = $_POST["searchId"];

//資材情報検索処理コントローラー
//searchIDの値を元に処理を割り振る
if($searchId == "search"){
    searchMaterialInfo();
}else if($searchId == "comparison"){
    searchMaterialComparison();
}else if($searchId == "unique"){
    searchMaterialunique();
}

//入力された情報を元に検索を実行し、検索結果を返す
function searchMaterialInfo(){
    $select = array();
    $select[0] = "MATERIAL_ID";
    $select[1] = "MATERIAL_NUMBER";
    $select[2] = "MATERIAL_NAME";
    $select[3] = "LENGTH";
    $select[4] = "UNIT";


    $where = array();
    if (!empty($_POST["materialId"]))  $where["materialId"]   = $_POST["materialId"];
    if (!empty($_POST["materialNo"]))  $where["materialNo"]   = $_POST["materialNo"];
    if (!empty($_POST["materialName"]))$where["materialName"] = $_POST["materialName"];
    if (!empty($_POST["length"]))      $where["length"]       = $_POST["length"];
    if (!empty($_POST["unit"]))        $where["unit"]         = $_POST["unit"];

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
        echo "<h2>検索結果</h2>";
        echo "<table class='table table'>";
        echo "<thead>";
        echo "<tr>";
        echo '<th class="th-result" scope="col"></th>';
        echo '<th class="th-result" scope="col">資材番号</th>';
        echo '<th class="th-result" scope="col">資材名</th>';
        echo '<th class="th-result" scope="col">長さ</th>';
        echo '<th class="th-result" scope="col">単位</th>';
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";

        foreach ($result as $row) {
            echo '<tr id="tr'.$count.'"  onclick="sentaku('.$count.')">';
            echo '<td class="td-result">'.$count.'<input type="hidden" id="id'.$count.'" value="'.$Toolbox->es($row["MATERIAL_ID"]).'"></td>';
            echo '<td class="td-result" id="number'.$count.'">'. $Toolbox->es($row["MATERIAL_NUMBER"]). "</td>";
            echo '<td class="td-result" id="name'.$count.'">'. $Toolbox->es($row["MATERIAL_NAME"])."</td>";
            echo '<td class="td-result" id="length'.$count.'">'.$Toolbox->es($row["LENGTH"])."</td>";
            echo '<td class="td-result" id="unit'.$count.'">'.$Toolbox->es($Toolbox->unitChenge($row["UNIT"])). "</td>";
            echo "</tr>";

            $count++;
        }
        echo "</tbody>";
        echo "</table>";
    }
}

/*
 *
 */
function searchMaterialComparison(){

    //select句カラムを指定する
    $select = array();
    $select[0] = "MATERIAL_ID";
    $select[1] = "MATERIAL_NUMBER";
    $select[2] = "MATERIAL_NAME";
    $select[3] = "LENGTH";
    $select[4] = "UNIT";

    //資材IDをもとに資材情報を取り出す
    $where = $_POST["materialId"];

    //DBの情報を格納する配列を宣言する
    $dbMaterialInfo = array();

    //dbAcsessをインスタンス化
    $Pdo = new dbAccsess();

    //SQLクラスをインスタンス化
    $Sql = new sqlCreate();

    //toolboxクラスをインスタンス化
    $Toolbox = new toolbox();

    $result = $Sql->materialIdSearch($select, $where, $Pdo);

    //配列にDBの情報を格納する
    foreach ($result as $row){
        $dbMaterialInfo["materialId"] =  $Toolbox->es($row["MATERIAL_ID"]);
        $dbMaterialInfo["materialNo"] =  $Toolbox->es($row["MATERIAL_NUMBER"]);
        $dbMaterialInfo["materialName"] =  $Toolbox->es($row["MATERIAL_NAME"]);
        $dbMaterialInfo["length"] =  $Toolbox->es($row["LENGTH"]);
        $dbMaterialInfo["unit"] =  $Toolbox->es($row["UNIT"]);
    }

    //結果をjson形式で返す
    header('Content-Type: application/json');

    echo json_encode($dbMaterialInfo);

}

/*
 * 資材登録画面で入力された「資材番号」と「資材名」がDBに登録されていないかをチェックする
 * 返す値は0が無し、1があり
 */

function searchMaterialUnique(){

    //画面へ返す配列を宣言
    $materialCheck = array();
    $materialCheck["materialNo"] = null;
    $materialCheck["materialName"]   = null;

    //
    $materialNumber = array();
    $materialName   = array();

    //入力された資材番号、資材名を変数に格納する
    $materialNumber["materialNo"] = $_POST["materialNo"];
    $materialName["materialName"]     = $_POST["materialName"];

    //dbAcsessをインスタンス化
    $Pdo = new dbAccsess();

    //SQLクラスをインスタンス化
    $Sql = new sqlCreate();

    //toolboxクラスをインスタンス化
    $Toolbox = new toolbox();

    //資材番号がユニークかどうかDBを検索する
    //資材番号は入力必須ではないので未入力ならスルーする
    if(isset($materialNumber["materialNo"])){
        $materialCheck["materialNo"] = "nothing";
    }else{
        $resultNumber = $Sql->uniqueDBCheck("MATERIAL", $materialNumber, $Pdo);

        //DBの検索結果に基づき、戻り値用を格納する
        //検索結果が0であれば「$materialCheck["materialNumber"]」に0を代入
        //0以外であれば「$materialCheck["materialNumber"]」に1を代入
        if(isset($resultNumber[0]["NUM"])){
            if($Toolbox->es($resultNumber[0]["NUM"]) == 0) $materialCheck["materialNo"] = 0;
            if($Toolbox->es($resultNumber[0]["NUM"]) != 0) $materialCheck["materialNo"] = 1;
        }
    }

    //資材名がユニークかどうかDBを検索する
    //資材名は入力必須なので必ずユニーク検索を行う
    $resultName = $Sql->uniqueDBCheck("MATERIAL",$materialName, $Pdo);


    //DBの検索結果に基づき、戻り値用を格納する
    //検索結果が0であれば「$materialCheck["materialName"]」に0を代入
    //0以外であれば「$materialCheck["materialName"]」に1を代入
    if(isset($resultName[0]["NUM"])){
        if($Toolbox->es($resultName[0]["NUM"]) == 0) $materialCheck["materialName"] = 0;
        if($Toolbox->es($resultName[0]["NUM"]) != 0) $materialCheck["materialName"] = 1;
    }

    //結果をjson形式で返す
    header('Content-Type: application/json');

    echo json_encode($materialCheck);

}

?>