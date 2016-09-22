<?php
namespace Telus\Digital\Libraries\DarkLaunch\Interfaces;

interface DarkLaunchInterface {

  public function __construct(\Redis $redisConnection);

  /**
  * Determines whether a dark launch feature is active
  * @param $feature string - The name of the feature
  * @return boolean - True if a feature is enabled, false if not
  */
  public function featureEnabled($featureName);

  /**
  * Get a list of projects
  * @return An arry containing the list of projects
  */
  public function projects();

  /**
  * Get a list of users
  * @return An arry containing a list of features for a project and user
  */
  public function users();

  /**
  * Get a list of features
  * @return An arry containing a list of features for a project and user
  */
  public function features();

  /**
  * Get a Dark Launch feature
  * @param $feature string - The namespace when accessing redis
  * @return An array of values - The hash keys and values for the feature
  */
  public function feature($feature_name);

  /**
  * Set a Dark Launch feature
  * @param $feature_name string - The name of the feature
  * @param $feature_values array - An associative array of the features keys and values
  */
  public function enableFeature($feature_name, $feature_values);

  /**
  * Disable a dark launch feature
  * @param $feature string - The name of the feature
  */
  public function disableFeature($feature_name);

  /**
  * Parses a features value
  * @param $feature array - An associative array of a features attributes
  * @return boolean TRUE if feature is enabled
  */
  public function parse($feature);

  /**
  * Returns TRUE or FALSE based on the boolean value
  * @param $feature array - An associative array of the features attributes
  * @return boolean
  */
  public function parseBoolean($feature);

  /**
  * Returns TRUE when the current time is between start and stop time and FALSE otherwise
  * @param $feature array - A associative array of a features attributes
  * @return boolean TRUE if feature is enabled
  * Exception is thrown when stop time is before end time
  */
  public function parseTime($feature);

  /**
  * Returns TRUE when the current time is between start and stop time and FALSE otherwise
  * @param $start int - A unix time of start time
  * @param $stop int - A unix time of stop time
  * @return boolean TRUE if in between start and stop time
  */
  public function timeIsValid($start, $stop);

  /**
  * Returns TRUE or FALSE for X % of the time
  * @param $feature array - A associative array of the features attributes
  * @return boolean
  */
  public function parsePercentage($feature);

  /**
  * Returns integer value of a feature
  * @param $feature array - A associative array of the features attributes
  * @return integer
  */
  public function parseInt($feature);

  /**
  * Returns the string value
  * @param $feature array - An associative array of the features attributes
  * @return string
  */
  public function parseString($feature);

}