<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Login extends BaseController
{
    public function index()
    {
        return view('login/index', [
            'title' => 'Login',
            'alert' => $this->session->alert,
        ]);
    }

    public function doLogin()
    {
        $adminMdl = new \App\Models\Admin();
        $username = $this->request->getPost('username');
        $adminObj = $adminMdl->where('username', $username)->first();
        if (!is_null($adminObj)) {
            if (password_verify($this->request->getPost('password'), $adminObj->password)) {
                $this->session->set([
                    'isLoggedIn'    => true,
                    'userId'        => $adminObj->id,
                ]);
                return redirect()->to('/admin');
            } else {
                $this->session->setFlashdata('alert', ['type' => 'warning', 'msg' => 'Maaf, Password Salah']);
                return redirect()->to('/login');
            }
        } else {
            $this->session->setFlashdata('alert', ['type' => 'warning', 'msg' => 'Maaf, Username Salah']);
            return redirect()->to('/login');
        }
    }
}
