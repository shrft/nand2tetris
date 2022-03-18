<?php
// https://drive.google.com/file/d/19fe1PeGnggDHymu4LlVY08KmDdhMVRpm/view

require_once('./CodeWriter.php');
require_once('./Parser.php');

$in  = './MemoryAccess/BasicTest/BasicTest.vm';
$out = './MemoryAccess/BasicTest/BasicTest.asm';
$p = new Parser($in);
$w = new CodeWriter($out);



while(true){
   if($p->commandType() == 'C_ARITHMETIC'){
      $w->writeArithmetic($p->current());
   }elseif($p->commandType() == 'C_PUSH'){
      $w->writePushPop($p->commandType(), $p->arg1(), $p->arg2());
   }else if($p->commandType() == 'C_POP') {
      $w->writePushPop($p->commandType(), $p->arg1(), $p->arg2());
   }
   if($p->hasMoreCommands()){
      $p->advance();
   }else{
      break;
   }
}
$w->output();