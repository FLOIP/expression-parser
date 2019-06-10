PEGJS_TAG="node:8-alpine"
DOCKER_RUN=docker run --rm -it -v `pwd`:/src -u `id -u` -w '/src'
PEGJS=$(DOCKER_RUN) $(PEGJS_TAG) npx pegjs
PARSER_NAME=FloipParser
PARSER_SOURCE=src/grammar/floip.pegjs
PHP_OUT=src/php/$(PARSER_NAME).php
JS_OUT=src/js/$(PARSER_NAME).js

.PHONY: clean default all parse-php parse-js

default: all

node_modules: package.json
	docker run --rm -it -v `pwd`:`pwd` -w `pwd` -u `id -u` $(PEGJS_TAG) npm install

$(PHP_OUT): node_modules $(PARSER_SOURCE)
	$(PEGJS) --plugin phpegjs -o $(PHP_OUT) $(PARSER_SOURCE)

$(JS_OUT): node_modules $(PARSER_SOURCE)
	$(PEGJS) -o $(JS_OUT) $(PARSER_SOURCE)

parse-php: $(PHP_OUT)

parse-js: $(JS_OUT)

all: parse-php parse-js

clean:
	rm -rf node_modules
