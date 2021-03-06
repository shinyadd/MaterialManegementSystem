<?php
require_once 'tools/sqlCreate.php';
require_once 'tools/dbAccess.php';
require_once 'capsule/machine.php';

//セッションを開始
session_start();
//セッションでログイン情報を確認
session_start();
$machine = unserialize($_SESSION['user']);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>資材を編集する　-資材管理システム-</title>
<script src="js/jquery-3.4.1.min.js" type="text/javascript"></script>
<script src="js/bootstrap.bundle.js" type="text/javascript"></script>

<script src="js/inputMaterialCheck.js" type="text/javascript" charset="utf-8"></script>
<LINK rel="stylesheet" href="css/maindesign.css" type="text/css">
<link rel="stylesheet" href="css/bootstrap.min.css">

<script type="text/javascript">

	//検索カード内の資材番号、資材名、長さをチェックする
	//検索の場合、資材名は入力必須では無いので未記入でもOK
	function inputCheckSearch(){
		var materialNo   = document.search.materialNo.value;
		var materialName = document.search.materialName.value;
		var length       = document.search.length.value;
		var inputCheck = false;

		var flg  = materialNoCheck(materialNo,10);
		var flg2 = materialNameCheckSearch(materialName,40);
		var flg3 = lengthCheck(length , 5);

		//資材番号をチェックする
		if(flg == false){
			alert("「資材番号」は半角英数、10文字以内で入力してください");
		}

		//資材名をチェックする
		if(flg2 == false){
			alert("「資材名」は半角英数、40文字以内で入力してください");
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

	//更新カード内の資材番号、資材名、長さをチェックする
	function inputCheckUpdate(updateMaterialInfo){
		var materialNo   = updateMaterialInfo["materialNo"];
		var materialName = updateMaterialInfo["materialName"];
		var length       = updateMaterialInfo["length"];
		var inputCheck = false;

		var flg  = materialNoCheck(materialNo,10);
		var flg2 = materialNameCheck(materialName,40);
		var flg3 = lengthCheck(length , 5);

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


	//資材情報更新画面に入力された情報を取得
	function searchMaterialInfoGet(){
		//資材情報検索画面に入力された情報を配列に格納
		var searchMaterialInfo = new Array();

		searchMaterialInfo["materialId"]   = document.search.materialId.value;
		searchMaterialInfo["materialNo"]   = document.search.materialNo.value;
		searchMaterialInfo["materialName"] = document.search.materialName.value;
		searchMaterialInfo["length"]       = document.search.length.value;
		searchMaterialInfo["unit"]         = document.search.unit.value;

		return searchMaterialInfo;
	}

	//検索を実行して、結果を受け取る
	function searchClient(){
		//HTMLから受け取るデータです。
		var data = {request : $('#request').val()};
		//ここからajaxの処理です。
	    $.ajax({
	    	//POST通信
	        type: "POST",
			//ここでデータの送信先URLを指定します。
			url: 'material/search/search_material.php',
			data: {                //呼び出し先のパラメータ
				searchId     : "search",
				materialId   : document.search.materialId_search.value,
				materialNo   : document.search.materialNo.value,
	        	materialName : document.search.materialName.value,
	        	length       : document.search.length.value,
	        	unit         : document.search.unit.value
	        },
			//処理が成功したら
			success : function(data, dataType) {
				document.getElementById("setsumei").style.display ="none";
				document.getElementById("success").style.display = "block";
				//HTMLファイル内の該当箇所にレスポンスデータを追加します。
				$('#search_result').html(data);
			},
			//処理がエラーであれば
			error : function() {
				alert('通信エラーです');
			}
		});
		//submitによる画面リロードを防いでいます。
		return false;
	}

	//「検索」ボタン押下後、入力チェックを行い、trueなら検索を開始する。
	function searchBotton(){
  		//入力された情報をチェックする
  		//入力が正しければ検索を開始する
  		if(inputCheckSearch()){

  			//検索を実行
	    	searchClient();
  		}
    }



	//更新用に入力された資材情報とDBに保存されている資材情報を比較する
	//入力された情報が全て同じであればfalse , 差異があればtrue
	function dbMaterialInfoGet(){
		//HTMLから受け取るデータです。
		//var data = {request : $('#request').val()};

		//DBに保存されている資材情報を格納するため配列を宣言
		var dbMaterialInfo = new Array();

		//資材IDを元に検索を実行し、DBに保存されている値を取得
		$.ajax({
	    	//POST通信
	        type: "POST",
	        dataType:"json",
			//ここでデータの送信先URLを指定します。
			url: 'material/search/search_material.php',
			data: {                //呼び出し先のパラメータ
				searchId     : "comparison",
				materialId   : document.update.materialId.value
	        },
			//処理が成功したら
			success : function(data){
				alert(data);

				//返ってきた情報をdbMaterialInfoに格納していく
				dbMaterialInfo["materialId"] = data["materialId"];
				dbMaterialInfo["materialNo"] = data["materialNo"];
				dbMaterialInfo["materialNAme"] = data["materialName"];
				dbMaterialInfo["length"] = data["length"];
				dbMaterialInfo["unit"] = data["unit"];

			},
			//処理がエラーであれば
			error : function() {
				alert('通信エラー(dbMaterialInfoGet())');
			}
		});

		return dbMaterialInfo;
	}

	//「更新」ボタン押下後、入力チェックを行い、trueならモーダルを表示する。
	function updateBotton(){

		var updateMaterialInfo = updateMaterialInfoGet();

		//資材IDを取得する
		var materialId = document.update.materialId.value;

		//DBに保存されている資材情報を格納するため配列を宣言
		var dbMaterialInfo = new Array();


		$.when(
			//資材IDを元に検索を実行し、DBに保存されている値を取得
			$.ajax({
		    //POST通信
			type: "POST",
		    dataType:"json",
			//ここでデータの送信先URLを指定します。
			url: 'material/search/search_material.php',
			data: {                //呼び出し先のパラメータ
				searchId     : "comparison",
				materialId   : materialId
				},
			//処理が成功したら
			success : function(data){

				//返ってきた情報をdbMaterialInfoに格納していく
				dbMaterialInfo["materialId"] = data["materialId"];
				dbMaterialInfo["materialNo"] = data["materialNo"];
				dbMaterialInfo["materialName"] = data["materialName"];
				dbMaterialInfo["length"] = data["length"];
				dbMaterialInfo["unit"] = data["unit"];

			},
			//処理がエラーであれば
			error : function() {
				alert('通信エラー(dbMaterialInfoGet)');
			}
		})

		).done(function(){
			//入力された情報をチェックする。入力が正しければ更新を開始する
	  		if(inputCheckUpdate(updateMaterialInfo)){
	  			//入力された情報と保存されている情報を比較して、差異があれば表示する
				if(inputComparisonCheck(dbMaterialInfo,updateMaterialInfo)){

					//確認用のテーブルを生成する
					updateConfirmHtmlCreate(dbMaterialInfo,updateMaterialInfo);
					//モーダルを表示する
					$('#exampleModalCenter').modal("show")

				}else{
					alert("値が変更されていません");
				}
	  		}
		})
	}

	//資材情報更新画面に入力された情報を取得
	function updateMaterialInfoGet(){
		//資材情報更新画面に入力された情報を配列に格納
		var updateMaterialInfo = new Array();

		updateMaterialInfo["materialId"]   = document.update.materialId.value;
		updateMaterialInfo["materialNo"]   = document.update.materialNo.value;
		updateMaterialInfo["materialName"] = document.update.materialName.value;
		updateMaterialInfo["length"]       = document.update.length.value;
		updateMaterialInfo["unit"]         = document.update.unit.value;

		return updateMaterialInfo;
	}

	//資材IDを元に検索を実行し、DBに保存されている値を取得
	function dbMaterialInfoGet(materialId){
		$.ajax({
		    //POST通信
			type: "POST",
		    dataType:"json",
			//ここでデータの送信先URLを指定します。
			url: 'material/search/search_material.php',
			data: {                //呼び出し先のパラメータ
				searchId     : "comparison",
				materialId   : materialId
				},
			//処理が成功したら
			success : function(data){

				//返ってきた情報をdbMaterialInfoに格納していく
				dbMaterialInfo["materialId"] = data["materialId"];
				dbMaterialInfo["materialNo"] = data["materialNo"];
				dbMaterialInfo["materialName"] = data["materialName"];
				dbMaterialInfo["length"] = data["length"];
				dbMaterialInfo["unit"] = data["unit"];

			},
			//処理がエラーであれば
			error : function() {
				alert('通信エラー(dbMaterialInfoGet)');
			}
		})
	}

	//モーダルの変更内容確認のテーブルの要素を作成する
	function updateConfirmHtmlCreate(dbMaterial,updateMaterial){

		var tableElement = '<table class="table table-borderless" border="1">';

		//materialNoのdbの値と入力された値に違いがあれば要素追加
		if(!materialNoComparison(dbMaterial["materialNo"],updateMaterial["materialNo"])){

			tableElement += "<tr><th class='th-modal' bgcolor='orange'>資材番号</th></tr>"
							+"<tr><td class='td-modal-db'>"+dbMaterial["materialNo"]+"</td></tr>"
							+"<tr><td class='td-modal-db'>↓</td></tr>"
							+"<tr><td class='td-modal-up'>"+updateMaterial["materialNo"]+"</td></tr>";

		}

		//materialNameのdbの値と入力された値に違いがあれば要素追加
		if(!materialNoComparison(dbMaterial["materialName"],updateMaterial["materialName"])){

			tableElement += "<tr><th class='th-modal' bgcolor='orange'>資材名</th></tr>"
							+"<tr><td class='td-modal-db'>"+dbMaterial["materialName"]+"</td></tr>"
							+"<tr><td class='td-modal-db'>↓</td></tr>"
							+"<tr><td class='td-modal-up'>"+updateMaterial["materialName"]+"</td></tr>";

		}

		//lengthのdbの値と入力された値に違いがあれば要素追加
		if(!materialNoComparison(dbMaterial["length"],updateMaterial["length"])){

			tableElement += "<tr><th class='th-modal' bgcolor='orange'>長さ</th></tr>"
							+"<tr><td class='td-modal-db'>"+dbMaterial["length"]+"</td></tr>"
							+"<tr><td class='td-modal-db'>↓</td></tr>"
							+"<tr><td class='td-modal-up'>"+updateMaterial["length"]+"</td></tr>";

		}

		//unitのdbの値と入力された値に違いがあれば要素追加
		if(!materialNoComparison(dbMaterial["unit"],updateMaterial["unit"])){

			tableElement += "<tr><th class='th-modal' bgcolor='orange'>長さ</th></tr>"
							+"<tr><td class='td-modal-db'>"+unitChangerReverse(dbMaterial["unit"])+"</td></tr>"
							+"<tr><td class='td-modal-db'>↓</td></tr>"
							+"<tr><td class='td-modal-up'>"+unitChangerReverse(updateMaterial["unit"])+"</td></tr>";

		}

		tableElement += "</table>";

		// tableのタグが反映されない為jQueryで実装
		$(function () {
			$("#update_Confirm").html(tableElement);
		});

	}

	function updateModalButton(){

		//資材情報を更新する
		updateClient();

		//入力ボックスに入力されている値をリセットする
		updateInputBoxReset();

		//更新した資材情報を資材IDを元に検索し、画面を更新する
		screeenUpdate();

		//モーダルを閉じる
		$('#exampleModalCenter').modal("hide")

	}

	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//資材情報を更新する
	function updateClient(){
		//HTMLから受け取るデータです。
		var data = {request : $('#request').val()};
		//ここからajaxの処理です。
	    $.ajax({
	    	//POST通信
	        type: "POST",
			//ここでデータの送信先URLを指定します。
			url: 'material/update/update_material.php',
			data: {                //呼び出し先のパラメータ
				materialId   : document.update.materialId.value,
	        	materialNo   : document.update.materialNo.value,
	        	materialName : document.update.materialName.value,
	        	length       : document.update.length.value,
	        	unit         : document.update.unit.value

	        },
			//処理が成功したら
			success : function(data, dataType) {
				alert("更新されました。");
			},
			//処理がエラーであれば
			error : function() {
				alert('通信エラー(updateClient())');
			}
		});
		//submitによる画面リロードを防いでいます。
		return false;
	}

	//入力ボックスに入力されている値をリセットする
	function updateInputBoxReset(){

		document.update.materialId.value = "";
		document.update.materialNo.value = "";
		document.update.materialName.value = "";
		document.update.length.value = "";
		document.update.unit.value = 0;

	}

	function screeenUpdate(){

		//資材情報検索画面に入力された情報を配列に格納
		var searchMaterialInfo = new Array();

		searchMaterialInfo["materialId"]   = document.update.materialId.value;
		searchMaterialInfo["materialNo"]   = "";
		searchMaterialInfo["materialName"] = "";
		searchMaterialInfo["length"]       = "";
		searchMaterialInfo["unit"]         = 0;

		//検索を実行
    	searchClient(searchMaterialInfo);

	}

	//検索結果欄を選択した場合、背景をオレンジにする。もう一度クリックすると白に変わる。
	function sentaku(num){
		var tr = document.getElementById("tr"+num).style.backgroundColor;

		if(tr == "white" || tr == ""){
			document.getElementById("tr" + num).style.backgroundColor="orange";

			document.getElementById("update").style.display ="block";
			document.getElementById("search").style.display ="none";


			document.update.materialId.value = document.getElementById("id"+num).value;
			document.update.materialNo.value = document.getElementById("number"+num).textContent;
			document.update.materialName.value = document.getElementById("name"+num).textContent;
			document.update.length.value = document.getElementById("length"+num).textContent;
			document.update.unit.value = unitChanger(document.getElementById("unit"+num).textContent);

		}else if(tr == "orange"){

			document.getElementById("tr" + num).style.backgroundColor="white";

			document.getElementById("update").style.display ="none";
			document.getElementById("search").style.display ="block";

		}
	}

	//単位の値を文字から数値に変換する
	function unitChanger(unit){

		var unitChange = "";

		if(unit == "枚"){
			unitChanged = 1;
		}else if(unit == "巻"){
			unitChange = 2;
		}else if(unit == "ー"){
            unitChange = 0;
        }
		return unitChange;
	}

	//単位の値を数値から文字に変換する
	function unitChangerReverse(unit){

		var unitChange = "";

		if(unit == 0){
			unitChanged = "ー";
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

	/* **********検索ボックスの「検索」ボタンの装飾*************** */
    .btn-square-search {
        display: inline-block;
        padding: 0.5em 1em;
        text-decoration: none;
        background: #668ad8;/*ボタン色*/
        color: #FFF;
        border-bottom: solid 4px #627295;
        border-radius: 3px;
    }

    .btn-square-search:active {
        /*ボタンを押したとき*/
        -webkit-transform: translateY(4px);
        transform: translateY(4px);/*下に動く*/
        border-bottom: none;/*線を消す*/
     }

	/* **********編集ボックスの「更新」ボタンの装飾*************** */
    .btn-square-update {
        display: inline-block;
        padding: 0.5em 1em;
        text-decoration: none;
        background: #FFA500;/*ボタン色*/
        color: #FFF;
        border-bottom: solid 4px #A89978;
        border-radius: 3px;
    }

    .btn-square-update:active {
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

	/* **********左メニューの画像の位置************* */

	.dynawave {
  		display: inline;/* 文章の流れの中に置いている画像なので inline でも良いが今回は inline-block が適当 */
  		position: relative; /* 通常の文章の流れにある要素なので relative が適切 */
  		top: 20px; /* 下に 10px 下げる */
	}


	/* **********検索（編集）ボックス内、カードナビの色変更************* */
	/* **********編集ボックス内、カードナビの色をオレンジに変更************* */
	/* active (faded) */
	.nav-pills .pill-1 .nav-link {
    	background-color: rgba(255,165, 0);
    	color: white;
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
		<a href="material_registr.php"          class="list-group-item list-group-item-action">資材を登録</a>
		<a href="material_edit.php"             class="list-group-item active">資材を検索・編集する</a>
		<a href="#"                             class="list-group-item list-group-item-action">ライン別資材検索</a>
	</div><!-- list-group list-group-flush -->
</DIV>

<DIV id="Main_area" align="center">
<main class="pt-5 mx-lg-5">
	<h1>資材管理システム</h1><br>
	<h2><?php echo $machine->getName(); ?></h2>
	<h2>ー資材を検索・編集するー</h2>
	<br>
  <!-- 資材検索モード　ここから -->
  <div id="search" >
  <div class="card">
    <!-- カードのヘッダー -->
    <div class="card-header">
      <!-- ピル型のナビゲーション：card-header-pills -->
      <ul class="nav nav-pills card-header-pills">
        <li class="nav-item"> <spnn class="nav-link active" href="#">資材を検索する</span> </li>
        <li class="nav-item"> <span class="nav-link" href="#"><font color="gray">資材を編集する</font></span> </li>
      </ul>
    </div><!-- card-header -->
    <!-- カードの本文 -->
    <div class="card-body">
    <div class="infobox">
    <form name="search" id="search" method="post">
    <input type="hidden" id="materialId_search" value="" />
    <table class="info" width="900px">
  	<tr>
  		<th class="td-css">資材番号</th>
  		<th class="td-css">資材名　※入力必須項目です</th>
  		<th class="td-css">長さ</th>
  		<th class="td-css">単位</th>
  		<th class="th-css13" width="100" rowspan="2">
  			<div class="btn-square-search" onclick="searchBotton()">検索</div><!-- btn-square -->
		</th>
  	</tr>
  	<tr>
  		<td class="td-css"><input type = "text" size="17" name="materialNo" id="materialNo_search" value="" /></td>
  		<td class="td-css"><input type = "text" size="40" name="materialName"  value="" /></td>
  		<td class="td-css"><input type = "text" size="15" name="length" value="" /></td>
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
  <!-- 資材検索モード　ここまで -->

  <!-- 資材編集モード　ここから -->
  <div id="update" >
  <div class="card">
    <!-- カードのヘッダー -->
    <div class="card-header">
      <!-- ピル型のナビゲーション：card-header-pills -->
      <ul class="nav nav-pills card-header-pills">
        <li class="nav-item"> <span class="nav-link"><font color="gray">資材を検索する</font></span> </li>
        <li class="nav-item  pill-1"> <span class="nav-link active">資材を編集する</span> </li>
      </ul>
    </div>
    <!-- カードの本文 -->
    <div class="card-body">
    <div class="infobox">
	<form name="update" id="update" method="post">
    <table class="info" width="900px">
  	<tr>
  		<th class="td-css">資材番号</th>
  		<th class="td-css">資材名　※入力必須項目です</th>
  		<th class="td-css">長さ</th>
  		<th class="td-css">単位</th>
  		<th class="th-css13" width="100" rowspan="2">
  			<div class="btn-square-update" onclick="updateBotton()">更新</div><!-- btn-square -->
		</th>
  	</tr>
  	<tr>
  		<td class="td-css"><input type = "text" size="17" name="materialNo" id="materialNo_update" value="" /></td>
  		<td class="td-css"><input type = "text" size="40" name="materialName" value="" /></td>
  		<td class="td-css"><input type = "text" size="15" name="length" value=""/></td>
  		<td class="td-css">
  			<select name="unit">
				<option value="0" selected>−</option>
				<option value="1">枚</option>
				<option value="2">巻</option>
			</select>
		</td>
  	</tr>
  	</table>
  	<input type ="hidden" name="materialId" id="materialId_update" value="" />
	</form>
	</div><!-- infobox -->
  	</div><!-- card-body -->
  </div><!-- card -->
  </div><!-- update -->
  <script>
	//資材編集欄は初期状態は非表示にする
	document.getElementById("update").style.display ="none";
  </script>
  <!-- 資材編集モード　ここまで -->

  <div id="setsumei">
  	<hr size="20" color="black" noshade>
  	<ul>
  		<li>ここに検索の説明文を書きます。</li>
  	</ul>
  </div><!-- setsumei -->
  <div id="success">
  	<hr size="20" color="black" noshade>
  	<h2>検索結果</h2><br>
  	<div class="card">
  		<div class="card-body">
  			<div id="search_result"></div>
  		</div><!-- card-body-->
  	</div><!-- card -->
  </div><!-- success -->
  <script>
	//検索結果を初期状態は非表示にする
	document.getElementById("success").style.display ="none";
  </script>
</main>
</DIV>

<!-- モーダル部分始まり -->
<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalCenterTitle">資材情報更新の確認</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
			</div>
			<div class="modal-body">
				以下の内容で資材情報を更新します。よろしいですか？
				<br>
				<br>
				<div id="update_Confirm"></div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" onclick="updateModalButton();">保存する</button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal">閉じる</button>

			</div>
		</div>
	</div>
</div>
<!-- モーダル部分終わり -->
</BODY>
</html>
