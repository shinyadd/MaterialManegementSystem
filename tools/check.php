<?php
class check{

    //文字数をチェックする。
    //$check＝チェックされる対象の値、$num＝最大値
    function lengthCheck($check , $num){
        $flg = true;
        //$checkの文字数、桁数が$numを超える場合falseを返す
        //$numの数値と同じ文字数の場合はtrue
        if(strlen($check) > $num){
            $flg = false;
        }
        return $flg;
    }

    //値がnullもしくは””（空白）かをチェックする
    function nullCheck($check){
        $flg = true;
        //$checkがnullまたは空白の場合falseを返す
        if($check == null || $check == "" ){
            $flg = false;
        }
        return $flg;
    }

    //値が半角英数かをチェックする
    function alphanumericCheck($check){
        $flg = true;
        //半角英数でなければfalseを返す
        if (!preg_match("/^[a-zA-Z0-9]+$/", $check)) {
            $flg = false;
        }
        return $flg;
    }

    //値が半角数かをチェックする
    function numberCheck($check){
        $flg = true;
        //半角数値でなければfalseを返す
        if (!preg_match("/^[0-9]+$/", $check)) {
            $flg = false;
        }
        return $flg;
    }

}