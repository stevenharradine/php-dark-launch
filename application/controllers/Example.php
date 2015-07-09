<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Example extends CI_Controller
{
  public function __construct()
  {    
    parent::__construct();
    $this->load->library('dark_launch');
  }


  public function index()
  {
    if($this->dark_launch->feature_enabled('time_example')){
      // code to execute if feature is enabled
    }
  }
}
