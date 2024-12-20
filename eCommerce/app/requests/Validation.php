<?php
include_once __DIR__."\..\database\config.php";

class Validation {
    private $name;
    private $value;
    public function __construct($name,$value) {
        $this->name = $name;
        $this->value = $value;
    }

    public function required() : string {
        
        return (empty($this->value)) ? "$this->name is required" : "";
    }

    public function regex($pattern) : string {
        
        return (preg_match($pattern,$this->value)) ? "" : "$this->name is invalid";
    }

    public function unique($table) : string {
        
        $query = "SELECT * FROM `$table` WHERE `$this->name` = '$this->value'";
        $config = new config;
        $result = $config->runDQL($query);
        return (empty($result)) ? "" : "This $this->name is already exists";
    }

    public function confirmed($valueConfirmation) : string {
        
        return ($this->value == $valueConfirmation) ? "" : "$this->name is not confirmed";
    }

    public function integers() : string {

        return (is_numeric($this->value)) ? "" : "$this->name must be numbers";
    }

    public function digits() : string {

        return (strlen($this->value) == 5) ? "" : "$this->name can't be more than 5 digits";
    }

}