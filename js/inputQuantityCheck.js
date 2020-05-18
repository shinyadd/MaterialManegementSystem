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
		if(check.match(/^[0-9]*$/)){
			 flg = true;
		 }
		return flg;
    }

//////////////////////////使用数入力画面///////////////////////////////////////

