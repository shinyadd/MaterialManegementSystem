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
  <title>製品を検索・編集する</title>
  <script src="js/jquery-3.4.1.min.js" type="text/javascript"></script>
<script src="js/bootstrap.bundle.js" type="text/javascript"></script>

<script src="js/inputProductCheck.js" type="text/javascript" charset="utf-8"></script>
<LINK rel="stylesheet" href="css/maindesign.css" type="text/css">
<link rel="stylesheet" href="css/bootstrap.min.css">
<script type="text/javascript">
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
		<a href="product_edit.php"              class="list-group-item active">製品を検索・編集する</a>
		<a href="material_registr.php"          class="list-group-item list-group-item-action">資材を登録</a>
		<a href="material_edit.php"             class="list-group-item list-group-item-action">資材を検索・編集する</a>
		<a href="#"                             class="list-group-item list-group-item-action">ライン別資材検索</a>
	</div><!-- list-group list-group-flush -->
</DIV>
<DIV id="Main_area" align="center">
<main class="pt-5 mx-lg-5">
</main>
</DIV>
</BODY>
</html>
