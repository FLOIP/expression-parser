
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
  $this->_member = function($key, $value) {
    return [
      'type' => 'MEMBER',
      'key' => $key,
      'value' => $value,
      'location' => call_user_func($this->_location)
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
  $this->_method = function($call, $args) {
    return [
      'type' => 'METHOD',
      'call' => $call,
      'args' => $args,
      'location' => call_user_func($this->_location)
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
  $this->_math = function($lhs, $rhs, $operator) {
    return [
      'type' => 'MATH',
      'rhs' => $rhs,
      'lhs' => $lhs,
      'operator' => $operator,
      'location' => call_user_func($this->_location)
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
  $this->_logic = function($lhs, $rhs, $operator) {
    return [
      'type' => 'LOGIC',
      'rhs' => $rhs,
      'lhs' => $lhs,
      'operator' => $operator,
      'location' => call_user_func($this->_location)
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
      return [
        'type' => 'ESCAPE',
        'location' => call_user_func($this->_location)
      ];
    };
  ?> **/

  /** <?php
    $_location = function() {
      return [
        'offset' => $this->offset(),
        'line' => $this->line(),
        'column' => $this->column()
      ];
    };
    // Bind the location fn to the parser instance to allow private method access
    $this->_location = $_location->bindTo($this);
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
    return call_user_func_array($this->_method, [$call, $args]);
  ?> **/
  }

Function_Args = arg:(arg:Function Arg_Delimiter? {return arg /**<?php return $arg;?> **/} / arg:(Math / Logic / Member_Access / '"' ch:$chars+ '"' {return ch /**<?php return $ch; ?>**/} / $numbers+) Arg_Delimiter? {return arg /**<?php return $arg;?> **/}) {return arg /**<?php return $arg;?> **/}

// Member access -- contact.name | contact
Member_Access = lhs:$AtomicExpression+ rhs:('.' inner:$AtomicExpression+ {return inner /**<?php return $inner;?> **/})? {
  return new member(lhs, rhs, location())
  /** <?php
    return call_user_func_array($this->_member, [$lhs, $rhs]);
  ?> **/
}

// Logic
Math = lhs:(Member_Access / $numbers+) ws* op:$math_chars ws+ rhs:(Member_Access / $numbers+) ws* {
  return new math(lhs, rhs, op, location())
  /** <?php
    return $this->_math($lhs, $rhs, $op);
  ?> **/
}
Logic = lhs:(Member_Access / $numbers+) ws* op:$logic_chars ws+ rhs:(Member_Access / $numbers+) ws* {
  return new logic(lhs, rhs, op, location())
  /** <?php
    return call_user_func_array($this->_logic, [$lhs, $rhs, $op]);
  ?> **/
}

Escaped_Identifier = Identifier Identifier {
  return new escape(location())
  /** <?php
    return call_user_func_array($this->_escape, []);
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
