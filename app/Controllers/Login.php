<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\IncomingRequest;

class Login extends BaseController
{
    public function index()
    {
        $session = session();
        return view('login/index', [
            'title' => 'Login',
            'alert' => $session->alert,
        ]);
    }

    public function doLogin()
    {
        $session = session();
        $adminMdl = new \App\Models\Admin();
        $request = service('request');
        $username = $request->getPost('username');
        $adminObj = $adminMdl->where('username', $username)->first();
        if (!is_null($adminObj)) {
            if (password_verify($request->getPost('password'), $adminObj->password)) {
                $session->set(['isLoggedIn' => true]);
                return redirect()->to('/admin');
            } else {
                $session->setFlashdata('alert', ['type' => 'warning', 'msg' => 'Maaf, Password Salah']);
                return redirect()->to('/login');
            }
        } else {
            $session->setFlashdata('alert', ['type' => 'warning', 'msg' => 'Maaf, Username Salah']);
            return redirect()->to('/login');
        }
    }
}
