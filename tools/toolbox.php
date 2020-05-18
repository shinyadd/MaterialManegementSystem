<?php
class toolbox{
    //unitの値に対し「枚」「巻」を返す。0=「-」、1=「枚」、2=「巻」
    function unitChenge($unit){
        $unitChenged = "";

        if($unit == 0){
            $unitChenged = "-";
        }else if($unit == 1){
            $unitChenged = "枚";
        }else if($unit == 2){
            $unitChenged = "巻";
        }
        return $unitChenged;
    }

    // XSS対策のためのHTMLエスケープ
    function es($data, $charset='UTF-8'){
        // $dataが配列のとき
        if (is_array($data)){
            // 再帰呼び出し
            return array_map(__METHOD__, $data);
        } else {
            // HTMLエスケープを行う
            return htmlspecialchars($data, ENT_QUOTES, $charset);
        }
    }

    //単位の値を数値から文字に変換する
    function lengthChangerReverse($lngth){

        $lngthChange = "";

        if($lngth == 0){
            $lngthChange = "ー";
        }else if($lngth == 1){
            $lngthChange = "630mm";
        }else if($lngth == 2){
            $lngthChange = "570mm";
        }else if($lngth == 3){
            $lngthChange = "520mm";
        }
        return $lngthChange;
    }

    //資材種類のDBデータをhtml変えて返す
    function materialTypeHtml($materialType,$num,$selected){

        echo '<select class="mtype" id="mtype'.$num.'">';

        foreach ($materialType as $row) {
            if($row["MATERIAL_TYPE_ID"] == $selected){
                echo '<option value="'.$this->es($row["MATERIAL_TYPE_ID"]).'" selected>'.$this->es($row["MATERIAL_TYPE_NAME"])."</option>";
            }else{
                echo '<option value="'.$this->es($row["MATERIAL_TYPE_ID"]).'">'.$this->es($row["MATERIAL_TYPE_NAME"])."</option>";
            }

        }

        echo "</select>";

    }

    //資材名を全て取得する（サンプル用、ホントは検索して使用するようにしたい）
    function materialNameGet($materialName,$num,$selected){

        $optionHtml = '<select name="mtype'.$num.'">';

        foreach ($materialName as $row) {

            if($row["MATERIAL_ID"] == $selected){
                $optionHtml += '<option value="'.$row["MATERIAL_ID"].'" selected>'.$row["MATERIAL_NAME"]."</option>";
            }else{
                $optionHtml += '<option value="'.$row["MATERIAL_ID"].'">'.$row["MATERIAL_NAME"]."</option>";
            }

        }

        $optionHtml += "</select>";

        return $optionHtml;
    }

    //C直対策。0時から12時までは前日の日付にする為の関数
    //$hourが0から12の間であればtrue
    function hourCheck($hour){

        $flg = false;

        if($hour > 0 && $hour <=12){
            $flg = true;
        }

        return $flg;

    }

    //現在の時間から３直のどれかを判断して返す
    //A=1 B=2 C=3
    function tyokuGet($hour){

        $tyoku = null;

        //A直　14時〜20時の間にページを開いた時
        if($hour >= 12 && $hour < 20 ) $tyoku = 1;
        //B直　20時〜4時の間にページを開いた時
        if(($hour >= 20 && $hour <= 24 )||( $hour >= 0 && $hour < 4 ))  $tyoku = 2;
        //B直　4時〜時の間にページを開いた時
        if($hour >= 4  && $hour < 12 )  $tyoku = 3;

        return $tyoku;

    }

    //引数の数値から前直の直の数値を返す
    function zentyokuGet($tyoku){

        $zentyoku = null;

        //A直（1）の場合C直（3）を返す
        if($tyoku == 1) $zentyoku = 3;
        //B直（2）の場合C直（1）を返す
        if($tyoku == 2) $zentyoku = 1;
        //C直（3）の場合C直（2）を返す
        if($tyoku == 3) $zentyoku = 1;

        return $zentyoku;
    }

