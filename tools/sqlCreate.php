<?php
class sqlCreate{


    /*
     * Select句を生成する
     * $column は配列で列名を入れたもの
     */

    function selectCreate( $column ){

        $select = "SELECT ";


        for($i=0; $i<count($column); $i++){
            //$column(配列)から項目を取り出し$selectに追加していく
            //,を最後につけてはいけないのです、$columnの

            if($i+1 == count($column)){
                $select = $select . $column[$i] . " ";
            }else{
                $select = $select . $column[$i] . " , ";
            }
        }

        return $select;
    }

    /*
     * セレクト句を生成する
     * select句で全ての項目を取得する場合に使用する
     */
    function selectCreateAll(){
        $select = "SELECT * ";

        return $select;
    }

    //FROM句を生成する。一つのテーブルの場合のみ有効
    function fromCreate($table) {
        $from = "FROM ";

        $from = $from . $table . " ";

        return $from;

    }

    //where句を生成、資材編集ページのMATERIALテーブル検索でのみ有効
    function whereCreateMaterial($conditions){

        $where ="";
        $conCount = 0;

        //WHERE句は条件の二つ目はANDでつなぐため
        if(isset($conditions["materialId"]))     $conCount = $conCount + 1;
        if(isset($conditions["materialNumber"])) $conCount = $conCount + 1;
        if(isset($conditions["materialName"]))   $conCount = $conCount + 1;
        if(isset($conditions["length"]))         $conCount = $conCount + 1;
        if(isset($conditions["unit"]))           $conCount = $conCount + 1;

        if($conCount != 0) {
            $where = "WHERE ";

            //MATERIAL_IDの条件
            if(isset($conditions["materialId"])){

                $where = $where . 'MATERIAL_ID = '. $conditions[materialId].' ';

            }

            //MATERIAL_NUMBERの条件
            if(isset($conditions["materialNumber"])){

                $where = $where . 'MATERIAL_NUMBER LIKE "%'. $conditions["materialNumber"].'%"';

            }else if(isset($conditions["materialNumber"])){

                $where = $where . 'AND MATERIAL_NUMBER LIKE "%'. $conditions["materialNumber"].'%"';

            }

            //MATERIAL_NAMEの条件
            if($conditions["materialName"] != "" && $conCount == 1){

                $where = $where . 'MATERIAL_NAME LIKE "%'. $conditions["materialName"].'%"';

            }else if($conditions["materialName"] != "" && $conditions["materialName"] != ""){

                $where = $where . 'AND MATERIAL_NAME LIKE "%'. $conditions["materialName"].'%"';

            }

            //LENGTH
            if(isset($conditions["length"]) && $conCount == 1){

                $where = $where . 'LENGTH = '. $conditions["length"] . ' ';

            }else if(isset($conditions["length"]) && $conCount > 1){
                $where = $where . 'AND LENGTH = '. $conditions["length"] . ' ';
            }

            //UNIT
            if(isset($conditions["unit"]) && $conCount == 1){

                $where = $where . 'UNIT = '. $conditions["unit"] . ' ';

            }else if(isset($conditions["unit"]) && $conCount > 1){
                $where = $where . 'AND UNIT = '. $conditions["unit"] . ' ';
            }
        }

        return $where;
    }

