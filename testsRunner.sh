#!/bin/bash

cd tests
../vendor/bin/phpunit --static-backup --coverage-text=./coverage/res.txt --whitelist . --testdox-text ./coverage/log.txt -v ./ *Test.php
