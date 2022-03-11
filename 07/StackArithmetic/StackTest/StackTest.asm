// <<Push constant 17 to stack at 0 
// <<Set 17 to D
@17
D=A
// Set 17 to D>>
// <<Push d to stack
@0
A=M
M=D
// Push d to stack>>
// <<Increment sp
@0
M=M+1
// Increment sp>>
// Push constant 17 to stack>>

// <<Push constant 17 to stack at 7 
// <<Set 17 to D
@17
D=A
// Set 17 to D>>
// <<Push d to stack
@0
A=M
M=D
// Push d to stack>>
// <<Increment sp
@0
M=M+1
// Increment sp>>
// Push constant 17 to stack>>

// <<EQ at 14 
// <<Decrement sp
@0
M=M-1
// Decrement sp>>
// <<Pop to D
// <<Select Current Memory Address
@0
A=M
// Select Current Memory Address>>
D=M
// Pop to D>>
// <<Decrement sp
@0
M=M-1
// Decrement sp>>
// <<Select Current Memory Address
@0
A=M
// Select Current Memory Address>>
D=D-M
@IF_TRUE_24
D;JEQ
(IF_FALSE_24)
// Array
// <<Select Current Memory Address
@0
A=M
// Select Current Memory Address>>
M=0
@IF_END_24
0;JMP
(IF_TRUE_24)
// set to true
// <<Select Current Memory Address
@0
A=M
// Select Current Memory Address>>
M=-1
(IF_END_24)
// <<Increment sp
@0
M=M+1
// Increment sp>>
// EQ>>

// <<Push constant 17 to stack at 36 
// <<Set 17 to D
@17
D=A
// Set 17 to D>>
// <<Push d to stack
@0
A=M
M=D
// Push d to stack>>
// <<Increment sp
@0
M=M+1
// Increment sp>>
// Push constant 17 to stack>>

// <<Push constant 16 to stack at 43 
// <<Set 16 to D
@16
D=A
// Set 16 to D>>
// <<Push d to stack
@0
A=M
M=D
// Push d to stack>>
// <<Increment sp
@0
M=M+1
// Increment sp>>
// Push constant 16 to stack>>

// <<EQ at 50 
// <<Decrement sp
@0
M=M-1
// Decrement sp>>
// <<Pop to D
// <<Select Current Memory Address
@0
A=M
// Select Current Memory Address>>
D=M
// Pop to D>>
// <<Decrement sp
@0
M=M-1
// Decrement sp>>
// <<Select Current Memory Address
@0
A=M
// Select Current Memory Address>>
D=D-M
@IF_TRUE_60
D;JEQ
(IF_FALSE_60)
// Array
// <<Select Current Memory Address
@0
A=M
// Select Current Memory Address>>
M=0
@IF_END_60
0;JMP
(IF_TRUE_60)
// set to true
// <<Select Current Memory Address
@0
A=M
// Select Current Memory Address>>
M=-1
(IF_END_60)
// <<Increment sp
@0
M=M+1
// Increment sp>>
// EQ>>

// <<Push constant 16 to stack at 72 
// <<Set 16 to D
@16
D=A
// Set 16 to D>>
// <<Push d to stack
@0
A=M
M=D
// Push d to stack>>
// <<Increment sp
@0
M=M+1
// Increment sp>>
// Push constant 16 to stack>>

// <<Push constant 17 to stack at 79 
// <<Set 17 to D
@17
D=A
// Set 17 to D>>
// <<Push d to stack
@0
A=M
M=D
// Push d to stack>>
// <<Increment sp
@0
M=M+1
// Increment sp>>
// Push constant 17 to stack>>

// <<EQ at 86 
// <<Decrement sp
@0
M=M-1
// Decrement sp>>
// <<Pop to D
// <<Select Current Memory Address
@0
A=M
// Select Current Memory Address>>
D=M
// Pop to D>>
// <<Decrement sp
@0
M=M-1
// Decrement sp>>
// <<Select Current Memory Address
@0
A=M
// Select Current Memory Address>>
D=D-M
@IF_TRUE_96
D;JEQ
(IF_FALSE_96)
// Array
// <<Select Current Memory Address
@0
A=M
// Select Current Memory Address>>
M=0
@IF_END_96
0;JMP
(IF_TRUE_96)
// set to true
// <<Select Current Memory Address
@0
A=M
// Select Current Memory Address>>
M=-1
(IF_END_96)
// <<Increment sp
@0
M=M+1
// Increment sp>>
// EQ>>

