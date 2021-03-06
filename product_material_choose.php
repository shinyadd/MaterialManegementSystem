<?php
ini_set('display_errors',1);
require_once 'tools/dbAccess.php';
require_once 'tools/sqlCreate.php';
require_once 'tools/toolbox.php';
require_once 'capsule/machine.php';


//セッションを開始
session_start();

//セッションでログイン情報を確認
$machine = unserialize($_SESSION['user']);

//セッション情報がなければログインページへ飛ばす
if($machine == false){
    header('Location: login.php');
}


//dbAcsessをインスタンス化
$Pdo = new dbAccsess();

//SQLクラスをインスタンス化
$Sql = new sqlCreate();

//toolboxをインスタンス化
$tool = new toolbox();

//製品情報を取得する
$productInfo = array();
$productInfo["productId"]   = $_POST["productId"];
$productInfo["productNo"]   = $_POST["productNo"];
$productInfo["productName"] = $_POST["productName"];
$productInfo["lngth"]       = $_POST["lngth"];

//資材種類の情報を取得する
$materialType = $Sql->materialTypeGet($machine->getId(),$productInfo["productId"], $Pdo);

//製品登録資材の情報を取得する
$pdtInfo = $Sql->pdtmaterialInfoGet($productInfo["productId"], $Pdo);

//検索結果の行をカウントする
$count = 1;

//jsに受け渡し用にmaterialTypeをjson形式に直す

$j_materialType = json_encode($materialType, JSON_UNESCAPED_UNICODE);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>トップページ　-資材管理システム-</title>
<script src="js/jquery-3.4.1.min.js" type="text/javascript"></script>
<script src="js/jquery-ui.min.js" type="text/javascript"></script>
<script src="js/bootstrap.bundle.js" type="text/javascript"></script>
<script src="js/inputChooseCheck.js" type="text/javascript" charset="utf-8"></script>

<LINK rel="stylesheet" href="css/maindesign.css" type="text/css">
<link rel="stylesheet" href="css/bootstrap.min.css">
<link rel="stylesheet" href="css/jquery-ui.css">

