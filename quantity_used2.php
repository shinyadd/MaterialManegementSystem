<?php
require_once 'capsule/machine.php';
require_once 'tools/toolbox.php';
require_once 'tools/sqlCreate.php';
require_once 'tools/dbAccess.php';


//セッションでログイン情報を確認
session_start();
$machine = unserialize($_SESSION['user']);

//セッション情報がなければログインページへ飛ばす
if($machine == false){
    header('Location: login.php');
}

//セッションがnullならログインページに飛ばす

//toolboxをインスタンス化
$tool = new toolbox();

//sqlクラスをインスタンス化
$sql = new sqlCreate();

//dbAccessをインスタンス化
$Pdo = new dbAccsess();

//製品情報を設置する
$productInfo = array();

$productInfo["productId"]   = $machine->getMaking();

//製品情報をDBから取得する
$select = array();
$select[0] = "PRODUCT_ID";
$select[1] = "PRODUCT_NUMBER";
$select[2] = "PRODUCT_NAME";
$select[3] = "PRODUCT_LENGTH";

$productDbInfo = $sql->productSearchOne($select, $productInfo, $Pdo);

$productInfo["productNo"]   = $productDbInfo[0]["PRODUCT_NUMBER"];
$productInfo["productName"] = $productDbInfo[0]["PRODUCT_NAME"];
$productInfo["length"]       = $productDbInfo[0]["PRODUCT_LENGTH"];

//製造日を取得する
$productDate = $_POST["productdate"];

//直を取得する
$tyoku = $_POST["tyoku"];

//前直の日付を設定する
$zentyokuDate = null;

//前直の番号を取得する
$zentyoku = $tool->zentyokuGet($tyoku);

//前直の現場本数
$zentyokuSite = null;

//productdateが指定されていない場合、現在の時刻からページを作成する（通常に開いた場合）
if($productDate == "" || $productDate == null ){
    //タイムゾーンの設定
    date_default_timezone_set('Asia/Tokyo');

    //現在の日付を取得
    $year  = (int)date("Y");
    $month = (int)date("m");
    $day   = (int)date("d");
    //現在の時間を取得
    $hour = (int)date("H");

    //C直の場合、日付を一日戻す
    if($tool->hourCheck($hour)){
        $day = $day-1;
    }

    //現在の日付を作成する
    $productDate = $year.$month.$day;

    //現在、生産している製品の資材種類の名前を取得する
    $materialType = $sql->pdtmaterialInfoGet($machine->getMaking(),$Pdo);

    //ページを開いた時、編集したいであろう直の番号を取得する
    $tyoku = $tool->tyokuGet($hour);

    //前直の番号を取得する
    $zentyoku = $tool->zentyokuGet($tyoku);

    //今直の現場本数を取得する
    $kontyokuSite = $sql->tyokuSiteremainingGet($machine->getMaking(), $productDate, $tyoku,$materialType,$Pdo);

    //前直の日付を取得する
    $zentyokuDate = $tool->zentyokuDateGet($tyoku, $productDate, $machine->getMaking(), $sql, $Pdo);

    if(isset($zentyokuDate)){
        $zentyokuSite = $sql->tyokuSiteremainingGet($machine->getMaking(), $zentyokuDate, $zentyoku,$materialType,$Pdo);
    }else{
        //dbに記録がなければ全ての残本数を0にする
        if(count($zentyokuSite) == 0) {

            for($i=0;$i<count($materialType);$i++){
                $zentyokuSite[$i]["SITEREMAINING"] = 0;
            }
        }
    }

    //今直の荷揚げ数を取得する
    $niage = $sql->tyokuNiageGet($machine->getMaking(), $productDate, $tyoku,$materialType,$Pdo);

    //今直の荷下げ数を取得する
    $nisage = $sql->tyokuNisageGet($machine->getMaking(), $productDate, $tyoku,$materialType,$Pdo);

    //今直の使用数を取得する
    $use = $sql->tyokuUseGet($machine->getMaking(), $productDate, $tyoku, $materialType, $Pdo);

}



if(isset($zentyokuDate)){
    $zentyokuSite = $sql->tyokuSiteremainingGet($machine->getMaking(), $zentyokuDate, $zentyoku,$materialType,$Pdo);
}else{
    //dbに記録がなければ全ての残本数を0にする
    if(count($zentyokuSite) == 0) {

        for($i=0;$i<count($materialType);$i++){
            $zentyokuSite[$i]["SITEREMAINING"] = 0;
        }
    }
}

//今直の荷揚げ数を取得する
$niage = $sql->tyokuNiageGet($machine->getMaking(), $productDate, $tyoku,$materialType,$Pdo);

//今直の荷下げ数を取得する
$nisage = $sql->tyokuNisageGet($machine->getMaking(), $productDate, $tyoku,$materialType,$Pdo);