    //where句を生成、「製品編集」「製品へ資材を登録」ページのPRODUCTテーブル検索でのみ有効
    function whereCreateProduct($conditions){

        $where ="";
        $conCount = 0;

        //WHERE句は条件の二つ目はANDでつなぐため
        if(isset($conditions["productId"]))     $conCount = $conCount + 1;
        if(isset($conditions["productNumber"])) $conCount = $conCount + 1;
        if(isset($conditions["productName"]))   $conCount = $conCount + 1;
        if(isset($conditions["length"]))         $conCount = $conCount + 1;

        if($conCount != 0) {
            $where = "WHERE ";

            //PRODUCT_IDの条件
            if(isset($conditions["productId"])){

                $where = $where . 'PRODUCT_ID = '. $conditions[productId].' ';

            }

            //PRODUCT_NUMBERの条件
            if(isset($conditions["productNumber"])){

                $where = $where . 'PRODUCT_NUMBER = '. $conditions["productNumber"].' ';

            }else if(isset($conditions["productNumber"])){

                $where = $where . 'AND PRODUCT_NUMBER = '. $conditions["productNumber"].' ';

            }

            //PRODUCT_NAMEの条件
            if($conditions["productName"] != "" && $conCount == 1){

                $where = $where . 'PRODUCT_NAME LIKE "%'. $conditions["productName"].'%"';

            }else if($conditions["productName"] != "" && $conditions["productName"] != ""){

                $where = $where . 'AND PRODUCT_NAME LIKE "%'. $conditions["productName"].'%"';

            }

            //LENGTH
            if(isset($conditions["length"]) && $conCount == 1){

                $where = $where . 'PRODUCT_LENGTH = '. $conditions["length"] . ' ';

            }else if(isset($conditions["length"]) && $conCount > 1){
                $where = $where . 'AND PRODUCT_LENGTH = '. $conditions["length"] . ' ';
            }
        }

        return $where;
    }

    /*資材情報比較用に使用
     * WHERE句にID番号をセットする
     *
     */
    function whereComparisonId($where){

        $where = "WHERE MATERIAL_ID = " .$where. " ";

        return $where;
    }

    /*
     * 製品登録時の比較用に使用
     * where句の生成をする
     */
    function whereProductComparison($productInfo){

        $where = "";

        if(isset($productInfo["productNumber"])){
            $where = "WHERE PRODUCT_NAMBER = '" .$productInfo["productNumber"]. "' ";
        }

        if(isset($productInfo["productName"])){
            $where = "WHERE PRODUCT_NAME = '" .$productInfo["productName"]. "' ";
        }


        return $where;
    }

    /*
     * 「資材」「製品」登録時の比較用に使用
     * where句の生成をする
     */
    function whereComparison($tableName, $screenInfo){

        $where = "";

        //資材情報を比較する場合
        if($tableName == "MATERIAL"){
            if(isset($screenInfo["materialNo"])){
                $where = "WHERE MATERIAL_NAMBER = '" .$screenInfo["materialNumber"]. "' ";
            }

            if(isset($screenInfo["materialName"])){
                $where = "WHERE MATERIAL_NAME = '" .$screenInfo["materialName"]. "' ";
            }
        }

        //製品情報を比較する場合
        if($tableName == "PRODUCT"){
            if(isset($screenInfo["productNo"])){
                $where = "WHERE PRODUCT_NAMBER = '" .$screenInfo["productNumber"]. "' ";
            }

            if(isset($screenInfo["productName"])){
                $where = "WHERE PRODUCT_NAME = '" .$screenInfo["productName"]. "' ";
            }
        }


        return $where;
    }

    //update文set句を生成、資材編集ページのMATERIALテーブル検索でのみ有効
    function setCreate($value){

        $set = "SET UPDATE_DATE = NOW()";

        //MATERIAL_NUMBERの値の設置
        $set = $set . ', MATERIAL_NUMBER = "'. $value[materialNo].'" ';

        //MATERIAL_NAMEの値の設置
        $set = $set . ', MATERIAL_NAME = "'. $value[materialName].'" ';

        //LENGTH
        $set = $set . ', LENGTH = '. $value[length] . ' ';

        //UNIT
        $set = $set . ', UNIT = '. $value[unit] . ' ';

        return $set;

    }

    //SQLを生成し、機械の情報を取りだす
    function machineInfoGet( $num , $Pdo){
        $machineId =  $num;

        //SELECT文を作成する
        $sql = "SELECT MACHINE_ID , MACHINE_NAME , MACHINE_NO , WORKING_FORM , MAKING_PRODUCT ".
            "FROM MACHINE ".
            "WHERE MACHINE_ID = ".$machineId;
        $machineInfo = $Pdo-> dbSelect($sql);

        return $machineInfo;
    }

    //資材を登録する
    function materialInsert( $material , $Pdo){

        if($material["length"] == "") $material["length"]  = "null";

        //INSERT文を作成する
        $sql = "INSERT INTO MATERIAL (INSERT_DATE,UPDATE_DATE,MATERIAL_NAME,MATERIAL_NUMBER,LENGTH,UNIT)".
            " VALUES(NOW(),".
            "NOW(),".
            '"'.$material['materialName'].'"'.",".
            '"'.$material['materialNo'].'"'.",".
            $material['length'].",".
            $material['unit'].
            ");";

            $flg = $Pdo-> dbDataOperation($sql);

            return $flg;

    }

