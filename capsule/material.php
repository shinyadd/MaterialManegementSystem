<?php
class machine{
    private $materialId;
    private $materialName;
    private $materialNo;
    private $sheetLength;
    private $unit;
    private $bikou;

    //機械の情報を取得する
    public function getId(){
        return $this->materialId;
    }

    public function getName(){
        return $this->machineName;
    }

    public function getNo(){
        return $this->materialNo;
    }

    public function getSheet(){
        return $this->sheetLength;
    }

    public function getUnit(){
        return $this->unit;
    }

    public function getBikou(){
        return $this->bikou;
    }

    //機械の情報をセットする
    public function setId($Id) {
        $this->materialId = $Id;
        ;
    }

    public function setName($Name) {
        $this->materialName = $Name;
        ;
    }

    public function setNo($No) {
        $this->materialNo = $No;
        ;
    }

    public function setSheet($sheet) {
        $this->sheetLength = $sheet;
        ;
    }

    public function setUnit($unit) {
        $this->unit = $unit;
        ;
    }

    public function setBikou($bikou) {
        $this->bikou = $bikou;
        ;
    }

    //DBから取得した結果をセットする
    public function setDbSet($result){
        $this->materialId = $result[0]["MATERIAL_ID"];
        $this->materialName = $result[0]["MATERIAL_NAME"];
        $this->materialNo = $result[0]["MATERIAL_NO"];
        $this->sheetLength = $result[0]["SHEET_LENGTH"];
        $this->unit = $result[0]["UNIT"];
        $this->bikou = $result[0]["BIKOU"];
    }
}