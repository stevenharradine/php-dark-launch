# Dark Launch Codeigniter

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

1. Clone this repo 
2. Move `application/libraries/Dark_launch.php` and `application/config/dark_launch.php` into their respective folders in your project.
3. Use `require_once` to require `libraries/Dark_launch.php` in your controllers or autoload it via your preferred method

## Usage

// TODO: write usage

### Tunables:
- // $redis - send a redis instance to it 
- // $project - the name of the project e.g uss-consumer
- // $user - the name of the user dark launching e.g x173034


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

#TODO

- Write some tests with behat!