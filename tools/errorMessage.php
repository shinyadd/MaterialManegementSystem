<?php
class errorMessege{

    private  $errorMsg = [];

    function errorMsgSet($num){

        switch($num){
            case 1:
                $this->errorMsg[] = "資材番号は半角英数字で10文字以内で入力してください";
                break;
            case 2:
                $this->errorMsg[] = "資材番号は半角英数で入力してください";
                break;
            case 3:
                $this->errorMsg[] = "資材名は入力必須項目です";
                break;
            case 4:
                $this->errorMsg[] = "資材名は半角で40文字、全角で20文字で入力して下さい";
                break;
            case 5:
                $this->errorMsg[] = "長さは半角の数で入力してください";
                break;
            case 6:
                $this->errorMsg[] = "長さは半角数で５桁以内で入力してください";
                break;
        }

    }

    function getErrorMsg(){
        return $this->errorMsg;
    }

}