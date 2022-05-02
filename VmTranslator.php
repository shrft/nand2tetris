<?php
ini_set('memory_limit', '512M'); 
// https://drive.google.com/file/d/19fe1PeGnggDHymu4LlVY08KmDdhMVRpm/view

require_once(__DIR__ .'/CodeWriter.php');
require_once(__DIR__.'/Parser.php');


function compile($p, $w){
   while(true){
      if($p->commandType() == 'C_ARITHMETIC'){
         $w->writeArithmetic($p->current());
      }elseif($p->commandType() == 'C_PUSH'){
         $w->writePushPop($p->commandType(), $p->arg1(), $p->arg2());
      }else if($p->commandType() == 'C_POP') {
         $w->writePushPop($p->commandType(), $p->arg1(), $p->arg2());
      }else if($p->commandType() == 'LABEL'){
         $w->writeLabel($p->arg1());
      }else if($p->commandType() == 'IFGOTO'){
         $w->writeIfGoto($p->arg1());
      }else if($p->commandType() == 'GOTO'){
         $w->writeGoto($p->arg1());
      }else if($p->commandType() == 'FUNC'){
         $w->writeFunction($p->arg1(), $p->arg2());
      }else if($p->commandType() == 'RETURN'){
         $w->writeReturn($p->arg1(), $p->arg2());
      }else if($p->commandType() == 'C_CALL'){
         $w->writeCall($p->arg1(), $p->arg2());
      }else{
         throw new Exception('unknown command type');
      }

      if($p->hasMoreCommands()){
         $p->advance();
      }else{
         break;
      }
   }

}

$in  = './projects/08/FunctionCalls/FibonacciElement/';
$out = './projects/08/FunctionCalls/FibonacciElement/FibonacciElement.asm';

$files = glob("$in*.vm");
// var_dump($files);exit();
$w = new CodeWriter($out);
$w->init();

foreach($files as $f){
   $p = new Parser($f);
   compile($p, $w);
}

$w->output();