    //資材情報を取り出す
    function materialSearch($column , $conditions , $Pdo){
        $sql = "";
        $materialInfo = null;


        //Select句を作成する
        $sql = $this->selectCreate($column);

        //FROM句を作成する
        $sql = $sql. $this->fromCreate("MATERIAL");

        //WHERE句を作成する
        $sql =$sql. $this->whereCreateMaterial($conditions);


        $materialInfo = $Pdo-> dbSelect($sql);

        return $materialInfo;
    }

    //製品情報を取り出す
    function productSearch($column , $conditions , $Pdo){
        $sql = "";
        $productInfo = null;


        //Select句を作成する
        $sql = $this->selectCreate($column);

        //FROM句を作成する
        $sql = $sql. $this->fromCreate("PRODUCT");

        //WHERE句を作成する
        $sql = $sql. $this->whereCreateProduct($conditions);

        //ORDER BY句を追加する
        //製品長を630 570 520 の順番に並べ替える。未記入（0）の場合は一番最後に持ってくる
        $sql = $sql." ORDER BY CASE WHEN (`PRODUCT_LENGTH` = 0) THEN 10 END,PRODUCT_LENGTH ASC";


        $productInfo = $Pdo-> dbSelect($sql);

        return $productInfo;
    }

    //製品情報をPRODUCT_IDを元にひとつだけ取り出す
    function productSearchOne($column , $conditions , $Pdo){
        $sql = "";
        $productInfo = null;


        //Select句を作成する
        $sql = $this->selectCreate($column);

        //FROM句を作成する
        $sql = $sql. $this->fromCreate("PRODUCT");

        //WHERE句を作成する
        $sql = $sql. $this->whereCreateProduct($conditions);


        $productInfo = $Pdo-> dbSelect($sql);

        return $productInfo;
    }

    //資材情報を更新する
    function materialUpdate($keyNo , $value , $Pdo){

        $sql = "UPDATE MATERIAL ";

        //set句を生成する
        $sql = $sql.$this->setCreate($value);

        //WHERE句を作成する
        $sql = $sql."WHERE MATERIAL_ID = ".$keyNo;

        //update分を実行する。成功すればtrue 失敗すればfalse　を返す
        return $Pdo->dbDataOperation($sql);
    }

    //比較用に資材IDをもとに資材情報を取り出す
    function materialIdSearch($select, $where , $Pdo){
        $sql = "";
        $materialInfo = null;


        //Select句を作成する
        $sql = $this->selectCreate($select);

        //FROM句を作成する
        $sql = $sql. $this->fromCreate("MATERIAL");

        //WHERE句を作成する
        $sql =$sql. $this->whereComparisonId($where);


        $materialInfo = $Pdo-> dbSelect($sql);

        return $materialInfo;

    }

    /*
     * 「資材」「製品」登録する際、資材番号と資材名がすでに登録されているかどうかを調べる
     * カウント関数を使う
     */
    function uniqueDBCheck($tableName , $where , $Pdo){

        $sql = "SELECT COUNT( * ) AS NUM ";
        $Count = null;

        //FROM句を作成する
        $sql = $sql. $this->fromCreate($tableName);

        //WHERE句を作成する
        $sql =$sql. $this->whereComparison($tableName,$where);

        $Count = $Pdo-> dbSelect($sql);

        return $Count;

    }

