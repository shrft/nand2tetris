<?php 

class CodeWriter{
    private $outFile = '';
    private $commands = [];
    function __construct($outputFile){
        $this->outFile = $outputFile;
        file_put_contents($this->outFile,"");
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
    private function addLine(){
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
    private function selectCurrentMemoryAddress(){
        $this->comment('<<Select Current Memory Address');
        $this->addCommands([
            '@0',
            'A=M',
        ]);
        $this->comment('Select Current Memory Address>>');
    }
    private function comment($comment){
        $this->addCommands(['// ' . $comment]);
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
        $this->selectCurrentMemoryAddress();
        $this->addCommands(['D=M']);
        $this->comment('Pop to D>>');
    }
    private function addCurrentStackValueAndD(){
        $this->comment('<<Add current stack value and D');
        $this->selectCurrentMemoryAddress();
        $this->addCommands(['M=D+M']);
        $this->comment('Add current stack value and D>>');
    }
    private function subDfromCurrentStackValue(){
        $this->comment('<<Subtract D from current stack');
        $this->selectCurrentMemoryAddress();
        $this->addCommands(['M=M-D']);
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
        $this->comment("<<ADD at {$this->getLine()} ");
        $this->decrementSp();
        $this->popToD();
        $this->decrementSp();
        $this->addCurrentStackValueAndD();
        $this->incrementSp();
        $this->comment('ADD>>');
        $this->addLine();
    }
    private function sub(){
        $this->comment("<<SUB  at {$this->getLine()}");
        $this->decrementSp();
        $this->popToD();
        $this->decrementSp();
        $this->subDfromCurrentStackValue();
        $this->incrementSp();
        $this->comment('SUB>>');
        $this->addLine();
    }
    private function neg(){
        $this->comment("<<NEG at {$this->getLine()} ");
        $this->decrementSp();
        $this->selectCurrentMemoryAddress();
        $this->addCommands(['M=-M']);
        $this->incrementSp();
        $this->comment('NEG>>');
        $this->addLine();
    }
    private function eq(){
        $this->comment("<<EQ at {$this->getLine()} ");
        $this->compare('JEQ');
        $this->comment('EQ>>');
        $this->addLine();
    }
    private function gt(){
        $this->comment("<<GT  at {$this->getLine()} ");
        $this->compare('JLT');
        $this->comment('GT>>');
        $this->addLine();
    }
    private function lt(){
        $this->comment("<<LT  at {$this->getLine()} ");
        $this->compare('JGT');
        $this->comment('LT>>');
        $this->addLine();
    }
    private function and(){
        $this->comment("<<AND at {$this->getLine()} ");
        // select y
        $this->decrementSp();
        $this->popToD();
        $this->decrementSp();
        $this->selectCurrentMemoryAddress();
        $this->addCommands(['M=D&M']);
        $this->incrementSp();
        $this->comment('AND>>');
        $this->addLine();
    }
    private function or(){
        $this->comment("<<OR at {$this->getLine()} ");
        // select y
        $this->decrementSp();
        $this->popToD();
        $this->decrementSp();
        $this->selectCurrentMemoryAddress();
        $this->addCommands(['M=D|M']);
        $this->incrementSp();
        $this->comment('OR>>');
        $this->addLine();
    }
    private function not(){
        $this->comment("<<NOT at {$this->getLine()} ");
        $this->decrementSp();
        $this->selectCurrentMemoryAddress();
        $this->addCommands(['M=!M']);
        $this->incrementSp();
        $this->comment('NOT>>');
        $this->addLine();
    }
    private function compare($type){
        $this->decrementSp();
        $this->popToD();
        $this->decrementSp();

        $this->selectCurrentMemoryAddress();
        // y-x
        $this->addCommands(['D=D-M']);

        $this->addIfElse(
            "D;$type",
            function(){
                $this->comment('set to true');
                $this->selectCurrentMemoryAddress();
                $this->addCommands(['M=-1']);
            },
            function(){
                $this->comment(['set to false']);
                $this->selectCurrentMemoryAddress();
                $this->addCommands(['M=0']);
            }
        );
        $this->incrementSp();
    }
    private function pushConstant($value){
        $this->comment("<<Push constant $value to stack at {$this->getLine()} ");
        $this->setD($value);
        $this->pushDtoStack();
        $this->incrementSp();
        $this->comment("Push constant $value to stack>>");
        $this->addLine();
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
        }
    }
    // write to file
    function output(){
        // array_unshift($data, '');
        print_r($this->commands);
        foreach($this->commands as $d){
            file_put_contents($this->outFile,$d."\n",FILE_APPEND);
        }
    }
}

// $w = new CodeWriter("./tmp.txt");
// $w->writeArithmetic("and");
// $w->output();