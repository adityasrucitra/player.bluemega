<?php

namespace App\Controllers;

use App\Entities\User;
use App\Models\UserModel;
use Myth\Auth\Controllers\AuthController as MythAuthController;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use stdClass;

class AuthController extends MythAuthController
{
    public function __construct()
    {
        parent::__construct();
        $this->config = new \Config\Auth();
    }

    /**
     * Attempt to register a new user.
     */
    public function attemptRegister()
    {
        // Check if registration is allowed
        if (!$this->config->allowRegistration) {
            return redirect()->back()->withInput()->with('error', lang('Auth.registerDisabled'));
        }

        $users = model(UserModel::class);

        // Validate basics first since some password rules rely on these fields
        $rules = config('Validation')->registrationRules ?? [
            'username' => 'required|alpha_numeric_space|min_length[3]|max_length[30]|is_unique[users.username]',
            'email' => 'required|valid_email|is_unique[users.email]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Validate passwords since they can only be validated properly here
        $rules = [
            'password' => 'required',
            'pass_confirm' => 'required|matches[password]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $rules = [
            'first_name' => 'required',
            'last_name' => 'required',
            'phone_number' => 'required',
            'citizen_number' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Save the user
        $allowedPostFields = array_merge(['password'], $this->config->validFields, $this->config->personalFields);

        $user = new User($this->request->getPost($allowedPostFields));

        $this->config->requireActivation === null ? $user->activate() : $user->generateActivateHash();

        // Ensure default group gets assigned if set
        if (!empty($this->config->defaultUserGroup)) {
            // $users = $users->withGroup($this->config->defaultUserGroup);
        }

        $users = $users->setProfileFields([
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
            'phone_number' => $this->request->getPost('phone_number'),
        ]);

        if (!$users->save($user)) {
            return redirect()->back()->withInput()->with('errors', $users->errors());
        }

        $authorize = service('authorization');
        $authorize->addUserToGroup($users->getInsertID(), 'User');

        if ($this->config->requireActivation !== null) {
            $activator = service('activator');
            $sent = $activator->send($user);

            if (!$sent) {
                return redirect()->back()->withInput()->with('error', $activator->error() ?? lang('Auth.unknownError'));
            }

            // Success!
            return redirect()->route('login')->with('message', lang('Auth.activationSuccess'));
        }

        // Success!
        return redirect()->route('login')->with('message', lang('Auth.registerSuccess'));
    }

    /**
     * Attempts to verify the user's credentials
     * through a POST request.
     */
    public function attemptLogin()
    {
        $header = $this->request->getServer('HTTP_AUTHORIZATION');

        $rules = [
            'login' => 'required',
            'password' => 'required',
        ];
        if ($this->config->validFields === ['email']) {
            $rules['login'] .= '|valid_email';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $login = $this->request->getPost('login');
        $password = $this->request->getPost('password');
        $remember = (bool) $this->request->getPost('remember');

        // Determine credential type
        $type = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // Try to log them in...
        if (!$this->auth->attempt([$type => $login, 'password' => $password], $remember)) {
            if (!$header) {
                return redirect()->back()->withInput()->with('error', $this->auth->error() ?? lang('Auth.badAttempt'));
            }
            return $this->response->setJSON([
                'logged_in' => false,
                'token' => null,
                'error' => 'Token required!'
            ]);
        }

        if ($header) {
            $key = getenv('TOKEN_SECRET');
            $today = new \DateTime('now', new \DateTimeZone('UTC'));
            $todayTimestamp = $today->getTimestamp();
            $today->modify('+1 day');
            $endTimestamp = $today->getTimestamp();
            $payload = [
                'iat' => $todayTimestamp,
                'nbf' => $endTimestamp,
                'uid' => user()->id,
                'email' => user()->email
            ];
            $token = JWT::encode($payload, $key, 'HS256');
            return $this->response->setJSON([
                'logged_in' => true,
                'token' => $token
            ]);
        }

        // Is the user being forced to reset their password?
        if ($this->auth->user()->force_pass_reset === true) {
            return redirect()->to(route_to('reset-password') . '?token=' . $this->auth->user()->reset_hash)->withCookies();
        }

        $redirectURL = session('redirect_url') ?? site_url('/');
        unset($_SESSION['redirect_url']);

        helper('auth');

        //get user timezone
        $db = \Config\Database::connect();
        $builder = $db->table('profile p')
            ->select('p.timezone')
            ->where('p.user_id', user()->id)
            ->get()->getRowArray();
        // $_SESSION['timezone'] = $builder['timezone'];

        $session = \Config\Services::session();
        $session->set(['timezone' => $builder['timezone']]);

        return redirect()->to($redirectURL)->withCookies()->with('message', lang('Auth.loginSuccess'));
    }

    /**
     * .
     */
    public function JWTLogin()
    {
        $rules = [
            'login' => 'required',
            'password' => 'required',
        ];
        if ($this->config->validFields === ['email']) {
            $rules['login'] .= '|valid_email';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $login = $this->request->getPost('login');
        $password = $this->request->getPost('password');
        $remember = (bool) $this->request->getPost('remember');

        // Determine credential type
        $type = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // Try to log them in...
        if (!$this->auth->attempt([$type => $login, 'password' => $password], $remember)) {
            return $this->response->setJSON([
                'logged_in' => false,
                'token' => null,
            ]);
        }

        $key = getenv('TOKEN_SECRET');
        $today = new \DateTime('now', new \DateTimeZone('UTC'));
        $todayTimestamp = $today->getTimestamp();
        $today->modify('+1 day');
        $endTimestamp = $today->getTimestamp();
        $payload = [
            'iat' => $todayTimestamp,
            'uid' => $this->auth->user()->id,
            'email' => $this->auth->user()->email
        ];
        $token = JWT::encode($payload, $key, 'HS256');

        //Load token from cache
        if (!cache('users_jwt')) {
            cache()->save('users_jwt', [], PHP_INT_MAX);
        }
        $usersJWT = cache('users_jwt');

        // echo '<pre>';
        // print_r($usersJWT);
        // echo '</pre>';
        // die();

        //remove token with same user id (.same uid)
        $selectedKey = null;
        foreach($usersJWT as $k => $uj){
            $decoded = JWT::decode($k, new Key($key, 'HS256'));
            if($decoded->uid == $this->auth->user()->id){
                $selectedKey = $k;
                break;
            }           
        }
        if($selectedKey){
            unset($usersJWT[$k]);
        }

        //save token to cache for later use
        $usersJWT[$token] = $payload;
        cache()->save('users_jwt', $usersJWT, PHP_INT_MAX);

        return $this->response->setJSON([
            'logged_in' => true,
            'token' => $token
        ]);
    }

    /**
     * .
     */
    public function JWTCheck()
    {
        $token = $this->request->getServer('HTTP_AUTHORIZATION');

        $response = [
            'status' => false,
        ];
        if (!$token) {
            return $this->response->setJSON($response);
        }

        $key = getenv('TOKEN_SECRET');
        $usersJWT = cache('users_jwt');

        if (!$usersJWT) {
            $response['message'] = 'Not yet logged-in';
            return $this->response->setJSON($response);
        } else {
            if (!array_key_exists($token, $usersJWT)) {
                $response['message'] = 'Not yet logged-in';
                return $this->response->setJSON($response);
            }
        }

        try {
            $decoded = JWT::decode($token, new Key($key, 'HS256'));

            $response['status'] = true;
            $response['jwt'] = [
                'iat' => $decoded->iat,
                'uid' => $decoded->uid,
                'email' => $decoded->email
            ];
        } catch (\Throwable $th) {
            $response['message'] = 'JWT fail';
            return $this->response->setJSON($response);
        }

        return $this->response->setJSON($response);
    }


}
