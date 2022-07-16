<?php
// fswatch -o /Users/shirafuta/work/nand2tetris/projects/10/ | xargs -n1 sh -c "php engine.php  > MainResult.xml || true"
require_once('tokenizer.php');

class GrammarException extends Exception
{
    public function __construct($message, $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
class Xml
{
    private $xml = '';
    function __construct()
    {
    }
    public function print()
    {
        $dom = new DOMDocument();
        $xml = '<class>' . $this->xml . '</class>';
        $dom->loadXml($xml);
        $dom->formatOutput = true;
        $dom->ignoreWhiteSpa = true;
        echo $dom->saveXml();
    }

    public function addTag($key)
    {
        $this->xml .= "<$key>";
        return $this;
    }

    public function endTag($key)
    {
        $this->xml .= "</$key>";
    }
    public function addKeyword($value)
    {
        $this->xml .= "<keyword> $value </keyword>";
    }
    public function addSymbol($value)
    {
        $this->xml .= "<symbol> $value </symbol>";
    }
    public function addIdentifier($value)
    {
        $this->xml .= "<identifier> $value </identifier>";
    }
    public function addInteger($value)
    {
        $this->xml .= "<integerConstant> $value </integerConstant>";
    }
    public function addString($value)
    {
        $this->xml .= "<stringConstant> $value </stringConstant>";
    }
    // keyword, symbol, identifier, int, string
    public function addTerminalElement($tokenType, $token)
    {
        switch ($tokenType) {
            case 'KEYWORD':
                $this->addKeyword($token);
                # code...
                break;
            case 'SYMBOL':
                $this->addSymbol($token);
                break;
            case 'IDENTIFIER':
                $this->addIdentifier($token);
                break;
            case 'INT_CONST':
                $this->addInteger($token);
                break;
            case 'STRING_CONST':
                $this->addString($token);
                # code...
                break;
            default:
                exit('unknown token type.');
                break;
        }
    }
}

class CompilationEngine
{
    private $in;
    private $out;
    private $tokenizer;
    public $xml;

    function __construct($in, $out)
    {
        $this->in = $in;
        $this->out = $out;
        $this->tokenizer = new Tokenizer($this->in);
        $this->xml = new Xml();
    }
    function throwIfTokenDoesNotMatch($rule, $val)
    {
        if ($rule == $val) {
            return true;
        }
        print_r([$rule, $val]);
        throw new GrammarException('token does not comform to the rules');
    }
    function compileClass()
    {
        $token = $this->tokenizer->currentToken();

        // if class, go next or throw error

        $this->throwIfTokenDoesNotMatch('class', $token);

        $this->xml->addKeyword($token);


        $this->tokenizer->advance();
        $token = $this->tokenizer->currentToken();

        // if identifier, go next or throw error
        if ($this->tokenizer->isIdentifier($token)) {
            $this->xml->addIdentifier($token);
        } else {
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

        while(true){
            $token = $this->tokenizer->nextToken();
            // exit($token);

            if(in_array($token, ['constructor','function','method'])){
                // if compileSubroutineDec, go next or throw error
                $this->compileSubroutineDec();
                print($token);
                print('hello');
                exit('exit;');
            }else{
                print('break');
                exit('break');
                break;
            }
        }
        exit('while end');





   

        // var_dump($token);
        $this->tokenizer->advance();
        $token = $this->tokenizer->currentToken();

        // if }, go next or throw error
        // var_dump($token);
        echo $this->xml->print();
        exit();

        // $this->throwIfTokenDoesNotMatch('}', $token);
        // exit();
    }
    function grammarError($val)
    {
        throw new GrammarException($val);
    }
    function isTypeKeyword($val)
    {
        return in_array($val, ['int', 'char', 'boolean']);
        // TODO: ClassName should be valid too.
    }
    function canBeClassName($val)
    {
        return true;
    }
    function compileClassVarDec()
    {
        $xml = $this->xml->addTag('classVarDec');
        // var_dump($xml);

        // static or field
        $token = $this->tokenizer->currentToken();
        if (!in_array($token, ['static', 'field'])) {
            $this->grammarError($token);
        }
        $xml->addKeyword($token, $xml);

        $this->tokenizer->advance();
        $token = $this->tokenizer->currentToken();

        // int|char|boolean|className
        if (!$this->isTypeKeyword($token)) {
            $this->grammarError('not a type:' . $token);
        }
        $xml->addKeyword($token, $xml);


        $this->tokenizer->advance();
        $token = $this->tokenizer->currentToken();

        // varName
        if (!$this->tokenizer->isIdentifier($token)) {
            $this->grammarError('not identifier:', $token);
        }
        $xml->addIdentifier($token, $xml);

        $this->tokenizer->advance();
        $token = $this->tokenizer->currentToken();

        if (!in_array($token, [',', ';'])) throw new GrammarException('not , or ;');

        while ($token != ';') {
            if ($token != ',') $this->grammarError('should be comma.');

            $xml->addSymbol($token);

            $this->tokenizer->advance();
            $token = $this->tokenizer->currentToken();

            if (!$this->tokenizer->isIdentifier($token)) {
                $this->grammarError('not identifier:', $token);
            }
            $xml->addIdentifier($token);

            $this->tokenizer->advance();
            $token = $this->tokenizer->currentToken();
        }

        $xml->addSymbol($token);
        // exit('here');
        $this->xml->endTag('classVarDec');
    }
    function compileSubroutineDec()
    {
        $xml = $this->xml->addTag('subroutineDec');
        $token = $this->tokenizer->eat();
        
        $xml->addKeyword($token);

        $this->tokenizer->advance();
        $token = $this->tokenizer->currentToken();

        if ($this->isTypeKeyword($token)) {
            $xml->addKeyword($token);
        } else {
            if ($token != 'void') {
                $this->grammarError($token);
            }
            $xml->addKeyword($token);
        }

        $this->tokenizer->advance();
        $token = $this->tokenizer->currentToken();

        $xml->addIdentifier($token);

        // add "("
        $this->tokenizer->advance();
        $token = $this->tokenizer->currentToken();
        $xml->addSymbol($token);

        $xml->addTag('parameterList');
        $xml->endTag('parameterList');
        // $xml->addKeyword("mykey");


        // $this->compileParameterList($xml);

        // add ")"
        $this->tokenizer->advance();
        $token = $this->tokenizer->currentToken();
        $xml->addSymbol($token);

        $this->compileSubroutineBody();

        // 
        $this->xml->endTag('subroutineDec');
    }
    function compileParameterList($parent)
    {
        $xml = $parent->addTag('parameterList');
        $xml = $parent->endTag('parameterList');
    }
    function compileSubroutineBody()
    {
        $this->xml->addTag('subroutineBody');

        $token = $this->tokenizer->eat();


        // add {
        $this->xml->addSymbol($token);


        // varDec*
        while (true) {
            $token = $this->tokenizer->nextToken();
            if ($token != 'var') {
                break;
            }

            $this->compileVarDec();
        }

        // statements
        $this->compileStatement();

        // add }
        $token = $this->tokenizer->eat();
        $this->xml->addSymbol($token);

        $this->xml->endTag('subroutineBody');
    }
    // slide 90
    // https://drive.google.com/file/d/1ujgcS7GoI-zu56FxhfkTAvEgZ6JT7Dxl/view
    function compileVarDec()
    {
        $this->xml->addTag('varDec');
        $token = $this->tokenizer->eat();

        // add var
        $this->xml->addKeyword($token);
        // exit($token);

        $token = $this->tokenizer->eat();


        // add type
        if ($this->tokenizer->isKeyword($token)) { // int,char,boolean

            $this->xml->addKeyword($token);
        } else { // className
            $this->xml->addIdentifier($token);
        }


        while (true) {
            // add varName
            $token = $this->tokenizer->eat();
            $this->xml->addIdentifier($token);

            // end if ;.
            $token = $this->tokenizer->eat();
            if ($token == ';') {
                $this->xml->addSymbol(';');
                $this->xml->endTag('varDec');
                return;
            }
            if ($token == ',') {
                $this->xml->addSymbol($token);
            }
        }
        // $this->xml->endTag('varDec');
    }
    function compileStatements()
    {
        while (true) {

            $token = $this->tokenizer->nextToken();
            switch ($token) {
                case 'let':
                    $this->compileLet();
                    break;
                case 'if':
                    $this->compileIf();
                    break;
                case 'while':
                    $this->compileWhile();
                    break;
                case 'do':
                    $this->compileDo();
                    break;
                case 'return':
                    $this->compileReturn();
                    break;
                default:
                    return;
            }
        }
    }
    function compileStatement(){
        $this->xml->addTag('statements');
        $this->compileStatements();
        // $this->compileLet();

        $this->xml->endTag('statements');
    }
    function compileLet()
    {
        $this->xml->addTag('letStatement');
        // let
        $token = $this->tokenizer->eat();
        $this->xml->addKeyword($token);

        // varName
        $token = $this->tokenizer->eat();
        $this->xml->addIdentifier($token);

        $token = $this->tokenizer->eat();

        // []
        if ($token == '[') {
            // [
            $this->xml->addSymbol($token);

            // expression:
            //   term(op term)
            //      int,str,keyword,varName, varName[],subroutineCall,(expression),unaryOp term
            // term 
            $token = $this->tokenizer->eat();
            $type = $this->tokenizer->tokenType();
            $this->xml->addTerminalElement($type, $token);
            
            // add ]
            $token = $this->tokenizer->eat();
            $this->xml->addSymbol($token);
            $token = $this->tokenizer->eat();
        }

        // add =
        $this->xml->addSymbol($token);

        // add expression
        $this->xml->addTag('expression');
        $this->xml->addTag('term');
        $token = $this->tokenizer->eat();
        $type = $this->tokenizer->tokenType();
        $this->xml->addTerminalElement($type, $token);
        $this->xml->endTag('term');
        $this->xml->endTag('expression');

        // add ;
        $token = $this->tokenizer->eat();
        $this->xml->addSymbol($token);
        $this->xml->endTag('letStatement');
    }
    function compileIf()
    {
        ここから
    }
    function compileWhile()
    {
        exit('while not implemented');
    }
    function compileDo()
    {
        $this->xml->addTag('doStatement');
        // add do
        $token = $this->tokenizer->eat();
        $this->xml->addKeyword($token);

        // add subroutine call
        // add subroutine name
        $token = $this->tokenizer->eat();
        $this->xml->addIdentifier($token);

        // add . 
        $token = $this->tokenizer->eat();
        $this->xml->addSymbol($token);

        // add function name
        $token = $this->tokenizer->eat();
        $this->xml->addIdentifier($token);

        // add (
        $token = $this->tokenizer->eat();
        $this->xml->addSymbol($token);
        
        // addExpressionList
        $this->compileExpressionList();

        // add )
        $token = $this->tokenizer->eat();
        $this->xml->addSymbol($token);

        // add ;
        $token = $this->tokenizer->eat();
        $this->xml->addSymbol($token);

        $this->xml->endTag('doStatement');
    }
    function compileReturn()
    {
        $this->xml->addTag('returnStatement');
        // add return
        $token = $this->tokenizer->eat();
        $this->xml->addKeyword($token);

        // add ;
        $token = $this->tokenizer->eat();
        $this->xml->addSymbol($token);

        $this->xml->endTag('returnStatement');

        // exit('hello');
    }
    function compileTerm()
    {
        exit('term not implemented').
    }
    function compileExpressionList()
    {
        $this->xml->addTag('expressionList');
        $this->xml->endTag('expressionList');
    }
}

$e = new CompilationEngine('ExpressionLessSquare/Main.jack', 'out.xml');
$e->compileClass();
