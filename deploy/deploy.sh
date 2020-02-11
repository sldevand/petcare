#!/bin/bash

red=`tput setaf 1`
green=`tput setaf 2`
reset=`tput sgr0`

function myEcho(){
    echo ""
    echo "${green}--> $1 ${reset}"
}

#Local vars
LOCAL_WWW_PATH=/var/www
LOCAL_APP_NAME=petcare
LOCAL_APP_PATH=$LOCAL_WWW_PATH/$LOCAL_APP_NAME
LOCAL_BUILD_PATH=$LOCAL_APP_PATH/build
LOCAL_REPO_PATH=$LOCAL_APP_PATH/build/$LOCAL_APP_NAME

#Git vars
GIT_PATH=https://github.com/sldevand/petcare.git
GIT_BRANCH=develop

#Remote vars
REMOTE_HOST=pi@raspi3
REMOTE_WWW_PATH=/home/pi/tmp
REMOTE_APP_NAME=petcare
REMOTE_APP_LINK=petcare-api
REMOTE_APP_PATH=$REMOTE_WWW_PATH/$REMOTE_APP_NAME
REMOTE_APP_LINK_PATH=$REMOTE_WWW_PATH/$REMOTE_APP_LINK
REMOTE_SCRIPT_PATH=$LOCAL_APP_PATH/deploy/remote/commands.sh

myEcho "***START $LOCAL_APP_NAME deployer script START***"

myEcho "Local : Build App"
rm -rvf $LOCAL_BUILD_PATH
mkdir $LOCAL_BUILD_PATH

myEcho "Local : Git clone $GIT_BRANCH branch"
cd $LOCAL_BUILD_PATH
git clone --single-branch --branch $GIT_BRANCH $GIT_PATH

myEcho "Local : Remove unused files for production"
rm -rfv $LOCAL_REPO_PATH/src/.env*
rm -rfv $LOCAL_REPO_PATH/tests
rm -rfv $LOCAL_REPO_PATH/var/db/.gitkeep
rm -rfv $LOCAL_REPO_PATH/.gitignore
rm -rfv $LOCAL_REPO_PATH/*.md
rm -rfv $LOCAL_REPO_PATH/*.xml
rm -rfv $LOCAL_REPO_PATH/.git

#TODO add remote .env files
myEcho "***END $LOCAL_APP_NAME deployer script END***"

scp -r $LOCAL_REPO_PATH $REMOTE_HOST:$REMOTE_WWW_PATH

ssh $REMOTE_HOST 'bash -s' < $REMOTE_SCRIPT_PATH

#rm -rf $LOCAL_BUILD_PATH