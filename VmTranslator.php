<?php
ini_set('memory_limit', '512M'); 
// https://drive.google.com/file/d/19fe1PeGnggDHymu4LlVY08KmDdhMVRpm/view

require_once(__DIR__ .'/CodeWriter.php');
require_once(__DIR__.'/Parser.php');

$in  = './projects/08/ProgramFlow/FibonacciSeries/FibonacciSeries.vm';
$out = './projects/08/ProgramFlow/FibonacciSeries/FibonacciSeries.asm';
$p = new Parser($in);
$w = new CodeWriter($out);



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
   }


   if($p->hasMoreCommands()){
      $p->advance();
   }else{
      break;
   }
}
$w->output();