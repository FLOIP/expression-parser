// Simple Arithmetics Grammar
// ==========================
//
// Accepts expressions like "2 * (3 + 4)" and computes their value.

{
    function unroll(arr) { return arr.join('') }
    context: {
        contact: {
            name: "Marshawn Lynch"
            jersey: 24
            age: 30
            tel: "+12065551212"
            birthday: "22-04-1986"
            __value__: "Marshawn Lynch"
        }
        channel: {
            name: "Twilio 1423"
            address: "1423"
        }
    }
}



Block = expr:(Escaped_Identifier / ex:Expression / $Text+ {} )* {
    return expr
}

// An expression can look like -- @(FUNC_CALL(args/expression)) / @(member.access) / @(member) / @member.access / @member
Expression = ws* Text* Identifier OpenParen? ex:(Function / Member_Access) CloseParen? ws* {return ex}

// Function looks like @(SOME_METHOD(arguments))
Function = call:$valid_expression_characters+ args:(OpenParen inner:Function_Args* CloseParen {return {inner}}) {return {type: 'FUNCTION', call:call, loc: location(), args:args.inner}}

Function_Args = arg:(arg:Function Arg_Delimiter? {return arg} / arg:Member_Access Arg_Delimiter? {return arg}) {return arg}

// Member access -- contact.name | contact
Member_Access = lhs:$AtomicExpression+ rhs:('.' inner:$AtomicExpression+ {return inner})? {return {type: 'ACCESS', obj: lhs, key:rhs, loc: location()}}


OpenParen = '('
CloseParen = ')'
Identifier = '@' 
Arg_Delimiter = (',' ws*)
Escaped_Identifier = Identifier Identifier {return {type: 'ESCAPE', loc: location()}}
AtomicExpression = valid_variable_characters
Text = [^@]
ws "whitespace"
  = [ \t\n\r]
valid_variable_characters = [a-zA-Z0-9_]
valid_expression_characters = [A-Z_]
valid_math_characters = [-+*/]
              