<?php
require 'tools/session.php';
 $session = new session();

 //セッションを開始
 session_start();

 //セッションに値が入って入ればセッションを破棄する
 if($_SESSION["user"]){
     $session->killSession();
 }

?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>資材管理システム</title>
  <script>
	function OnLinkClick(machineId){

  		//取得したIDの値を設置する
  		document.form.machineId.value = machineId;
    	//submitの送信先を設定
    	document.form.action="top.php";
    	//submit()でフォームの内容を送信
		document.form.submit();
    }
  </script>

</head>
<body>
  <h1>資材管理システム</h1>
  <form name="form" method="post">
  <table style="border-style: none;">
   <tr>
    <th>機械名</th>
  </tr>
  <tr>
    <td><a href="#" onclick="OnLinkClick(1);">ワイドパッド</a></td>
  </tr>
  <tr>
    <td>尿パッド</td>
  </tr>
  <tr>
    <td>パンツ</td>
  </tr>
  <tr>
    <td>ベビー</td>
  </tr>
  <tr>
    <td>ポイズ1号機</td>
  </tr>
  <tr>
    <td>ポイズ2号機</td>
  </tr>
  <tr>
    <td>ポイズ3号機</td>
  </tr>
  <tr>
    <td>ポイズ4号機</td>
  </tr>
  </table>
  <input type = "hidden" id="machineID" name="machineId" value="" />
  </form>

    <input name="mytext" type="text" />

    <!-- ボタン要素で送信ボタンを設置 -->
    <button id="btn">送信</button>


</body>
</html>
