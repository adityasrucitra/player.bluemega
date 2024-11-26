<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Myth\Auth\Models\UserModel;

class AuthAPI extends ResourceController
{
    use ResponseTrait;

    /**
     * .
     */
    public function __construct()
    {
        $this->auth = service('authentication');
    }

    /**
     * .
     */
    public function tokenize()
    {
        helper(['form']);
        $rules = [
            'email' => 'required|valid_email',
            'password' => 'required|min_length[6]',
        ];
        if (!$this->validate($rules)) {
            return $this->fail($this->validator->getErrors());
        }

        $email = $this->request->getVar('email');
        $password = $this->request->getVar('password');

        $model = new UserModel();
        $user = $model->where('email', $email)->first();
        if (!$user) {
            return $this->failNotFound('Email Not Found');
        }

        // Try to log in...
        if (!$this->auth->attempt(['email' => $email, 'password' => $password], false)) {
            return $this->fail('Wrong Password');
        }

        $res = [
            'status' => 200,
            'error' => null,
            'token' => null,
        ];

        //check if the user has ceated a token and the token still active
        $currentTime = new \DateTime('now', new \DatetimeZone('UTC'));

        $jwtModel = new \App\Models\JwtModel();
        $userJwt = $jwtModel->where('valid_until >= ', $currentTime->getTimestamp())
                    ->where('user_id', $user->id)
                    ->orderBy('valid_until', 'DESC')
                    ->first();
        if ($userJwt) {
            $res['token'] = $userJwt['token'];

            return $this->response->setJSON($res);
        }

        //if user haven't generate token or token has expired, create new token
        $key = getenv('TOKEN_SECRET');
        $payload = [
            // 'iat' => $currentTime->getTimestamp(),
            'uid' => $user->id,
            'email' => $user->email,
        ];
        $newData = [
            'user_id' => $user->id,
            'created_at' => $currentTime->getTimestamp(),
        ];

        //add 24 hours to current time
        $currentTime->add(new \DateInterval('PT24H'));
        $payload['exp'] = $currentTime->getTimestamp();

        $token = JWT::encode($payload, $key, 'HS256');

        $res['token'] = $token;

        $newData['token'] = $token;
        $newData['valid_until'] = $currentTime->getTimestamp();

        $jwtModel->insert($newData, false);

        return $this->response->setJSON($res);
    }

    /**
     * .
     */
    public function detokenize()
    {
        $key = getenv('TOKEN_SECRET');
        $header = $this->request->getServer('HTTP_AUTHORIZATION');
        if (!$header) {
            return $this->failUnauthorized('Token Required');
        }

        $token = $header;

        try {
            $decoded = JWT::decode($token, new Key($key, 'HS256'));

            $currentTime = new \DateTime('now', new \DatetimeZone('UTC'));

            // $tokenValidUntil = new \DateTime($decoded->exp, new \DateTimeZone('UTC'));
            if ($currentTime->getTimestamp() >= $decoded->exp) {
                return $this->fail('Token has expired');
            }

            $currentTime->setTimestamp($decoded->exp);

            $response = [
                'id' => $decoded->uid,
                'email' => $decoded->email,
                'valid_until' => $currentTime->format('Y-m-d H:i:s'),
            ];

            return $this->response->setJSON($response);
        } catch (\Throwable $th) {
            return $this->fail('Invalid Token');
        }
    }
}
