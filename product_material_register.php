<?php
require_once 'tools/sqlCreate.php';
require_once 'tools/dbAccess.php';
require_once 'capsule/machine.php';

//セッションを開始
session_start();
//セッションでログイン情報を確認

$machine = unserialize($_SESSION['user']);

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>製品を資材を登録する　-資材管理システム-</title>
<script src="js/jquery-3.4.1.min.js" type="text/javascript"></script>
<script src="js/bootstrap.bundle.js" type="text/javascript"></script>

<script src="js/inputProductCheck.js" type="text/javascript" charset="utf-8"></script>
<LINK rel="stylesheet" href="css/maindesign.css" type="text/css">
<link rel="stylesheet" href="css/bootstrap.min.css">

<script type="text/javascript">
//検索カード内の資材番号、資材名、長さをチェックする
//検索の場合、資材名は入力必須では無いので未記入でもOK
function inputCheckSearch(){
	var productNo   = document.search.productNo.value;
	var productName = document.search.productName.value;
	var inputCheck = false;

	var flg  = productNoCheck(productNo,10);
	var flg2 = productNameCheckSearch(productName,40);

	//資材番号をチェックする
	if(flg == false){
		alert("「資材番号」は半角英数、10文字以内で入力してください");
	}

	//資材名をチェックする
	if(flg2 == false){
		alert("「資材名」は半角英数、40文字以内で入力してください");
	}

	if(flg && flg2){
		inputCheck = true;
	}

	return inputCheck;

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
		url: 'product/search/search_product.php',
		data: {                //呼び出し先のパラメータ
			searchId     : "search",
			productNo   : document.search.productNo.value,
        	productName : document.search.productName.value,
        	length       : document.search.length.value,
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

//検索結果欄を選択した場合、背景をオレンジにする。もう一度クリックすると白に変わる。
function sentaku(num){
	var tr = document.getElementById("tr"+num).style.backgroundColor;

	if(tr == "white" || tr == ""){
		document.getElementById("tr" + num).style.backgroundColor="orange";

		//選択した製品情報を取得する
		var productInfo = productInfoGet(num);

		//確認用のテーブルを生成する
		productConfirmHtmlCreate(productInfo);

		//モーダルを表示する
		$('#productModalCenter').modal("show")

	}else if(tr == "orange"){

		document.getElementById("tr" + num).style.backgroundColor="white";
	}
}

//検索結果から選択した製品の情報を取得する
function productInfoGet(num){

	var productInfo = new Array();

	productInfo["num"]         = num;
	productInfo["productId"]   = document.getElementById("id"+num).value;
	productInfo["productNo"]   = document.getElementById("number"+num).textContent;
	productInfo["productName"] = document.getElementById("name"+num).textContent;
	productInfo["lngth"]      = document.getElementById("length"+num).textContent;

	return productInfo;
}

function productConfirmHtmlCreate(productInfo){

	var tableElement = '<table class="table table-borderless" border="1">';

	//productNoの要素追加
	tableElement += "<tr><th class='th-modal' bgcolor='orange'>製品番号</th></tr>"
				   +"<tr><td class='td-modal-db'>"+productInfo["productNo"]+"</td></tr>";

	//productNameの要素追加
	tableElement += "<tr><th class='th-modal' bgcolor='orange'>資材名</th></tr>"
					+"<tr><td class='td-modal-up'>"+productInfo["productName"]+"</td></tr>";

	//lengthの要素追加
	tableElement += "<tr><th class='th-modal' bgcolor='orange'>長さ</th></tr>"
		+"<tr><td class='td-modal-db'>"+productInfo["lngth"]+"</td></tr>";

	//<table>タグ終了
	tableElement += "</table>";

	//選択した欄のnum（番号、順番）を記憶する
	tableElement += '<input type="hidden" id="sentaku" value="'+productInfo["num"]+'" >';

	// tableのタグが反映されない為jQueryで実装
	$(function () {
		$("#product_Confirm").html(tableElement);
	});
}

//製品長の値を文字から数値に変換する
function lengthChanger(length){

	var lengthChange = "";

	if(length == "630mm"){
		lengthChange = 1;
	}else if(length == "570mm"){
		lengthChange = 2;
	}else if(length == "520mm"){
		lengthChange = 3;
	}else if(length == "ー"){
		lengthChange = 0;
    }
	return lengthChange;
}

//モーダルの閉じるボタンを押した時の処理
function closeModalButton(){

	//選択した製品の順番（上からの番号）を取得
	var num = document.getElementById("sentaku").value;

	//選択した製品の欄を白に戻す
	document.getElementById("tr" + num).style.backgroundColor="white";

	//モーダルを閉じる
	$('#productModalCenter').modal("hide")
}

//モーダルの確認ボタンを押した時の処理
//product_idを取得して製品に資材を登録する画面に送る
function productModalButton(){

	//選択した製品の順番（上からの番号）を取得
	var num = document.getElementById("sentaku").value;

	//選択した製品情報を取得する
	var productInfo = productInfoGet(num);

	//productIdの値を設置する
	document.search.productId.value = productInfo["productId"];

	//productNoの値を設置する
	document.search.productNo.value = productInfo["productNo"];

	//productNameの値を設置する
	document.search.productName.value = productInfo["productName"];

	//lengthの値を設置する
	document.search.lngth.value = lengthChanger(productInfo["lngth"]);

	//submitの送信先を設定
	document.search.action="product_material_choose.php";
	//submit()でフォームの内容を送信
	document.search.submit();

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
<h2>ー製品に資材を登録するー</h2>
<br>
<!-- 資材検索モード　ここから -->
<div id="search" >
<div class="card">
<!-- カードのヘッダー -->
<div class="card-header">
<!-- ピル型のナビゲーション：card-header-pills -->
<ul class="nav nav-pills card-header-pills">
<li class="nav-item"> <span class="nav-link active" href="#">製品を検索する</span> </li>
</ul>
</div><!-- card-header -->
<!-- カードの本文 -->
<div class="card-body">
<div class="infobox">
<form name="search" id="search" method="post">
<table class="info" width="900px">
<tr>
<th class="td-css">製品番号</th>
<th class="td-css">製品名</th>
<th class="td-css">長さ</th>
<th class="th-css13" width="100" rowspan="2">
<div class="btn-square-search" onclick="searchBotton()">検索</div><!-- btn-square -->
</th>
</tr>
<tr>
<td class="td-css"><input type = "text" size="17" name="productNo" id="productNo" value="" /></td>
<td class="td-css"><input type = "text" size="40" name="productName" id="productName" value="" /></td>
<td class="td-css">
  	<select name="lngth" id="lngth">
		<option value="0" selected>−</option>
		<option value="1">630mm</option>
		<option value="2">570mm</option>
		<option value="3">520mm</option>
	</select>
</td>
</tr>
</table>
<input type = "hidden" name="productId" id="productId" value="" />
</form>
</div><!-- infobox -->
</div><!-- card-body -->
</div><!-- card -->
</div><!-- search -->
<!-- 資材検索モード　ここまで -->


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
<div class="modal fade" id="productModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
<div class="modal-dialog modal-dialog-centered" role="document">
<div class="modal-content">
<div class="modal-header">
<h5 class="modal-title" id="exampleModalCenterTitle">製品情報の確認</h5>
<button type="button" class="close" data-dismiss="modal" aria-label="Close">
<span aria-hidden="true">&times;</span>
</button>
</div>
<div class="modal-body">
以下の製品に資材情報を登録します。<br>
「確認」押下後、登録画面に移動します。
<br>
<br>
<div id="product_Confirm"></div>
</div>
<div class="modal-footer">
<button type="button" class="btn btn-primary" onclick="productModalButton();">確認</button>
<button type="button" class="btn btn-secondary" onclick="closeModalButton();">閉じる</button>
</div>
</div>
</div>
</div>
<!-- モーダル部分終わり -->
</BODY>
</html>