    /*MATERIAL_TYPEのデータを取得して返す
     *
     */
    function materialTypeGet($machineId,$productId , $Pdo){

        /*$sql = "SELECT PDT.MATERIAL_TYPE_ID , MT.MATERIAL_TYPE_NAME ".
               "FROM PDTMATERIAL PDT JOIN MATERIAL_TYPE MT ON PDT.MATERIAL_TYPE_ID = MT.MATERIAL_TYPE_ID ".
               "WHERE PDT.PRODUCT_ID = ".$productId." ".
               "AND PDT.MACHINE_ID = ".$machineId." ".
               "GROUP BY PDT.MATERIAL_TYPE_ID ".
               "ORDER BY MT.TYPE_ORDER";*/

        $sql = "SELECT MATERIAL_TYPE_ID , MATERIAL_TYPE_NAME ".
               "FROM MATERIAL_TYPE ".
               "WHERE MACHINE_ID = ".$machineId." ".
               "ORDER BY TYPE_ORDER";



        /*$sql = "SELECT MATERIAL_TYPE_ID , MATERIAL_TYPE_NAME FROM MATERIAL_TYPE WHERE STAT = 1 AND MACHINE_ID = ".$machineId
               ." ORDER BY TYPE_ORDER ASC";*/

        $typeInfo = $Pdo->dbSelect($sql);

        return $typeInfo;

    }

    function materialTypeGetPast($productId,$productDate,$tyoku,$Pdo){
        //
        $sql = "SELECT MATERIAL_TYPE_ID , MATERIAL_TYPE_NAME ".
               "FROM ((PDTMATERIAL PDT JOIN MATERIAL_TYPE MT ON PDT.MATERIAL_TYPE_ID = MT.MATERIAL_TYPE_ID) JOIN MATERIAL ML ON PDT.MATERIAL_ID = ML.MATERIAL_ID) ".
               "WHERE PRODUCT_ID = ".$productId." ".
               "AND PRODUCT_DATE = ".$productDate." ".
               "AND TYOKU = ".$tyoku." ".
               "ORDER BY ORDERS ASC";

    }

    //製品に資材を登録する画面（登録）用のSQL
    function pdtmaterialInfoGet($productId , $Pdo){

        $sql = "SELECT PDT.PRODUCT_ID ,PDT.MATERIAL_ID, PDT.MACHINE_ID, MT.MATERIAL_TYPE_NAME, MT.MATERIAL_TYPE_ID,ML.MATERIAL_NAME ".
               "FROM ((PDTMATERIAL PDT JOIN MATERIAL_TYPE MT ON PDT.MATERIAL_TYPE_ID = MT.MATERIAL_TYPE_ID) JOIN MATERIAL ML ON PDT.MATERIAL_ID = ML.MATERIAL_ID) ".
               "WHERE PDT.PRODUCT_ID = ".$productId." ".
               "ORDER BY PDT.PDTMATERIAL_ORDER ASC ";

        $pdtInfo = $Pdo->dbSelect($sql);

        return $pdtInfo;
    }

    //製品資材テーブルの件数を取得する
    function pdtmaterialCountGet($productId , $Pdo){

        $sql = "SELECT COUNT(*) FROM PDTMATERIAL WHERE PRODUCT_ID = ".$productId;

        $pdtInfo = $Pdo->dbSelect($sql);

        return $pdtInfo[0]["COUNT(*)"];

    }

    //製品資材テーブル行をinsertする
    function pdtmaterialInsert($productId, $machineId, $materialTypeId, $materialId, $order, $Pdo){

        $sql = "INSERT INTO PDTMATERIAL(INSERT_DATE , UPDATE_DATE , STAT , PRODUCT_ID , MATERIAL_ID , MATERIAL_TYPE_ID , MACHINE_ID , PDTMATERIAL_ORDER) ".
               "VALUE (NOW(),".
               "NOW(), ".
               "1, ".
               $productId.", ".
               $materialId[$order].", ".
               $materialTypeId[$order].", ".
               $machineId.", ".
               $order.")";

        return $Pdo-> dbDataOperation($sql);

    }

    //製品資材テーブルの行をupdateする
    function pdtmaterialUpdate($productId, $machineId, $materialTypeId, $materialId, $order, $Pdo){

        $sql = "UPDATE PDTMATERIAL SET ".
               "UPDATE_DATE = NOW(),".
               "MATERIAL_ID = ".$materialId[$order].", ".
               "MATERIAL_TYPE_ID = ".$materialTypeId[$order]." ".
               "WHERE STAT =  1 ".
               "AND PRODUCT_ID = ".$productId." ".
               "AND MACHINE_ID = ".$machineId." ".
               "AND PDTMATERIAL_ORDER = ".$order;

        return $Pdo-> dbDataOperation($sql);

    }

