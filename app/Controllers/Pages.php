<?php

namespace App\Controllers;

class Pages extends BaseController
{
    public function index()
    {

        $data = [
            'title' => 'Home | Ahmad Fahmy',
            'tes' => ["Satu", "Dua", "Tiga"]
        ];
        return view('pages/home', $data);
    }

    public function about(){
        $data = [
            'title' => 'About | Ahmad Fahmy'
        ];
        return view('pages/about',$data);
    }

    public function contact(){
        $data = [
            'title' => 'Contact | Ahmad Fahmy',
            'alamat' => [
                [
                    'tipe' => 'Rumah',
                    'alamat' => 'Jl. abc No.123',
                    'kota' => 'Malang'
                ],
                [
                    'tipe' => 'Kantor',
                    'alamat' => 'Sumbersekar',
                    'kota' => 'Malang'
                ]
             ]
        ];

        return view('pages/contact', $data);
    }
}
