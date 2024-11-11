<?php

declare(strict_types=1);

require 'vendor/autoload.php';
// namespace Api;

include 'Api.php';
include 'Database.php';
include 'User.php';

use Api\Api;
use Api\Database;
use Api\User;
use \Firebase\JWT\JWT;
use \Firebase\JWT\JWK;
use Dotenv\Dotenv;
 
class UserApi extends Api
{
    public function register()
    {
        $name = $this->requestParams['name'] ?? '';
        $lastName = $this->requestParams['last_name'] ?? '';
        $birthdate = $this->requestParams['birthdate'] ?? null;
        $biorgaphy = $this->requestParams['biography'] ?? '';
        $city = $this->requestParams['city'] ?? '';
        $password = $this->requestParams['password'] ?? '';

        if($name && $password){
            $pdo = (new Database())->getConnection();

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
 
            $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, birthdate, biography, city, password) VALUES (?, ?, ?, ?, ?, ?) RETURNING id");
            
            if ($stmt->execute([$name, $lastName, $birthdate, $biorgaphy, $city, $hashedPassword])) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                return $this->response(['message' => 'User registered successfully with userId = ' . $user['id']], 200);
            } else {
                return $this->response(['message' => 'Registration failed.'], 500);
            }
        }

        if (empty($name) || empty($password)) {
            return $this->response(['message' => 'Username and password are required.'], 422);
            return;
        }
    
        return $this->response("Saving error", 500);
    }
 
    public function login(){
        $userId = $this->requestParams['user_id'] ?? '';
        $password = $this->requestParams['password'] ?? '';

        if (empty($userId) || empty($password)) {
            return $this->response(['message' => 'UserId and password are required.'], 422);
        }

        $pdo = (new Database())->getConnection();
        $stmt = $pdo->prepare("SELECT id, password FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $payload = [
                'iat' => time(),
                'exp' => time() + (60 * 60), 
                'id' => $user['id'] 
            ];

            $dotenv = Dotenv::createImmutable(__DIR__);
            $dotenv->load();
    
            $secretKey = $_ENV['JWT_SECRET'];
    
            $jwt = JWT::encode($payload, $secretKey, 'HS256');

            return $this->response(['message' => 'Login successful', 'Bearer token' => $jwt], 200);
        } else {
            return $this->response(['message' => 'Invalid userId or password'], 422);
        }
    }

    public function getUser($request) {

        $userId = $request['id'] ?? '';

        $pdo = (new Database())->getConnection();

        $stmt = $pdo->prepare("SELECT id, first_name, last_name, birthdate, biography, city FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $rawUser = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if(empty($rawUser)) {
            return $this->response('User not found', 404);
        }

        $user = new User;
	
	    $user->setFirstName($rawUser['first_name']);
        $user->setLastName($rawUser['last_name']);
        $user->setBirthdate($rawUser['birthdate']);
        $user->setBiography($rawUser['biography']);
        $user->setCity($rawUser['city']);
	
        return $this->response($user, 200);
    }
}