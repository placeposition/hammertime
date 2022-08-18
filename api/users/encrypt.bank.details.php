<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $secretKey  = 'bGS6lzFqvvSQ8ALbOxatm7/Vk7mLQyzqaS34Q4oR1ew=';

    include('../../includes/Cryptor.php');
   
    $cryptor = New Cryptor($secretKey);

    $details = [];

    $details[] = [
        'sortcode' => '99-88-77',
        'account' => '11111111'
    ];

    $details[] = [
        'sortcode' => '66-55-44',
        'account' => '22222222'
    ];


    $details[] = [
        'sortcode' => '33-44-55',
        'account' => '33333333'
    ];

    $details[] = [
        'sortcode' => '55-22-66',
        'account' => '44444444'
    ];


    foreach ($details as $detail) {
       
        echo $cryptor->encrypt(base64_encode(json_encode($detail)));
        echo '<br /><br />';
    }
?>