    //製品資材情報を削除する
    function pdtmaterialDelete($productId, $machineId ,  $order, $Pdo){

        $sql = "DELETE FROM PDTMATERIAL ".
               "WHERE PRODUCT_ID = ".$productId." ".
               "AND MACHINE_ID = ".$machineId." ".
               "AND PDTMATERIAL_ORDER = ".$order;


        return $Pdo-> dbDataOperation($sql);

    }

    //今直がA直の場合、前回の生産日のC直の日付を取得する（現在切替は考慮指定ない）productDate＝今直の製造日
    function zentyokuDateGet($productDate,$productId, $Pdo) {

        $sql = "SELECT MAX(PRODUCT_DATE) AS ZENDATE ".
            "FROM SITEREMAINING ".
            "WHERE ".$productDate." > PRODUCT_DATE ".
            "AND PRODUCT_ID = ".$productId." ".
            "GROUP BY  PRODUCT_ID";

        $dbinfo = $Pdo->dbSelect($sql);

        $zentyokuDate = $dbinfo[0]["ZENDATE"];

        return $zentyokuDate;


    }

    //現場残を取得する。製品IDと製造日、直を指定する
    function tyokuSiteremainingGet($productId,$productDate,$tyoku,$materialTypeInfo,$Pdo){

        $sql = "SELECT SIT.SITEREMAINING ".
               "FROM ((SITEREMAINING SIT JOIN PDTMATERIAL PDT ON SIT.MATERIAL_ID = PDT.MATERIAL_ID)JOIN MATERIAL_TYPE MT ON PDT.MATERIAL_TYPE_ID = MT.MATERIAL_TYPE_ID) JOIN MATERIAL ML ON PDT.MATERIAL_ID = ML.MATERIAL_ID ".
               "WHERE SIT.PRODUCT_ID = ".$productId." ".
               "AND SIT.PRODUCT_DATE = ".$productDate." ".
               "AND SIT.TYOKU = ".$tyoku." ".
               "ORDER BY MT.TYPE_ORDER ASC,ML.MATERIAL_ORDER ASC";


        $tyokuSiteInfo = $Pdo->dbSelect($sql);

        //dbに記録がなければ全ての残本数を0にする
        if(count($tyokuSiteInfo) == 0) {

            for($i=0;$i<count($materialTypeInfo);$i++){
               $tyokuSiteInfo[$i]["SITEREMAINING"] = 0;
               $tyokuSiteInfo[$i]["MATERIAL_TYPE_ID"] = $materialTypeInfo[$i]["MATERIAL_TYPE_ID"];
               $tyokuSiteInfo[$i]["MATERIAL_ID"]      = $materialTypeInfo[$i]["MATERIAL_ID"];
            }
        }

        return $tyokuSiteInfo;

    }

    //過去の現場残を取得する。製品IDと製造日、直を指定する
    function tyokuSiteremainingGetPast($productId,$productDate,$tyoku,$Pdo){

        $sql = "SELECT SIT.SITEREMAINING ,SIT.MATERIAL_ID ,SIT.MATERIAL_TYPE_ID ,MT.MATERIAL_TYPE_NAME ,ML.MATERIAL_NAME ".
            "FROM (SITEREMAINING SIT JOIN MATERIAL_TYPE MT ON SIT.MATERIAL_TYPE_ID = MT.MATERIAL_TYPE_ID) JOIN MATERIAL ML ON SIT.MATERIAL_ID = ML.MATERIAL_ID ".
            "WHERE SIT.PRODUCT_ID = ".$productId." ".
            "AND SIT.PRODUCT_DATE = ".$productDate." ".
            "AND SIT.TYOKU = ".$tyoku." ".
            "ORDER BY SIT.ORDERS ASC";


        $tyokuSiteInfo = $Pdo->dbSelect($sql);

        return $tyokuSiteInfo;

    }

