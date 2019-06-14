PEGJS_TAG="node:8-alpine"
PHP_TAG="php:5.5-alpine"
COMPOSER_TAG="composer"
DOCKER_RUN=docker run --rm -it -v `pwd`:/src -u `id -u` -w '/src'
PEGJS=$(DOCKER_RUN) $(PEGJS_TAG) npx pegjs
PARSER_NAME=Parser
PARSER_SOURCE=src/pegjs/floip.pegjs
PHP_OUT=dist/Base$(PARSER_NAME).php
PHPEGJS_OPTIONS={"phpegjs":{"parserNamespace": "Floip", "parserClassName": "BaseParser"}}
JS_OUT=dist/$(PARSER_NAME).js

.PHONY: clean default parsers parse-php parse-js

default: parsers

node_modules: package.json
	docker run --rm -it -v `pwd`:`pwd` -w `pwd` -u `id -u` $(PEGJS_TAG) npm install

$(PHP_OUT): node_modules $(PARSER_SOURCE)
	$(PEGJS) --plugin phpegjs -o $(PHP_OUT) --extra-options '$(PHPEGJS_OPTIONS)' $(PARSER_SOURCE)

$(JS_OUT): node_modules $(PARSER_SOURCE)
	$(PEGJS) -o $(JS_OUT) $(PARSER_SOURCE)

parse-php: $(PHP_OUT)

parse-js: $(JS_OUT)

parsers: parse-php parse-js

vendor: composer.json
	$(DOCKER_RUN) $(COMPOSER_TAG) composer install --ignore-platform-reqs

test: vendor
	$(DOCKER_RUN) $(PHP_TAG) ./vendor/bin/phpunit 

clean:
	rm -rf node_modules
	rm -rf vendor
