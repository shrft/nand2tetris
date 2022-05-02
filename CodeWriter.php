<?php 

class CodeWriter{
    private $outFile = '';
    private $commands = [];
    private $hierarchy = 0;
    private $fileName = '';
    private $currentFunction = [];
    function __construct($outputFile){
        $this->outFile = $outputFile;
        $this->outFileH = $outputFile . "h";
        file_put_contents($this->outFile,"");
        file_put_contents($this->outFileH,"");
    }
    public function init(){
        // set SP to 256
        $this->addCommands([
            '@256',
            'D=A',
            '@0',
            'M=D'
        ]);
        // call sys.init
        $this->addCommands([
            '@Sys.init',
            '0;JMP'
        ]);
    }
    public function addBreakPoint(){
        $this->addCommands([
            '@24576'
        ]);
    }
    private function startsWith($string, $searchWord){
        return strpos($string, $searchWord) === 0;
    }

    private function addCommands($commands){
        $this->commands = array_merge($this->commands, $commands);
    }
    private function addIfElse($condition, $trueCommands, $falseCommands){
        $ln = $this->getLine();
        $l_if_true = "IF_TRUE_$ln";
        $l_if_false = "IF_FALSE_$ln";
        $l_if_end = "IF_END_$ln";

        $this->addCommands(["@$l_if_true"]);
        $this->addCommands([$condition]);

        $this->addCommands(["($l_if_false)"]);
        // $this->addCommands($falseCommands);
        $falseCommands();
        $this->addCommands(["@$l_if_end"]);
        $this->addCommands(['0;JMP']);

        $this->addCommands(["($l_if_true)"]);
        // $this->addCommands($trueCommands);
        $trueCommands();

        $this->addCommands(["($l_if_end)"]);

    }
    private function addLineIfRootCommand(){
        if($this->hierarchy > 0) return;
        $this->addCommands(['']);
    }
    private function incrementSp(){
        $this->comment('<<Increment sp');
        $this->addCommands([
                '@0',
                'M=M+1',
        ]);
        $this->comment('Increment sp>>');
    }
    private function decrementSp(){
        $this->comment('<<Decrement sp');
        $this->addCommands(
            [
                '@0',
                'M=M-1'
            ]
        );
        $this->comment('Decrement sp>>');
    }
    private function selectMemorySPPointTo(){
        $this->comment('<<Select Current Memory Address');
        $this->addCommands([
            '@0',
            'A=M',
        ]);
        $this->comment('Select Current Memory Address>>');
    }
    private function comment($comment){
        $this->addCommands(['// ' . str_repeat(' ',$this->hierarchy).$comment]);
    }
    private function pushDToStack(){
        $this->comment('<<Push d to stack');
        $this->addCommands([
            '@0',
            'A=M',
            'M=D'
        ]);
        $this->comment('Push d to stack>>');
    }
    private function setD($value){
        $this->comment("<<Set $value to D");
        $this->addCommands([
                "@$value",
                'D=A'
        ]);
        $this->comment("Set $value to D>>");
    }
    private function popToD(){
        $this->comment('<<Pop to D');
        $this->hierarchy++;
        $this->selectMemorySPPointTo();
        $this->hierarchy--;
        $this->addCommands(['D=M']);
        $this->comment('Pop to D>>');
    }
    private function addCurrentStackValueAndD(){
        $this->comment('<<Add current stack value and D');
        $this->hierarchy++;
        $this->selectMemorySPPointTo();
        $this->addCommands(['M=D+M']);
        $this->hierarchy--;
        $this->comment('Add current stack value and D>>');
    }
    private function subDfromCurrentStackValue(){
        $this->comment('<<Subtract D from current stack');
        $this->hierarchy++;
        $this->selectMemorySPPointTo();
        $this->addCommands(['M=M-D']);
        $this->hierarchy--;
        $this->comment('Subtract D from current stack>>');
    }
    private function getCommandCount(){
        $count=0;
        foreach($this->commands as $c){
            if($c=='')continue;
            if($this->startsWith($c, '(')) continue;
            if(!$this->startsWith($c, '//')) $count++;
        }
        return $count;
    }
    private function getLine(){
        return $this->getCommandCount();
    }

