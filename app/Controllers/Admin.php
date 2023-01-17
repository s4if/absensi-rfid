<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;


class Admin extends BaseController
{
    use ResponseTrait;
    public function index()
    {
        return view('admin/index', [
            'title' => 'Beranda',
            'alert' => $this->session->alert,
        ]);
    }

    public function password()
    {
        $id = $this->session->user_id;
        $data = $this->request->getVar();
        $admin_mdl = new \App\Models\Admin();
        $admin_obj = $admin_mdl->find($id);
        if (is_null($admin_obj)) {
            return $this->failNotFound('Admin dengan ID:'.$id." tidak ditemukan");
        }
        if ($data->new_password != $data->confirm_password) {
            return $this->failValidationError('Cek kembali password yang dimasukkan');
        }
        if (!password_verify((string) $data->old_password, (string) $admin_obj->password)) {
            return $this->failValidationError('Cek kembali password yang dimasukkan');
        }
        $admin_mdl->update($id, [
            'password' => password_hash((string) $data->new_password, PASSWORD_BCRYPT)
        ]);
        return $this->respondCreated([]);
    }
}
