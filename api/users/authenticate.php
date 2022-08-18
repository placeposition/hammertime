<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    header("Content-Type: application/json");

    require '../../vendor/autoload.php';
    use Firebase\JWT\JWT;
    use Firebase\JWT\Key;

    cors();

    $time = time();

	$response = [
        'success' => false, 
        'id' => null,
        'username' =>  null,
        'firstName' => null,
        'lastName' => null,
        'token' => ''
    ];

    //Make sure that it is a POST request.
    if(strcasecmp($_SERVER['REQUEST_METHOD'], 'POST') != 0){
        $response['message'] = 'Request method must be POST!';
    }

    //Make sure that the content type of the POST request has been set to application/json
    $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
    if(strcasecmp($contentType, 'application/json') != 0){
        $response['message'] = 'Content type must be: application/json';
    }

    $content = trim(file_get_contents("php://input"));

    $v = json_decode($content, true);

    if(!is_array($v)){
        $response['message'] = 'Received content contained invalid JSON!';
    }

    if (!empty($v['username']) && !empty($v['password'])) {

        include('../../includes/connection.php');
        $db = pdo_connection();

        $sql = $db->prepare("SELECT 
            U.user_id AS id, U.user_fname AS firstName, U.user_lname AS lastName, U.username
        FROM 
            users AS U 
        WHERE 
            U.user_active = 1 AND 
            U.username_clean = :username_clean AND 
            U.user_password = :user_password 
        LIMIT 1");

        $sql->execute([
            ':username_clean' => trim(strtolower(htmlspecialchars($v['username'], ENT_COMPAT, 'UTF-8'))),
            ':user_password' => MD5($v['password'])
        ]);

        if ($sql->rowCount() == 1) {

            $u = $sql->fetch(\PDO::FETCH_ASSOC);

            $secretKey  = 'bGS6lzFqvvSQ8ALbOxatm7/Vk7mLQyzqaS34Q4oR1ew=';

            $payload = [
                'iat'  => $time,
                'iss'  => 'https://api.underthehammer.localhost',
                'nbf'  => $time,      
                'exp'  => $time + 86400,
                'userName' => $u['username']
            ];
                        
            $jwt = JWT::encode($payload, $secretKey, 'HS256');
        
            $response = array_merge($u, ['success' => true, 'token' => $jwt]);
        }
    }

    echo json_encode($response, JSON_NUMERIC_CHECK);

    function cors() {
    
        // Allow from any origin
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
            // you want to allow, and if so:
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age: 86400');    // cache for 1 day
        }
        
        // Access-Control headers are received during OPTIONS requests
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
                // may also be using PUT, PATCH, HEAD etc
                header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
            
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
                header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
        
            exit(0);
        }
    }
?>