    //荷揚げ数を取得する。製品IDと製造日、直を指定する
    function tyokuNiageGet($productId, $productDate, $tyoku,$materialTypeInfo,$Pdo){

        $sql = "SELECT AD.ADD_MATERIAL_SPOT ".
               "FROM ((ADD_MATERIAL_SPOT AD JOIN PDTMATERIAL PDT ON AD.MATERIAL_ID = PDT.MATERIAL_ID)JOIN MATERIAL_TYPE MT ON PDT.MATERIAL_TYPE_ID = MT.MATERIAL_TYPE_ID) JOIN MATERIAL ML ON PDT.MATERIAL_ID = ML.MATERIAL_ID ".
               "WHERE AD.PRODUCT_ID = ".$productId." ".
               "AND AD.PRODUCT_DATE = ".$productDate." ".
               "AND AD.TYOKU = ".$tyoku." ".
               "ORDER BY MT.TYPE_ORDER ASC,ML.MATERIAL_ORDER ASC";

        $niageInfo = $Pdo->dbSelect($sql);

        //dbに記録がなければ全ての残本数を0にする
        if(count($niageInfo) == 0) {

            for($i=0;$i<count($materialTypeInfo);$i++){
                $niageInfo[$i]["ADD_MATERIAL_SPOT"] = 0;
            }
        }

        return $niageInfo;
    }

    //過去の荷揚げ数を取得する。製品IDと製造日、直を指定する
    function tyokuNiageGetPast($productId,$productDate,$tyoku,$Pdo){

        $sql = "SELECT ADD.ADD_MATERIAL_SPOT ,ADD.MATERIAL_ID ,ADD.MATERIAL_TYPE_ID ,MT.MATERIAL_TYPE_NAME ,ML.MATERIAL_NAME ".
            "FROM (ADD_MATERIAL_SPOT ADD JOIN MATERIAL_TYPE MT ON ADD.MATERIAL_TYPE_ID = MT.MATERIAL_TYPE_ID) JOIN MATERIAL ML ON SIT.MATERIAL_ID = ML.MATERIAL_ID ".
            "WHERE ADD.PRODUCT_ID = ".$productId." ".
            "AND ADD.PRODUCT_DATE = ".$productDate." ".
            "AND ADD.TYOKU = ".$tyoku." ".
            "ORDER BY ADD.ORDERS ASC";


        $tyokuNiageInfo = $Pdo->dbSelect($sql);

        return $tyokuNiageInfo;

    }

    //荷下げ数を取得する。製品IDと製造日、直を指定する
    function tyokuNisageGet($productId, $productDate, $tyoku,$materialTypeInfo,$Pdo){

        $sql = "SELECT SUB.SUBTRACTION ".
            "FROM ((SUBTRACTION SUB JOIN PDTMATERIAL PDT ON SUB.MATERIAL_ID = PDT.MATERIAL_ID)JOIN MATERIAL_TYPE MT ON PDT.MATERIAL_TYPE_ID = MT.MATERIAL_TYPE_ID) JOIN MATERIAL ML ON PDT.MATERIAL_ID = ML.MATERIAL_ID ".
            "WHERE SUB.PRODUCT_ID = ".$productId." ".
            "AND SUB.PRODUCT_DATE = ".$productDate." ".
            "AND SUB.TYOKU = ".$tyoku." ".
            "ORDER BY MT.TYPE_ORDER ASC,ML.MATERIAL_ORDER ASC";

        $nisageInfo = $Pdo->dbSelect($sql);

        //dbに記録がなければ全ての残本数を0にする
        if(count($nisageInfo) == 0) {

            for($i=0;$i<count($materialTypeInfo);$i++){
                $nisageInfo[$i]["SUBTRACTION"] = 0;
            }
        }

        return $nisageInfo;

    }

    //過去の荷下げ数を取得する。製品IDと製造日、直を指定する
    function tyokuNisageGetPast($productId,$productDate,$tyoku,$Pdo){

        $sql = "SELECT SUB.SUBTRACTION ,SUB.MATERIAL_ID ,SUB.MATERIAL_TYPE_ID ,MT.MATERIAL_TYPE_NAME ,ML.MATERIAL_NAME ".
            "FROM (SUBTRACTION SUB JOIN MATERIAL_TYPE MT ON SUB.MATERIAL_TYPE_ID = MT.MATERIAL_TYPE_ID) JOIN MATERIAL ML ON SUB.MATERIAL_ID = ML.MATERIAL_ID ".
            "WHERE ADD.PRODUCT_ID = ".$productId." ".
            "AND SUB.PRODUCT_DATE = ".$productDate." ".
            "AND SUB.TYOKU = ".$tyoku." ".
            "ORDER BY SUB.ORDERS ASC";


        $tyokuNisageInfo = $Pdo->dbSelect($sql);

        return $tyokuNisageInfo;

    }

