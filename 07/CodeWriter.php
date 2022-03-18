<?php 

class CodeWriter{
    private $outFile = '';
    private $commands = [];
    private $hierarchy = 0;
    function __construct($outputFile){
        $this->outFile = $outputFile;
        $this->outFileH = $outputFile . "h";
        file_put_contents($this->outFile,"");
        file_put_contents($this->outFileH,"");
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
                $this->addCommands(['M=-1']);
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
            }
            if($segment == 'local'){
                $this->pushLocal($index);
            }
            if($segment == 'argument'){
                $this->pushArgument($index);
            }
            if($segment == 'this'){
                $this->pushThis($index);
            }
            if($segment == 'that'){
                $this->pushThat($index);
            }
            if($segment == 'temp'){
                $this->pushTemp($index);
            }
        }
        if($command == 'C_POP'){
            if($segment == 'local'){
                $this->popLocal($index);
            }
            if($segment == 'argument'){
                $this->popArgument($index);
            }
            if($segment == 'this'){
                $this->popThis($index);
            }
            if($segment == 'that'){
                $this->popThat($index);
            }
            if($segment == 'temp'){
                $this->popTemp($index);
            }
        }
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