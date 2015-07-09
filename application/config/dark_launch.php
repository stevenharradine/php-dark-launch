<?php
defined('BASEPATH') OR exit('No direct script access allowed');
  
// A list of default values for Dark Launch features
$config['dark_launch_features'] = [];

// Dark launch with time values
// Feature is enabled if time is in between start and stop times
$config['dark_launch_features']['time_example'] = [
                                            'type' => 'time',
                                            'start' => 1416382080,
                                            'stop' => 1486382080];
// Dark launch with boolean values
// Feature is enabled if value is true
$config['dark_launch_features']['boolean_example'] = [
                                              'type' => 'boolean',
                                              'value' => FALSE];

// Dark launch with percentage values
// Feature is enabled randomly X % of the time
$config['dark_launch_features']['percentage_example'] = [
                                              'type' => 'percentage',
                                              'value' => 30];