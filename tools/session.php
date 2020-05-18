<?php
class session{

    //セッションを破棄する
    function killSession(){
        // セッション変数の値を空にする
        $_SESSION = [];
        // セッションクッキーを破棄する
        if (isset($_COOKIE[session_name()])){
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time()-36000, $params['path']);
        }
        // セッションを破棄する
        session_destroy();
    }

}