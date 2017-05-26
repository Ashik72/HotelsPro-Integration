<?php

if(!defined('WPINC')) // MUST have WordPress.
	exit('Do NOT access this file directly: '.basename(__FILE__));

/**
 * DB
 */
class DB
{

  function __construct()
  {
    # code...
  }

  public static function Connect() {

    $conn = NULL;

    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if ($conn->connect_error) {
        die();
      return;
    }


    return $conn;

  }

  public static function ConnectConf($db_host, $db_user, $db_pass, $db_name) {

    $conn = NULL;

    mysqli_report(MYSQLI_REPORT_STRICT);

    try {
      $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

    } catch (Exception $e) {
      return $e->getMessage();
    }



    if ($conn->connect_error) {
        die();
      return;
    }


    return $conn;

  }

  public static function ConnectTitan($titan = "") {

    if (empty($titan))
      return;

      $db_host = $titan->getOption( 'db_host');
      $db_name = $titan->getOption( 'db_name');
      $db_user = $titan->getOption( 'db_user');
      $db_password = $titan->getOption( 'db_password');
      $db_charset = $titan->getOption( 'db_charset');

      $response = static::ConnectConf($db_host, $db_user, $db_password, $db_name);
      //$response->set_charset($db_charset);

      return $response;
   }



}


 ?>
