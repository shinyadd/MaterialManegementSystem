<?php
require_once 'capsule/machine.php';
//セッションでログイン情報を確認
session_start();
$machine = unserialize($_SESSION['user']);

//セッションがnullならログインページに飛ばす

?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>資材登録</title>
  <script src="js/jquery-3.4.1.min.js" type="text/javascript"></script>
<script src="js/bootstrap.bundle.js" type="text/javascript"></script>

<script src="js/inputMaterialCheck.js" type="text/javascript" charset="utf-8"></script>
<LINK rel="stylesheet" href="css/maindesign.css" type="text/css">
<link rel="stylesheet" href="css/bootstrap.min.css">
<script type="text/javascript">

	//資材番号、資材名、長さをチェックする
	function inputCheck(){
		var materialNo = document.register.materialNo.value;
		var materialName = document.register.materialName.value;
		var length = document.register.length.value;
		var inputCheck = false;

		var flg  = materialNoCheck(materialNo,10);
		var flg2 = materialNameCheck(materialName,40);
		var flg3 = materialLengthCheck(length , 5);

		//資材番号をチェックする
		if(flg == false){
			alert("「資材番号」は半角英数、10文字以内で入力してください");
		}

		//資材名をチェックする
		if(flg2 == false){
			alert("「資材名」は入力必須項目です\n「資材名」は半角英数、40文字以内で入力してください");
		}

		//長さをチェックする
		if(flg3 == false){
			alert("「長さ」は半角英数、5桁以内で入力してください");
		}

		if(flg && flg2 && flg3){
			inputCheck = true;
		}

		return inputCheck;

	}


	function registerhMordalBotton(){
  		//入力された情報をチェックする
  		if(inputCheck()){
	    	//submitの送信先を設定
	    	document.register.action="confirmation.php";
	    	//submit()でフォームの内容を送信
			document.register.submit();
  		}
    }

	//「登録」ボタンを押下後に起動
	function registerBotton(){
		//入力された情報をチェックする
  		if(inputCheck()){
  			//資材番号、資材名がユニークかどうかをDB検索をして調べる
  			//二つともユニークならモーダルを表示する
  	  		registerUniqueCheck();
  		}
	}

	//資材番号、資材名がユニークかどうかをDB検索をして調べる
	function registerUniqueCheck(){

		//HTMLから受け取るデータです。
		//svar data = {request : $('#request').val()};

		var checkMaterialInfo = new Array();

		var registerMaterialInfo = resgisterMaterialInfoGet();


		$.when(
			//資材IDを元に検索を実行し、DBに保存されている値を取得
			$.ajax({
		    //POST通信
			type: "POST",
			dataType:"json",
			//ここでデータの送信先URLを指定します。
			url: 'material/search/search_material.php',
			data: {                //呼び出し先のパラメータ
				searchId     : "unique",
				materialNo   : document.register.materialNo.value,
	        	materialName : document.register.materialName.value
				},
			//処理が成功したら
			success : function(data){

				//返ってきた情報をdbMaterialInfoに格納していく

				checkMaterialInfo["materialNumber"] = data["materialNo"];
				checkMaterialInfo["materialName"] = data["materialName"];

			},
			//処理がエラーであれば
			error : function(XMLHttpRequest, textStatus, errorThrown , data) {
			    alert('error!!!');

			  　　console.log("XMLHttpRequest : " + XMLHttpRequest.status);
			  	 console.log("XMLHttpRequest : " + XMLHttpRequest.responseText);
			  　　console.log("textStatus     : " + textStatus);
			  　　console.log("errorThrown    : " + errorThrown.message);
			  	 console.log("data   : " + data);

			}
			})

		).done(function(){
			//入力された資材番号がユニークでなければアラートを表示
	  		if(checkMaterialInfo["materialNumber"] != "nothing" && checkMaterialInfo["materialNumber"] != 0){

				alert("入力された資材番号はすでに登録されています");
	  		}

	  		//入力された資材名がユニークでなければアラートを表示
	  		if(checkMaterialInfo["materialName"] != 0){

				alert("入力された資材名はすでに登録されています");
	  		}

			//DB検索の結果、資材番号がユニーク（0）または未入力（nothing）の場合
	  		if(checkMaterialInfo["materialNumber"] == 0 || checkMaterialInfo["materialNumber"] == "nothing"){
				//資材名がユニークの場合
		  		if(checkMaterialInfo["materialName"] == 0){

		  			//確認用のテーブルを生成する
					registerConfirmHtmlCreate(registerMaterialInfo);

		  			//モーダルを表示する
					$('#registerModalCenter').modal("show")
		  		}
	  		}
		})
	}

	//資材情報更新画面に入力された情報を取得
	function resgisterMaterialInfoGet(){
		//資材情報更新画面に入力された情報を配列に格納
		var registerMaterialInfo = new Array();

		registerMaterialInfo["materialNo"]   = document.register.materialNo.value;
		registerMaterialInfo["materialName"] = document.register.materialName.value;
		registerMaterialInfo["lngth"]       = document.register.lngth.value;
		registerMaterialInfo["unit"]         = document.register.unit.value;

		return registerMaterialInfo;
	}

	//モーダルの変更内容確認のテーブルの要素を作成する
	function registerConfirmHtmlCreate(registerMaterial){

		var tableElement = '<table class="table table-borderless" border="1">';

		//資材番号、長さは未入力であれば「-」を表示する
		if(registerMaterial["materialNo"] == null || registerMaterial["materialNo"] == "") registerMaterial["materialNo"] = "未記入";
		if(registerMaterial["lngth"] == null || registerMaterial["lngth"] == "") registerMaterial["lngth"] = "未記入";

		//materialNoの要素追加
		if(registerMaterial["materialNo"] == "未記入"){
			tableElement += "<tr><th class='th-modal' bgcolor='orange'>資材番号</th></tr>"
				+"<tr><td class='td-modal-db'>"+registerMaterial["materialNo"]+"</td></tr>";
		}else{
			tableElement += "<tr><th class='th-modal' bgcolor='orange'>資材番号</th></tr>"
						+"<tr><td class='td-modal-up'>"+registerMaterial["materialNo"]+"</td></tr>";
		}

		//materialNameの要素追加
		tableElement += "<tr><th class='th-modal' bgcolor='orange'>資材名</th></tr>"
						+"<tr><td class='td-modal-up'>"+registerMaterial["materialName"]+"</td></tr>";

		//lengthのdbの値と入力された値に違いがあれば要素追加
		if(registerMaterial["lngth"] == "未記入"){
			tableElement += "<tr><th class='th-modal' bgcolor='orange'>長さ</th></tr>"
				+"<tr><td class='td-modal-db'>"+registerMaterial["lngth"]+"</td></tr>";
		}else{
			tableElement += "<tr><th class='th-modal' bgcolor='orange'>長さ</th></tr>"
						+"<tr><td class='td-modal-up'>"+registerMaterial["lngth"]+"</td></tr>";
		}


		//unitのdbの値と入力された値に違いがあれば要素追加
		tableElement += "<tr><th class='th-modal' bgcolor='orange'>単位</th></tr>"
						+"<tr><td class='td-modal-up'>"+unitChangerReverse(registerMaterial["unit"])+"</td></tr>";

		tableElement += "</table>";

		// tableのタグが反映されない為jQueryで実装
		$(function () {
			$("#register_Confirm").html(tableElement);
		});
	}

	//資材を登録する
	function registerModalButton(){

		//資材情報を登録する
		registerClient();

		//入力ボックスに入力されている値をリセットする
		registerInputBoxReset();

		//モーダルを閉じる
		$('#registerModalCenter').modal("hide")
	}


	function registerClient(){
		//HTMLから受け取るデータです。
		var data = {request : $('#request').val()};
		//ここからajaxの処理です。
	    $.ajax({
	    	//POST通信
	        type: "POST",
			//ここでデータの送信先URLを指定します。
			url: 'material/register/register_material.php',
			data: {
				//呼び出し先のパラメータ
	        	materialNo   : document.register.materialNo.value,
	        	materialName : document.register.materialName.value,
	        	length       : document.register.lngth.value,
	        	unit         : document.register.unit.value

	        },
			//処理が成功したら
			success : function() {
				alert("更新されました。");
			},
			//処理がエラーであれば
			error : function() {
				alert('通信エラー(registerClient())');
			}
		});
		//submitによる画面リロードを防いでいます。
		return false;
	}

	//入力ボックスに入力されている値をリセットする
	function registerInputBoxReset(){

		document.register.materialNo.value = "";
		document.register.materialName.value = "";
		document.register.lngth.value = "";
		document.register.unit.value = 0;

	}

	//単位の値を数値から文字に変換する
	function unitChangerReverse(unit){

		var unitChange = "";

		if(unit == 0){
			unitChange = "ー";
		}else if(unit == 1){
			unitChange = "枚";
		}else if(unit == 2){
            unitChange = "巻";
        }
		return unitChange;
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

	/* **********更新確認モーダルのテーブルのセル内の設定*************** */
	.th-modal {
		text-align: center;
		font-size: medium;
	}

	.td-modal-db{
		text-align: center;
		font-size: medium;
	}

	.td-modal-up{
		text-align: center;
		font-size: large;
		font-weight: bold;
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
		<a href="quantity_used.php"             class="list-group-item list-group-item-action">使用した資材数を入力する</a>
		<a href="product_material_register.php" class="list-group-item list-group-item-action">製品に資材を登録・変更する</a>
		<a href="product_register.php"          class="list-group-item list-group-item-action">製品を登録</a>
		<a href="product_edit.php"              class="list-group-item list-group-item-action">製品を検索・編集する</a>
		<a href="material_registr.php"          class="list-group-item active">資材を登録</a>
		<a href="material_edit.php"             class="list-group-item list-group-item-action">資材を検索・編集する</a>
		<a href="#"                             class="list-group-item list-group-item-action">ライン別資材検索</a>
	</div><!-- list-group list-group-flush -->
</DIV>

<DIV id="Main_area" align="center">
<main class="pt-5 mx-lg-5">
<h1>資材登録画面</h1>
	<h2><?php echo $machine->getName(); ?></h2>
	<h2>ー資材を登録するー</h2>
	<br>
  <!-- 資材検索モード　ここから -->
  <div id="search" >
  <div class="card">
    <!-- カードのヘッダー -->
    <div class="card-header">
      <!-- ピル型のナビゲーション：card-header-pills -->
      <ul class="nav nav-pills card-header-pills">
        <li class="nav-item"> <spnn class="nav-link active" href="#">資材を登録する</span> </li>
      </ul>
    </div><!-- card-header -->
    <!-- カードの本文 -->
    <div class="card-body">
    <div class="infobox">
    <form name="register" id="search" method="post">
    <input type="hidden" id="materialId_search" value="" />
    <table class="info" width="900px">
  	<tr>
  		<th class="td-css">資材番号</th>
  		<th class="td-css">資材名　※入力必須項目です</th>
  		<th class="td-css">長さ</th>
  		<th class="td-css">単位</th>
  		<th class="th-css13" width="100" rowspan="2">
  			<div class="btn-square-register" onclick="registerBotton()">登録</div><!-- btn-square -->
		</th>
  	</tr>
  	<tr>
  		<td class="td-css"><input type = "text" size="17" name="materialNo" id="materialNo_search" value="" /></td>
  		<td class="td-css"><input type = "text" size="40" name="materialName"  value="" /></td>
  		<td class="td-css"><input type = "text" size="15" name="lngth" value="" /></td>
  		<td class="td-css">
  			<select name="unit">
				<option value="0" selected>−</option>
				<option value="1">枚</option>
				<option value="2">巻</option>
			</select>
		</td>
  	</tr>
  	</table>

  	</form>
  	</div><!-- infobox -->
  	</div><!-- card-body -->
  </div><!-- card -->
  </div><!-- search -->
</main>
</DIV>

<!-- モーダル部分始まり -->
<div class="modal fade" id="registerModalCenter" tabindex="-1" role="dialog" aria-labelledby="registerModalCenterTitle" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalCenterTitle">資材情報更新の登録</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
			</div>
			<div class="modal-body">
				以下の内容で資材情報を更新します。よろしいですか？
				<br>
				<br>
				<div id="register_Confirm"></div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" onclick="registerModalButton();">保存する</button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal">閉じる</button>

			</div>
		</div>
	</div>
</div>
<!-- モーダル部分終わり -->
</BODY>
</html>
