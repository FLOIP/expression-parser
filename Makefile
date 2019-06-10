PEGJS_TAG="node:8-alpine"
DOCKER_RUN=docker run --rm -it -v `pwd`:/src -u `id -u` -w '/src'
PEGJS=$(DOCKER_RUN) $(PEGJS_TAG) npx pegjs
PARSER_NAME=FloipParser
OUT_DIR=src
PARSER_SOURCE=floip.pegjs

.PHONY: clean default

default: parser

node_modules:
	mkdir -p node_modules
	docker run --rm -it -v `pwd`:`pwd` -w `pwd` -u `id -u` $(PEGJS_TAG) npm install

parser: node_modules src/grammar/$(PARSER_SOURCE)
	$(PEGJS) --plugin phpegjs -o $(OUT_DIR)/php/$(PARSER_NAME).php src/grammar/$(PARSER_SOURCE)
	$(PEGJS) -o $(OUT_DIR)/js/$(PARSER_NAME).js src/grammar/$(PARSER_SOURCE)

clean:
	rm -rf node_modules
	rm -f $(OUT_DIR)/js/$(PARSER_NAME).js
	rm -f $(OUT_DIR)/php/$(PARSER_NAME).php