//今直の使用数を取得する
$use = $sql->tyokuUseGet($machine->getMaking(), $productDate, $tyoku, $materialType, $Pdo);


//一番左の番号を表示するための変数
$count = 1;
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>使用した資材数を入力する</title>
  <script src="js/jquery-3.4.1.min.js" type="text/javascript"></script>
<script src="js/bootstrap.bundle.js" type="text/javascript"></script>

<script src="js/inputProductCheck.js" type="text/javascript" charset="utf-8"></script>
<LINK rel="stylesheet" href="css/maindesign.css" type="text/css">
<link rel="stylesheet" href="css/bootstrap.min.css">
<script type="text/javascript">
var today = <?php echo $productDate ?>;
var zenday = <?php echo $zentyokuDate ?>;
var tyoku = <?php echo $tyoku ?>;

//「保存」ボタン押下後の処理
function saveBotton(){


	//配列を宣言、取得
	var zentyoku = koumokuGet("zen_zan");
	var niage    = koumokuGet("nige");
	var nisage   = koumokuGet("nisage");
	var genbazan = koumokuGet("kon_zan");
	var siyosu   = koumokuGet("use");

	//ここからajaxの処理です。
    $.ajax({
    	//POST通信
        type: "POST",
		//ここでデータの送信先URLを指定します。
		url: 'quantity/register_quantity.php',
		data: {                //呼び出し先のパラメータ
			searchId : "register",
			zentyoku : zentyoku,
        	niage    : niage,
        	nisage   : nisage,
        	genbazan : genbazan,
        	siyosu   : siyosu,
        	today    : today,
        	zenday   : zenday,
        	tyoku    : tyoku

        },
		//処理が成功したら
		success : function(data, dataType) {
			alert("保存しました");
		},
		//処理がエラーであれば
		error : function() {
			alert('通信エラー(saveButton()');
		}
	});
	//submitによる画面リロードを防いでいます。
	return false;
}


function koumokuGet(name){

	var koumoku = new Array();

	var count = document.getElementById("count").value;

	//nameの値を取得する
	for(i=0;i>count;i++){
		//数値を取得する
		var element = document.getElementById(name+count).value;
		//material_idを取得する
		var material_id = document.getElementById("material_id"+count).value;
		//materialtype_idを取得する
		var materialtype_id = document.getElementById("materialtype_id"+count).value;

		//空白の場合は0を代入する
		if(!nullCheck(element)) element = 0;

		//戻り値用変数koumokuに取得した数値を格納する
		koumoku[i][0] = element;
		//material_idを格納する
		koumoku[i][1] = material_id;
		//materialtype_idを格納する
		koumoku[i][2] = materialtype_id;

	}

	return koumoku;

}

</script>
 <style>
    input[type=text]{
        height:40px;
    }

    .btn-square {
        display: inline-block;
        padding: 0.5em 1em;
        text-decoration: none;
        background: #668ad8;/*ボタン色*/
        color: #FFF;
        border-bottom: solid 4px #627295;
        border-radius: 3px;
    }

    /* **********検索ボックスの「検索」ボタンの装飾*************** */
    .btn-square-register {
        display: inline-block;
        padding: 0.5em 1em;
        text-decoration: none;
        background: #668ad8;/*ボタン色*/
        color: #FFF;
        border-bottom: solid 4px #627295;
        border-radius: 3px;
    }

    .btn-square-register:active {
        /*ボタンを押したとき*/
        -webkit-transform: translateY(4px);
        transform: translateY(4px);/*下に動く*/
        border-bottom: none;/*線を消す*/
     }

     /* **********検索ボックスのテーブルの線の有無とセルの中身の位置*************** */
	/* すべての線を表示
	   セルの中身　中央寄せ*/
	.td-css {
    	border: 1px solid black;
    	text-align: center;
	}

	/* 検索ボタン、更新ボタンの周りの線を消す
	   上下左右すべての線を非表示
	   セルの中身　右寄せ*/
	.th-css13 {
    	border: none;
    	text-align: right;
	}

	/* **********検索結果のテーブルのセル内の設定*************** */
	.th-result {
		text-align: center;
	}

	.td-result{
		text-align: center;
	}

	input {
    text-align: center;
}
textarea {
    text-align: center;
}

  </style>
