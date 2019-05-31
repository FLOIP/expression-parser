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


Start = ws* Text* ex:(ex:Expression ws* {return ex})* Text* {return ex}

Expression = Identifier OpenParen? exp:((func:Method_Call {return {type: "func", data:func}} / mem:Member_Access {return {type: "obj", data:mem}} / $Text+ {}) CloseParen?)* {return {exp:exp}}

// Method_Call looks like @(SOME_METHOD(arguments))
Method_Call = call:$valid_expression_characters+ args:(OpenParen inner:Method_Args* CloseParen {return {inner}}) {return {call:call, args:args.inner}}

Method_Args = arg:(Method_Call / Member_Access) {return arg} / (',' ws*) {}
// Member access -- contact.name | contact
Member_Access = lhs:$AtomicExpression+ rhs:('.' inner:$AtomicExpression+ {return inner})? {return {type: 'MemberAccess', obj: lhs, key:rhs, loc: location()}}


/*Block = lhs:Text? ex:Expression* rhs:Text?*/

Text = [^@]

/*Expression = '@' stuff:(ParenExpression / VariableExpression){return stuff}
VariableExpression = Text [^ \t\n\r]*
ParenExpression = OpenParen Text CloseParen*/
OpenParen = '('
CloseParen = ')'

Identifier = '@'
AtomicExpression = valid_variable_characters


ws "whitespace"
  = [ \t\n\r]
char "character" = [^ \t\n\r]*
valid_variable_characters = [a-zA-Z0-9_]
valid_expression_characters = [A-Z_]
valid_math_characters = [-+*/]
              