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
        $admin_mdl = new \App\Models\Admin();
        $username = $this->request->getPost('username');
        $admin_obj = $admin_mdl->where('username', $username)->first();
        if (!is_null($admin_obj)) {
            if (password_verify((string) $this->request->getPost('password'), (string) $admin_obj->password)) {
                $this->session->set([
                    'is_logged_in'    => true,
                    'user_id'        => $admin_obj->id,
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
