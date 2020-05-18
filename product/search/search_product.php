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
    searchProductInfo();
}else if($searchId == "comparison"){
    searchMaterialComparison();
}else if($searchId == "unique"){
    searchProductUnique();
}

//入力された情報を元に検索を実行し、検索結果を返す
function searchProductInfo(){
    $select = array();
    $select[0] = "PRODUCT_ID";
    $select[1] = "PRODUCT_NUMBER";
    $select[2] = "PRODUCT_NAME";
    $select[3] = "PRODUCT_LENGTH";


    $where = array();
    if (!empty($_POST["productNo"]))  $where["productNo"]   = $_POST["productNo"];
    if (!empty($_POST["productName"]))$where["productName"] = $_POST["productName"];
    if (!empty($_POST["length"]))      $where["length"]       = $_POST["length"];


    //dbAcsessをインスタンス化
    $Pdo = new dbAccsess();

    //SQLクラスをインスタンス化
    $Sql = new sqlCreate();

    //toolboxクラスをインスタンス化
    $Toolbox = new toolbox();

    $result = $Sql->productSearch($select ,$where, $Pdo);

    $count = 1;
    //データベースより取得したデータを一行ずつ表示する
    if(count($result) == 0){
        echo "<center>検索した結果、0件でした。</center>";
    }else if(count($result) != 0){
        echo "<table class='table table'>";
        echo "<thead>";
        echo "<tr>";
        echo '<th class="th-result" scope="col"></th>';
        echo '<th class="th-result" scope="col">製品番号</th>';
        echo '<th class="th-result" scope="col">製品名</th>';
        echo '<th class="th-result" scope="col">長さ</th>';
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";

        foreach ($result as $row) {
            echo '<tr id="tr'.$count.'"  onclick="sentaku('.$count.')">';
            echo '<td class="td-result">'.$count.'<input type="hidden" id="id'.$count.'" value="'.$Toolbox->es($row["PRODUCT_ID"]).'"></td>';
            echo '<td class="td-result" id="number'.$count.'">'. $Toolbox->es($row["PRODUCT_NUMBER"]). "</td>";
            echo '<td class="td-result" id="name'.$count.'">', $Toolbox->es($row["PRODUCT_NAME"]), "</td>";
            echo '<td class="td-result" id="length'.$count.'">', $Toolbox->es($Toolbox->lengthChangerReverse($row["PRODUCT_LENGTH"])), "</td>";
            echo "</tr>";

            $count++;
        }
        echo "</tbody>";
        echo "</table>";
    }
}

/*
 * 製品登録画面で入力された「製品番号」と「製品名」がDBに登録されていないかをチェックする
 * 返す値は0が無し、1があり
 */

function searchProductUnique(){

    //画面へ返す配列を宣言
    $productCheck = array();
    $productCheck["productNo"] = null;
    $productCheck["productName"]   = null;

    //
    $productNo = array();
    $productName   = array();

    //入力された資材番号、資材名を変数に格納する
    $productNo["productNo"] = $_POST["productNo"];
    $productName["productName"]     = $_POST["productName"];

    //dbAcsessをインスタンス化
    $Pdo = new dbAccsess();

    //SQLクラスをインスタンス化
    $Sql = new sqlCreate();

    //toolboxクラスをインスタンス化
    $Toolbox = new toolbox();

    //資材番号がユニークかどうかDBを検索する
    //資材番号は入力必須ではないので未入力ならスルーする
    //入力画面に返す値は「nothing」とする
    if(isset($productNo["productNo"])){
        $productCheck["productNo"] = "nothing";
    }else{
        $resultNumber = $Sql->uniqueDBCheck("PRODUCT", $productNo, $Pdo);

        //DBの検索結果に基づき、戻り値用を格納する
        //検索結果が0であれば「$productCheck["productNumber"]」に0を代入
        //0以外であれば「$productCheck["productNumber"]」に1を代入
        if(isset($resultNumber[0]["NUM"])){
            if($Toolbox->es($resultNumber[0]["NUM"]) == 0) $productCheck["productNo"] = 0;
            if($Toolbox->es($resultNumber[0]["NUM"]) != 0) $productCheck["productNo"] = 1;
        }
    }

    //製品名がユニークかどうかDBを検索する
    //製品名は入力必須なので必ずユニーク検索を行う
    $resultName = $Sql->uniqueDBCheck("PRODUCT", $productName, $Pdo);


    //DBの検索結果に基づき、戻り値用を格納する
    //検索結果が0であれば「$productCheck["productName"]」に0を代入
    //0以外であれば「$productCheck["productName"]」に1を代入
    if(isset($resultName[0]["NUM"])){
        if($Toolbox->es($resultName[0]["NUM"]) == 0) $productCheck["productName"] = 0;
        if($Toolbox->es($resultName[0]["NUM"]) != 0) $productCheck["productName"] = 1;
    }

    //結果をjson形式で返す
    header('Content-Type: application/json');

    echo json_encode($productCheck);

}
