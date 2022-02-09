<?php

class Parser 
{
    private $commands;
    private $pos = 0;

    function __construct($file){
        $rows = file($file);
        foreach($rows as $row){
            $row = trim($row);
            if($row !== "" && strpos($row, '//') !== 0){
                $this->commands[] = $row;
            }
        }
    }
    function startsWith($string, $searchWord){
        return strpos($string, $searchWord) === 0;
    }
    function contains($string, $searchWord){
        return strpos($string, $searchWord) !== false;
    }
    function current(){
        return $this->commands[$this->pos];
    }
    function hasMoreCommands(){
        return array_key_exists($this->pos+1, $this->commands );
    }
    function advance(){
        $this->pos++;
    }
    function commandType(){
        // TODO: add command type L later;
        if($this->startsWith($this->current(), '@')){
            return "A";
        }
        return "C";
    }
    function symbol(){
        return str_replace('@','',$this->current());
        // return xxx of @xxx
    }
    function dest(){
        if($this->contains($this->current(), "=")){
            return explode("=", $this->current())[0];
        }
    }
    function comp(){
        $equalPos = strpos($this->current(), "=");
        $semiColonPos = strpos($this->current(), ";");

        $start = $equalPos?$equalPos+1:0;
        $end   = $semiColonPos?:strlen($this->current())+1;

        $length = $end - $start;
        // print_r([$start, $end, $length]);
        // print_r()
        return substr($this->current(), $start, $length);
    }
    function jump(){
        $semiColonPos = strpos($this->current(), ";");
        if($semiColonPos !== false){
            return substr($this->current(), $semiColonPos+1);
        }
    }
}

// $file = "./../add/Add.asm";
// $file = "./../max/MaxL.asm";
// echo "reading file $file...\n";
// $parser = new Parser($file);

// while(true){
//     if($parser->commandType() == "C"){
//         echo $parser->jump() . "\n";
//     }

//     if($parser->hasMoreCommands()){
//         $parser->advance();
//     }else{
//         exit;
//     }
// }