
{
  var member = function(key, value, location) {
    return {
      type: 'MEMBER',
      key: key,
      value: value,
      location: location,
    }
  }
  /** <?php
  $this->_member = function($key, $value, $location) {
    return (object)[
      'type' => 'MEMBER',
      'key' => $key,
      'value' => $value,
      'location' => $location
    ];
  };
  ?> **/
  var method = function(call, args, location) {
    return {
      type: 'METHOD',
      call: call,
      args: args,
      location: location
    }
  }
  /** <?php
  $this->_method = function($call, $args, $location) {
    return (object)[
      'type' => 'MEMBER',
      'call' => $call,
      'args' => $args,
      'location' => $location
    ];
  };
  ?> **/
  var math = function(lhs, rhs, operator, location) {
    return {
      type: 'MATH',
      lhs: lhs,
      rhs: rhs,
      operator: operator,
      location: location
    }
  }
  /** <?php
  $this->_math = function($lhs, $rhs, $operator, $location) {
    return (object)[
      'type' => 'MATH',
      'rhs' => $rhs,
      'lhs' => $lhs,
      'operator' => $operator,
      'location' => $location
    ];
  };
  ?> **/
  var logic = function(lhs, rhs, operator, location) {
    return {
      type: 'LOGIC',
      lhs: lhs,
      rhs: rhs,
      operator: operator,
      location: location
    }
  }
  /** <?php
  $this->_logic = function($lhs, $rhs, $operator, $location) {
    return (object)[
      'type' => 'LOGIC',
      'rhs' => $rhs,
      'lhs' => $lhs,
      'operator' => $operator,
      'location' => $location
    ];
  };
  ?> **/
  var escape = function(location) {
    return {
      type: 'ESCAPE',
      location: location
    }
  }
  /** <?php
    $this->_escape = function($location) {
      return (object)[
        'type' => 'ESCAPE',
        'location' => $location
      ];
    };
  ?> **/
}

Block = expr:(Escaped_Identifier / ex:Expression / $Text+ {} )* {
    return expr
    /** <?php
      return $expr;
    ?> **/
}

// An expression can look like -- @(FUNC_CALL(args/expression)) / @(member.access) / @(member) / @member.access / @member
Expression = ws* Text* Identifier OpenParen? ex:(Function / Math / Logic / Member_Access) CloseParen? ws* {
  return ex
  /** <?php
    return $ex;
  ?> **/
}

// Function looks like @(SOME_METHOD(arguments))
Function = call:$valid_expression_characters+ OpenParen args:( Function_Args* ) CloseParen {
  return new method(call, args, location())
  /** <?php
    return $this->_method($call, $args, $this->location());
  ?> **/
  }

Function_Args = arg:(arg:Function Arg_Delimiter? {return arg /**<?php return $arg;?> **/} / arg:(Math / Logic / Member_Access / $chars+) Arg_Delimiter? {return arg /**<?php return $arg;?> **/}) {return arg /**<?php return $arg;?> **/}

// Member access -- contact.name | contact
Member_Access = lhs:$AtomicExpression+ rhs:('.' inner:$AtomicExpression+ {return inner /**<?php return $inner;?> **/})? {
  return new member(lhs, rhs, location())
  /** <?php
    return $this->_member($lhs, $rhs, $this->location());
  ?> **/
}

// Logic
Math = lhs:(Member_Access / $numbers+) ws* op:$math_chars ws+ rhs:(Member_Access / $numbers+) ws* {
  return new math(lhs, rhs, op, location())
  /** <?php
    return $this->_math($lhs, $rhs, $op, $this->location());
  ?> **/
}
Logic = lhs:(Member_Access / $numbers+) ws* op:$logic_chars ws+ rhs:(Member_Access / $numbers+) ws* {
  return new logic(lhs, rhs, op, location())
  /** <?php
    return $this->_logic($lhs, $rhs, $op, $this->location());
  ?> **/
}

Escaped_Identifier = Identifier Identifier {
  return new escape(location())
  /** <?php
    return $this->_escape($this->location());
  ?> **/
}

OpenParen = '('
CloseParen = ')'
Identifier = '@' 
Arg_Delimiter = (',' ws*)
AtomicExpression = valid_variable_characters
Text = [^@]
chars = [a-zA-Z0-9 ]
ws "whitespace"
  = [ \t\n\r]
valid_variable_characters = [a-zA-Z_]
valid_expression_characters = valid_variable_characters

logic_chars = [=<>!]
math_chars = [-+*\^/]
numbers = [0-9.]
