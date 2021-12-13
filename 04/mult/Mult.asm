// This file is part of www.nand2tetris.org
// and the book "The Elements of Computing Systems"
// by Nisan and Schocken, MIT Press.
// File name: projects/04/Mult.asm

// Multiplies R0 and R1 and stores the result in R2.
// (R0, R1, R2 refer to RAM[0], RAM[1], and RAM[2], respectively.)
//
// This program only needs to handle arguments that satisfy
// R0 >= 0, R1 >= 0, and R0*R1 < 32768.

// sum = 0;
// for(i=0; i < R1; i++){
//     sum += R0
// }

// if R1 - count > 0
// add R1 to sum
// else
//  go to end

    // init
    // result
    @R2
    M=0
    // counter
    @count
    M=0

(LOOP)
    // go to end if R1 - count == 0
    @R1
    D=M
    @count
    D=D-M
    @END
    D;JEQ

    // add R1 to sum
    @R0
    D=M
    @R2
    M=D+M

    // increment count
    @count
    M=M+1

    // go back
    @LOOP
    0;JEQ

(END)
    @END
    0;JMP
