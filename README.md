# Floip Expression Evaluator

This project has everything needed to parse and evaluate expressions as defined
in [The Flow Expressions Specification](https://floip.gitbooks.io/flow-specification/content/fundamentals/expressions.html)

The expression parser is written in PEG and compiled with 
[PEG.js](https://pegjs.org/) into both a js target and a php target.

# Building
The included `makefile` should be used to build the parsers:

`make`, the default task, will build the js and php PEG parsers using `docker`
for the environment by default. You can set the environment variable USE_DOCKER 
to anything but 'true' to skip using docker -- e.g. `make USE_DOCKER=false`

Tests via phpunit are included in `/tests` and should be run with `make test`
These tests will ensure compatibility with Laravel 5.1 and 5.8.
These tests will ensure compatibility with PHP 5.5 and PHP 7.2.
These tests are run automatically when a push is made to bitbucket.

# Releasing
1. Always rebuild the parsers and run the evaluator tests
2. Tag the release with the new semantic version (e.g. `git tag v1.0.3`)
3. Push the tag (e.g. `git push --tags`)

# Including in a Viamo project
## PHP
Add the repository to your `composer.json` like so:

```json
    "repositories": [
        {
            "type": "vcs",
            "url": "git@bitbucket.org:voto/floip-expression-evaluator.git"
        }
    ],
```

And then add this package as a new dependency:
`composer require viamo/floip-eval "^1.0"`

or add directly to your `composer.json` and `composer update`:
```json
    "require": {
        "viamo/floip-eval": "^1.0"
    }
```

## NPM
Add the repository to your `package.json` like so:
```json
  "dependencies": {
    "floip-eval": "bitbucket:voto/floip-expression-evaluator#v1.0.1"
  }
```
And then `npm install`

# How to add a new Expression type
To show how the different parts of this project work together, let's go through 
an example of adding a new expression type to parse and evaluate.

The expression type we will add will look for a string like:
```
Math is easy 1, 2, @(1 + 1 + 1)
```
and evaluate it to
```
Math is easy as 1, 2, 3
```

## 1. Adding PEG grammar

The first thing we should do is add our expression to the grammar in 
`src/pegjs/floip.pegjs`.

Each definition in this file is built on rules, which themselves can be built 
on other rules. Ultimately, each rule will match some text.


```
1. Start = (Expression / $Text+)*

2. Expression = Escaped_Identifier / Closed_Expression / Open_Expression

3. Closed_Expression = id:Expression_Identifier OpenParen ex:Expression_Types cp:(CloseParen {return location() /**<?php return call_user_func($this->_location); ?> **/})

4. Expression_Types = Escaped_Identifier / Logic / Concatenation / Function / Member_Access
```
1. We can see that the grammar starts by looking for either an expression or text:
2. Our grammar is a _closed expression_ since it's enclosed in parens, so we will 
add it to the rules of Closed_Expression.
3. We can see here that a Closed_Expression matches Expression_Types:
4. This is where we want to add our new expression type grammar!

We will add a new rule (Math) to Expression_Types:
```
Expression_Types = Escaped_Identifier / Math / Logic / Concatenation / Function / Member_Access
```

And we can then define the rule:
```
1. Math = lhs:Math_Arg ws* op:$math_chars ws+ rhs:(Math / Math_Arg) ws* {
  return new math(lhs, rhs, op, location())
  /** <?php
    return call_user_func_array($this->_math, [$lhs, $rhs, $op]);
  ?> **/
}

2. Math_Arg = Math_Arg_Inner_Math / Function / Member_Access / $numbers+
3. Math_Arg_Inner_Math = OpenParen child:Math CloseParen { return child; /**<?php return $child; ?>**/}
4. math_chars = [-+*\^/]
```

1. In our new rule, we an argument that is followed by a math operator and another 
argument.
2. It's a good idea to specify arguments in another rule, so that we can add to 
them later (like how we did with Expression_Types).
Rules in the form `foo / bar` match from left to right, so it's important to 
consider precedence!

### Building objects in your AST
You may have noticed that there was some object creation happening in our new
Math expresion.
For our AST, we want expressions to be represented as objects so that we can 
describe the properties of the expression, such as expression arguments and 
location in the string.

At the top of our grammar file (`src/pegjs/floip.pegjs`) we can define 
variables and functions which we can use in rules:

```js
  var math = function(lhs, rhs, operator, location) {
    return {
      type: 'MATH',
      lhs: lhs,
      rhs: rhs,
      operator: operator,
      location: location
    }
  }
```
Following every rule, we can create a predicate that does something with the 
matching text. In our Math rule, we build a Math object and return it into our
AST.

We can also use PHP in our predicates by surrounding the code with a special 
comment syntax:
```php
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
```

When we parse our string now, we should get an AST that looks like this:
```json
[
   "Math is easy 1, 2, ",
   {
      "type": "MATH",
      "lhs": "1",
      "rhs": {
         "type": "MATH",
         "lhs": "1",
         "rhs": "1",
         "operator": "+",
         "location": {
            "start": {
               "offset": 25,
               "line": 1,
               "column": 26
            },
            "end": {
               "offset": 30,
               "line": 1,
               "column": 31
            }
         }
      },
      "operator": "+",
      "location": {
         "start": {
            "offset": 19,
            "line": 1,
            "column": 20
         },
         "end": {
            "offset": 31,
            "line": 1,
            "column": 32
         }
      }
   }
]
```
#

## Evaluating the new expression type
Now that we have our new grammar, we generate the parsers: `make`

We now need to handle the new expression type in our evaluator.

`src/php/Evaluator.php`

Evaluator::evaluate takes a string and a context and produces a string 
with the evaluated values of each expression contained within the string.

Each expression in the AST is transformed into a Node object, and each Node
has a type -- such as "Math".

So, in order to evaluate our new expression type, we need to create a new 
NodeEvaluator and register it with our Evaluator.

We will create `MathNodeEvaluator` in the namespace 
`Viamo\Floip\Evaluator\MathNodeEvaluator`:

```php
<?php

namespace Viamo\Floip\Evaluator;
use Viamo\Floip\Contract\EvaluatesExpression;

class MathNodeEvaluator implements EvaluatesExpression
{
    public function evaluate(Node $node, array $context) {
        // a naive implementation
        $lhs = $node['lhs'];
        $rhs = $node['rhs'];
        return $lhs + $rhs;
    }

    public function handles() {
        return 'MATH';
    }
}
```

Note that our `MathNodeEvaluator` implements `EvaluatesExpression`. To add a 
new NodeEvaluator to the Evaluator, it must follow this contract.

The Node object we are passed can be accessed just like the plain array it was 
defined as in our PEG grammar.

### Registering the NodeEvaluator

Now, when we are ready to instantiate and use our Evaluator, we will register 
our Math evaluator before evaluating an expression:
```php
   $evaluator = new Evaluator(new Parser);
   $evaluator->addNodeEvaluator(new MathNodeEvaluator);

   $result = $evaluator->evaluate('Math is easy 1, 2, @(1 + 1 + 1)', []);
```

We should now get `Math is easy 1, 2, 3` as the value of `$result`.

### Writing Tests
You should write tests for your new NodeEvaluator!

## Service Provider
Of course, we typically will want all of our NodeEvaluators registered when 
using the Evaluator in a production environment, so we will provide a 
ServiceProvider to do so in `src/php/Providers/ExpressioEvaluatorServiceProvider`.

We can register our new NodeEvaluator in the `getNodeEvaluators` method, which 
returns an array of NodeEvaluators:
```php
    protected function getNodeEvaluators()
    {
        return [
            ...
            new MathNodeEvaluator, // Our new class
        ];
    }
```

Now, after registering our service provider in Laravel, we can get the evaluator
instance with NodeEvaluators already registered by typehinting it in the 
constructor of the class in which we want to use it:
```php
class EvaluationController {

    protected $evaluator;
    protected $user;

    public function __construct(Evaluator $evaluator, User $user) {
        $this->evaluator = $evaluator;
        $this->user = $user;
    }

    public function evaluate(Request $request) {
        return $this->evaluator->evaluate($request->get('expression'), $this->user->getContext());
    }
}
```

