#!/bin/bash

FILE=./phpDocumentor.phar
if [ ! -f "$FILE" ]; then
  wget http://www.phpdoc.org/phpDocumentor.phar
fi

php ./phpDocumentor.phar -d ./src -t ./docs --template="clean"
