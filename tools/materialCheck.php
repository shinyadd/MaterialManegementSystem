<?php
require_once 'check.php';

class materialCheck extends check {

    //資材番号をチェックする
    function materialNoCheck($materialNo , $num , $errorMsg){
        $flg = false;

        //空白の場合はチェックしない
        if($this->nullCheck($materialNo)){
            //空白、nullでなければ英数かをチェックする
            if($this->alphanumericCheck($materialNo)){
                //英数であれば文字数をチェックする。半角で10文字を超えるとエラー
                if($this->lengthCheck($materialNo, $num)){
                    $flg = true;
                }else{
                    //資材番号は半角英数字で10文字以内で入力してください
                    $errorMsg -> errorMsgSet(1);
                }
            }else{
                //資材番号は半角英数で入力してください
                $errorMsg -> errorMsgSet(2);
            }
        }else{
            $flg = true;
        }
        return $flg;
    }

    //資材名をチェックする
    function materialNameCheck($materialName , $num , $errorMsg){
        $flg = false;

        //空白の場合エラー
        if($this->nullCheck($materialName)){
            //半角で40文字、全角で20文字を超えるとエラー
            if($this->lengthCheck($materialName, $num)){
                $flg = true;
            }else{
                //資材名は半角で40文字、全角で20文字で入力して下さい
                $errorMsg->errorMsgSet(4);
            }
        }else{
            //資材名は入力必須項目です
            $errorMsg->errorMsgSet(3);
        }
        return $flg;
    }

    //長さをチェックする
    function materialLengthCheck($length, $num , $errorMsg){
        $flg = false;

        //空白の場合チェックしない
        if($this->nullCheck($length)){
            //半角数値かをチェック
            if($this->numberCheck($length)){
                //半角で5桁を超えるとエラー
                if($this->lengthCheck($length, $num)){
                    $flg = true;
                }else{
                    //長さは半角数で５桁以内で入力してください
                    $errorMsg->errorMsgSet(6);
                }
            }else{
                //長さは半角の数で入力してください
                $errorMsg->errorMsgSet(5);
            }
        }
        return $flg;
    }

    //資材登録画面から入力された値をチェックする
    function materialInputCheck($material , $errorMsg){
        $materialNo = $material["materialNo"];
        $materialName = $material["materialName"];
        $length = $material["length"];

        $flg = false;

        if($this->materialNoCheck($materialNo, 10, $errorMsg)
            && $this->materialNameCheck($materialName, 40 , $errorMsg)
            && $this->materialLengthCheck($length, 5, $errorMsg)
           ){
            $flg = true;
        }

        return $flg;
    }
}