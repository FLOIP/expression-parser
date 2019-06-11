/*
 * Generated by PEG.js 0.10.0.
 *
 * http://pegjs.org/
 */

"use strict";

function peg$subclass(child, parent) {
  function ctor() { this.constructor = child; }
  ctor.prototype = parent.prototype;
  child.prototype = new ctor();
}

function peg$SyntaxError(message, expected, found, location) {
  this.message  = message;
  this.expected = expected;
  this.found    = found;
  this.location = location;
  this.name     = "SyntaxError";

  if (typeof Error.captureStackTrace === "function") {
    Error.captureStackTrace(this, peg$SyntaxError);
  }
}

peg$subclass(peg$SyntaxError, Error);

peg$SyntaxError.buildMessage = function(expected, found) {
  var DESCRIBE_EXPECTATION_FNS = {
        literal: function(expectation) {
          return "\"" + literalEscape(expectation.text) + "\"";
        },

        "class": function(expectation) {
          var escapedParts = "",
              i;

          for (i = 0; i < expectation.parts.length; i++) {
            escapedParts += expectation.parts[i] instanceof Array
              ? classEscape(expectation.parts[i][0]) + "-" + classEscape(expectation.parts[i][1])
              : classEscape(expectation.parts[i]);
          }

          return "[" + (expectation.inverted ? "^" : "") + escapedParts + "]";
        },

        any: function(expectation) {
          return "any character";
        },

        end: function(expectation) {
          return "end of input";
        },

        other: function(expectation) {
          return expectation.description;
        }
      };

  function hex(ch) {
    return ch.charCodeAt(0).toString(16).toUpperCase();
  }

  function literalEscape(s) {
    return s
      .replace(/\\/g, '\\\\')
      .replace(/"/g,  '\\"')
      .replace(/\0/g, '\\0')
      .replace(/\t/g, '\\t')
      .replace(/\n/g, '\\n')
      .replace(/\r/g, '\\r')
      .replace(/[\x00-\x0F]/g,          function(ch) { return '\\x0' + hex(ch); })
      .replace(/[\x10-\x1F\x7F-\x9F]/g, function(ch) { return '\\x'  + hex(ch); });
  }

  function classEscape(s) {
    return s
      .replace(/\\/g, '\\\\')
      .replace(/\]/g, '\\]')
      .replace(/\^/g, '\\^')
      .replace(/-/g,  '\\-')
      .replace(/\0/g, '\\0')
      .replace(/\t/g, '\\t')
      .replace(/\n/g, '\\n')
      .replace(/\r/g, '\\r')
      .replace(/[\x00-\x0F]/g,          function(ch) { return '\\x0' + hex(ch); })
      .replace(/[\x10-\x1F\x7F-\x9F]/g, function(ch) { return '\\x'  + hex(ch); });
  }

  function describeExpectation(expectation) {
    return DESCRIBE_EXPECTATION_FNS[expectation.type](expectation);
  }

  function describeExpected(expected) {
    var descriptions = new Array(expected.length),
        i, j;

    for (i = 0; i < expected.length; i++) {
      descriptions[i] = describeExpectation(expected[i]);
    }

    descriptions.sort();

    if (descriptions.length > 0) {
      for (i = 1, j = 1; i < descriptions.length; i++) {
        if (descriptions[i - 1] !== descriptions[i]) {
          descriptions[j] = descriptions[i];
          j++;
        }
      }
      descriptions.length = j;
    }

    switch (descriptions.length) {
      case 1:
        return descriptions[0];

      case 2:
        return descriptions[0] + " or " + descriptions[1];

      default:
        return descriptions.slice(0, -1).join(", ")
          + ", or "
          + descriptions[descriptions.length - 1];
    }
  }

  function describeFound(found) {
    return found ? "\"" + literalEscape(found) + "\"" : "end of input";
  }

  return "Expected " + describeExpected(expected) + " but " + describeFound(found) + " found.";
};

