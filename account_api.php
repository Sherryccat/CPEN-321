<?php
/**
 * Created by PhpStorm.
 * User: shenz
 * Date: 10/6/2017
 * Time: 12:15 AM
 */

require('config.php');

//Construct some vars fot JSON
$jsonObj = new stdClass();
$msg = null;
$result = null;
$userID = null;
// Success: 1 for authenticated. 2 for registered.
// Error: -1 for wrong credential. -2 for fail to register. 99 for wrong request format.

if (isset($_POST['api_key']) and $_POST['api_key'] == "hello") {
    //Authenticate
    if ($_POST['action'] == "authenticate" and isset($_POST['username']) and isset($_POST['password'])) {
        //Assigning posted values to variables.
        $username = $conn->real_escape_string($_POST['username']);
        $password = $conn->real_escape_string($_POST['password']);
        $password = hash($algo, $salt . $password);
        //Checking the values are existing in the database or not
        $query = "SELECT * FROM `user` WHERE username='$username' and password='$password'";

        $result = $conn->query($query) or $msg = $conn->error;
        $row = $result->fetch_assoc();
        $count = $conn->affected_rows;
        //If the posted values are equal to the database values, then result will be 1.
        if ($count == 1) {
            $msg = "Authenticate successfully.";
            $res_code = 1;
            $userID = $row['id'];
        } else {
            //If the login credentials doesn't match, he will be shown with an error message.
            $msg = "Invalid Login Credentials.";
            $res_code = -1;
        }
    } //Register new user
    elseif ($_POST['action'] == "register" and isset($_POST['username']) and isset($_POST['password']) and isset($_POST['email'])) {
        //3.1.1 Assigning posted values to variables.
        $username = $conn->real_escape_string($_POST['username']);
        $password = $conn->real_escape_string($_POST['password']);
        $password = hash($algo, $salt . $password);
        $email = $conn->real_escape_string($_POST['email']);
        //Add the account entry
        $query = "INSERT INTO `user`  (`username`, `email`, `password`) VALUES ('$username', '$email', '$password')";

        if ($conn->query($query)) {
            $msg = "New account created";
            $res_code = 2;
            $userID = $conn->insert_id;
        } else {
            $msg = $conn->error;
            $res_code = -2;
        }

    }
} else {
    $msg = "Wrong request format";
    $res_code = 99;
}


$jsonObj->message = $msg;
$jsonObj->res_code = $res_code;
$jsonObj->userID = $userID;

header('Content-Type: application/json');
echo json_encode($jsonObj);