</head>
<BODY class="grey lighten-3">
<DIV id="Left_area" align="center" >

        <span class="dynawave"><img src="img/dynawave.jpg" class="img-fluid" alt=""></span>

    <br>
    <br>
    <br>
    ー資材管理システムー
    <br>
    <br>
    <br>
    <br>
    メニュー
    <br>
    <br>
 	<div class="list-group list-group-flush">
		<a href="top.php"                       class="list-group-item list-group-item-action">トップページ</a>
		<a href="quantity_used.php"             class="list-group-item active">使用した資材数を入力する</a>
		<a href="product_material_register.php" class="list-group-item list-group-item-action">製品に資材を登録・変更する</a>
		<a href="product_register.php"          class="list-group-item list-group-item-action">製品を登録</a>
		<a href="product_edit.php"              class="list-group-item list-group-item-action">製品を検索・編集する</a>
		<a href="material_registr.php"          class="list-group-item list-group-item-action">資材を登録</a>
		<a href="material_edit.php"             class="list-group-item list-group-item-action">資材を検索・編集する</a>
		<a href="#"                             class="list-group-item list-group-item-action">ライン別資材検索</a>
	</div><!-- list-group list-group-flush -->
</DIV>
<DIV id="Main_area" align="center">
<main class="pt-5 mx-lg-5">
<h1>資材管理システム</h1><br>
	<h2><?php echo $machine->getName(); ?></h2>
	<h2>-使用数入力画面-</h2>
	<br>
	<div class="card">
  		<div class="card-body">
  		<dib class="hyoji">
  			<table class="info" width="900px">
  				<tr>
  					<th class="td-css">製品番号</th>
  					<th class="td-css">製品名</th>
  					<th class="td-css">長さ</th>
  					<th class="th-css13" width="100" rowspan="2">
  					<div class="btn-square-register" onclick="saveBotton()">保存</div><!-- btn-square -->
					</th>
  				</tr>
  				<tr>
  					<td class="td-css"><?php echo $productInfo["productNo"] ?></td>
  					<td class="td-css"><?php echo $productInfo["productName"] ?></td>
  					<td class="td-css"><?php echo $tool->lengthChangerReverse($productInfo["length"]); ?></td>
  				</tr>
  			</table>
			<br>
  			<input type="button" value="◀︎">
  			<input type="text" name="year" size="4" value="<?php echo $year ?>"/>年
  			<?php $tool->selectMonthGet($month) ?>月
  			<?php $tool->selectdaysGet($day)?>
  			<?php $tool->selectTyokusGet($tyoku)?>
  			<input type="button" value="表示︎︎">
  			<input type="button" value="▶︎︎">
			<br>
			<br>
  			<table border width= "900px" id="quantity_used">
				<tr>
					<th></th>
					<th width="200px" class="th-result"></th>
					<th width="350px" class="th-result">資材名</th>
					<th width="55px" class="th-result">前直残</th>
					<th width="70px" class="th-result">荷上げ数</th>
					<th width="70px" class="th-result">荷下げ数</th>
					<th width="85px" class="th-result">現場残合計</th>
					<th width="55px" class="th-result">使用数</th>
				</tr>
				<?php for($i=0;$i<count($materialType);$i++){?>
				<tr>
					<td class="th-result"><?php  echo $count ?></td>
					<td class="th-result"><?php echo $materialType[$i]["MATERIAL_TYPE_NAME"]?></td>
					<td class="th-result"><?php echo $materialType[$i]["MATERIAL_NAME"]?></td>
					<td class="th-result"><input type= "text" id="zen_zan<?php echo $count ?>"  size="5" value="<?php echo $tool->zeroCheck($zentyokuSite[$i]["SITEREMAINING"])?>" /></td>
					<td class="th-result"><input type= "text" id="niage<?php echo $count ?>"   size="7" value="<?php echo $tool->zeroCheck($niage[$i]["ADD_MATERIAL_SPOT"])?>" /></td>
					<td class="th-result"><input type= "text" id="nisage<?php echo $count ?>"   size="7" value="<?php echo $tool->zeroCheck($nisage[$i]["SUBTRACTION"])?>" /></td>
					<td class="th-result"><input type= "text" id="kon_zan<?php echo $count ?>" size="9" value="<?php echo $tool->zeroCheck($kontyokuSite[$i]["SITEREMAINING"]) ?>"/></td>
					<td class="th-result"><input type= "text" id="use<?php echo $count ?>"     size="5" value="<?php echo $tool->zeroCheck($use[$i]["USE_SPOT"])?>"/></td>
					<input type="hidden" name="material_id<?php echo $count?>" value="<?php echo $materialType[$i]["MATERIAL_ID"]?>" />
					<input type="hidden" name="materialtype_id<?php echo $count?>" value="<?php echo $materialType[$i]["MATERIAL_TYPE_ID"]?>" />
				</tr>
				<?php $count++;
                     }?>
			</table>
			<input type="hidden" id="count" value="<?php echo $count-1 ?>">
			<input type="hidden" id="product_id" value="<?php echo $materialType[0]["PRODUCT_ID"]?>">
			<input type="hidden" id="machine_id" value="<?php echo $materialType[0]["MACHINE_ID"]?>">
		</div><!-- hyoji -->
  		</div><!-- card-body-->
  	</div><!-- card -->

</main>
</DIV>
</BODY>
</html>