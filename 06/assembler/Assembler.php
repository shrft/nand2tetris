<?php
include "./Code.php";
include "./Parser.php";
include "./SymbolTable.php";

// $file = "./../add/Add.asm";
// $file = "./../max/MaxL.asm";
// $file = "./../pong/PongL.asm";
// $file = "./../rect/RectL.asm";

// $file = "./../max/Max.asm";
// $file = "./../pong/Pong.asm";
$file = "./../rect/Rect.asm";


$p = new Parser($file);
$c = new Code();
$t = new SymbolTable();

// addLabelAddressesToTable();
$romAddress = 0;
while(true){

    if($p->commandType() == "L"){
        $t->addEntry($p->symbol(), $romAddress);
    }else{
        $romAddress++;
    }

    if($p->hasMoreCommands()){
        $p->advance();
    }else{
        break;
    }
}   
// print_r($t->getTable());
// exit();

$p = new Parser($file);
$nextRamAddress = 16;
while(true){
    if($p->commandType() == "A"){
        // echo $p->current()."\n";
        if(is_numeric($p->symbol())){
            echo str_pad(decbin($p->symbol()),16,"0",STR_PAD_LEFT)."\n";
        }else{
            // echo "hello";
            // echo $p->symbol()."\n";
            if($t->contains($p->symbol())){
                // echo $t->getAddress($p->symbol())."\n";
                // exit('h');
                echo str_pad(decbin($t->getAddress($p->symbol())),16,"0",STR_PAD_LEFT)."\n";

            }else{
                // exit('hey');
                echo str_pad(decbin($nextRamAddress),16,"0",STR_PAD_LEFT)."\n";
                $t->addEntry($p->symbol(), $nextRamAddress);
                $nextRamAddress++;
            }
        }
    }elseif($p->commandType() == "C"){
        // echo $p->current()."\n";
        // echo $p->comp()."\n";
        // echo $c->comp($p->comp())."\n";
        // exit();
        echo "111" . $c->comp($p->comp()) . $c->dest($p->dest()) . $c->jump($p->jump()) . "\n";
    }

    if($p->hasMoreCommands()){
        $p->advance();
    }else{
        exit;
    }
}