    //資材入力画面で「年」を取得。
    function selectYearGet($dbOldest,$newest,$selected){

        //引数が整数でなければ整数に変換する
        if(!is_int($dbOldest)) $newest =(int)$dbOldest;
        if(!is_int($newest)) $newest =(int)$newest;
        if(!is_int($selected)) $selected =(int)$selected;

        echo '<select name="year" id="year">';

        if($dbOldest == "" || $dbOldest == null){

            echo '<option value="'.$selected.'" selected>'.$selected."</option>";

        }else{

            for($i=$dbOldest; $i<=$newest; $i++){

                if($i == $selected){
                    echo '<option value="'.$i.'" selected>'.$i."</option>";
                }else{
                    echo '<option value="'.$i.' ">'.$i."</option>";
                }

            }
        }

    }

    //「月」を取得
    function selectMonthGet($selected){

        echo '<select name="month" id="month">';

        for($i=1; $i<=12; $i++){

            if($i == $selected){
                echo '<option value="'.$i.'" selected>'.$i."</option>";
            }else{
                echo '<option value="'.$i.' ">'.$i."</option>";
            }

        }

        echo '</select>';

    }

    //「日」を取得
    function selectdaysGet($selected){

        echo '<select name="days" id="days">';

        for($i=1; $i<=31; $i++){

            if($i == $selected){
                echo '<option value="'.$i.'" selected>'.$i."</option>";
            }else{
                echo '<option value="'.$i.' ">'.$i."</option>";
            }

        }

        echo '</select>';

    }

    //「日」を取得
    function selectTyokusGet($selected){

        $hyojiTyoku = "";

        if($selected == 1) $hyojiTyoku = "A直";
        if($selected == 2) $hyojiTyoku = "B直";
        if($selected == 3) $hyojiTyoku = "C直";

        echo '<select name="tyoku" id="tyoku">';

        for($i=1; $i<=3; $i++){

            if($i == $selected){
                echo '<option value="'.$i.'" selected>'.$hyojiTyoku."</option>";
            }else{
                echo '<option value="'.$i.' ">'.$hyojiTyoku."</option>";
            }

        }

        echo '</select>';

    }

    //前直残表示用htmlを返す
    function zentyokuSiteremianingHtml($zentyokuSiteremainning){

        if(count($zentyokuSiteremainning) == 0 || $zentyokuSiteremainning["SITEREMAINNING"] == ""){
            echo '<td><center><input type= "text" name="zentyoku_zan" size="4" value="0" /></center></td>';
        }else{
            echo '<td><center><input type= "text" name="zentyoku_zan" size="4" value="'.$zentyokuSiteremainning["SITEREMAINNING"].'" /></center></td>';
        }

    }

    //今直残表示用htmlを返す
    function kontyokuSiteremianingHtml($kontyokuSiteremainning){

        if(count($kontyokuSiteremainning) == 0 || $kontyokuSiteremainning["SITEREMAINNING"] == ""){
            echo '<td><center><input type= "text" name="zentyoku_zan" size="4" value="0" /></center></td>';
        }else{
            echo '<td><center><input type= "text" name="zentyoku_zan" size="4" value="'.$kontyokuSiteremainning["SITEREMAINNING"].'" /></center></td>';
        }

    }

    //
    function quantityTableHtml($zenSite,$konSite){

        if(isset($zenSite) && isset($konSite)){
            foreach ($zenSite as $zenZan){
                foreach ($konSite as $konZan){
                    $this->zentyokuSiteremianingHtml($zenZan);
                    $this->kontyokuSiteremianingHtml($konZan);
                }
            }
        }
    }

    //表示する数値が0ならば""にする
    function zeroCheck($num){

        $retnum = null;

        if($num == 0){
            $retnum = "";
        }else{
            $retnum = $num;
        }

        return $retnum;

    }

    //前直の日付を取得する。
    function zentyokuDateGet($tyoku, $productDate, $productId,$sql,$Pdo){
        //
        $zentyokuDate = null;

        if($tyoku == 1){
            //今直がA直の場合、前回の生産日のC直の日付を取得する（現在切替は考慮しない）
            $zentyokuDate = $sql->zentyokuDateGet($productDate,$productId, $Pdo);
        }else{
            //今直がA直の以外場合、今直と同じ日付を設定する
            $zentyokuDate = $productDate;
        }

        return $zentyokuDate;
    }


}