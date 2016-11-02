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


Initialize a Dark Launch object:

```php
use TelusDigital\Dark_Launch;

// Dark Launching requires a redis instance to use
$redis = new Redis();
$redis->connection('127.0.0.1');

$params = ['redis' => $redis];
$dark_launch = new Dark_Launch($params);
```

### Tunables:


### Config

Dark Launch defaults can be one of three types

1. boolean
A feature is enabled when value is TRUE
```php
['type' => 'boolean', 'value' => TRUE]
```

2. time
A feature is enabled if in between start and stop time
```php
['type' => 'time', 'start' => 1419451200, 'stop' => 1420056000]
```

3. percentage
A feature is enabled X % of the time
```php
['type' => 'percentage', 'value' => 30]
```
## Methods



## Running Tests

Log into the docker container

```
$ cd ~/home/app/code
$ vendor/bin/phinx migrate -c config-phinx.php
$ vendor/bin/phpunit
```