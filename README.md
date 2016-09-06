# PHP Dark Launch

[![Code Climate](https://codeclimate.com/github/noqcks/php-dark-launch/badges/gpa.svg)](https://codeclimate.com/github/noqcks/php-dark-launch)
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
- $redis - obj - send a redis instance to it 
- $project - string  the name of the project
- $user - string - the name of the user dark launching
- $config - array - an array of default dark launch values

```php
// $redis
$redis = new Redis();
$redis->connect('127.0.0.1');


// $config
// this can be from a codeigniter config  
// e.g $this->load->config('dark_launch');
//     $config = $this->config->item('dark_launch_features');
$config = ['feature-1' => ['type' => 'boolean', 'value' => TRUE], 'feature-1' => ['type' => percentage, 'value' => 30]];

// passing params to Dark_Launch constructor
$params = ['redis' => $redis, 'config' => $config, 'user' => 'ben', 'project' => 'my-awesome-project']
$dark_launch = new Dark_Launch($params);
```

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

### feature_enabled()
Returns TRUE if a feature is enabled
```php
$this->dark_launch->feature_enabled('my-awesome-feature');
```
### projects()
Returns a list of projects
```php
$this->dark_launch->projects();
```
### users()
Returns a list of users for a project
```php
$this->dark_launch->users();
```
### features()
Returns a list of features for a project and user
```php
$this->dark_launch->features();
```
### get_feature()
Returns an associative array with attributes about a feature
```php
$this->dark_launch->get_feature('my-awesome-feature');
// return = ['type' => 'boolean', 'value' => 'true']
```
### enable_feature()
Enables a dark launch feature
```php
$feature_name = 'my-awesome-feature'
$feature_value = ['type' => 'percent', 'value' => 30]
$this->dark_launch->enable_feature($feature_name, $feature_value);
```

### disable_feature()
Disables a dark launch feature
```php
$this->dark_launch->disable_feature('my-awesome-feature');
```

## Running Tests

```
vendor/behat/behat/bin/behat
```

## License
[MIT](https://tldrlegal.com/license/mit-license)

## Contributors
* Layne Geck | [email](mailto:layne.geck@gmail.com)
* Prashant Kandathil | [email](mailto:prashant@techsamurais.com)
* Ben Visser | [email](mailto:benjamin.visser@telus.com)

