<?php
// fswatch -o /Users/shirafuta/work/nand2tetris/projects/10/engine.php | xargs -n1 sh -c "clear && printf '\e[3J' |php engine.php| yq -p=xml -o=xml . || true"
// fswatch -o /Users/shirafuta/work/nand2tetris/projects/10/engine.php | xargs -n1 sh -c "php engine.php| yq -p=xml -o=xml .  > MainResult.xml"
require_once('tokenizer.php');

class GrammarException extends Exception{
    public function __construct($message, $code = 0, Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}
class Xml{
    private $xml;
    private $dom;
    private $nodeTree = [];
    static function make(){
        // return new Xml(new SimpleXMLElement('<class></class>'));
        $dom = new DOMDocument();
        // $doc = new DOMDocument();
        $dom->loadXml("<class><keyword>abc</keyword><symbol>;</symbol><keyword>def</keyword></class>");
        $dom->formatOutput = true;
        echo $dom->saveXml();
        exit();
        // $dom->formatOutput = false;
        $dom->formatOutput = false;
        $dom->preserveWhiteSpace = true;
        $element = $dom->createElement('class', '');
        $child = $dom->appendChild($element);
        // var_dump($dom);
        return new Xml($child, $dom);
    }
    function __construct($xml,$document){
        $this->dom = $document;
        $this->xml = $xml;
    }
    public function print(){
        echo $this->dom->saveXML();
    }
    public function addChild($key, $value){
        $child = $this->dom->createElement($key, $value);
        if($this->dom->lastChild){
            // $this->dom->lastChild->after($child);
            // print_r($this->dom->lastChild->nodeName);
            foreach($this->dom->childNodes as $node){
                print_r($this->dom->childNodes);
            }
            // exit()
            $this->xml->appendChild($child);
        }else{
                print_r($this->dom->childNodes);
            $this->xml->appendChild($child);
        }


        return $child;
    }
    public function addEmptyChildToXml($key){
        // return new Xml($this->addChild("<$key></$key>"));
        return new Xml($this->addChild("$key", ""), $this->dom);
    }
 
    public function addChildToXml($key, $value){
        $this->addChild($key, ' ' . $value . ' ');
    }
    public function addKeyword($value){
        $this->addChildToXml('keyword', $value);
    }
    public function addSymbol($value){
        $this->addChildToXml('symbol', $value);
    }
    public function addIdentifier($value){
        $this->addChildToXml('identifier', $value);
    }
    function getOriginal(){
        return $this->xml;
    }
    function __call($method, $arguments){
        return call_user_func_array([$this->xml, $method], $arguments);
    }

}

class CompilationEngine{
    private $in;
    private $out;
    private $tokenizer;
    public $xml;
    // private $t = $tokenizer;

    function __construct($in, $out){
        $this->in = $in;
        $this->out = $out;
        $this->tokenizer = new Tokenizer($this->in);
        // print_r($this->tokenizer->tokens);
        // $this->xml = new Xml(new SimpleXMLElement('<class></class>'));
        $this->xml = Xml::make();
        // $this->xml = new Xml();
    }
    // private function addEmptyChildToXml($key){
    //     return new Xml($this->addChild("<$key></$key>"));
    // }
 
    // private function addChildToXml($key, $value){
    //     $this->addChild($key, ' ' . $value . ' ');
    // }
    // private function addKeyword($value, $xml = null){
    //     $this->addChildToXml('keyword', $value, $xml);
    // }
    // private function addSymbol($value, $xml=null){
    //     $this->addChildToXml('symbol', $value, $xml);
    // }
    // private function addIdentifier($value, $xml=null){
    //     $this->addChildToXml('identifier', $value, $xml);
    // }
 
