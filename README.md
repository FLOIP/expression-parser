# Floip Expression Evaluator

This project has everything needed to parse and evaluate expressions as defined
in [The Flow Expressions Specification](https://floip.gitbooks.io/flow-specification/content/fundamentals/expressions.html)

The expression parser is written in PEG and compiled with PEG.js into both a
js target and a php target.

The included `makefile` can be used to build the parsers:

`make`, the default task, will build the js and php PEG parsers using `docker`
for the environment by default. You can set the environment variable ENV to nil
to skip using docker -- e.g. `make ENV=`

Tests via phpunit are included in `/tests`