// <<Push constant 892 to stack at 108 
// <<Set 892 to D
@892
D=A
// Set 892 to D>>
// <<Push d to stack
@0
A=M
M=D
// Push d to stack>>
// <<Increment sp
@0
M=M+1
// Increment sp>>
// Push constant 892 to stack>>

// <<Push constant 891 to stack at 115 
// <<Set 891 to D
@891
D=A
// Set 891 to D>>
// <<Push d to stack
@0
A=M
M=D
// Push d to stack>>
// <<Increment sp
@0
M=M+1
// Increment sp>>
// Push constant 891 to stack>>

// <<LT  at 122 
// <<Decrement sp
@0
M=M-1
// Decrement sp>>
// <<Pop to D
// <<Select Current Memory Address
@0
A=M
// Select Current Memory Address>>
D=M
// Pop to D>>
// <<Decrement sp
@0
M=M-1
// Decrement sp>>
// <<Select Current Memory Address
@0
A=M
// Select Current Memory Address>>
D=D-M
@IF_TRUE_132
D;JGT
(IF_FALSE_132)
// Array
// <<Select Current Memory Address
@0
A=M
// Select Current Memory Address>>
M=0
@IF_END_132
0;JMP
(IF_TRUE_132)
// set to true
// <<Select Current Memory Address
@0
A=M
// Select Current Memory Address>>
M=-1
(IF_END_132)
// <<Increment sp
@0
M=M+1
// Increment sp>>
// LT>>

// <<Push constant 891 to stack at 144 
// <<Set 891 to D
@891
D=A
// Set 891 to D>>
// <<Push d to stack
@0
A=M
M=D
// Push d to stack>>
// <<Increment sp
@0
M=M+1
// Increment sp>>
// Push constant 891 to stack>>

// <<Push constant 892 to stack at 151 
// <<Set 892 to D
@892
D=A
// Set 892 to D>>
// <<Push d to stack
@0
A=M
M=D
// Push d to stack>>
// <<Increment sp
@0
M=M+1
// Increment sp>>
// Push constant 892 to stack>>

// <<LT  at 158 
// <<Decrement sp
@0
M=M-1
// Decrement sp>>
// <<Pop to D
// <<Select Current Memory Address
@0
A=M
// Select Current Memory Address>>
D=M
// Pop to D>>
// <<Decrement sp
@0
M=M-1
// Decrement sp>>
// <<Select Current Memory Address
@0
A=M
// Select Current Memory Address>>
D=D-M
@IF_TRUE_168
D;JGT
(IF_FALSE_168)
// Array
// <<Select Current Memory Address
@0
A=M
// Select Current Memory Address>>
M=0
@IF_END_168
0;JMP
(IF_TRUE_168)
// set to true
// <<Select Current Memory Address
@0
A=M
// Select Current Memory Address>>
M=-1
(IF_END_168)
// <<Increment sp
@0
M=M+1
// Increment sp>>
// LT>>

// <<Push constant 891 to stack at 180 
// <<Set 891 to D
@891
D=A
// Set 891 to D>>
// <<Push d to stack
@0
A=M
M=D
// Push d to stack>>
// <<Increment sp
@0
M=M+1
// Increment sp>>
// Push constant 891 to stack>>

// <<Push constant 891 to stack at 187 
// <<Set 891 to D
@891
D=A
// Set 891 to D>>
// <<Push d to stack
@0
A=M
M=D
// Push d to stack>>
// <<Increment sp
@0
M=M+1
// Increment sp>>
// Push constant 891 to stack>>

// <<LT  at 194 
// <<Decrement sp
@0
M=M-1
// Decrement sp>>
// <<Pop to D
// <<Select Current Memory Address
@0
A=M
// Select Current Memory Address>>
D=M
// Pop to D>>
// <<Decrement sp
@0
M=M-1
// Decrement sp>>
// <<Select Current Memory Address
@0
A=M
// Select Current Memory Address>>
D=D-M
@IF_TRUE_204
D;JGT
(IF_FALSE_204)
// Array
// <<Select Current Memory Address
@0
A=M
// Select Current Memory Address>>
M=0
@IF_END_204
0;JMP
(IF_TRUE_204)
// set to true
// <<Select Current Memory Address
@0
A=M
// Select Current Memory Address>>
M=-1
(IF_END_204)
// <<Increment sp
@0
M=M+1
// Increment sp>>
// LT>>

