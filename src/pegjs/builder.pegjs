{
  const ifControl = function(condition, trueResult, falseResult) {
  	return 'IF(' + condition + ",'" + trueResult + "', '" + (falseResult == null ? '' : falseResult) + "')"
  }
}

Root = (IfControl / AndControl / ws {return null})*

AndControl = 'if' ws* conditions:MultiCondition ws* trueResult:Statement ws* falseResult:ElseControl? {
	return ifControl("AND(" + conditions.reduce((a, b) => a + ',' + b) + ")", trueResult, falseResult)
}

IfControl = 'if' ws* condition:(Condition) ws* trueResult:Statement ws* falseResult:ElseControl? {
  return ifControl(condition, trueResult, falseResult == null ? '' : falseResult)
}

ElseControl = 'else' ws* output:Statement { return output }

Expression = lhs:$(valid_variable_characters+ ('.' valid_variable_characters+)*) ws* op:$(logic_chars+) ws* rhs:$([^)&]+) {
	return lhs + ' ' + op + ' ' + rhs
}

Condition = OpenParen exp:$Expression CloseParen { return exp }

MultiCondition = OpenParen ands:(exp:$Expression ws* {return exp}/ ws* '&&' ws* exp:$Expression ws* {return exp})* CloseParen { return ands }

Statement = OpenStatement ws* output:(QuotedText / ws* / IfControl) ws* CloseStatement { return output }



Quote = '"'
QuotedText = SingleQuotedText / DoubleQuotedText
SingleQuotedText = "'" ch:$[^']+ "'" {return ch; /**<?php return $ch; ?>**/}
DoubleQuotedText = '"' ch:$[^"]+ '"' {return ch; /**<?php return $ch; ?>**/}
OpenParen = '('
CloseParen = ')'
OpenStatement = '{'
CloseStatement = '}'
Identifier = '@'
Concat_Operator = '&'
Arg_Delimiter = (',' ws*)
MemberVariable = valid_variable_characters
Text = [^@]
chars = [a-zA-Z_0-9]
ws "whitespace"
  = [ \t\n\r]
valid_variable_characters = [a-zA-Z_]
valid_expression_characters = valid_variable_characters

logic_chars = '<=' / '>=' / [=<>] / '!='
math_chars = [-+*\^/]
numbers = [0-9.]
