<?php

class Code{
    function dest($s){
        if($s==="" || $s===null) return "000";
        if($s==="M") return "001";
        if($s==="D") return "010";
        if($s==="MD") return "011";
        if($s==="A") return "100";
        if($s==="AM") return "101";
        if($s==="AD") return "110";
        if($s==="AMD") return "111";
    }
    function comp($s){
        if($s==="0")  return "0101010";
        if($s==="1")  return "0111111";
        if($s==="-1") return "0111010";
        if($s==="D")  return "0001100";
        if($s==="A")  return "0110000";
        if($s==="!D") return "0001101";
        if($s==="!A") return "0110001";
        if($s==="-D") return "0001111";
        if($s==="-A") return "0110011";
        if($s==="D+1")return "0011111";
        if($s==="A+1")return "0110111";
        if($s==="D-1")   return "0001110";
        if($s==="A-1")   return "0110010";
        if($s==="D+A")   return "0000010";
        if($s==="D-A")   return "0010011";
        if($s==="A-D")   return "0000111";
        if($s==="D&A")   return "0000000";
        if($s==="D|A")   return "0010101";

        if($s==="M")   return   "1110000";
        if($s==="!M")   return  "1110001";
        if($s==="-M")   return  "1110011";
        if($s==="M+1")   return "1110111";
        if($s==="M-1")   return "1110010";
        if($s==="D+M")   return "1000010";
        if($s==="D-M")   return "1010011";
        if($s==="M-D")   return "1000111";
        if($s==="D&M")   return "1000000";
        if($s==="D|M")   return "1010101";
    }
    function jump($s){
        if($s===null || $s==="") return "000";
        if($s==="JGT") return "001";
        if($s==="JEQ") return "010";
        if($s==="JGE") return "011";
        if($s==="JLT") return "100";
        if($s==="JNE") return "101";
        if($s==="JLE") return "110";
        if($s==="JMP") return "111";
    }
}

// $code = new Code();
// echo $code->jump("JGT");