// <<Push constant 32767 to stack at 216 
// <<Set 32767 to D
@32767
D=A
// Set 32767 to D>>
// <<Push d to stack
@0
A=M
M=D
// Push d to stack>>
// <<Increment sp
@0
M=M+1
// Increment sp>>
// Push constant 32767 to stack>>

// <<Push constant 32766 to stack at 223 
// <<Set 32766 to D
@32766
D=A
// Set 32766 to D>>
// <<Push d to stack
@0
A=M
M=D
// Push d to stack>>
// <<Increment sp
@0
M=M+1
// Increment sp>>
// Push constant 32766 to stack>>

// <<GT  at 230 
// <<Decrement sp
@0
M=M-1
// Decrement sp>>
// <<Pop to D
// <<Select Current Memory Address
@0
A=M
// Select Current Memory Address>>
D=M
// Pop to D>>
// <<Decrement sp
@0
M=M-1
// Decrement sp>>
// <<Select Current Memory Address
@0
A=M
// Select Current Memory Address>>
D=D-M
@IF_TRUE_240
D;JLT
(IF_FALSE_240)
// Array
// <<Select Current Memory Address
@0
A=M
// Select Current Memory Address>>
M=0
@IF_END_240
0;JMP
(IF_TRUE_240)
// set to true
// <<Select Current Memory Address
@0
A=M
// Select Current Memory Address>>
M=-1
(IF_END_240)
// <<Increment sp
@0
M=M+1
// Increment sp>>
// GT>>

// <<Push constant 32766 to stack at 252 
// <<Set 32766 to D
@32766
D=A
// Set 32766 to D>>
// <<Push d to stack
@0
A=M
M=D
// Push d to stack>>
// <<Increment sp
@0
M=M+1
// Increment sp>>
// Push constant 32766 to stack>>

// <<Push constant 32767 to stack at 259 
// <<Set 32767 to D
@32767
D=A
// Set 32767 to D>>
// <<Push d to stack
@0
A=M
M=D
// Push d to stack>>
// <<Increment sp
@0
M=M+1
// Increment sp>>
// Push constant 32767 to stack>>

// <<GT  at 266 
// <<Decrement sp
@0
M=M-1
// Decrement sp>>
// <<Pop to D
// <<Select Current Memory Address
@0
A=M
// Select Current Memory Address>>
D=M
// Pop to D>>
// <<Decrement sp
@0
M=M-1
// Decrement sp>>
// <<Select Current Memory Address
@0
A=M
// Select Current Memory Address>>
D=D-M
@IF_TRUE_276
D;JLT
(IF_FALSE_276)
// Array
// <<Select Current Memory Address
@0
A=M
// Select Current Memory Address>>
M=0
@IF_END_276
0;JMP
(IF_TRUE_276)
// set to true
// <<Select Current Memory Address
@0
A=M
// Select Current Memory Address>>
M=-1
(IF_END_276)
// <<Increment sp
@0
M=M+1
// Increment sp>>
// GT>>

// <<Push constant 32766 to stack at 288 
// <<Set 32766 to D
@32766
D=A
// Set 32766 to D>>
// <<Push d to stack
@0
A=M
M=D
// Push d to stack>>
// <<Increment sp
@0
M=M+1
// Increment sp>>
// Push constant 32766 to stack>>

// <<Push constant 32766 to stack at 295 
// <<Set 32766 to D
@32766
D=A
// Set 32766 to D>>
// <<Push d to stack
@0
A=M
M=D
// Push d to stack>>
// <<Increment sp
@0
M=M+1
// Increment sp>>
// Push constant 32766 to stack>>

// <<GT  at 302 
// <<Decrement sp
@0
M=M-1
// Decrement sp>>
// <<Pop to D
// <<Select Current Memory Address
@0
A=M
// Select Current Memory Address>>
D=M
// Pop to D>>
// <<Decrement sp
@0
M=M-1
// Decrement sp>>
// <<Select Current Memory Address
@0
A=M
// Select Current Memory Address>>
D=D-M
@IF_TRUE_312
D;JLT
(IF_FALSE_312)
// Array
// <<Select Current Memory Address
@0
A=M
// Select Current Memory Address>>
M=0
@IF_END_312
0;JMP
(IF_TRUE_312)
// set to true
// <<Select Current Memory Address
@0
A=M
// Select Current Memory Address>>
M=-1
(IF_END_312)
// <<Increment sp
@0
M=M+1
// Increment sp>>
// GT>>

