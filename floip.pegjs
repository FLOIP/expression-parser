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


Start = ws* head:$Text* expr:(Expression* ws*/ Text*) { return {head:head, expr:expr} }

Expression = Identifier exp:((mem:Member_Access {return {mem:mem}} / func:Method_Call {return {func:func}} / text:$Text+ {return {text:text}} ))* {return {exp:exp}}
Member_Access = OpenParen? lhs:$valid_variable_characters+'.'rhs:$valid_variable_characters+ CloseParen? {return {loc: location(), lhs:lhs, rhs:rhs}}
Method_Call = OpenParen exp:valid_expression_characters+ (MethodCall / CloseParen) {return "Hello"}


/*Block = lhs:Text? ex:Expression* rhs:Text?*/

Text = [^@]

/*Expression = '@' stuff:(ParenExpression / VariableExpression){return stuff}
VariableExpression = Text [^ \t\n\r]*
ParenExpression = OpenParen Text CloseParen*/
OpenParen = '('
CloseParen = ')'

Identifier = '@'
Expr = Identifier exp:(MethodCall* / MemberAccess*)  {return exp}
MethodCall = (OpenParen $AtomicExpression CloseParen)
MemberAccess = lhs:AtomicExpression+ rhs:('.' inner:AtomicExpression+ {return inner}) {return {type: 'MemberAccess', lhs: lhs, rhs:rhs}}
AtomicExpression = valid_variable_characters



/**Expression
  = head:Term tail:(_ ("+" / "-") _ Term)* {
      return tail.reduce(function(result, element) {
        if (element[1] === "+") { return result + element[3]; }
        if (element[1] === "-") { return result - element[3]; }
      }, head);
    }**/


ws "whitespace"
  = [ \t\n\r]
char "character" = [^ \t\n\r]*
valid_variable_characters = [a-zA-Z0-9_]
valid_expression_characters = [A-Z]
              