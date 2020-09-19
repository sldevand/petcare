#!/bin/bash

red=`tput setaf 1`
green=`tput setaf 2`
reset=`tput sgr0`

function myEcho(){
    echo ""
    echo "${green}--> $1 ${reset}"
}

myEcho "Executing remote script"

REMOTE_HOST=pi@raspi3
REMOTE_WWW_PATH=/var/www
REMOTE_TMP_PATH=/home/pi/tmp
REMOTE_APP_NAME=petcare
REMOTE_APP_LINK=petcare-api
REMOTE_APP_PATH=$REMOTE_WWW_PATH/$REMOTE_APP_NAME
REMOTE_APP_LINK_PATH=$REMOTE_WWW_PATH/$REMOTE_APP_LINK
REMOTE_COMPOSER=/usr/bin/composer
REMOTE_ENV_FILE=/home/pi/deployFiles/petcare-api/.env

myEcho "***Remote : removing previous symbolic link***"
sudo rm -rvf $REMOTE_WWW_PATH/$REMOTE_APP_LINK

myEcho "***Remote : removing previous site***"
sudo rm -rvf $REMOTE_WWW_PATH/$REMOTE_APP_NAME

myEcho "Remote : Move from tmp folder to app folder"
sudo mv -v $REMOTE_TMP_PATH/$REMOTE_APP_NAME $REMOTE_APP_PATH &&

myEcho "Remote : Composer Install" &&
cd $REMOTE_APP_PATH &&
sudo $REMOTE_COMPOSER install &&
sudo rm -rfv $REMOTE_APP_PATH/composer.* &&

myEcho "Remote : Add src/.env file" &&
sudo cp $REMOTE_ENV_FILE $REMOTE_APP_PATH/src &&

myEcho "Remote : Install database" &&
sudo chmod +x $REMOTE_APP_PATH/bin/console &&
sudo $REMOTE_APP_PATH/bin/console setup:install &&

myEcho "Remote : Creating symbolic link" &&
sudo ln -s $REMOTE_APP_PATH/public $REMOTE_WWW_PATH/$REMOTE_APP_LINK &&

myEcho "Remote : Giving correct rights" &&
sudo chown -R www-data:www-data $REMOTE_APP_PATH &&
sudo chown -R www-data:www-data $REMOTE_WWW_PATH/$REMOTE_APP_LINK &&
sudo find $REMOTE_APP_PATH -type d -exec chmod 0755 {} \; &&
sudo find $REMOTE_APP_PATH -type f -exec chmod 0644 {} \; &&
sudo chmod +x $REMOTE_APP_PATH/bin/console &&
sudo chmod +x $REMOTE_APP_PATH/bin/cron.php