// <<Push constant 57 to stack at 324 
// <<Set 57 to D
@57
D=A
// Set 57 to D>>
// <<Push d to stack
@0
A=M
M=D
// Push d to stack>>
// <<Increment sp
@0
M=M+1
// Increment sp>>
// Push constant 57 to stack>>

// <<Push constant 31 to stack at 331 
// <<Set 31 to D
@31
D=A
// Set 31 to D>>
// <<Push d to stack
@0
A=M
M=D
// Push d to stack>>
// <<Increment sp
@0
M=M+1
// Increment sp>>
// Push constant 31 to stack>>

// <<Push constant 53 to stack at 338 
// <<Set 53 to D
@53
D=A
// Set 53 to D>>
// <<Push d to stack
@0
A=M
M=D
// Push d to stack>>
// <<Increment sp
@0
M=M+1
// Increment sp>>
// Push constant 53 to stack>>

// <<ADD at 345 
// <<Decrement sp
@0
M=M-1
// Decrement sp>>
// <<Pop to D
// <<Select Current Memory Address
@0
A=M
// Select Current Memory Address>>
D=M
// Pop to D>>
// <<Decrement sp
@0
M=M-1
// Decrement sp>>
// <<Add current stack value and D
// <<Select Current Memory Address
@0
A=M
// Select Current Memory Address>>
M=D+M
// Add current stack value and D>>
// <<Increment sp
@0
M=M+1
// Increment sp>>
// ADD>>

// <<Push constant 112 to stack at 357 
// <<Set 112 to D
@112
D=A
// Set 112 to D>>
// <<Push d to stack
@0
A=M
M=D
// Push d to stack>>
// <<Increment sp
@0
M=M+1
// Increment sp>>
// Push constant 112 to stack>>

// <<SUB  at 364
// <<Decrement sp
@0
M=M-1
// Decrement sp>>
// <<Pop to D
// <<Select Current Memory Address
@0
A=M
// Select Current Memory Address>>
D=M
// Pop to D>>
// <<Decrement sp
@0
M=M-1
// Decrement sp>>
// <<Subtract D from current stack
// <<Select Current Memory Address
@0
A=M
// Select Current Memory Address>>
M=M-D
// Subtract D from current stack>>
// <<Increment sp
@0
M=M+1
// Increment sp>>
// SUB>>

// <<NEG at 376 
// <<Decrement sp
@0
M=M-1
// Decrement sp>>
// <<Select Current Memory Address
@0
A=M
// Select Current Memory Address>>
M=-M
// <<Increment sp
@0
M=M+1
// Increment sp>>
// NEG>>

// <<AND at 383 
// <<Decrement sp
@0
M=M-1
// Decrement sp>>
// <<Pop to D
// <<Select Current Memory Address
@0
A=M
// Select Current Memory Address>>
D=M
// Pop to D>>
// <<Decrement sp
@0
M=M-1
// Decrement sp>>
// <<Select Current Memory Address
@0
A=M
// Select Current Memory Address>>
M=D&M
// <<Increment sp
@0
M=M+1
// Increment sp>>
// AND>>

// <<Push constant 82 to stack at 395 
// <<Set 82 to D
@82
D=A
// Set 82 to D>>
// <<Push d to stack
@0
A=M
M=D
// Push d to stack>>
// <<Increment sp
@0
M=M+1
// Increment sp>>
// Push constant 82 to stack>>

// <<OR at 402 
// <<Decrement sp
@0
M=M-1
// Decrement sp>>
// <<Pop to D
// <<Select Current Memory Address
@0
A=M
// Select Current Memory Address>>
D=M
// Pop to D>>
// <<Decrement sp
@0
M=M-1
// Decrement sp>>
// <<Select Current Memory Address
@0
A=M
// Select Current Memory Address>>
M=D|M
// <<Increment sp
@0
M=M+1
// Increment sp>>
// OR>>

// <<NOT at 414 
// <<Decrement sp
@0
M=M-1
// Decrement sp>>
// <<Select Current Memory Address
@0
A=M
// Select Current Memory Address>>
M=!M
// <<Increment sp
@0
M=M+1
// Increment sp>>
// NOT>>

