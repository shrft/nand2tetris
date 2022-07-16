<?php
ini_set('memory_limit', '-1'); 

class Tokenizer{
    private $file;
    private $keywords = ['class', 'constructor', 'function','method','field','static','var','int','char','boolean','void','true','false','null','this','let','do','if','else','while','return'];
    private $symbol = ['{','}','(',')','[',']','.',',','+','-','*','/','&','|','<','>','=','~'];
    private $isComment = false;
    public $tokenIndex = 0;
    public $tokens = [];
    public $xml;

    public function __construct($file){
        $this->file = $file;
        $this->xml = new SimpleXMLElement('<tokens></tokens>');
        $this->tokenizeIntoArray();
    }
    public function hasMoreTokens()
    {
        if(array_key_exists($this->tokenIndex + 1, $this->tokens)){
            return true;
        }
        return false;
    }
    public function advance()
    {
        $this->tokenIndex++;
    }
    public function eat(){
        $this->advance();
        return $this->currentToken();
    }
    public function tokenType()
    {
        if($this->isKeyword($this->currentToken())){
            return 'KEYWORD';
        }
        if($this->isSymbol($this->currentToken())){
            return 'SYMBOL';
        }
        if($this->isIdentifier($this->currentToken())){
            return 'IDENTIFIER';
        }
        if($this->isIntConst($this->currentToken())){
            return 'INT_CONST';
        }
        if($this->isStringConst($this->currentToken())){
            return 'STRING_CONST';
        }
        throw new Exception('unknown token type');
    }
    public function nextToken(){
        return $this->tokens[$this->tokenIndex+1];
    }
    public function currentToken(){
        return $this->tokens[$this->tokenIndex];
    }
    public function tkn(){
        return $this->currentToken();
    }
    public function keyword()
    {
        if($this->isKeyword($this->currentToken())){
            return $this->currentToken();
        }
        throw new Exception('not a keyword');
    }
    public function symbol()
    {
        if($this->isSymbol($this->currentToken())){
            return $this->currentToken();
        }
        throw new Exception('not a symbol');
    }
    public function identifier()
    {
        if($this->isIdentifier($this->currentToken())){
            return $this->currentToken();
        }
        throw new Exception('not an identifier');
    }
    public function intVal()
    {
        if($this->isIntConst($this->currentToken())){
            return $this->currentToken();
        }
        throw new Exception('not an int const');
    }
    public function stringVal()
    {
        if($this->isStringConst($this->currentToken())){
            return $this->currentToken();
        }
        throw new Exception('not a string const'); 
    }
    private function stripComment($line){
        $ret = preg_match('/\/\/|\/\*\*/', $line, $matches, PREG_OFFSET_CAPTURE);
        if($ret){
            $splitAt=$matches[0][1];
            return substr($line, 0, $splitAt);
        }
        return $line;
    }
    public function tokenizeLine($line){
        $line = $this->stripComment($line);
        $line = trim($line);

        preg_match('/\/\/|\/\*\*|[\s*{}()[\]\.,;\+\-\*\/&|<>=~]/', $line, $matches, PREG_OFFSET_CAPTURE);
        $splitAt=$matches[0][1];

        if($splitAt > 0){
            $length = $splitAt;
        }else{
            $length = $splitAt+1;
        }

        $token = substr($line, 0,$length);
        $this->tokens[] = $token;
        $next = substr($line, $length);
        $next = trim($next);
        if(strlen($next) > 0){
            $this->tokenizeLine($next);
        }
    }
    public function isKeyword($value){
        $keywords = ['class','constructor','function','method','field','static','var','int','char','boolean','void','true','false','null','this','let','do','if','else','while','return'];
        return in_array($value, $keywords);
    }
    public function isIdentifier($value){
        $regex = '^[a-zA-Z_]+[a-zA-Z_0-9]*';
        return preg_match('/^[a-zA-Z_]+[a-zA-Z_0-9]*/',$value, $matches);
    }
    public function isIntConst($value){
        return is_numeric($value);
    }

    public function isSymbol($value){
        $symbols = ['{','}','(',')','[',']','.',',',';','+','-','*','/','&','|','<','>','=','~'];
        return in_array($value, $symbols);
    }
    public function isStringConst($value)
    {
        return !($this->isKeyword($value) ||
               $this->isIdentifier($value) ||
               $this->isIntConst($value) ||
               $this->isSymbol($value));
    }
    private function addChildToXml($key, $value){
        $this->xml->addChild($key, ' ' . $value . ' ');
    }
    private function addKeyword($value){
        $this->addChildToXml('keyword', $value);
    }
    private function addSymbol($value){
        $this->addChildToXml('symbol', $value);
    }
    private function addIdentifier($value){
        $this->addChildToXml('identifier', $value);
    }
    private function parseToTokenXml($value){
        if($this->isKeyword($value)){
            $this->addKeyword($value);
            return;
        }
        if($this->isSymbol($value)){
            $this->addSymbol($value);
            return;
        }
        $this->addIdentifier($value);
    }
    public function parseTokensToTokenXml(){
        foreach($this->tokens as $token){
            $this->parseToTokenXml($token);
        }
    }
    public function tokenizeIntoArray(){
        $file= file($this->file);
        foreach($file as $line){
            if($this->isCommentLine($line)) continue;
            $line = trim($line);
            if($line=="") continue;

            $this->tokenizeLine($line);
        }
    }
    private function isCommentLine($line){
        $line = trim($line);
        if(str_starts_with($line, '//')) return true;
        if(str_starts_with($line, '/**')) return true;
        return false;
    }
}
// $file = $argv[1];
// $t = new Tokenizer($file);
// echo $t->isIntConst(1234);
// echo $t->isIntConst('1234');
// echo $t->isIntConst('a');
// $t->tokenizeIntoArray();
// // print_r($t->tokens);
// $t->parseTokensToTokenXml();
// echo $t->xml->asXML();

// $t = ['a','b'];
// $i = 0;
// echo array_key_exists($i + 1, $t);


// 次
// compile engineを作成する
// 以下のendnoteのあたりと
// https://drive.google.com/file/d/1ujgcS7GoI-zu56FxhfkTAvEgZ6JT7Dxl/view
// 以下の動画を見ながら仕様を把握する
// https://www.coursera.org/learn/nand2tetris2/lecture/0H0Wq/unit-4-5-parser-logic