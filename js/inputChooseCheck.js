/**
 *
 */
//check＝チェックされる対象の値、$num＝最大値
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

	//半角数字かをチェックする
	function alphanumericCheck(check){
		var flg = false;
		check = (check==null)?"":check;
		if(check.match(/[1-9]|[1-9][0-9]/)){
			 flg = true;
		 }
		return flg;
    }

	//半角数字かをチェックする
	function numericCheck(check){
		var flg = false;
		check = (check==null)?"":check;
		if(!isNaN(check)){
			 flg = true;
		 }
		return flg;
    }

	////////////////////////////////////////////////////////////////
	//番号を入力した際、入力チェックを行う
	function orderCheck(check){

		var flg = false;

		if(nullCheck(check)){
			if(lengthCheck(check,2)){
				if(numericCheck(check)){
					flg = true;
				}else{
					alert("番号は2桁以内の半角数字で入力してください");
				}
			}else{
				alert("番号は2桁以内の半角数字で入力してください");
			}
		}else{
			alert("番号は2桁以内の半角数字で入力してください");
		}

		return flg;

	}

		//////////////////////////資材検索///////////////////////////////////////
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