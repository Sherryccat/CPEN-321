<?php
/**
 * Created by PhpStorm.
 * User: zhongjie.shen
 * Date: 8/31/2017
 * Time: 4:47 PM
 */

//header("Access-Control-Allow-Origin: *");

//For Database
$server = "localhost";
$username = "http.user";
$password = "userhttp";
$dbName = "db01";

//For password protection
$algo = 'sha512';
$salt = "cheese";

// Create connection
$conn = new mysqli($server, $username, $password, $dbName);