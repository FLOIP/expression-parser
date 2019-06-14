
/**
 * This block is the intiializer. Code here will be run before the parser
 * starts parsing, and is available to predicates.
 */
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
      'lhs' => $lhs,
      'rhs' => $rhs,
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
    // we can build the location information the same way as
    // it is available in js via location()
    $_location = function() {
        $offset_start = $this->peg_reportedPos;
        $offset_end = $this->peg_currPos;
        $compute_pd_start = $this->peg_computePosDetails($offset_start);
        $compute_pd_end = $this->peg_computePosDetails($offset_end);
      return [
        'start' => [
          'offset' => $offset_start,
          'line' => $compute_pd_start['line'],
          'column' => $compute_pd_start['column'],
        ],
        'end' => [
          'offset' => $offset_end,
          'line' => $compute_pd_end['line'],
          'column' => $compute_pd_end['column'],
        ]
      ];
    };
    // Bind the location fn to the parser instance to allow private access
    $this->_location = $_location->bindTo($this);
  ?> **/
}

/**
 * We look for any number of expressions or escaped identifiers in the string.
 * Just consume all other text and return nothing.
 */
Start = expr:(Escaped_Identifier / ex:Expression / $Text+ {} )* {
    return expr
    /** <?php
      return $expr;
    ?> **/
}

/**
 * An expression can look like -- @(FUNC_CALL(args/expression)) / @(member.access) / @(member) / @member.access / @member
 * Top-level expressions always start with an identifier -- inner ones do not.
 */
Expression = ws* Text* id:(Identifier {return location() /**<?php return call_user_func($this->_location); ?>**/}) OpenParen? ex:Expression_Types cp:(CloseParen? {return location() /**<?php return call_user_func($this->_location); ?>**/}) ws* {
  // we want the location to begin with the identifier for a given expression
  // we want the location to end with the closing paren (or where it would be if absent)
  ex.location.start = id.start;
  ex.location.end = cp.end;
  return ex
  /** <?php
    $ex['location'] = ['start' => $id['start'], 'end' => $cp['end']];
    return $ex;
  ?> **/
}

/**
 * There are different types of expressions with different syntax.
 * The order they are expressed here is the order in which the parser tries
 * to match them.
 */
Expression_Types = Math / Logic / Function / Member_Access

/**
 * Function looks like @(SOME_METHOD(argument, argument...))
 * Functions always wrap their arguments in parens.
 */
Function = call:$valid_expression_characters+ OpenParen args:( Function_Args* ) CloseParen {
  return new method(call, args, location())
  /** <?php
    return call_user_func_array($this->_method, [$call, $args]);
  ?> **/
  }

/**
 * Function arguments are variadic and comma delimited
 */
Function_Args = arg:(arg:Function_Arg_Types Arg_Delimiter? {return arg /**<?php return $arg;?> **/}) {return arg /**<?php return $arg;?> **/}

/**
 * Functions can take any other kind of expression as an argument, or quoted text, or numbers.
 * This means that you can also nest functions deeply.
 */
Function_Arg_Types = Function_Arg_Inner_Function / Math / Logic / Member_Access / Quote ch:$chars+ Quote {return ch /**<?php return $ch; ?>**/} / $('-'* numbers+)
Function_Arg_Inner_Function = arg:Function Arg_Delimiter? {return arg /**<?php return $arg;?> **/}

/**
 * Member access can look like 'contact.name' or 'contact'
 * In the case a user passes a string like 'google.com', which can look like a
 * member access, as long as 'google' does not exist in the context then our
 * evaluator should just print the literal 'google.com'
 */
Member_Access = lhs:$AtomicExpression+ rhs:('.' inner:$AtomicExpression+ {return inner /**<?php return $inner;?> **/})? {
  return new member(lhs, rhs, location())
  /** <?php
    return call_user_func_array($this->_member, [$lhs, $rhs]);
  ?> **/
}

/**
 * Math looks like @(1 + 2)
 */
Math = lhs:(Function / Member_Access / $numbers+) ws* op:$math_chars ws+ rhs:(Function / Member_Access / $numbers+) ws* {
  return new math(lhs, rhs, op, location())
  /** <?php
    return call_user_func_array($this->_math, [$lhs, $rhs, $op]);
  ?> **/
}

/**
 * Logic looks like @(1 < 2)
 */
Logic = lhs:(Function / Member_Access / $numbers+) ws* op:$logic_chars ws+ rhs:(Function / Member_Access / $numbers+) ws* {
  return new logic(lhs, rhs, op, location())
  /** <?php
    return call_user_func_array($this->_logic, [$lhs, $rhs, $op]);
  ?> **/
}

/**
 * We can ignore the identifier by typing it twice, i.e. '@@' => '@'
 */
Escaped_Identifier = Identifier Identifier {
  return new escape(location())
  /** <?php
    return call_user_func_array($this->_escape, []);
  ?> **/
}

Quote = '"'
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

logic_chars = '<=' / '>=' / [=<>!]
math_chars = [-+*\^/]
numbers = [0-9.]
