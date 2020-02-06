PEGJS_TAG="node:8-alpine"
PHP_55="floip-php:5.5-alpine"
PHP_56="floip-php:5.6-alpine"
PHP_72="floip-php:7.2-alpine"
COMPOSER_TAG="floip-php:5.5-alpine"
DOCKER_RUN=docker run $(DOCKER_OPTS) -v `pwd`:/src -u `id -u` -w '/src' -e COMPOSER_HOME=.composer
PEGJS=$(DOCKER_RUN) $(PEGJS_TAG) npx pegjs
PARSER_NAME=Parser
PARSER_SOURCE=src/pegjs/floip.pegjs
PARSER_CLASS=BaseExpressionParser
PHP_OUT=dist/$(PARSER_CLASS).php
PHPEGJS_OPTIONS={"cache" : "true", "phpegjs":{"parserNamespace": "Viamo", "parserClassName": "$(PARSER_CLASS)"}}
TSPEGJS_OPTIONS={"cache" : "true", "tspegjs":{}}
JS_OUT=dist/$(PARSER_NAME).js
TS_OUT=src/ts/$(PARSER_NAME).ts
ENV=docker
DOCKER_OPTS=--rm -it

.PHONY: clean default parsers parse-php parse-js parse-ts docker-php testall prepare-for-test

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

$(TS_OUT): node_modules $(PARSER_SOURCE)
ifeq ($(ENV),docker)
	$(PEGJS) --plugin ts-pegjs -o $(TS_OUT) --extra-options '$(TSPEGJS_OPTIONS)' $(PARSER_SOURCE)
	$(DOCKER_RUN) $(PEGJS_TAG) npm run build
else
	npx pegjs --plugin ts-pegjs -o $(TS_OUT) --extra-options '$(TSPEGJS_OPTIONS)' $(PARSER_SOURCE)
	npm run build
endif

parse-php: $(PHP_OUT)

parse-js: $(JS_OUT)

parse-ts: $(TS_OUT)

parsers: parse-php parse-ts

vendor: composer.json
ifeq ($(ENV),docker)
	$(DOCKER_RUN) $(COMPOSER_TAG) composer install --ignore-platform-reqs
else
	composer install
endif

docker-php:
ifeq ($(ENV),docker)
	docker build -t $(PHP_55) .docker/php/5.5
	docker build -t $(PHP_72) .docker/php/7.2
endif

prepare-for-test: docker-php
	# since we are testing against different environments we must be fresh
	rm -f composer.lock
	rm -rf vendor

test55: prepare-for-test
	docker build -t $(PHP_55) .docker/php/5.5
	cp composer.json composer.json.bak
	$(DOCKER_RUN) $(PHP_55) php -d memory_limit=-1 /usr/bin/composer require --dev "orchestra/testbench:~3.1.0"
	$(DOCKER_RUN) $(PHP_55) ./vendor/bin/phpunit
	mv composer.json.bak composer.json

test72: prepare-for-test
	docker build -t $(PHP_72) .docker/php/7.2
	cp composer.json composer.json.bak
	$(DOCKER_RUN) $(PHP_72) php -d memory_limit=-1 /usr/bin/composer require --dev "orchestra/testbench:~3.8.0"
	$(DOCKER_RUN) $(PHP_72) ./vendor/bin/phpunit
	mv composer.json.bak composer.json

test:
ifeq ($(ENV),docker)
	make test55 && make test72
else
	./vendor/bin/phpunit
endif

clean:
	rm -rf node_modules
	rm -rf vendor
	rm -rf .composer
ifeq ($(ENV),docker)
	docker rmi $(PHP_55) 2>/dev/null || true
	docker rmi $(PHP_56) 2>/dev/null || true
	docker rmi $(PHP_72) 2>/dev/null || true
endif
