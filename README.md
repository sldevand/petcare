# Petcare Rest API
A Rest API to manage your pet care notebook in php.

Made with [Slim Framework](https://www.slimframework.com),
Slim is a PHP micro framework that helps you quickly write simple yet powerful web applications and APIs. 

## Requirements
This app uses [SQLite](https://sqlite.org/index.html), install PHP SQLite extension.  
Install [Composer](https://getcomposer.org).

## Installation

Clone the repository
```
git clone https://github.com/sldevand/petcare.git
```
Install vendors
```
composer install
```

Make bin/console executable
```
sudo chmod +x bin/console
```

Installation of the database and modules
```
bin/console setup:install
```
Start the app with php server on localhost:8080
```
composer start
```

## Tests
Launch tests with Composer and PhpUnit
```
composer test
```
With more verbosity (debug mode)
```
composer testDebug
```

