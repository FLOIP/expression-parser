PEGJS_TAG="node:8-alpine"
PHP_80="floip-php:8.0-alpine"
PHP_81="floip-php:8.1-alpine"
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
USE_DOCKER=true
ENV=local
DOCKER_OPTS=--rm

.PHONY: clean \
				default \
				parsers \
				parse-php \
				parse-js \
				parse-ts \
				docker-php \
				testall \
				prepare-for-test \
				docker-php-80 \
				docker-php-81

default: parsers

node_modules: package.json
ifeq ($(USE_DOCKER),true)
	docker run --rm -v `pwd`:`pwd` -w `pwd` -u `id -u` $(PEGJS_TAG) npm install
else
	npm install
endif

$(PHP_OUT): node_modules $(PARSER_SOURCE)
ifeq ($(USE_DOCKER),true)
	$(PEGJS) --plugin phpegjs -o $(PHP_OUT) --extra-options '$(PHPEGJS_OPTIONS)' $(PARSER_SOURCE)
else
	npx pegjs --plugin phpegjs -o $(PHP_OUT) --extra-options '$(PHPEGJS_OPTIONS)' $(PARSER_SOURCE)
endif

$(TS_OUT): node_modules $(PARSER_SOURCE)
ifeq ($(USE_DOCKER),true)
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
ifeq ($(USE_DOCKER),true)
	$(DOCKER_RUN) $(PHP_81) composer install --ignore-platform-reqs
else
	composer install
endif

docker-php-80:
	docker build -t $(PHP_80) .docker/php/8.0

docker-php-81:
	docker build -t $(PHP_81) .docker/php/8.1

prepare-for-test: docker-php
	# since we are testing against different environments we must be fresh
	touch composer.lock
	rm -rf vendor

.ci/8.0/composerL6.lock: composer.json
	make prepare-for-test
	cp composer.json .ci/8.0/composerL6.json
	$(DOCKER_RUN) -e COMPOSER=.ci/8.0/composerL6.json $(PHP_80) php -d memory_limit=-1 /usr/bin/composer require --dev "orchestra/testbench:~4.0" --no-suggest

.ci/8.0/composerL7.lock: composer.json
	make prepare-for-test
	cp composer.json .ci/8.0/composerL7.json
	$(DOCKER_RUN) -e COMPOSER=.ci/8.0/composerL7.json $(PHP_80) php -d memory_limit=-1 /usr/bin/composer require --dev "orchestra/testbench:~5.0" --no-suggest

.ci/8.0/composerL8.lock: composer.json
	make prepare-for-test
	cp composer.json .ci/8.0/composerL8.json
	$(DOCKER_RUN) -e COMPOSER=.ci/8.0/composerL8.json $(PHP_80) php -d memory_limit=-1 /usr/bin/composer require --dev "orchestra/testbench:~6.0" --no-suggest

.ci/8.1/composerL8.lock: composer.json
	make prepare-for-test
	cp composer.json .ci/8.1/composerL8.json
	$(DOCKER_RUN) -e COMPOSER=.ci/8.1/composerL8.json $(PHP_81) php -d memory_limit=-1 /usr/bin/composer require --dev "orchestra/testbench:~6.0" --no-suggest

test80: prepare-for-test docker-php-80 .ci/8.0/composerL6.lock .ci/8.0/composerL7.lock .ci/8.0/composerL8.lock
	$(DOCKER_RUN) -e COMPOSER=.ci/8.0/composerL6.json $(PHP_80) composer install --no-suggest
	$(DOCKER_RUN) $(PHP_80) ./vendor/bin/phpunit
	rm .ci/8.0/composer*.lock
	$(DOCKER_RUN) -e COMPOSER=.ci/8.0/composerL7.json $(PHP_80) composer install --no-suggest
	$(DOCKER_RUN) $(PHP_80) ./vendor/bin/phpunit
	rm .ci/8.0/composer*.lock
	$(DOCKER_RUN) -e COMPOSER=.ci/8.0/composerL8.json $(PHP_80) composer install --no-suggest
	$(DOCKER_RUN) $(PHP_80) ./vendor/bin/phpunit
	rm .ci/8.0/composer*.lock

test81: prepare-for-test docker-php-81 .ci/8.1/composerL8.lock
	$(DOCKER_RUN) -e COMPOSER=.ci/8.1/composerL8.json $(PHP_81) composer install --no-suggest
	$(DOCKER_RUN) $(PHP_81) ./vendor/bin/phpunit
	rm .ci/8.1/composer*.lock

test:
	make test80 && make test81

clean:
	rm -rf node_modules
	rm -rf vendor
	rm -rf .composer
	docker rmi $(PHP_80) 2>/dev/null || true
	docker rmi $(PHP_81) 2>/dev/null || true

rector-81:
	$(DOCKER_RUN) -e COMPOSER=.ci/8.1/composerL8.json $(PHP_81) composer install --no-suggest
	$(DOCKER_RUN) $(PHP_81) vendor/bin/rector process