    function throwIfTokenDoesNotMatch($rule, $val){
        if($rule == $val){
            return true;
        }
        print_r([$rule, $val]);
        throw new GrammarException('token does not comform to the rules');

    }
    function compileClass(){
        $token = $this->tokenizer->currentToken();

        // if class, go next or throw error
        
        $this->throwIfTokenDoesNotMatch('class', $token);
       
        $this->xml->addKeyword($token);


        $this->tokenizer->advance();
        $token = $this->tokenizer->currentToken();

        // if identifier, go next or throw error
        if($this->tokenizer->isIdentifier($token)){
            $this->xml->addIdentifier($token);
        }else{
            throw new GrammarException('not identifier');
        }
        $this->tokenizer->advance();
        $token = $this->tokenizer->currentToken();

        // if {, go next or throw error
        $this->throwIfTokenDoesNotMatch('{', $token);
        $this->xml->addSymbol('{');


        $this->tokenizer->advance();
        $token = $this->tokenizer->currentToken();

        $this->compileClassVarDec();

        // var_dump($token);
        $this->tokenizer->advance();
        $token = $this->tokenizer->currentToken();

        // if compileSubroutineDec, go next or throw error
        $this->compileSubroutineDec();
        $this->xml->print();
        exit();



        $this->tokenizer->advance();

        $this->compileSubroutineBody();

        var_dump($token);
        $this->tokenizer->advance();
        $token = $this->tokenizer->currentToken();

        // if }, go next or throw error
        // var_dump($token);
        echo $this->xml->print();
        exit();

        // $this->throwIfTokenDoesNotMatch('}', $token);
        // exit();
    }
    function grammarError($val){
        throw new GrammarException($val);
    }
    function isTypeKeyword($val){
        return in_array($val, ['int','char','boolean']);
        // TODO: ClassName should be valid too.
    }
    function canBeClassName($val){
        return true;
    }
    function compileClassVarDec(){
        $xml = $this->xml->addEmptyChildToXml('classVarDec');
        // var_dump($xml);

        // static or field
        $token = $this->tokenizer->currentToken();
        if(!in_array($token, ['static','field'])){
            $this->grammarError($token);
        }
        $xml->addKeyword($token, $xml);

        $this->tokenizer->advance();
        $token = $this->tokenizer->currentToken();

        // int|char|boolean|className
        if(!$this->isTypeKeyword($token)){
            $this->grammarError('not a type:' . $token);
        }
        $xml->addKeyword($token, $xml);


        $this->tokenizer->advance();
        $token = $this->tokenizer->currentToken();

        // varName
        if(!$this->tokenizer->isIdentifier($token)){
            $this->grammarError('not identifier:', $token);
        }
        $xml->addIdentifier($token, $xml);

        $this->tokenizer->advance();
        $token = $this->tokenizer->currentToken();

        if(!in_array($token, [',',';'])) throw new GrammarException('not , or ;');

        while($token != ';'){
            if($token != ',') $this->grammarError('should be comma.');

            $xml->addSymbol($token);

            $this->tokenizer->advance();
            $token = $this->tokenizer->currentToken();

            if(!$this->tokenizer->isIdentifier($token)){
                $this->grammarError('not identifier:', $token);
            }
            $xml->addIdentifier($token);

            $this->tokenizer->advance();
            $token = $this->tokenizer->currentToken();
        }

        $xml->addSymbol($token);
        // exit('here');

    }
    function compileSubroutineDec(){
        $xml = $this->xml->addEmptyChildToXml('subroutineDec');
        $token = $this->tokenizer->currentToken();
        if(!in_array($token, ['constructor','function', 'method'])){
            $this->grammarError($token);
        }
        $xml->addKeyword($token);

        $this->tokenizer->advance();
        $token = $this->tokenizer->currentToken();

       if($this->isTypeKeyword($token)){
            $xml->addKeyword($token);
       }else{
           if($token != 'void'){
                $this->grammarError($token);
           }
           $xml->addKeyword($token);
       }

        $this->tokenizer->advance();
        $token = $this->tokenizer->currentToken();

        $xml->addIdentifier($token);

        // add ")"
        $this->tokenizer->advance();
        $token = $this->tokenizer->currentToken();
        $xml->addSymbol($token);
        // $xml->addKeyword("mykey");
        

        // $this->compileParameterList($xml);
        $xml->addEmptyChildToXml('parameterList');
        // add ")"
        $this->tokenizer->advance();
        $token = $this->tokenizer->currentToken();
        $xml->addSymbol($token);
        $xml->addKeyword($token);
        echo "hellol";
        print_r($token);


        $this->xml->print();
        exit();


        // $xml->addSymbol("hello");


        // print_r($this->xml->asXml());
        // $this->xml->
        // print_r($this->xml->__toString());
        // $o =$this->xml->getOriginal();
        // var_dump("aa");
        // var_dump($o);
        // print_r($o);
        // echo (string)$xml->getOriginal();
        // echo (string)$this->xml->getOriginal();
        // exit();
    }
    function compileParameterList($parent){
        $xml = $parent->addEmptyChildToXml('parameterList');
        // $xml->addSymbol("hey");
        // $token = $this->tokenizer->currentToken();
        // exit($token);

        // $this->tokenizer->advance();
        // $token = $this->tokenizer->currentToken();

        // add int|char|boolean|ClassName
        // exit('hello');
        // if($this->isTypeKeyword($token)){
        //     $xml->addKeyword($token);
        // }else{
        //     $xml->addIdentifier($token);
        //     // exit($token);
        // }
        // exit($token);


        // $this->tokenizer->advance();
        // $token = $this->tokenizer->currentToken();

        // $xml->addIdentifier($token);

        // $this->tokenizer->advance();
        // $token = $this->tokenizer->currentToken();


        // $nextToken = $this->tokenizer->nextToken();

        // if($nextToken != ','){
        //     return;
        // }

        // $this->tokenizer->advance();
        // $token = $this->tokenizer->currentToken();

        // $xml->addSymbol($token);

        // $this->tokenizer->advance();
        // $token = $this->tokenizer->currentToken();

    }
    function compileSubroutineBody(){
        $token = $this->tokenizer->currentToken();

        // add {
        $this->xml->addSymbol($token);

        while(
            $this->tokenizer->nextToken()=='var'
        ){
            $this->compileVarDec();
        }
        // if start with var then compile var dec
    }
    // slide 90
    // https://drive.google.com/file/d/1ujgcS7GoI-zu56FxhfkTAvEgZ6JT7Dxl/view
    function compileVarDec(){
        $token = $this->tokenizer->eat();

        // add var
        $this->xml->addKeyword($token);

        $token = $this->tokenizer->eat();


        // add type
        if($this->tokenizer->isKeyword($token)){ // int,char,boolean

            $this->xml->addKeyword($token);
        }else{ // className
            $this->xml->addIdentifier($token);
        }


        while(true){
            // add varName
            $token = $this->tokenizer->eat();
            $this->xml->addIdentifier($token);

            // end if ;.
            $token = $this->tokenizer->eat();
            if($token == ';'){
                $this->xml->addSymbol(';');
                return;
            }
            if($token == ','){
                $this->xml->addSymbol($token);
            }
        }
    }
    function compileStatements(){}
    function compileLet(){}
    function compileIf(){}
    function compileWhile(){}
    function compileDo(){}
    function compileReturn(){}
    function compileTerm(){}
    function compileExpressionList(){}
}

$e = new CompilationEngine('ExpressionLessSquare/Main.jack', 'out.xml');
$e->compileClass();