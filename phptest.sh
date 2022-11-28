#!/bin/sh

PHP_BIN="php"

#checking php syntax
for folder in "app" "application" "tests"; do
    echo -n "Checking syntax for *.php in ./${folder}, "
    error_output=`find "./${folder}" -type f -iname "*.php" -exec ${PHP_BIN} -l {} \; | grep -i "Errors.parsing"`
    if [ "$error_output" != "" ]; then
        echo -e "\033[41;37mfailed!\033[0m"
        echo -e "\033[41;37m$error_output\033[0m"
        exit 1
    else
        echo "OK!"
    fi
done
