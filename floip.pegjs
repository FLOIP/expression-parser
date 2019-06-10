
{
  var member = function(key, value, location) {
    return {
      type: 'MEMBER',
      key: key,
      value: value,
      location: location,
    }
  }
  var method = function(call, args, location) {
    return {
      type: 'METHOD',
      call: call,
      args: args,
      location: location
    }
  }
  var math = function(lhs, rhs, operator, location) {
    return {
      type: 'MATH',
      lhs: lhs,
      rhs: rhs,
      operator: operator,
      location: location
    }
  }
  var logic = function(lhs, rhs, operator, location) {
    return {
      type: 'LOGIC',
      lhs: lhs,
      rhs: rhs,
      operator: operator,
      location: location
    }
  }
}

Block = expr:(Escaped_Identifier / ex:Expression / $Text+ {} )* {
    return expr
}

// todo -- logic / math expressions @(contact.age + 2) @(contact.age <= 18)

// An expression can look like -- @(FUNC_CALL(args/expression)) / @(member.access) / @(member) / @member.access / @member
Expression = ws* Text* Identifier OpenParen? ex:(Function / Math / Member_Access) CloseParen? ws* {return ex}

// Function looks like @(SOME_METHOD(arguments))
Function = call:$valid_expression_characters+ args:(OpenParen inner:Function_Args* CloseParen {return {inner}}) {return new method(call, args, location())}

Function_Args = arg:(arg:Function Arg_Delimiter? {return arg} / arg:(Math / Member_Access) Arg_Delimiter? {return arg}) {return arg}

// Member access -- contact.name | contact
Member_Access = lhs:$AtomicExpression+ rhs:('.' inner:$AtomicExpression+ {return inner})? {return new member(lhs, rhs, location())}

// Logic
Math = lhs:(Member_Access / $not_math_chars+) ws+ op:$math_chars ws+ rhs:(Member_Access / $not_math_chars+) ws* {return new math(lhs, rhs, op, location())}
Comparison = .+



OpenParen = '('
CloseParen = ')'
Identifier = '@' 
Arg_Delimiter = (',' ws*)
Escaped_Identifier = Identifier Identifier {return {type: 'ESCAPE', loc: location()}}
AtomicExpression = valid_variable_characters
Text = [^@]
ws "whitespace"
  = [ \t\n\r]
valid_variable_characters = [a-zA-Z_]
valid_expression_characters = [A-Z_]
math_chars = [-+*/]
not_math_chars = [^-+*/()]
valid_logic_characters = [=<>!]
