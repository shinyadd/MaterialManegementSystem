<?php
class machine{
    private $machineId;
    private $machineName;
    private $machineNo;
    private $workingForm;
    private $makingProduct;

   //機械の情報を取得する
    public function getId(){
        return $this->machineId;
    }

    public function getName(){
        return $this->machineName;
    }

    public function getNo(){
        return $this->machineNo;
    }

    public function getWorking(){
        return $this->workingForm;
    }

    public function getMaking(){
        return $this->makingProduct;
    }

    //機械の情報をセットする
    public function setId($Id) {
        $this->machineId = $Id;
    }

    public function setName($Name) {
        $this->machineName = $Name;
    }

    public function setNo($No) {
        $this->machineNo = $No;
    }

    public function setWorkingForm($wf) {
        $this->workingForm = $wf;
    }

    public function setMakingProduct($mp) {
        $this->makingProduct = $mp;
    }

    //DBから取得した結果をセットする
    public function setDbSet($result){
        $this->machineId = $result[0]["MACHINE_ID"];
        $this->machineName = $result[0]["MACHINE_NAME"];
        $this->machineNo = $result[0]["MACHINE_NO"];
        $this->workingForm = $result[0]["WORKING_FROM"];
        $this->makingProduct = $result[0]["MAKING_PRODUCT"];
    }
}