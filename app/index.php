<?php
declare(strict_types=1);

include 'UserApi.php';
 
try {
    $api = new UserApi();
    echo $api->run();
} catch (Exception $e) {
    echo json_encode(Array('error' => $e->getMessage()));
}