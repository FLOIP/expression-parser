PEGJS_TAG="node:8-alpine"
PHP_TAG="floip-php:5.5-alpine"
COMPOSER_TAG="composer"
DOCKER_RUN=docker run --rm -it -v `pwd`:/src -u `id -u` -w '/src'
PEGJS=$(DOCKER_RUN) $(PEGJS_TAG) npx pegjs
PARSER_NAME=Parser
PARSER_SOURCE=src/pegjs/floip.pegjs
PARSER_CLASS=BaseExpressionParser
PHP_OUT=dist/$(PARSER_CLASS).php
PHPEGJS_OPTIONS={"cache" : "true", "phpegjs":{"parserNamespace": "Viamo", "parserClassName": "$(PARSER_CLASS)"}}
JS_OUT=dist/$(PARSER_NAME).js
ENV=docker

.PHONY: clean default parsers parse-php parse-js docker-php

default: parsers

node_modules: package.json
ifeq ($(ENV),docker)
	docker run --rm -it -v `pwd`:`pwd` -w `pwd` -u `id -u` $(PEGJS_TAG) npm install
else
	npm install
endif

$(PHP_OUT): node_modules $(PARSER_SOURCE)
ifeq ($(ENV),docker)
	$(PEGJS) --plugin phpegjs -o $(PHP_OUT) --extra-options '$(PHPEGJS_OPTIONS)' $(PARSER_SOURCE)
else
	npx pegjs --plugin phpegjs -o $(PHP_OUT) --extra-options '$(PHPEGJS_OPTIONS)' $(PARSER_SOURCE)
endif

$(JS_OUT): node_modules $(PARSER_SOURCE)
ifeq ($(ENV),docker)
	$(PEGJS) -o $(JS_OUT) $(PARSER_SOURCE)
else
	npx pegjs -o $(JS_OUT) $(PARSER_SOURCE)
endif

parse-php: $(PHP_OUT)

parse-js: $(JS_OUT)

parsers: parse-php parse-js

vendor: composer.json
ifeq ($(ENV),docker)
	$(DOCKER_RUN) $(COMPOSER_TAG) composer install --ignore-platform-reqs
else
	composer install
endif

docker-php:
ifeq ($(ENV),docker)
	docker build -t $(PHP_TAG) .docker/php
endif

test: vendor docker-php
ifeq ($(ENV),docker)
	$(DOCKER_RUN) $(PHP_TAG) ./vendor/bin/phpunit 
else
	./vendor/bin/phpunit
endif

clean:
	rm -rf node_modules
	rm -rf vendor
ifeq ($(ENV),docker)
	docker rmi $(PHP_TAG) 2>/dev/null || true
endif