    // private function getNumOfArg(){
    //     $size = count($this->commands);
    //     $lastIndex = $size-1;
    //     for($i=0; $i<$size; $i++){
    //         $row = $this->commands[$lastIndex-$i];
    //         if($this->startsWith($row, 'function')){
    //             return explode(" ",$row)[2];
    //         }
    //     }
    //     throw new Exception('function not found');
    // }
 
    private function add(){
        $this->comment("<<ADD ");
        $this->hierarchy++;
        $this->decrementSp();
        $this->popToD();
        $this->decrementSp();
        $this->addCurrentStackValueAndD();
        $this->incrementSp();
        $this->hierarchy--;
        $this->comment('ADD>>');
        $this->addLineIfRootCommand();
    }
    private function sub(){
        $this->comment("<<SUB");
        $this->hierarchy++;
        $this->decrementSp();
        $this->popToD();
        $this->decrementSp();
        $this->subDfromCurrentStackValue();
        $this->incrementSp();
        $this->hierarchy--;
        $this->comment('SUB>>');
        $this->addLineIfRootCommand();
    }
    private function neg(){
        $this->comment("<<NEG");
        $this->hierarchy++;
        $this->decrementSp();
        $this->selectMemorySPPointTo();
        $this->addCommands(['M=-M']);
        $this->incrementSp();
        $this->hierarchy--;
        $this->comment('NEG>>');
        $this->addLineIfRootCommand();
    }
    private function eq(){
        $this->comment("<<EQ");
        $this->hierarchy++;
        $this->compare('JEQ');
        $this->hierarchy--;
        $this->comment('EQ>>');
        $this->addLineIfRootCommand();
    }
    private function gt(){
        $this->comment("<<GT");
        $this->hierarchy++;
        $this->compare('JLT');
        $this->hierarchy--;
        $this->comment('GT>>');
        $this->addLineIfRootCommand();
    }
    private function lt(){
        $this->comment("<<LT");
        $this->hierarchy++;
        $this->compare('JGT');
        $this->hierarchy--;
        $this->comment('LT>>');
        $this->addLineIfRootCommand();
    }
    private function and(){
        $this->comment("<<AND  ");
        // select y
        $this->hierarchy++;
        $this->decrementSp();
        $this->popToD();
        $this->decrementSp();
        $this->selectMemorySPPointTo();
        $this->addCommands(['M=D&M']);
        $this->incrementSp();
        $this->hierarchy--;
        $this->comment('AND>>');
        $this->addLineIfRootCommand();
    }
    private function or(){
        $this->comment("<<OR  ");
        $this->hierarchy++;
        $this->decrementSp();
        $this->popToD();
        $this->decrementSp();
        $this->selectMemorySPPointTo();
        $this->addCommands(['M=D|M']);
        $this->incrementSp();
        $this->hierarchy--;
        $this->comment('OR>>');
        $this->addLineIfRootCommand();
    }
    private function not(){
        $this->comment("<<NOT  ");
        $this->hierarchy++;
        $this->decrementSp();
        $this->selectMemorySPPointTo();
        $this->addCommands(['M=!M']);
        $this->incrementSp();
        $this->hierarchy--;
        $this->comment('NOT>>');
        $this->addLineIfRootCommand();
    }
    private function compare($type){
        $this->hierarchy++;
        $this->decrementSp();
        $this->popToD();
        $this->decrementSp();

        $this->selectMemorySPPointTo();
        // y-x
        $this->addCommands(['D=D-M']);

        $this->addIfElse(
            "D;$type",
            function(){
                $this->comment('set to true');
                $this->selectMemorySPPointTo();
                $this->addCommands([
                    'M=1'
                ]);
            },
            function(){
                $this->comment(['set to false']);
                $this->selectMemorySPPointTo();
                $this->addCommands(['M=0']);
            }
        );
        $this->incrementSp();
        $this->hierarchy--;
    }
    private function pushConstant($value){
        $this->comment("<<Push constant $value to stack  ");
        $this->hierarchy++;
        $this->setD($value);
        $this->pushDtoStack();
        $this->incrementSp();
        $this->hierarchy--;
        $this->comment("Push constant $value to stack>>");
        $this->addLineIfRootCommand();
    }
    private function pushLocal($index){
        $this->pushFrom('local', 1, $index);
    }
    private function pushArgument($index){
        $this->pushFrom('argument', 2, $index);
    }
    private function pushThis($index){
        $this->pushFrom('this', 3, $index);
    }
    private function pushThat($index){
        $this->pushFrom('that', 4, $index);
    }
    private function pushPointer($index){
        // 0: this
        // 1: that
        $this->comment("<<Push pointer[$index] to stack");

        if($index == 0){
            $address = 3;
        }else{
            $address = 4;
        }
        $this->addCommands([
            "@$address",
            "D=M",
        ]);
        $this->selectMemorySPPointTo();
        $this->addCommands([
            'M=D'
        ]);
        $this->incrementSp();
        $this->comment("Push pointer[$index] to stack>>");
    }
    private function pushStatic($index){
        $this->comment("<<Push from static[$index]");
        $this->addCommands([
            "@{$this->fileName}.$index",
            'D=M',
            'A=0',
            'A=M',
            'M=D'
        ]);
        $this->incrementSp();
        $this->comment("Push from static[$index]>>");
    }
    private function popStatic($index){
        $this->comment("<<Pop to static[$index]");
        $this->decrementSp();
        $this->addCommands([
            'A=0',
            'A=M',
            'D=M',
            "@{$this->fileName}.$index",
            'M=D',
        ]);
        $this->comment("Pop to static[$index]>>");
    }
    private function popPointer($index){
        // 0: this
        // 1: that
        $this->comment("<<Pop to pointer[$index]");

        if($index == 0){
            $address = 3;
        }else{
            $address = 4;
        }
        $this->decrementSp();
        $this->selectMemorySPPointTo();
        $this->addCommands([
            'D=M'
        ]);

        $this->addCommands([
            "@$address",
            "M=D",
        ]);
        
        $this->comment("Pop to pointer[$index]>>");
    }
    private function pushTemp($index){
        $segmentName = 'temp';
        $this->comment("<<Push $segmentName\[$index] to stack  ");
        $this->hierarchy++;

        // add temp start pos to stack
        $this->addCommands([
            "@5",
            'D=A',
        ]);
        $this->selectMemorySPPointTo();
        $this->addCommands([
            'M=D'
        ]);
        $this->incrementSp();

        // add index to stack
        $this->pushConstant($index);
        $this->add();

        // put local target pos to D.
        $this->decrementSp();
        $this->popToD();

        // put local value to D
        $this->addCommands([
            'A=D',
            'D=M',
        ]);

        // push local value to stack
        $this->pushDToStack();

        $this->incrementSp();
        $this->hierarchy--;
        $this->comment("Push $segmentName\[$index] to stack>>");
        $this->addLineIfRootCommand();

    }
    private function pushFrom($segmentName,$startPos, $index){
        $this->comment("<<Push $segmentName\[$index] to stack  ");
        $this->hierarchy++;

        // add local start pos to stack
        $this->addCommands([
            "@$startPos",
            'D=M',
        ]);
        $this->selectMemorySPPointTo();
        $this->addCommands([
            'M=D'
        ]);
        $this->incrementSp();

        // add index to stack
        $this->pushConstant($index);
        $this->add();

        // put local target pos to D.
        $this->decrementSp();
        $this->popToD();

        // put local value to D
        $this->addCommands([
            'A=D',
            'D=M',
        ]);

        // push local value to stack
        $this->pushDToStack();

        $this->incrementSp();
        $this->hierarchy--;
        $this->comment("Push $segmentName\[$index] to stack>>");
        $this->addLineIfRootCommand();
    }
    private function popTo($segmentName, $startPos, $index){
        $this->comment("<<Pop to $segmentName\[$index] from stack  ");
        $this->hierarchy++;

        // Add start pos to SP
        $this->addCommands([
            "@$startPos",
            'D=M',
        ]);
        $this->selectMemorySPPointTo();
        $this->addCommands([
            'M=D'
        ]);
        $this->incrementSp();

        $this->pushConstant($index);
        $this->add();

        $this->decrementSp();
        $this->decrementSp();
        $this->selectMemorySPPointTo();
        $this->popToD();
        // => D = popped value

        $this->incrementSp();
        $this->selectMemorySPPointTo();
        $this->addCommands([
            'A=M',
            'M=D'
        ]);
        $this->decrementSp();
        $this->hierarchy--;
        
        $this->comment("Pop to $segmentName\[$index] from stack>>");
        $this->addLineIfRootCommand();
    }
    private function popLocal($index){
        $this->popTo('local', 1, $index);
    }
    private function popArgument($index){
        $this->popTo('argument', 2, $index);
    }
    private function popThis($index){
        $this->popTo('this', 3, $index);
    }
    private function popThat($index){
        $this->popTo('that', 4, $index);
    }
    private function popTemp($index){
        $segmentName = 'temp';
        $this->comment("<<Pop to $segmentName\[$index] from stack  ");
        $this->hierarchy++;

        // Add start pos to SP
        $this->addCommands([
            "@5",
            'D=A',
        ]);
        $this->selectMemorySPPointTo();
        $this->addCommands([
            'M=D'
        ]);
        $this->incrementSp();

        $this->pushConstant($index);
        $this->add();

        $this->decrementSp();
        $this->decrementSp();
        $this->selectMemorySPPointTo();
        $this->popToD();
        // => D = popped value

        $this->incrementSp();
        $this->selectMemorySPPointTo();
        $this->addCommands([
            'A=M',
            'M=D'
        ]);
        $this->decrementSp();
        $this->hierarchy--;
        
        $this->comment("Pop to $segmentName\[$index] from stack>>");
        $this->addLineIfRootCommand();

    }
    function writeArithmetic($command){
        // add, sub, neg, eq, gt, lt, and, or, not
        $this->$command();
    }
    function writePushPop($command, $segment, $index){
        if($command == 'C_PUSH'){
            if($segment == 'constant'){
                $this->pushConstant($index);
            }else
            if($segment == 'local'){
                $this->pushLocal($index);
            }else
            if($segment == 'argument'){
                $this->pushArgument($index);
            }else
            if($segment == 'this'){
                $this->pushThis($index);
            }else
            if($segment == 'that'){
                $this->pushThat($index);
            }else
            if($segment == 'temp'){
                $this->pushTemp($index);
            }else
            if($segment == 'pointer'){
                $this->pushPointer($index);
            }else
            if($segment == 'static'){
                $this->pushStatic($index);
            }else{
                throw new Exception('undefined segment:' . $segment);
            }
        }
        if($command == 'C_POP'){
            if($segment == 'local'){
                $this->popLocal($index);
            }else
            if($segment == 'argument'){
                $this->popArgument($index);
            }else
            if($segment == 'this'){
                $this->popThis($index);
            }else
            if($segment == 'that'){
                $this->popThat($index);
            }else
            if($segment == 'temp'){
                $this->popTemp($index);
            }else
            if($segment == 'pointer'){
                $this->popPointer($index);
            }else 
            if($segment == 'static'){
                $this->popStatic($index);
            }else{
                throw new Exception('undefined segment:' . $segment);
            }
        }
    }
    function writeLabel($label){
        $this->addCommands([
           "($label)" 
        ]);
    }
    function writeGoto($label){
        $this->comment("<<write Goto for $label");
        $this->addCommands([
            "@$label",
            '0;JEQ'
        ]);
        $this->comment("write Goto for $label>>");
    }
    function writeIfGoto($label){
        $this->comment("<<write writeIfGoto for $label");
        $this->decrementSp();
        $this->popToD();
        $this->addCommands([
            "@$label",
            'D;JGT',
        ]);
        $this->comment("write writeIfGoto for $label>>");
    }
    function setFileName($name){
        $this->fileName = $name;
    }
    function writeFunction($name, $numOfArgs){
        // https://drive.google.com/file/d/1lBsaO5XKLkUgrGY6g6vLMsiZo6rWxlYJ/view
        $this->comment("<<write function $name");
        // array_push($this->currentFunction, $name);
        $this->addCommands([
            "($name)"
        ]);
        for($i=0; $i < $numOfArgs; $i++){
            $this->pushConstant(0);
        }
        $this->comment("write function $name>>");
    }
    function writeReturn(){

        $funcName = array_pop($this->currentFunction);
        $this->comment("<<write return for $funcName");


        $this->addCommands([
            '@LCL',
            'D=M',
            '@localBefore',
            'M=D'
        ]);

        // save return address
        $this->addCommands([
            '@localBefore',
            'D=M-1',
            'D=D-1',
            'D=D-1',
            'D=D-1',
            'A=D-1',
            'D=M',
            '@returnAddress',
            'M=D'
        ]);

        $this->decrementSp();
        $this->popToD();

        $this->comment('Copies the return value onto argument 0');
        $this->addCommands([
            '@ARG',
            'A=M',
            'M=D'
        ]);

        

        $this->comment('Sets SP for the caller');
        $this->addCommands([
            '@ARG',
            'D=M+1',
            '@SP',
            'M=D'
        ]);

        $this->comment('Restores that');
        $this->addCommands([
            '@LCL',
            'A=M-1',
            'D=M',
            '@THAT',
            'M=D'
        ]);

        $this->comment('restore this.');
        $this->addCommands([
            '@LCL',
            'D=M-1',
            'A=D-1',
            'D=M',
            '@THIS',
            'M=D'
        ]);
        $this->comment('restore local value');

        

        $this->comment('restore arg');
        $this->addCommands([
            '@localBefore',
            'D=M-1',
            'D=D-1',
            'A=D-1',
            'D=M',
            '@ARG',
            'M=D'
        ]);

        $this->comment('restore local');
        $this->addCommands([
            '@localBefore',
            'D=M-1',
            'D=D-1',
            'D=D-1',
            'A=D-1',
            'D=M',
            '@LCL',
            'M=D'
        ]);

        $this->comment('Jumps to the return address within the caller’s code');
        $this->addCommands([
            '@returnAddress',
            'A=M',
            '0;JMP'
        ]);

        $this->comment("write return for $funcName>>");
        $this->addLineIfRootCommand();
    }
    function writeCall($name, $nArgs){
        $this->comment("<<write call $name");

        $this->comment('push return address');
        $line = $this->getLine();
        $this->addCommands([
           "@$name.ret.$line",
           'D=A',
        ]);
        $this->pushDToStack();
        $this->incrementSp();

        $this->comment('push LCL');
        $this->addCommands([
            '@LCL',
            'D=M'
        ]);
        $this->pushDToStack();
        $this->incrementSp();

        $this->comment('push ARG');
        $this->addCommands([
            '@ARG',
            'D=M',
        ]);
        $this->pushDToStack();
        $this->incrementSp();

        $this->comment('push THIS');
        $this->addCommands([
            '@THIS',
            'D=M'
        ]);
        $this->pushDToStack();
        $this->incrementSp();

        $this->comment('push THAT');
        $this->addCommands([
            '@THAT',
            'D=M'
        ]);
        $this->pushDToStack();
        $this->incrementSp();

        $this->comment('reposition arg');
        $this->addCommands([
            '@5',
            'D=A',
            "@$nArgs",
            'D=D+A',
            '@SP',
            'D=M-D',
            '@ARG',
            'M=D'
        ]);

        $this->comment('set LCL');
        $this->addCommands([
            '@SP',
            'D=M',
            '@LCL',
            'M=D'
        ]);

        $this->comment('go to function');
        $this->addCommands([
           "@$name",
           '0;JMP'
        ]);

        $this->writeLabel("$name.ret.$line");

        $this->comment("write call $name>>");
        $this->addLineIfRootCommand();
    }
    // write to file
    function output(){
        // array_unshift($data, '');
        print_r($this->commands);
        $lineNum = 0;
        foreach($this->commands as $d){
            if($this->isCommand($d)){
                $prefix = '['.str_pad($lineNum,3, ' ', STR_PAD_LEFT).'] ';
            }else{
                $prefix = '';
            }
            file_put_contents($this->outFile,$d."\n",FILE_APPEND);
            file_put_contents($this->outFileH,$prefix.$d."\n",FILE_APPEND);
            if($this->isCommand($d)){
                $lineNum++;
            }
        }
    }
    private function isCommand($c){
        return !($c=='' || $this->startsWith($c, '(') || $this->startsWith($c, '//'));
    }
}

// $w = new CodeWriter("./tmp.txt");
// $w->writeArithmetic("and");
// $w->output();