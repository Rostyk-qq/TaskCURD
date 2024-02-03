<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Method: POST, GET');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

require_once('validate.php');

$validate = new ValidateParams();

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $data = file_get_contents("php://input");
    $data = json_decode($data, true);

    if(!empty($data)){
       $validate->validateAction($data);
    }
    else{
        http_response_code(500);
        echo json_encode(['status' => false, 'error' => ['code' => 500, 'message' => 'Data cannot exists!']]);
        die();
    }
}