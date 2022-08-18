<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    cors();

    require '../../vendor/autoload.php';
    use Firebase\JWT\JWT;
    use Firebase\JWT\Key;

    if (! preg_match('/Bearer\s(\S+)/', $_SERVER['REDIRECT_HTTP_AUTHORIZATION'], $matches)) {
        
        header('HTTP/1.0 400 Bad Request');
        exit;

    } else {

        $jwt = $matches[1];
        
        if (!$jwt) {
          
            header('HTTP/1.0 400 Bad Request');
            exit;
        }

        $secretKey  = 'bGS6lzFqvvSQ8ALbOxatm7/Vk7mLQyzqaS34Q4oR1ew=';
        $token = JWT::decode($jwt, new Key($secretKey, 'HS256'));
        $now = new DateTimeImmutable();

        $serverName = 'https://api.underthehammer.localhost';

        if ($token->iss !== $serverName ||
            $token->nbf > $now->getTimestamp() ||
            $token->exp < $now->getTimestamp())
        {
            header('HTTP/1.1 401 Unauthorized');
            exit;
        }

        include('../../includes/connection.php');
        include('../../includes/Cryptor.php');
        $db = pdo_connection();


        $cryptor = New Cryptor($secretKey);

        header("Content-Type: application/json");

        $response = [
            'success' => false, 
            'items' => []
        ];

        $sql = $db->prepare("SELECT 
            U.user_id AS id, U.user_fname AS firstName, U.user_lname AS lastName, U.username, U.role, U.bankdetails
        FROM 
            users AS U 
        WHERE 
            U.user_active = 1");

        $sql->execute([]);

        while ($u = $sql->fetch(\PDO::FETCH_ASSOC)) {


            $u['bankdetails'] = json_decode(base64_decode($cryptor->decrypt($u['bankdetails'])), true);

            $response['items'][] = $u;
        }

        echo json_encode($response, JSON_NUMERIC_CHECK);

    }

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