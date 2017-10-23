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
$addPost = null;
// Success: 1 for Item Added. 2 for list all successfully.3 for Bid successfully. 4 for Mark as sold successfully.
// Error: -1 for Item failed to add. -2 for fail to list all. -3 for fail to bid. -4 for failed to Mark as sold.99 for wrong request format.

if (isset($_POST['api_key']) and $_POST['api_key'] == "hello") {
    //Add new item
    if ($_POST['action'] == "add"
        and isset($_POST['name'])
        and isset($_POST['description'])
        and isset($_POST['location'])
        and isset($_POST['exp_time'])
        and isset($_POST['price'])
        and isset($_POST['image_url'])
    ) {

        //Assigning posted values to addPost Array.
        foreach($_POST as $key => $value){
            $addPost[$key] = $value;
        }
        //Checking if all the strings are legal sql code
        foreach($addPost as $key => $value){
            $value = $conn->real_escape_string($value);
        }

        /* clean the strings in the post, which is replaced by the above loop
        $item_name = $conn->real_escape_string($_POST['name']);
        $description = $conn->real_escape_string($_POST['description']);
        $location = $conn->real_escape_string($_POST['location']);
        $price = $conn->real_escape_string($_POST['price']);
        $image_url = $conn->real_escape_string($_POST['image_url']);
        $exp_time = $conn->real_escape_string($_POST['exp_time']);
        */

        list($res_code, $msg) = addItem($addPost, $conn);

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
    } elseif ($_POST['action'] == "bid"
        and isset($_POST['id'])
        and isset($_POST['price'])) {
        $id = $conn->real_escape_string($_POST['id']);
        $price = $conn->real_escape_string($_POST['price']);

        $query = "UPDATE `db01`.`items` SET `price`='$price' WHERE `id`='$id'";

        $result = $conn->query($query) or $msg = $conn->error;
        $count = $conn->affected_rows;
        if ($count == 1) {
            $msg = "Bid successfully.";
            $res_code = 3;
        } else {
            $msg = "Failed to bid";
            $res_code = -3;
        }

    } elseif ($_POST['action'] == "markSold"
        and isset($_POST['id'])
    ) {
        $id = $conn->real_escape_string($_POST['id']);

        $query = "UPDATE `db01`.`items` SET `sold`='1' WHERE `id`='$id'";

        $result = $conn->query($query) or $msg = $conn->error;
        $count = $conn->affected_rows;

        if ($count == 1) {
            $msg = "Mark as sold successfully.";
            $res_code = 4;
        } else {
            $msg = "Failed to mark item";
            $res_code = -4;
        }

    } elseif ($_POST['action'] == "delete"
        and isset($_POST['id'])
    ) {
        $id = $conn->real_escape_string($_POST['id']);

        $query = "DELETE FROM `items` WHERE `id`='$id'";

        $result = $conn->query($query) or $msg = $conn->error;
        $count = $conn->affected_rows;

        if ($count == 1) {
            $msg = "Delete successfully.";
            $res_code = 5;
        } else {
            $msg = "Failed to delete";
            $res_code = -5;
        }

    } else {
        $msg = "Missing parameter";
        $res_code = 99;
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
function addItem($addPost, $conn)
{
    $item_name = $addPost['name'];
    $description = $addPost['description'];
    $location = $addPost['location'];
    $price = $addPost['price'];
    $image_url = $addPost['image_url'];
    $exp_time = $addPost['exp_time'];
//Build and execute the INSERT queue
    $query = "INSERT INTO `items`  (`name`, `description`, `location`, `price`,`exp_time`) VALUES ('$item_name', '$description', '$location','$price','$exp_time')";

    $result = $conn->query($query) or $msg = $conn->error;
    $count = $conn->affected_rows;
    //If the posted values are equal to the database values, then result will be 1.
    if ($count == 1) {
        $msg = "Item added successfully.";
        $res_code = 1;
    } else {
        //If the login credentials doesn't match, he will be shown with an error message.
        $msg = $conn->error;
        $res_code = -1;
    }
    return array($res_code, $msg);
}
