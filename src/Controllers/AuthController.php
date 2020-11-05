<?php

namespace Src\Controllers;

use \Firebase\JWT\JWT;
use Src\Repositories\UserRepository;
use UnexpectedValueException;

class AuthController
{
    public function index()
    {
        return ['message' => 'API Email Service'];
    }

    public function register()
    {
        $input = json_decode(file_get_contents('php://input'), true);

        if (!$this->validate($input)) {
            return $this->unprocessableEntityResponse();
        }

        $userRepository = new UserRepository();
        $userRepository->insert($input);

        header('HTTP/1.1 201 Created');

        $response = [
            'success' => true,
            'message' => 'User has been created!',
        ];

        return $response;
    }

    public function login()
    {
        $input = json_decode(file_get_contents('php://input'), true);

        if (!$this->validate($input)) {
            return $this->unprocessableEntityResponse();
        }

        $userRepository = new UserRepository();
        $result = $userRepository->login($input);

        if (!$result) {
            return [
                'success' => false,
                'message' => 'Invalid username or password!'
            ];
        }

        $issuer_claim = 'email_service';
        $issuedat_claim = time();
        $expire_claim = $issuedat_claim + 3600;
        $token = [
            "iss" => $issuer_claim,
            "iat" => $issuedat_claim,
            "exp" => $expire_claim,
            "data" => $input
        ];

        $jwt = JWT::encode($token, $_ENV['JWT_SECRET']);

        $response = [
            'success' => true,
            'token' => $jwt
        ];

        return $response;
    }

    public function authenticate($token)
    {

        try {
            $jwt = JWT::decode($token, $_ENV['JWT_SECRET'], ['HS256']);

            if (!$jwt) {
                return false;
            }

            return true;
        } catch (UnexpectedValueException $e) {
            return false;
        }
    }

    private function validate($input)
    {
        if (!isset($input['username'])) {
            return false;
        }

        if (!isset($input['password'])) {
            return false;
        }

        return true;
    }

    private function unprocessableEntityResponse()
    {
        $response['success'] = false;
        $response['status_code'] = 'HTTP/1.1 422 Unprocessable Entity';
        $response['message'] = 'Invalid input';

        return $response;
    }
}
