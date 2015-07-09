<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Example extends CI_Controller
{
  public function __construct()
  {    
    parent::__construct();
  }


  public function index()
  {
    $redis = new Redis()
    $redis->connect('127.0.0.1');
    $this->load->config('dark_launch');
    $config = $this->config->items('dark_launch_features')
    $params = ['redis' => $redis, 'config' => $config]
    $dark_launch = new Dark_Launch($params);
  }
}
