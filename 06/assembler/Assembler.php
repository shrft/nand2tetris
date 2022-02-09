<?php
include "./Code.php";
include "./Parser.php";

// $file = "./../add/Add.asm";
// $file = "./../max/MaxL.asm";
// $file = "./../pong/PongL.asm";
$file = "./../rect/RectL.asm";

$p = new Parser($file);
$c = new Code();
// この辺から
while(true){
    if($p->commandType() == "A"){
        echo str_pad(decbin($p->symbol()),16,"0",STR_PAD_LEFT)."\n";
    }else{
        // c instruction
        echo "111" . $c->comp($p->comp()) . $c->dest($p->dest()) . $c->jump($p->jump()) . "\n";
    } 

    if($p->hasMoreCommands()){
        $p->advance();
    }else{
        exit;
    }
}