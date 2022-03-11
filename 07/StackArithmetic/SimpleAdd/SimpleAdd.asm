// __PUSH__ push constant 7 to stack.
//  set 7 to D
@7
D=A
// push d to stack
@0
A=M
M=D
//  increment sp
@0
M=M+1
// __END_OF_PUSH__
// __PUSH__ push constant 8 to stack.
//  set 8 to D
@8
D=A
// push d to stack
@0
A=M
M=D
//  increment sp
@0
M=M+1
// __END_OF_PUSH__
// ADD
//  decrement sp
@0
M=M-1
// set current memory address value to D.
@0
A=M
D=M
//  decrement sp
@0
M=M-1
// add current stack value and D
@0
A=M
M=D+M
//  increment sp
@0
M=M+1
