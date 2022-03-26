<?php

class Parser{
    private $commands;
    private $pos = 0;

    function __construct($file){
        $rows = file($file);
        foreach($rows as $row){
            $row = trim($row);
            if($row !== "" && strpos($row, '//') !== 0){
                if($this->contains($row, '//')){
                    $row = trim(explode('//',$row)[0]);
                }
                $this->commands[] = $row;
            }
        }
        print_r($this->commands);
    }
    private function startsWith($string, $searchWord){
        return strpos($string, $searchWord) === 0;
    }
    private function contains($string, $searchWord){
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
        if($this->startsWith($this->current(), 'push')){
            return 'C_PUSH';
        }
        if($this->startsWith($this->current(), 'pop')){
            return 'C_POP';
        }
        if($this->startsWith($this->current(), 'label')){
            return 'LABEL';
        }
        if($this->startsWith($this->current(), 'if-goto')){
            return 'IFGOTO';
        }
        if($this->startsWith($this->current(), 'goto')){
            return 'GOTO';
        }
        if(in_array(
            $this->current(),
            ['add','sub','neg','eq','gt','lt','and','or','not']
            )
        ){
            return 'C_ARITHMETIC';
        }
        throw new Exception('undefined command type.'. $this->current());
    }
    function arg1(){
        // echo $this->current()."\n";
        if($this->commandType() == 'C_ARITHMETIC'){
            return $this->current();
        }
        return explode(" ",$this->current())[1];
    }
    function arg2(){
        $types = [
            'C_PUSH','C_POP','C_FUNCTION','C_CALL'
        ];
        if(in_array($this->commandType(),$types)){
            return explode(" ",$this->current())[2];
        }else{
            echo 'arg2: skipping since the command is ' . $this->commandType() . ".\n";
        }
        // TODO: may need to add validation.
    }
}
// $file = '/Users/shirafuta/work/nand2tetris/projects/07/StackArithmetic/SimpleAdd/SimpleAdd.vm';
// $p = new Parser($file);
// echo "type:".$p->commandType(). "\n";
// echo "arg1:".$p->arg1() . "\n";
// echo "arg2:".$p->arg2() . "\n";
// while($p->hasMoreCommands()){
//     $p->advance();
//     echo "type:".$p->commandType(). "\n";
//     echo "arg1:".$p->arg1() . "\n";
//     echo "arg2:".$p->arg2() . "\n";
// }