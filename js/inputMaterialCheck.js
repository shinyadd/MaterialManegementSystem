	//$check＝チェックされる対象の値、$num＝最大値
	function lengthCheck(check,num){
		var flg = true;
		//$checkの文字数、桁数が$numを超える場合falseを返す
		//$numの数値と同じ文字数の場合はtrue
		if(check.length > num){
			flg = false;
		}
		return flg;
	}

	//値がnullもしくは””（空白）かをチェックする
    function nullCheck(check){
        var flg = true;
        //checkがnullまたは空白の場合falseを返す
        if(check == null || check == "" ){
            flg = false;
        }
        return flg;
    }

	//半角英数かをチェックする
	function alphanumericCheck(check){
		var flg = false;
		check = (check==null)?"":check;
		if(check.match(/^[A-Za-z0-9]*$/)){
			 flg = true;
		 }
		return flg;
    }

	//////////////////////////資材登録・編集///////////////////////////////////////
	//資材番号をチェックする
    function materialNoCheck(materialNo , num){
        var flg = false;

        //空白の場合はチェックしない
        if(nullCheck(materialNo)){
            //空白、nullでなければ英数かをチェックする
            if(alphanumericCheck(materialNo)){
                //英数であれば文字数をチェックする。半角で10文字を超えるとNG
                if(lengthCheck(materialNo, 10)){
                    flg = true;
                }
            }
        }else{
        	flg = true;
        }

        return flg;
    }

    //資材名をチェックする
    function materialNameCheck(materialName , num){
        flg = false;

        //空白の場合エラー
        if(nullCheck(materialName)){
            //半角で40文字、全角で20文字を超えるとエラー
            if(lengthCheck(materialName, num)){
                flg = true;
            }
        }
        return flg;
    }

  //資材名をチェックする(検索の時使用)
    function materialNameCheckSearch(materialName , num){
        flg = false;

        //検索の場合は空白の場合でもtrue
        if(nullCheck(materialName)){
            //半角で40文字、全角で20文字を超えるとエラー
            if(lengthCheck(materialName, num)){
                flg = true;
            }
        }else{
        	flg = true;
        }

        return flg;
    }

    //長さをチェックする
    function materialLengthCheck(length, num){
        flg = false;

        //空白の場合チェックしない
        if(nullCheck(length)){
            //半角数値かをチェック
            if(numberCheck(length)){
                //半角で5桁を超えるとエラー
                if(lengthCheck(length, num)){
                    flg = true;
                }
            }
        }else{
        	flg = true;
        }

        return flg;
    }

    /////////////ここから比較のためのチェック処理メソッド///////////////

    //資材IDを比較する。同じであればtrue,差異があればfalse
    function materialIdComparison(dbMaterialId,updateMaterialId){

    	var flg = false;

    	if(dbMaterialId === updateMaterialId){
    		flg = true;
		}

    	return flg;
    }

    //資材番号を比較する。同じであればtrue,差異があればfalse
    function materialNoComparison(dbMaterialNo,updateMaterialNo){

    	var flg = false;

    	var db = JSON.stringify( dbMaterialNo );
    	var up = JSON.stringify( updateMaterialNo );

    	if(dbMaterialNo === updateMaterialNo){
    		flg = true;
		}

    	return flg;
    }

    //資材名を比較する。同じであればtrue,差異があればfalse
    function materialNameComparison(dbMaterialName,updateMaterialName){

    	var flg = false;

    	var db = JSON.stringify( dbMaterialName );
    	var up = JSON.stringify( updateMaterialName );

    	if(db === up){
    		flg = true;
		}
    	return flg;
    }

    //長さを比較する。同じであればtrue,差異があればfalse
    function lengthComparison(dbMaterialLength,updateMaterialLength){

    	var flg = false;

    	if(dbMaterialLength === updateMaterialLength){
    		flg = true;
		}
    	return flg;
    }

    //unitを比較する。同じであればtrue,差異があればfalse
    function unitComparison(dbMaterialUnit,updateMaterialUnit){

    	var flg = false;

    	if(dbMaterialUnit === updateMaterialUnit){
    		flg = true;
		}
    	return flg;
    }

    //入力された値がDBの情報と差異があるかどうかをチェックする
    //差異が一つでもあればtrue 同じであればfalse
    function inputComparisonCheck(dbMaterial,updateMaterial){

    	var flg = false;

    	//materialIdが同じであれば個々の値を判定していく
    	if(materialIdComparison(dbMaterial["materialId"],updateMaterial["materialId"])){

    		var noFlg     = false;
    		var nameFlg   = false;
    		var lengthFlg = false;
    		var unitFlg   = false;

    		//materialNoのdbの値と入力された値に違いがあればtrue
    		if(!materialNoComparison(dbMaterial["materialNo"],updateMaterial["materialNo"])){

    			noFlg = true;

    		}

    		//materialNameのdbの値と入力された値に違いがあればtrue
    		if(!materialNameComparison(dbMaterial["materialName"],updateMaterial["materialName"])){

    			nameFlg = true;

    		}

    		//lengthのdbの値と入力された値に違いがあればtrue
			if(!lengthComparison(dbMaterial["length"],updateMaterial["length"])){

				lengthFlg = true;

			}

			//unitのdbの値と入力された値に違いがあればtrue
			if(!unitComparison(dbMaterial["unit"],updateMaterial["unit"])){

				unitFlg = true;

			}

			//入力された値のうちdbと一つでも差異があればflgをtrueにする
			if(noFlg || nameFlg || lengthFlg || unitFlg){
				flg = true;
			}
		}

    	return flg;
    }
