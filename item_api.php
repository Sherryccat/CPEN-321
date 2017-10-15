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
$res_code = null;
// Success: 1 for Item Added. 2 for registered.
// Error: -1 for Item failed to add. -2 for fail to register. 99 for wrong request format.

if (isset($_POST['api_key']) and $_POST['api_key'] == "hello") {
    //Add new item
    if ($_POST['action'] == "add"
        and isset($_POST['name'])
        and isset($_POST['description'])
        and isset($_POST['location'])
        and isset($_POST['exp_time'])) {
        //Assigning posted values to variables.
        $item_name = $conn->real_escape_string($_POST['name']);
        $description = $conn->real_escape_string($_POST['description']);
        $location = $conn->real_escape_string($_POST['location']);
        if (isset($_POST['image_url'])) {
            $image_url = $conn->real_escape_string($_POST['image_url']);
        }

        $exp_time = $conn->real_escape_string($_POST['exp_time']);

        list($res_code, $msg) = addItem($item_name, $description, $location, $exp_time, $conn);

    } elseif ($_POST['action'] == "listAll") {
        $query = "SELECT * FROM `items`";
        if ($result = $conn->query($query)) {
            $res_code = 2;
            $index = 0;
            while ($row = $result->fetch_assoc()) {
                $msg[$index] = $row;
                $index++;
            }
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

header('Content-Type: application/json');
echo json_encode($jsonObj);

/**
 * @param $item_name string
 * @param $description string
 * @param $location string
 * @param $exp_time string
 * @param $conn mysqli
 * @return array
 */
function addItem($item_name, $description, $location, $exp_time, $conn)
{
//Build and execute the INSERT queue
    $query = "INSERT INTO `items`  (`name`, `description`, `location`, `exp_time`) VALUES ('$item_name', '$description', '$location','$exp_time')";

    $result = $conn->query($query) or $msg = $conn->error;
    $count = $conn->affected_rows;
    //If the posted values are equal to the database values, then result will be 1.
    if ($count == 1) {
        $msg = "Item added successfully.";
        $result = 1;
    } else {
        //If the login credentials doesn't match, he will be shown with an error message.
        $msg = "Failed to add item";
        $result = -1;
    }
    return array($result, $msg);
}
