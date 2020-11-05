<?php

namespace Src\Controllers;

use Src\Services\EmailQueue;

class EmailController
{
    public function create()
    {
        $input = file_get_contents('php://input');

        if (!$this->validate(json_decode($input, true))) {
            return $this->unprocessableEntityResponse();
        }

        (new EmailQueue)->send($input);

        header('HTTP/1.1 201 Created');

        $response = [
            'success' => true,
            'message' => 'Email has been sent!'
        ];

        return $response;
    }

    private function validate($input)
    {
        if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        if (!isset($input['emailSubject'])) {
            return false;
        }

        if (!isset($input['emailBody'])) {
            return false;
        }

        return true;
    }

    private function unprocessableEntityResponse()
    {
        $response['success'] = false;
        $response['status_code'] = 'HTTP/1.1 422 Unprocessable Entity';
        $response['message'] = 'Invalid email';

        return $response;
    }
}
