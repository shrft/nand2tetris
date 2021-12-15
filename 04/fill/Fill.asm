// This file is part of www.nand2tetris.org
// and the book "The Elements of Computing Systems"
// by Nisan and Schocken, MIT Press.
// File name: projects/04/Fill.asm

// Runs an infinite loop that listens to the keyboard input.
// When a key is pressed (any key), the program blackens the screen,
// i.e. writes "black" in every pixel;
// the screen should remain fully black as long as the key is pressed. 
// When no key is pressed, the program clears the screen, i.e. writes
// "white" in every pixel;
// the screen should remain fully clear as long as no key is pressed.

// Put your code here.

// if(isKeyPressd)
//    blackenScreen
// else
//    whiten
// end
// go back

    

(LOOP)
    @KBD
    D=M

    // init i
    @i
    M=0

    @BLACKLOOP
    D;JGT
    
    @WHITELOOP
    0;JEQ

    @LOOP
    0;JEQ

(BLACKLOOP)
    // blacken screen
    // 1row = 16 * 32
    // screen = 1row * 256
    // check if @max - @i = 0
    // if 0 go to end
    // else proceed
    

    // set max
    @8192
    D=A
    @max
    M=D

    // end black loop if reach to end
    @max
    D=M
    @i
    D=D-M
    @LOOP
    D;JEQ

    // select register
    @SCREEN
    D=A
    @i
    A=M+D

    // set 1 to the row
    M=-1

    // increment i
    @i
    M=M+1

    // go to the beginning
    @BLACKLOOP
    0;JEQ

(WHITELOOP)
    // blacken screen
    // 1row = 16 * 32
    // screen = 1row * 256
    // check if @max - @i = 0
    // if 0 go to end
    // else proceed
    

    // set max
    @8192
    D=A
    @max
    M=D

    // end black loop if reach to end
    @max
    D=M
    @i
    D=D-M
    @LOOP
    D;JEQ

    // select register
    @SCREEN
    D=A
    @i
    A=M+D

    // set 1 to the row
    M=0

    // increment i
    @i
    M=M+1

    // go to the beginning
    @WHITELOOP
    0;JEQ

