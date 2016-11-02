# PHP Dark Launch

[![Build Status](https://travis-ci.org/telusdigital/php-dark-launch.svg?branch=master)](https://travis-ci.org/telusdigital/php-dark-launch)

A PHP library to dark launch features

[What is dark launching?](http://changelog.ca/log/2012/07/19/dark_launching_software_features)


## Table of contents

- [Installation](#installation)
- [Usage](#usage)
- [Methods](#methods)
- [License](#license)
- [Contributors](#contributors)

## Installation

```
composer require telusdigital/darklaunch
```

## Usage
- [Tunables](#tunables)
- [Config](#config)

The interface is documented here: `Telus\Digital\Libraries\DarkLaunch\Interfaces`

Initialize a Dark Launch object:

```php

$applicationConfig = new ApplicationConfig();
$developmentConfig = $applicationConfig->getValue("local-development");

$redisHost = $developmentConfig['redis']['host'];
$redisPort = $developmentConfig['redis']['port'];

$redisConnection = DatabaseConnectionLoader::getRedisConnection($redisHost, $redisPort);

$applicationConfig = new ApplicationConfig();
$developmentConfig = $applicationConfig->getValue("local-development");

$host = $developmentConfig['mysql']['host'];
$port = $developmentConfig['mysql']['port'];
$username = $developmentConfig['mysql']['userName'];
$password = $developmentConfig['mysql']['password'];
$database = $developmentConfig['mysql']['database'];

$mysqlConnection = $this->getMySqlConnection();

$darkLaunchLibrary = new DarkLaunchConfigAccessor($redisConnection, $mysqlConnection);
```

### Tunables

The list of environments supported but they can overwritten by the user
`Telus\Digital\Libraries\DarkLaunch\Implementations\ApplicationConfig`


### Config

Dark Launch defaults can be one of three types

* boolean

A feature is enabled when value is TRUE
```php
['type' => 'boolean', 'value' => TRUE]
```

* time

A feature is enabled if in between start and stop time
```php
['type' => 'time', 'start' => 1419451200, 'stop' => 1420056000]
```

* percentage

A feature is enabled X % of the time
```php
['type' => 'percentage', 'value' => 30]
```

* int

The integer value stored.
```php
['type' => 'int','value' => 100]
```

* string

The string value stored.
```php
['type' => 'string','value' => 'hello wrold']
```

* time_string

The string value returned between start and stop time
```php
['type' => 'time_string', 'start' => 0, 'stop' => 4611265200, 'value' => 'hello world']
```

* traffic_source

The feature is enabled for external or internal traffic
```php
['type' => 'traffic_source', 'value' => 'external'];
```
```php
['type' => 'traffic_source', 'value' => 'internal'];
```

* cookie

Returns TRUE if a cookie with the specified name set and FALSE if the cookie with specified name is not set
```php
['type' => 'cookie', 'value' => 'name-of-cookie'];
```

## Running Tests

Log into the docker container

```
$ cd ~/home/app/code
$ vendor/bin/phinx migrate -c config-phinx.php
$ vendor/bin/phpunit
```