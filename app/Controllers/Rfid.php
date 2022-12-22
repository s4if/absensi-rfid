<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Rfid extends BaseController
{
    public function index()
    {
        //
    }

    public function readRfid($device_id)
    {
        $token = $this->request->getGet('token');
        $rfid = $this->request->getGet('rfid');
        $device_builder = $this->db->table('devices');
        $device_builder->where('id', $device_id);
        $query = $device_builder->get();
        $device = $query->getRow();
        if (is_null($device)) {
            return 'device unknown';
        }
        if ($device->token == $token) {
            $data = [
                'rfid'          => $rfid,
                'updated_at'    => (new \DateTime('now'))->getTimestamp(),
                'device_id'     => $device_id
            ];
            $rfid_builder = $this->db->table('last_rfid');
            $rfid_builder->update($data, ['id' => 'DEFAULT']);
            return 'rfid logged';
        } else {
            return 'token invalid';
        }
        
    }
}
