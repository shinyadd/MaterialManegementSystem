<?php
require_once 'tools/sqlCreate.php';
require_once 'tools/dbAccess.php';
require_once 'capsule/machine.php';

//セッションを開始
session_start();
//ログイン画面で選ばれた機械のIDを取得する
if(isset($_POST["machineId"])){
    $machineId = $_POST["machineId"];

    //dbAcsessをインスタンス化
    $Pdo = new dbAccsess();
    //Sqlクラスをインスタンス化
    $Sql = new sqlCreate();

    //DBにアクセスして機械の情報を取得する
    $machineInfo = $Sql->machineInfoGet($machineId,$Pdo);

    //machineクラスをインスタンス化
    $machine = new machine();

    //機械の情報をmachineクラスに格納する
    $machine->setDbSet($machineInfo);

    //machineクラスの情報をセッションに保存する
    $_SESSION["user"] = serialize($machine);
}else{
    $machine = unserialize($_SESSION['user']);
}



?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>トップページ　-資材管理システム-</title>
<script src="js/jquery-3.4.1.min.js" type="text/javascript"></script>
<script src="js/bootstrap.bundle.js" type="text/javascript"></script>

<LINK rel="stylesheet" href="css/maindesign.css" type="text/css">
<link rel="stylesheet" href="css/bootstrap.min.css">
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
		<a href="top.php"                       class="list-group-item active">トップページ</a>
		<a href="quantity_used.php"             class="list-group-item list-group-item-action">使用した資材数を入力する</a>
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
<h2>トップページ</h2><br>
  	<div class="card">
  		<div class="card-body">
  			<ul>
  				<li>ここのページは機械の基本情報を表示する</li>
  				<li></li>
  			</ul>
  		</div><!-- card-body-->
  	</div><!-- card -->
</main>
</DIV>
</BODY>
</html>