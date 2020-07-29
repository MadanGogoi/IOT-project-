#this is used to get the data using HTTP post method.
<?php 
require 'database_connection.php';
$value = $_POST['value'];

$response = array();
$response["SUCCESS"] = 0;
//Get the current data and time 

date_default_timezone_set('Asia/kolkata');
$d = date("Y-m-d");

$t =  date("H:i:s");

//get database connection 

$database = new Database();

$con = $database-> getConnection();

if(!empty($value)){
    try{
        $STH = $con->prepare("INSERT INTO DATA2 (VALUE , TIME , DATE) VALUE (?,?,?)");
        $STH->bindParam(1, $value);
        $STH->bindParam(2,$t);
        $STH->bindParam(3,$d);
        $STH->execute();
        $response["SUCCESS"]  = 1;
        echo json_encode($response);
        }catch (PDOException $e) {
            echo $e -> getMessage();
    }
}else{
    
    echo json_encode($value);
}