<script>
	var productId = <?php echo $productInfo["productId"] ?>;

	var materialtype = <?php echo $j_materialType ?>;

	var machineId = <?php echo $machine->getId() ?>;


	//検索カード内の資材番号、資材名、長さをチェックする
	//検索の場合、資材名は入力必須では無いので未記入でもOK
	function inputCheckSearch(){
		var materialNo   = document.getElementById("modalSearchNumber").value;
		var materialName = document.getElementById("modalSearchName").value;
		var length       = document.getElementById("modalSearchLength").value;
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
			alert("「資材名」は半角の40文字、全角20字以内で入力してください");
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


	//選択されていない資材種類のセレクトボックスを作る
	function materalTypeSelect(materialtype , count){

		var materialTypeSelect = '<select id="mtype'+count+'">'
								+'<option value="0" >ー</option>';


        for(i=0; i<materialtype.length; i++) {
        	materialTypeSelect += '<option value="'+materialtype[i].MATERIAL_TYPE_ID+'" >'+materialtype[i].MATERIAL_TYPE_NAME+"</option>";
        }

        materialTypeSelect +="</select>";

        return materialTypeSelect;

	}

	//行を削除する
	function materialDelete(obj){

		//番号を取得
        var count = Number(document.getElementById("count").value);
		//番号に１を加える
		count = count -1;
		//htmlのcountを書き換える
		document.getElementById("count").value = count;

		// 削除ボタンを押下された行を取得
        tr = obj.parentNode.parentNode;
        // trのインデックスを取得して行を削除する
        tr.parentNode.deleteRow(tr.sectionRowIndex);

        // ソートが完了したら実行される。
        var rows = $('#allline .order');
    		for (var i = 0, rowTotal = rows.length; i < rowTotal; i += 1) {
        		$($('.order')[i]).text(i + 1);
        		$($('.mtype')[i]).attr('id', 'mtype'+(i+1));
        		$($('.name')[i]).attr('id', 'materialName'+(i+1));
        		$($('.materialid')[i]).attr('id', 'materialid'+(i+1));
        		$($('.changeButton')[i]).attr('onclick', 'materialChange('+(i+1)+');');
    		}

	}

	//行を追加する
	function linePlus(){
		var table = document.getElementById("pdt");

        //番号を取得
        var count = Number(document.getElementById("count").value);
		//番号に１を加える
		count = count +1;
		//htmlのcountを書き換える
		document.getElementById("count").value = count;

        // 行を行末に追加
        var row = table.insertRow(-1);
        //td分追加
        var cell1 = row.insertCell(-1);
        var cell2 = row.insertCell(-1);
        var cell3 = row.insertCell(-1);
        var cell4 = row.insertCell(-1);
        var cell5 = row.insertCell(-1);

        //class,idを追加
        cell1.setAttribute("class","td-css order");
        cell1.setAttribute("id","materialid"+count);

		cell2.setAttribute("class","td-css");

		cell3.setAttribute("class","td-css");
		cell3.setAttribute("id","materialName"+count);

		cell4.setAttribute("class","td-css");

		cell5.setAttribute("class","td-css");


		/*
		cell1.className = 'name';
		cell2.className = 'address';
		cell3.className = 'tel';

		*/

        // セルの内容入力
        cell1.innerHTML = count;
        cell2.innerHTML = materalTypeSelect(materialtype,count);
        cell3.innerHTML = 'ー';
        cell4.innerHTML = '<input type="button" value="資材選択" onclick= "materialChange('+count+')">'
        				 +'<input type="hidden" id="materialid'+count+'>"  value="" />';
        cell5.innerHTML = '<input type="button" value="削除" onclick= "materialDelete(this)">';

	}

	//資材名にポインタを合わすと背景がオレンジ色に変わる
	function orengeBackground(line){

		line.style.backgroundColor = "orange";
	}

	//資材名からポインタを外すと背景色を白に変える
	function whiteBackground(line){
		line.style.backgroundColor = "white";
	}


	function registerBotton(){
		//サーバーに送る情報を全てのラインから取得する
		//行数を取得する
        var count = document.getElementById("count").value;

		//pdtmaterialの配列を宣言
		var materialTypeId = new Array();
		var materialId     = new Array();

		//各行のmaterialTypeとmaterialIdを収納していく
		for (i=1;i<=count;i++){
			materialTypeId[i] = document.getElementById("mtype"+i).value;
			materialId[i]     = document.getElementById("materialid"+i).value;

		}

		//HTMLから受け取るデータです。
		var data = {request : $('#request').val()};
		//ここからajaxの処理です。
	    $.ajax({
	    	//POST通信
	        type: "POST",
			//ここでデータの送信先URLを指定します。
			url: 'pdtmaterial/register_pdtmaterial.php',
			data: {                //呼び出し先のパラメータ
				searchId        : "register",
				productId       : productId,
				machineId       : machineId,
				materialTypeId  : materialTypeId,
				materialId      : materialId,
				count           : count

	        },
			//処理が成功したら
			success : function(data, dataType) {
				alert('保存しました');
				location.reload(true);
			},
			//処理がエラーであれば
			error : function() {
				alert('通信エラーです');
			}
		});
		//submitによる画面リロードを防いでいます。
		return false;




	}


///////////////////////////////モーダル内の処理//////////////////////////////////////////////////
	//「資材変更」ボタンを押下後の処理。モーダルを表示する
	function materialChange(lineid){
		//モーダル内を初期化する
		//検索結果を初期状態は非表示にする
		document.getElementById("search_result").style.display ="none";
		document.getElementById("setsumei").style.display = "block";

		//lineidをモーダル内のタグに収納する
		document.getElementById("clickLineID").value = lineid;

		//モーダルを表示する
		$('#materialModalCenter').modal("show")

	}

	//モーダル内でポインタを合わすと背景が薄いオレンジ色に変わる
	function thinOrengeBackground(line){
		//行の色を取得する
		var lineColor = line.style.backgroundColor;

		if(lineColor != "orange"){
			line.style.backgroundColor = "#FFDCA5";
		}
	}

	//モーダル用、資材名からポインタを外すと背景色を白に変える
	function modalWhiteBackground(line){

		//行の色を取得する
		var lineColor = line.style.backgroundColor;

		if(lineColor != "orange"){
			line.style.backgroundColor = "white";
		}
	}

	//モーダルで選択された資材の処理
	function sentaku(num){

		//選択した行の色を取得
		var tr = document.getElementById("tr"+num);

		//行を未選択の場合(行がオレンジ色の場合)
		if(tr.style.backgroundColor == "rgb(255, 220, 165)" || tr.style.backgroundColor == "white"){

			//行数を取得する
			var count = document.getElementById("modal_count").value;

			//全てのモーダルの行の背景を白にする
			for(i=1;i<count;i++){
				document.getElementById("tr"+i).style.backgroundColor = "white";
			}

			//クリックした行が薄オレンジ色の場合、オレンジ色にする
			tr.style.backgroundColor = "orange";


			//選択した資材のIDと資材名を取得用タグに収納
			document.getElementById("sentaku_mtid").value = document.getElementById("id"+num).value;
			document.getElementById("sentaku_mtname").value = document.getElementById("name"+num).textContent;

			//モーダル内の「選択」ボタンをアクティブにする
			var chooseButton = document.getElementById("chooseButton");

			chooseButton.setAttribute("class","btn btn-primary");
			chooseButton.setAttribute("onclick","chooseModalButton();");

		}else if(tr.style.backgroundColor == "orange"){//行が選択済みの場合
			//クリックした行がオレンジ色の場合、白色にする
			tr.style.backgroundColor = "white";

			//モーダル内の「選択」ボタンを非アクティブにする
			var chooseButton = document.getElementById("chooseButton");

			chooseButton.setAttribute("class","btn btn-secondary");
			chooseButton.removeAttribute("onclick");

		}
	}

	//モーダル内の「選択」ボタンを押下した時の処理
	function chooseModalButton(){

		var chooseLineId = document.getElementById("clickLineID").value;

		document.getElementById("materialid"+chooseLineId).value = document.getElementById("sentaku_mtid").value;
		document.getElementById("materialName"+chooseLineId).textContent = document.getElementById("sentaku_mtname").value;

		//モーダルを閉じる
		$('#materialModalCenter').modal("hide")

	}

    ////////////////////モーダルからの資材情報検索//////////////////////////////////
    //「検索」ボタン押下後、入力チェックを行い、trueなら検索を開始する。
	function searchBotton(){
  		//入力された情報をチェックする
  		//入力が正しければ検索を開始する
  		if(inputCheckSearch()){
  			//検索を実行
	    	searchClient();
  		}
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
			url: 'pdtmaterial/register_pdtmaterial.php',
			data: {                //呼び出し先のパラメータ
				searchId     : "search",
				materialNo   : document.getElementById("modalSearchNumber").value,
	        	materialName : document.getElementById("modalSearchName").value,
	        	length       : document.getElementById("modalSearchLength").value
	        },
			//処理が成功したら
			success : function(data, dataType) {
				document.getElementById("setsumei").style.display ="none";
				document.getElementById("search_result").style.display = "block";
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
</script>
<style>
input[type=text]{
        height:30px;
    }

  div.result {
  width: 100px;
  height: 100px;
  overflow-y: scroll;
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
		<a href="product_material_register.php" class="list-group-item active">製品に資材を登録・変更する</a>
		<a href="product_register.php"          class="list-group-item list-group-item-action">製品を登録</a>
		<a href="product_edit.php"              class="list-group-item list-group-item-action">製品を検索・編集する</a>
		<a href="material_registr.php"          class="list-group-item list-group-item-action">資材を登録</a>
		<a href="material_edit.php"             class="list-group-item list-group-item-action">資材を検索・編集する</a>
		<a href="#"                             class="list-group-item list-group-item-action">ライン別資材検索</a>
	</div><!-- list-group list-group-flush -->
</DIV>

<DIV id="Main_area" align="center">
<main class="pt-5 mx-lg-5">
<h1>資材登録画面</h1>
	<h2><?php echo $machine->getName(); ?></h2>
	<h2>ー製品に資材を登録するー</h2>
	<br>
    <!-- 製品情報　ここから -->
	<div id="search" >
 	<div class="card">
    <!-- カードの本文 -->
    <div class="card-body">
    <div class="infobox">
    <form name="register" id="search" method="post">
    <input type="hidden" id="materialId_search" value="" />
    <table class="info" width="900px">
  	<tr>
  		<th class="td-css">製品番号</th>
  		<th class="td-css">製品名</th>
  		<th class="td-css">長さ</th>
  		<th class="th-css13" width="100" rowspan="2">
  			<div class="btn-square-register" onclick="registerBotton()">登録</div><!-- btn-square -->
		</th>
  	</tr>
  	<tr>
  		<td class="td-css"><?php echo $productInfo["productNo"] ?></td>
  		<td class="td-css"><?php echo $productInfo["productName"] ?></td>
  		<td class="td-css"><?php echo $tool->lengthChangerReverse($productInfo["lngth"]); ?></td>
  	</tr>
  	</table>

  	</form>
  	</div><!-- infobox -->
  	</div><!-- card-body -->
	</div><!-- card -->
	</div><!-- search -->
    <!-- 資材情報　ここから -->
	<br>
	<div class="card">
	<div class="card-body">
	<table id="pdt">
		<thead>
		<tr class="info" width= "900px">
			<th class="td-css">番号</th>
			<th class="td-css">資材種類</th>
			<th class="td-css">資材名</th>
			<th class="td-css"></th>
			<th class="td-css"></th>
		</tr>
		</thead>
		<tbody id="allline">
 	<?php foreach ($pdtInfo as $row) { ?>
		<tr id="line<?php echo $count?>" class="info" style="width: 900px;" onmouseover="orengeBackground(this);" onmouseout="whiteBackground(this);">
			<td class="td-css order" id="order<?php echo $count ?>"><?php echo $count?></td>
			<td class="td-css"><?php echo $tool->materialTypeHtml($materialType, $count, $tool->es($row["MATERIAL_TYPE_ID"])) ?></td>
			<td class="td-css name" id="materialName<?php echo $count ?>"  ><?php echo $tool->es($row["MATERIAL_NAME"]) ?></td>
			<input class="materialid" type="hidden" id="materialid<?php echo $count?>"  value="<?php echo $tool->es($row["MATERIAL_ID"])?>" />
			<td class="td-css" ><input type="button"  class="changeButton" value="資材選択" onclick= "materialChange('<?php echo $count?>')"></td>
			<td class="td-css" ><input type="button"  value="削除" onclick= "materialDelete(this)"></td>
		</tr>
	<?php $count++; }?>
		</tbody><!-- allline end -->
	</table>
	<input type="hidden" id="count" value="<?php echo $count-1?>" />
	<br>
	<input type="button" value="項目追加" onclick="linePlus()">
	</div><!-- card-body-->
	</div><!-- card -->
</main>
</DIV>
<script>
$('#allline').sortable();
$('#allline').disableSelection();

$('#allline').bind('sortstop', function (e, ui) {
    // ソートが完了したら実行される。
    var rows = $('#allline .order');
    for (var i = 0, rowTotal = rows.length; i < rowTotal; i += 1) {
        $($('.order')[i]).text(i + 1);
        $($('.mtype')[i]).attr('id', 'mtype'+(i+1));
        $($('.name')[i]).attr('id', 'materialName'+(i+1));
        $($('.materialid')[i]).attr('id', 'materialid'+(i+1));
        $($('.changeButton')[i]).attr('onclick', 'materialChange('+(i+1)+');');
    }
})
</script>

<!-- モーダル部分始まり -->
<div class="modal fade" id="materialModalCenter" tabindex="-1" role="dialog" aria-labelledby="materialModalCenterTitle" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="productModalCenterTitle">資材情報更新の検索</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
			</div>
			<div class="modal-body">
				<table align="center">
  					<tr>
  						<th class="td-css">資材番号</th>
  						<th class="td-css">資材名</th>
  						<th class="td-css">長さ</th>
  						<th class="th-css13" width="100" rowspan="2">
  						<div class="btn-square-register" onclick="searchBotton()">検索</div><!-- btn-square -->
						</th>
  					</tr>
  					<tr>
  						<td class="td-css"><input type="text"  size="17" id="modalSearchNumber" value="" /></td>
  						<td class="td-css"><input type="text"  size="40" id="modalSearchName" value="" /></td>
  						<td class="td-css"><input type="text"  size="15" id="modalSearchLength" value="" /></td>
  					</tr>
  				</table>
  				<hr>
  				<div id ="setsumei" align="center">検索結果がここに表示されます</div>
  				<br>
  				<div id="search_result" align="center">
  				</div>

  				<script>
					//検索結果を初期状態は非表示にする
					document.getElementById("search_result").style.display ="none";
				</script>
				</div>
			<input type="hidden" id="clickLineID" value="">
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" id="chooseButton">選択</button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal">閉じる</button>

			</div>
		</div>
	</div>
</div>
<!-- モーダル部分終わり -->
</BODY>
</html>
