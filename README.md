# Dark Launch Codeigniter

[![Join the chat at https://gitter.im/telusdigital/php-dark-launch](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/telusdigital/php-dark-launch?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

A CodeIgniter library to dark launch features

[What is dark launching?](http://changelog.ca/log/2012/07/19/dark_launching_software_features)

## Table of contents

- [Installation](#installation)
- [Usage](#usage)
- [Methods](#methods)
- [License](#license)
- [Contributors](#contributors)
- [TODO](#TODO)


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
$redis->connection('127.0.0.1');


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

2. time

3. percentage

## Methods

// TODO: write methods

### feature_enabled()
Check if a feature is enabled
```php
$this->dark_launch->feature_enabled();
```

## License
[MIT](https://tldrlegal.com/license/mit-license)

## Contributors
* Prashant Kandathil | [email](mailto:prashant@techsamurais.com)
* Ben Visser | [email](mailto:benjamin.visser@telus.com)

## TODO

- Write some tests with behat!
