#!/bin/bash
#Documentation:
#http://www.freeos.com/guides/lsst/
#http://linuxconfig.org/bash-scripting-tutorial

echo -e "\nWelcome to install script!\n"
echo "Choose what you want to do:"
echo " 1 - composer self-update and install"
echo " 2 - npm, bower install"
echo " 3 - gulp run"
echo " 4 - database schema:update"
echo " 5 - load fixtures (dev|test|default:dev)"
echo " 6 - clear cache (prod|dev|test|all|default:all)"
echo " 9 - run all command step by step"
echo " 0 - exit"
echo -n "Please choose > "

function composer_install
{
    echo "Composer install"
	composer self-update
	composer install -n
}

function npm_bower_install
{
	echo "Npm, bower install"
	npm install
	./node_modules/.bin/bower install
}

function gulp_run
{
	echo "Gulp run"
	./node_modules/.bin/gulp
}

function database_update
{
	echo "Updating database"
	app/console doctrine:schema:drop --force
	app/console doctrine:schema:update --force
}

function load_fixtures
{
	echo "Loading fixtures"

    if $input_argument ; then
        local input_argument='dev'
    fi

	case $input_argument in
        dev ) echo "Used - dev"
            app/console doctrine:fixtures:load -n
            ;;
        test ) echo "Used - test"
            app/console doctrine:fixtures:load -n -e test
            ;;
        exit ) echo "Good bye!"
    esac
}

function clear_cache
{
	echo "Clearing caches"

    if $input_argument ; then
        local input_argument='all'
    fi

	case $input_argument in
        prod ) echo "Used - prod"
            app/console cache:clear -e prod
            ;;
        dev ) echo "Used - dev"
            app/console cache:clear -e dev
            ;;
        test ) echo "Used - test"
            app/console cache:clear -e test
            ;;
        all ) echo "Used - all"
            app/console cache:clear -e prod
            app/console cache:clear -e dev
            app/console cache:clear -e test
            ;;
        exit ) echo "Good bye!"
    esac
}

function all_run
{
	echo "Run All scripts"
	composer_install
	npm_bower_install
	gulp_run
	database_update
	load_fixtures
	clear_cache
}


read input_command input_argument
echo $input_command $input_argument

case $input_command in
    1 ) composer_install
        ;;
    2 ) npm_bower_install
        ;;
    3 ) gulp_run
        ;;
    4 ) database_update
        ;;
    5 ) load_fixtures
		;;
    6 ) clear_cache
		;;
    9 ) all_run
		;;
    0 ) echo "Good bye!"
esac