    //使用数を取得する。製品IDと製造日、直を指定する
    function tyokuUseGet($productId, $productDate, $tyoku,$materialTypeInfo,$Pdo){

        $sql = "SELECT US.USE_SPOT ".
            "FROM ((USE_SPOT US JOIN PDTMATERIAL PDT ON US.MATERIAL_ID = PDT.MATERIAL_ID)JOIN MATERIAL_TYPE MT ON PDT.MATERIAL_TYPE_ID = MT.MATERIAL_TYPE_ID) JOIN MATERIAL ML ON PDT.MATERIAL_ID = ML.MATERIAL_ID ".
            "WHERE US.PRODUCT_ID = ".$productId." ".
            "AND US.PRODUCT_DATE = ".$productDate." ".
            "AND US.TYOKU = ".$tyoku." ".
            "ORDER BY MT.TYPE_ORDER ASC,ML.MATERIAL_ORDER ASC";

        $useInfo = $Pdo->dbSelect($sql);

        //dbに記録がなければ全ての残本数を0にする
        if(count($useInfo) == 0) {

            for($i=0;$i<count($materialTypeInfo);$i++){
                $useInfo[$i]["USE_SPOT"] = 0;
            }
        }

        return $useInfo;
    }

    //過去の使用数を取得する。製品IDと製造日、直を指定する
    function tyokuUseGetPast($productId,$productDate,$tyoku,$Pdo){

        $sql = "SELECT US.USE_SPOT ,US.MATERIAL_ID ,US.MATERIAL_TYPE_ID ,MT.MATERIAL_TYPE_NAME ,ML.MATERIAL_NAME ".
            "FROM (USE_SPOT US JOIN MATERIAL_TYPE MT ON US.MATERIAL_TYPE_ID = MT.MATERIAL_TYPE_ID) JOIN MATERIAL ML ON US.MATERIAL_ID = ML.MATERIAL_ID ".
            "WHERE US.PRODUCT_ID = ".$productId." ".
            "AND US.PRODUCT_DATE = ".$productDate." ".
            "AND US.TYOKU = ".$tyoku." ".
            "ORDER BY US.ORDERS ASC";


        $tyokuUseInfo = $Pdo->dbSelect($sql);

        return $tyokuUseInfo;

    }



    //現場残、使用数、追加資材（荷上げ）、荷下げテーブルの件数を取得する
    function quantitiyCountGet($productId , $productDate , $tyoku , $tablename , $Pdo){

        $table = "";

        switch ($tablename){
            case "zentyoku":
                $table = "SITEREMAINING ";
                break;
            case "niage":
                $table = "ADD_MATERIAL_SPOT ";
                break;
            case "nisage":
                $table = "SUBTRACTION ";
                break;
            case "genbazan":
                $table = "SITEREMAINING ";
                break;
            case "siyosu":
                $table = "USE_SPOT ";
                break;
        }

        $sql = "SELECT COUNT(*) AS COUNT ".
               "FROM ".$table.
               "WHERE PRODUCT_ID = ".$productId." ".
               "AND PRODUCT_DATE = ".$productDate." ".
               "AND TYOKU = ".$tyoku;

        $countInfo = $Pdo->dbSelect($sql);
        $count = $countInfo[0]["COUNT"];

        return $count;
    }

    //現場残、使用数、追加資材（荷上げ）、荷下げテーブルの更新（update）
    function quantitiyUpdate($productId,$materialId,$materialTypeId,$machineId,$productDate,$tyoku,$Pdo){

    }

    //現場残、使用数、追加資材（荷上げ）、荷下げテーブルの更新（insert）
    function quantitiyInsert($productId,$materialId,$materialTypeId,$machineId,$productDate,$tyoku,$Pdo){

    }

}
?>