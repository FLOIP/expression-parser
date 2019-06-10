PEGJS_TAG="node:8-alpine"
DOCKER_RUN=docker run --rm -it -v `pwd`:/src -u `id -u` -w '/src'
PARSER_NAME=FloipParser
OUT_DIR=src
PARSER_SOURCE=floip.pegjs

.PHONY: clean default

default: parser

node_modules:
	mkdir -p node_modules
	docker run --rm -it -v `pwd`:`pwd` -w `pwd` -u `id -u` $(PEGJS_TAG) npm install

parser: node_modules src/$(PARSER_SOURCE)
	$(DOCKER_RUN) $(PEGJS_TAG) npx pegjs --plugin phpegjs -o $(OUT_DIR)/$(PARSER_NAME).php src/$(PARSER_SOURCE)
	$(DOCKER_RUN) $(PEGJS_TAG) npx pegjs -o $(OUT_DIR)/$(PARSER_NAME).js src/$(PARSER_SOURCE)

clean:
	rm -rf node_modules
	rm -f $(OUT_DIR)/$(PARSER_NAME)*