function peg$parse(input, options) {
  options = options !== void 0 ? options : {};

  var peg$FAILED = {},

      peg$startRuleFunctions = { Block: peg$parseBlock },
      peg$startRuleFunction  = peg$parseBlock,

      peg$c0 = function() {},
      peg$c1 = function(expr) {
          return expr
          /** <?php
            return $expr;
          ?> **/
      },
      peg$c2 = function(ex) {
        return ex
        /** <?php
          return $ex;
        ?> **/
      },
      peg$c3 = function(call, args) {
        return new method(call, args, location())
        /** <?php
          return call_user_func_array($this->_method, [$call, $args]);
        ?> **/
        },
      peg$c4 = function(arg) {return arg /**<?php return $arg;?> **/},
      peg$c5 = ".",
      peg$c6 = peg$literalExpectation(".", false),
      peg$c7 = function(lhs, inner) {return inner /**<?php return $inner;?> **/},
      peg$c8 = function(lhs, rhs) {
        return new member(lhs, rhs, location())
        /** <?php
          return call_user_func_array($this->_member, [$lhs, $rhs]);
        ?> **/
      },
      peg$c9 = function(lhs, op, rhs) {
        return new math(lhs, rhs, op, location())
        /** <?php
          return $this->_math($lhs, $rhs, $op);
        ?> **/
      },
      peg$c10 = function(lhs, op, rhs) {
        return new logic(lhs, rhs, op, location())
        /** <?php
          return call_user_func_array($this->_logic, [$lhs, $rhs, $op]);
        ?> **/
      },
      peg$c11 = function() {
        return new escape(location())
        /** <?php
          return call_user_func_array($this->_escape, []);
        ?> **/
      },
      peg$c12 = "(",
      peg$c13 = peg$literalExpectation("(", false),
      peg$c14 = ")",
      peg$c15 = peg$literalExpectation(")", false),
      peg$c16 = "@",
      peg$c17 = peg$literalExpectation("@", false),
      peg$c18 = ",",
      peg$c19 = peg$literalExpectation(",", false),
      peg$c20 = /^[^@]/,
      peg$c21 = peg$classExpectation(["@"], true, false),
      peg$c22 = /^[a-zA-Z0-9 ]/,
      peg$c23 = peg$classExpectation([["a", "z"], ["A", "Z"], ["0", "9"], " "], false, false),
      peg$c24 = peg$otherExpectation("whitespace"),
      peg$c25 = /^[ \t\n\r]/,
      peg$c26 = peg$classExpectation([" ", "\t", "\n", "\r"], false, false),
      peg$c27 = /^[a-zA-Z_]/,
      peg$c28 = peg$classExpectation([["a", "z"], ["A", "Z"], "_"], false, false),
      peg$c29 = /^[=<>!]/,
      peg$c30 = peg$classExpectation(["=", "<", ">", "!"], false, false),
      peg$c31 = /^[\-+*\^\/]/,
      peg$c32 = peg$classExpectation(["-", "+", "*", "^", "/"], false, false),
      peg$c33 = /^[0-9.]/,
      peg$c34 = peg$classExpectation([["0", "9"], "."], false, false),

      peg$currPos          = 0,
      peg$savedPos         = 0,
      peg$posDetailsCache  = [{ line: 1, column: 1 }],
      peg$maxFailPos       = 0,
      peg$maxFailExpected  = [],
      peg$silentFails      = 0,

      peg$result;

  if ("startRule" in options) {
    if (!(options.startRule in peg$startRuleFunctions)) {
      throw new Error("Can't start parsing from rule \"" + options.startRule + "\".");
    }

    peg$startRuleFunction = peg$startRuleFunctions[options.startRule];
  }

  function text() {
    return input.substring(peg$savedPos, peg$currPos);
  }

  function location() {
    return peg$computeLocation(peg$savedPos, peg$currPos);
  }

  function expected(description, location) {
    location = location !== void 0 ? location : peg$computeLocation(peg$savedPos, peg$currPos)

    throw peg$buildStructuredError(
      [peg$otherExpectation(description)],
      input.substring(peg$savedPos, peg$currPos),
      location
    );
  }

  function error(message, location) {
    location = location !== void 0 ? location : peg$computeLocation(peg$savedPos, peg$currPos)

    throw peg$buildSimpleError(message, location);
  }

  function peg$literalExpectation(text, ignoreCase) {
    return { type: "literal", text: text, ignoreCase: ignoreCase };
  }

  function peg$classExpectation(parts, inverted, ignoreCase) {
    return { type: "class", parts: parts, inverted: inverted, ignoreCase: ignoreCase };
  }

  function peg$anyExpectation() {
    return { type: "any" };
  }

  function peg$endExpectation() {
    return { type: "end" };
  }

  function peg$otherExpectation(description) {
    return { type: "other", description: description };
  }

  function peg$computePosDetails(pos) {
    var details = peg$posDetailsCache[pos], p;

    if (details) {
      return details;
    } else {
      p = pos - 1;
      while (!peg$posDetailsCache[p]) {
        p--;
      }

      details = peg$posDetailsCache[p];
      details = {
        line:   details.line,
        column: details.column
      };

      while (p < pos) {
        if (input.charCodeAt(p) === 10) {
          details.line++;
          details.column = 1;
        } else {
          details.column++;
        }

        p++;
      }

      peg$posDetailsCache[pos] = details;
      return details;
    }
  }

  function peg$computeLocation(startPos, endPos) {
    var startPosDetails = peg$computePosDetails(startPos),
        endPosDetails   = peg$computePosDetails(endPos);

    return {
      start: {
        offset: startPos,
        line:   startPosDetails.line,
        column: startPosDetails.column
      },
      end: {
        offset: endPos,
        line:   endPosDetails.line,
        column: endPosDetails.column
      }
    };
  }

  function peg$fail(expected) {
    if (peg$currPos < peg$maxFailPos) { return; }

    if (peg$currPos > peg$maxFailPos) {
      peg$maxFailPos = peg$currPos;
      peg$maxFailExpected = [];
    }

    peg$maxFailExpected.push(expected);
  }

  function peg$buildSimpleError(message, location) {
    return new peg$SyntaxError(message, null, null, location);
  }

  function peg$buildStructuredError(expected, found, location) {
    return new peg$SyntaxError(
      peg$SyntaxError.buildMessage(expected, found),
      expected,
      found,
      location
    );
  }

  function peg$parseBlock() {
    var s0, s1, s2, s3, s4, s5;

    s0 = peg$currPos;
    s1 = [];
    s2 = peg$parseEscaped_Identifier();
    if (s2 === peg$FAILED) {
      s2 = peg$parseExpression();
      if (s2 === peg$FAILED) {
        s2 = peg$currPos;
        s3 = peg$currPos;
        s4 = [];
        s5 = peg$parseText();
        if (s5 !== peg$FAILED) {
          while (s5 !== peg$FAILED) {
            s4.push(s5);
            s5 = peg$parseText();
          }
        } else {
          s4 = peg$FAILED;
        }
        if (s4 !== peg$FAILED) {
          s3 = input.substring(s3, peg$currPos);
        } else {
          s3 = s4;
        }
        if (s3 !== peg$FAILED) {
          peg$savedPos = s2;
          s3 = peg$c0();
        }
        s2 = s3;
      }
    }
    while (s2 !== peg$FAILED) {
      s1.push(s2);
      s2 = peg$parseEscaped_Identifier();
      if (s2 === peg$FAILED) {
        s2 = peg$parseExpression();
        if (s2 === peg$FAILED) {
          s2 = peg$currPos;
          s3 = peg$currPos;
          s4 = [];
          s5 = peg$parseText();
          if (s5 !== peg$FAILED) {
            while (s5 !== peg$FAILED) {
              s4.push(s5);
              s5 = peg$parseText();
            }
          } else {
            s4 = peg$FAILED;
          }
          if (s4 !== peg$FAILED) {
            s3 = input.substring(s3, peg$currPos);
          } else {
            s3 = s4;
          }
          if (s3 !== peg$FAILED) {
            peg$savedPos = s2;
            s3 = peg$c0();
          }
          s2 = s3;
        }
      }
    }
    if (s1 !== peg$FAILED) {
      peg$savedPos = s0;
      s1 = peg$c1(s1);
    }
    s0 = s1;

    return s0;
  }

  function peg$parseExpression() {
    var s0, s1, s2, s3, s4, s5, s6, s7, s8;

    s0 = peg$currPos;
    s1 = [];
    s2 = peg$parsews();
    while (s2 !== peg$FAILED) {
      s1.push(s2);
      s2 = peg$parsews();
    }
    if (s1 !== peg$FAILED) {
      s2 = [];
      s3 = peg$parseText();
      while (s3 !== peg$FAILED) {
        s2.push(s3);
        s3 = peg$parseText();
      }
      if (s2 !== peg$FAILED) {
        s3 = peg$parseIdentifier();
        if (s3 !== peg$FAILED) {
          s4 = peg$parseOpenParen();
          if (s4 === peg$FAILED) {
            s4 = null;
          }
          if (s4 !== peg$FAILED) {
            s5 = peg$parseFunction();
            if (s5 === peg$FAILED) {
              s5 = peg$parseMath();
              if (s5 === peg$FAILED) {
                s5 = peg$parseLogic();
                if (s5 === peg$FAILED) {
                  s5 = peg$parseMember_Access();
                }
              }
            }
            if (s5 !== peg$FAILED) {
              s6 = peg$parseCloseParen();
              if (s6 === peg$FAILED) {
                s6 = null;
              }
              if (s6 !== peg$FAILED) {
                s7 = [];
                s8 = peg$parsews();
                while (s8 !== peg$FAILED) {
                  s7.push(s8);
                  s8 = peg$parsews();
                }
                if (s7 !== peg$FAILED) {
                  peg$savedPos = s0;
                  s1 = peg$c2(s5);
                  s0 = s1;
                } else {
                  peg$currPos = s0;
                  s0 = peg$FAILED;
                }
              } else {
                peg$currPos = s0;
                s0 = peg$FAILED;
              }
            } else {
              peg$currPos = s0;
              s0 = peg$FAILED;
            }
          } else {
            peg$currPos = s0;
            s0 = peg$FAILED;
          }
        } else {
          peg$currPos = s0;
          s0 = peg$FAILED;
        }
      } else {
        peg$currPos = s0;
        s0 = peg$FAILED;
      }
    } else {
      peg$currPos = s0;
      s0 = peg$FAILED;
    }

    return s0;
  }

  function peg$parseFunction() {
    var s0, s1, s2, s3, s4;

    s0 = peg$currPos;
    s1 = peg$currPos;
    s2 = [];
    s3 = peg$parsevalid_variable_characters();
    if (s3 !== peg$FAILED) {
      while (s3 !== peg$FAILED) {
        s2.push(s3);
        s3 = peg$parsevalid_variable_characters();
      }
    } else {
      s2 = peg$FAILED;
    }
    if (s2 !== peg$FAILED) {
      s1 = input.substring(s1, peg$currPos);
    } else {
      s1 = s2;
    }
    if (s1 !== peg$FAILED) {
      s2 = peg$parseOpenParen();
      if (s2 !== peg$FAILED) {
        s3 = [];
        s4 = peg$parseFunction_Args();
        while (s4 !== peg$FAILED) {
          s3.push(s4);
          s4 = peg$parseFunction_Args();
        }
        if (s3 !== peg$FAILED) {
          s4 = peg$parseCloseParen();
          if (s4 !== peg$FAILED) {
            peg$savedPos = s0;
            s1 = peg$c3(s1, s3);
            s0 = s1;
          } else {
            peg$currPos = s0;
            s0 = peg$FAILED;
          }
        } else {
          peg$currPos = s0;
          s0 = peg$FAILED;
        }
      } else {
        peg$currPos = s0;
        s0 = peg$FAILED;
      }
    } else {
      peg$currPos = s0;
      s0 = peg$FAILED;
    }

    return s0;
  }

  function peg$parseFunction_Args() {
    var s0, s1, s2, s3, s4;

    s0 = peg$currPos;
    s1 = peg$currPos;
    s2 = peg$parseFunction();
    if (s2 !== peg$FAILED) {
      s3 = peg$parseArg_Delimiter();
      if (s3 === peg$FAILED) {
        s3 = null;
      }
      if (s3 !== peg$FAILED) {
        peg$savedPos = s1;
        s2 = peg$c4(s2);
        s1 = s2;
      } else {
        peg$currPos = s1;
        s1 = peg$FAILED;
      }
    } else {
      peg$currPos = s1;
      s1 = peg$FAILED;
    }
    if (s1 === peg$FAILED) {
      s1 = peg$currPos;
      s2 = peg$parseMath();
      if (s2 === peg$FAILED) {
        s2 = peg$parseLogic();
        if (s2 === peg$FAILED) {
          s2 = peg$parseMember_Access();
          if (s2 === peg$FAILED) {
            s2 = peg$currPos;
            s3 = [];
            s4 = peg$parsechars();
            if (s4 !== peg$FAILED) {
              while (s4 !== peg$FAILED) {
                s3.push(s4);
                s4 = peg$parsechars();
              }
            } else {
              s3 = peg$FAILED;
            }
            if (s3 !== peg$FAILED) {
              s2 = input.substring(s2, peg$currPos);
            } else {
              s2 = s3;
            }
          }
        }
      }
      if (s2 !== peg$FAILED) {
        s3 = peg$parseArg_Delimiter();
        if (s3 === peg$FAILED) {
          s3 = null;
        }
        if (s3 !== peg$FAILED) {
          peg$savedPos = s1;
          s2 = peg$c4(s2);
          s1 = s2;
        } else {
          peg$currPos = s1;
          s1 = peg$FAILED;
        }
      } else {
        peg$currPos = s1;
        s1 = peg$FAILED;
      }
    }
    if (s1 !== peg$FAILED) {
      peg$savedPos = s0;
      s1 = peg$c4(s1);
    }
    s0 = s1;

    return s0;
  }

  function peg$parseMember_Access() {
    var s0, s1, s2, s3, s4, s5, s6;

    s0 = peg$currPos;
    s1 = peg$currPos;
    s2 = [];
    s3 = peg$parsevalid_variable_characters();
    if (s3 !== peg$FAILED) {
      while (s3 !== peg$FAILED) {
        s2.push(s3);
        s3 = peg$parsevalid_variable_characters();
      }
    } else {
      s2 = peg$FAILED;
    }
    if (s2 !== peg$FAILED) {
      s1 = input.substring(s1, peg$currPos);
    } else {
      s1 = s2;
    }
    if (s1 !== peg$FAILED) {
      s2 = peg$currPos;
      if (input.charCodeAt(peg$currPos) === 46) {
        s3 = peg$c5;
        peg$currPos++;
      } else {
        s3 = peg$FAILED;
        if (peg$silentFails === 0) { peg$fail(peg$c6); }
      }
      if (s3 !== peg$FAILED) {
        s4 = peg$currPos;
        s5 = [];
        s6 = peg$parsevalid_variable_characters();
        if (s6 !== peg$FAILED) {
          while (s6 !== peg$FAILED) {
            s5.push(s6);
            s6 = peg$parsevalid_variable_characters();
          }
        } else {
          s5 = peg$FAILED;
        }
        if (s5 !== peg$FAILED) {
          s4 = input.substring(s4, peg$currPos);
        } else {
          s4 = s5;
        }
        if (s4 !== peg$FAILED) {
          peg$savedPos = s2;
          s3 = peg$c7(s1, s4);
          s2 = s3;
        } else {
          peg$currPos = s2;
          s2 = peg$FAILED;
        }
      } else {
        peg$currPos = s2;
        s2 = peg$FAILED;
      }
      if (s2 === peg$FAILED) {
        s2 = null;
      }
      if (s2 !== peg$FAILED) {
        peg$savedPos = s0;
        s1 = peg$c8(s1, s2);
        s0 = s1;
      } else {
        peg$currPos = s0;
        s0 = peg$FAILED;
      }
    } else {
      peg$currPos = s0;
      s0 = peg$FAILED;
    }

    return s0;
  }

  function peg$parseMath() {
    var s0, s1, s2, s3, s4, s5, s6, s7;

    s0 = peg$currPos;
    s1 = peg$parseMember_Access();
    if (s1 === peg$FAILED) {
      s1 = peg$currPos;
      s2 = [];
      s3 = peg$parsenumbers();
      if (s3 !== peg$FAILED) {
        while (s3 !== peg$FAILED) {
          s2.push(s3);
          s3 = peg$parsenumbers();
        }
      } else {
        s2 = peg$FAILED;
      }
      if (s2 !== peg$FAILED) {
        s1 = input.substring(s1, peg$currPos);
      } else {
        s1 = s2;
      }
    }
    if (s1 !== peg$FAILED) {
      s2 = [];
      s3 = peg$parsews();
      while (s3 !== peg$FAILED) {
        s2.push(s3);
        s3 = peg$parsews();
      }
      if (s2 !== peg$FAILED) {
        s3 = peg$currPos;
        s4 = peg$parsemath_chars();
        if (s4 !== peg$FAILED) {
          s3 = input.substring(s3, peg$currPos);
        } else {
          s3 = s4;
        }
        if (s3 !== peg$FAILED) {
          s4 = [];
          s5 = peg$parsews();
          if (s5 !== peg$FAILED) {
            while (s5 !== peg$FAILED) {
              s4.push(s5);
              s5 = peg$parsews();
            }
          } else {
            s4 = peg$FAILED;
          }
          if (s4 !== peg$FAILED) {
            s5 = peg$parseMember_Access();
            if (s5 === peg$FAILED) {
              s5 = peg$currPos;
              s6 = [];
              s7 = peg$parsenumbers();
              if (s7 !== peg$FAILED) {
                while (s7 !== peg$FAILED) {
                  s6.push(s7);
                  s7 = peg$parsenumbers();
                }
              } else {
                s6 = peg$FAILED;
              }
              if (s6 !== peg$FAILED) {
                s5 = input.substring(s5, peg$currPos);
              } else {
                s5 = s6;
              }
            }
            if (s5 !== peg$FAILED) {
              s6 = [];
              s7 = peg$parsews();
              while (s7 !== peg$FAILED) {
                s6.push(s7);
                s7 = peg$parsews();
              }
              if (s6 !== peg$FAILED) {
                peg$savedPos = s0;
                s1 = peg$c9(s1, s3, s5);
                s0 = s1;
              } else {
                peg$currPos = s0;
                s0 = peg$FAILED;
              }
            } else {
              peg$currPos = s0;
              s0 = peg$FAILED;
            }
          } else {
            peg$currPos = s0;
            s0 = peg$FAILED;
          }
        } else {
          peg$currPos = s0;
          s0 = peg$FAILED;
        }
      } else {
        peg$currPos = s0;
        s0 = peg$FAILED;
      }
    } else {
      peg$currPos = s0;
      s0 = peg$FAILED;
    }

    return s0;
  }

  function peg$parseLogic() {
    var s0, s1, s2, s3, s4, s5, s6, s7;

    s0 = peg$currPos;
    s1 = peg$parseMember_Access();
    if (s1 === peg$FAILED) {
      s1 = peg$currPos;
      s2 = [];
      s3 = peg$parsenumbers();
      if (s3 !== peg$FAILED) {
        while (s3 !== peg$FAILED) {
          s2.push(s3);
          s3 = peg$parsenumbers();
        }
      } else {
        s2 = peg$FAILED;
      }
      if (s2 !== peg$FAILED) {
        s1 = input.substring(s1, peg$currPos);
      } else {
        s1 = s2;
      }
    }
    if (s1 !== peg$FAILED) {
      s2 = [];
      s3 = peg$parsews();
      while (s3 !== peg$FAILED) {
        s2.push(s3);
        s3 = peg$parsews();
      }
      if (s2 !== peg$FAILED) {
        s3 = peg$currPos;
        s4 = peg$parselogic_chars();
        if (s4 !== peg$FAILED) {
          s3 = input.substring(s3, peg$currPos);
        } else {
          s3 = s4;
        }
        if (s3 !== peg$FAILED) {
          s4 = [];
          s5 = peg$parsews();
          if (s5 !== peg$FAILED) {
            while (s5 !== peg$FAILED) {
              s4.push(s5);
              s5 = peg$parsews();
            }
          } else {
            s4 = peg$FAILED;
          }
          if (s4 !== peg$FAILED) {
            s5 = peg$parseMember_Access();
            if (s5 === peg$FAILED) {
              s5 = peg$currPos;
              s6 = [];
              s7 = peg$parsenumbers();
              if (s7 !== peg$FAILED) {
                while (s7 !== peg$FAILED) {
                  s6.push(s7);
                  s7 = peg$parsenumbers();
                }
              } else {
                s6 = peg$FAILED;
              }
              if (s6 !== peg$FAILED) {
                s5 = input.substring(s5, peg$currPos);
              } else {
                s5 = s6;
              }
            }
            if (s5 !== peg$FAILED) {
              s6 = [];
              s7 = peg$parsews();
              while (s7 !== peg$FAILED) {
                s6.push(s7);
                s7 = peg$parsews();
              }
              if (s6 !== peg$FAILED) {
                peg$savedPos = s0;
                s1 = peg$c10(s1, s3, s5);
                s0 = s1;
              } else {
                peg$currPos = s0;
                s0 = peg$FAILED;
              }
            } else {
              peg$currPos = s0;
              s0 = peg$FAILED;
            }
          } else {
            peg$currPos = s0;
            s0 = peg$FAILED;
          }
        } else {
          peg$currPos = s0;
          s0 = peg$FAILED;
        }
      } else {
        peg$currPos = s0;
        s0 = peg$FAILED;
      }
    } else {
      peg$currPos = s0;
      s0 = peg$FAILED;
    }

    return s0;
  }

  function peg$parseEscaped_Identifier() {
    var s0, s1, s2;

    s0 = peg$currPos;
    s1 = peg$parseIdentifier();
    if (s1 !== peg$FAILED) {
      s2 = peg$parseIdentifier();
      if (s2 !== peg$FAILED) {
        peg$savedPos = s0;
        s1 = peg$c11();
        s0 = s1;
      } else {
        peg$currPos = s0;
        s0 = peg$FAILED;
      }
    } else {
      peg$currPos = s0;
      s0 = peg$FAILED;
    }

    return s0;
  }

  function peg$parseOpenParen() {
    var s0;

    if (input.charCodeAt(peg$currPos) === 40) {
      s0 = peg$c12;
      peg$currPos++;
    } else {
      s0 = peg$FAILED;
      if (peg$silentFails === 0) { peg$fail(peg$c13); }
    }

    return s0;
  }

  function peg$parseCloseParen() {
    var s0;

    if (input.charCodeAt(peg$currPos) === 41) {
      s0 = peg$c14;
      peg$currPos++;
    } else {
      s0 = peg$FAILED;
      if (peg$silentFails === 0) { peg$fail(peg$c15); }
    }

    return s0;
  }

  function peg$parseIdentifier() {
    var s0;

    if (input.charCodeAt(peg$currPos) === 64) {
      s0 = peg$c16;
      peg$currPos++;
    } else {
      s0 = peg$FAILED;
      if (peg$silentFails === 0) { peg$fail(peg$c17); }
    }

    return s0;
  }

  function peg$parseArg_Delimiter() {
    var s0, s1, s2, s3;

    s0 = peg$currPos;
    if (input.charCodeAt(peg$currPos) === 44) {
      s1 = peg$c18;
      peg$currPos++;
    } else {
      s1 = peg$FAILED;
      if (peg$silentFails === 0) { peg$fail(peg$c19); }
    }
    if (s1 !== peg$FAILED) {
      s2 = [];
      s3 = peg$parsews();
      while (s3 !== peg$FAILED) {
        s2.push(s3);
        s3 = peg$parsews();
      }
      if (s2 !== peg$FAILED) {
        s1 = [s1, s2];
        s0 = s1;
      } else {
        peg$currPos = s0;
        s0 = peg$FAILED;
      }
    } else {
      peg$currPos = s0;
      s0 = peg$FAILED;
    }

    return s0;
  }

  function peg$parseText() {
    var s0;

    if (peg$c20.test(input.charAt(peg$currPos))) {
      s0 = input.charAt(peg$currPos);
      peg$currPos++;
    } else {
      s0 = peg$FAILED;
      if (peg$silentFails === 0) { peg$fail(peg$c21); }
    }

    return s0;
  }

  function peg$parsechars() {
    var s0;

    if (peg$c22.test(input.charAt(peg$currPos))) {
      s0 = input.charAt(peg$currPos);
      peg$currPos++;
    } else {
      s0 = peg$FAILED;
      if (peg$silentFails === 0) { peg$fail(peg$c23); }
    }

    return s0;
  }

  function peg$parsews() {
    var s0, s1;

    peg$silentFails++;
    if (peg$c25.test(input.charAt(peg$currPos))) {
      s0 = input.charAt(peg$currPos);
      peg$currPos++;
    } else {
      s0 = peg$FAILED;
      if (peg$silentFails === 0) { peg$fail(peg$c26); }
    }
    peg$silentFails--;
    if (s0 === peg$FAILED) {
      s1 = peg$FAILED;
      if (peg$silentFails === 0) { peg$fail(peg$c24); }
    }

    return s0;
  }

  function peg$parsevalid_variable_characters() {
    var s0;

    if (peg$c27.test(input.charAt(peg$currPos))) {
      s0 = input.charAt(peg$currPos);
      peg$currPos++;
    } else {
      s0 = peg$FAILED;
      if (peg$silentFails === 0) { peg$fail(peg$c28); }
    }

    return s0;
  }

  function peg$parselogic_chars() {
    var s0;

    if (peg$c29.test(input.charAt(peg$currPos))) {
      s0 = input.charAt(peg$currPos);
      peg$currPos++;
    } else {
      s0 = peg$FAILED;
      if (peg$silentFails === 0) { peg$fail(peg$c30); }
    }

    return s0;
  }

  function peg$parsemath_chars() {
    var s0;

    if (peg$c31.test(input.charAt(peg$currPos))) {
      s0 = input.charAt(peg$currPos);
      peg$currPos++;
    } else {
      s0 = peg$FAILED;
      if (peg$silentFails === 0) { peg$fail(peg$c32); }
    }

    return s0;
  }

  function peg$parsenumbers() {
    var s0;

    if (peg$c33.test(input.charAt(peg$currPos))) {
      s0 = input.charAt(peg$currPos);
      peg$currPos++;
    } else {
      s0 = peg$FAILED;
      if (peg$silentFails === 0) { peg$fail(peg$c34); }
    }

    return s0;
  }


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
      return (object)[
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
      return (object)[
        'type' => 'MEMBER',
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
      return (object)[
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
      return (object)[
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
        return (object)[
          'type' => 'ESCAPE',
          'location' => call_user_func($this->_location)
        ];
      };
    ?> **/

    /** <?php
      $_location = function() {
        return (object)[
          'offset' => $this->offset(),
          'line' => $this->line(),
          'column' => $this->column()
        ];
      };
      // Bind the location fn to the parser instance to allow private method access
      $this->_location = $_location->bindTo($this);
    ?> **/


  peg$result = peg$startRuleFunction();

  if (peg$result !== peg$FAILED && peg$currPos === input.length) {
    return peg$result;
  } else {
    if (peg$result !== peg$FAILED && peg$currPos < input.length) {
      peg$fail(peg$endExpectation());
    }

    throw peg$buildStructuredError(
      peg$maxFailExpected,
      peg$maxFailPos < input.length ? input.charAt(peg$maxFailPos) : null,
      peg$maxFailPos < input.length
        ? peg$computeLocation(peg$maxFailPos, peg$maxFailPos + 1)
        : peg$computeLocation(peg$maxFailPos, peg$maxFailPos)
    );
  }
}

module.exports = {
  SyntaxError: peg$SyntaxError,
  parse:       peg$parse
};
