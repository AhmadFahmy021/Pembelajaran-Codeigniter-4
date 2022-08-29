<?php 

namespace App\Controllers;

use App\Models\KomikModel;
use CodeIgniter\CodeIgniter;
use CodeIgniter\Exceptions\PageNotFoundException;

class Komik extends BaseController
{
    protected $komikModel;
    public function __construct()
    {
        $this->komikModel = new KomikModel();
    }
    public function index()
    {
        // $komik = $this->komikModel->findAll();
        $data = [
            'title' => 'Daftar Komik',
            'komik' => $this->komikModel->getKomik()
        ];  

        // $komikModel = new KomikModel();
        
        return view('komik/index', $data);
    }

    public function detail($slug)
    {
       
        $data = [
            'title' => 'Detail Komik',
            'komik' => $this->komikModel->getKomik($slug)
        ];

        // Jika data tidak ada di tabel 
        if (empty($data['komik'])) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Judul Komik'. $slug. ' Tidak Di temukan');
            
        }

        return view ('komik/detail', $data);
    }

    public function create()
    {
        // session();
        $data = [
            'title' => 'Form Tambah Data Komik',
            'validation' => \Config\Services::validation()
        ];

        return view('komik/create', $data);
    }

    public function save(){

        // Validasi Input
        if (!$this->validate([
            'judul' => [
                'rules' => 'required|is_unique[komik.judul]',
                'errors' => [
                    'required' => '{field} Komik Harus Di Isi.',
                    'is_unique' => '{field} Komik Sudah Terdaftar.'
                ]
                ],
                'sampul' => [
                    'rules' => 'uploaded[sampul]|max_size[sampul,1024]|is_image[sampul]|mime_in[sampul,image/jpg,image/jpeg,image/png]',
                    'errors' => [
                        'uploaded' => 'pilih gambar terlebih dahulu',
                        'max_size' => 'Gambar yang anda pilih di luar kapasitas',
                        'is_image' => 'Yang anda pilih bukan gambar',
                        'mime_in' => 'Yang anda pilih bukan gambar'
                    ]
                ]
        ])) {
            // $validation = \Config\Services::validation();
            
            // return redirect()->to('/komik/create')->withInput()->with('validation', $validation);
            return redirect()->to('/komik/create')->withInput();
        }
        //ambil gambar
        $fileSampul = $this->request->getFile('sampul');
        //apakah tidak ada gambar di upload
        if ($fileSampul->getError()==4) {
            $namaSampul = 'default.jpg';
        } else{
            //generete nama sampul
            $namaSampul = $fileSampul->getRandomName();
            //pindahkan ke folder img
            $fileSampul->move('img', $namaSampul);
        }

        $slug = url_title($this->request->getVar('judul'), '-', true);
        $this->komikModel->save([
            'judul' => $this->request->getVar('judul'),
            'slug' => $slug,
            'penulis' => $this->request->getVar('penulis'),
            'penerbit' => $this->request->getVar('penerbit'),
            'sampul' => $namaSampul
        ]);

        session()->setFlashdata('pesan', 'Data Berhasil di tambahkan.');
        return redirect()->to('/komik');
    }

    public function delete($id)
    {

        //gambar berdasarkan id
        $komik = $this->komikModel->find($id);

        //Cek jika gamabr bukan default.jpg
        if ($komik['sampul'] != 'default.jpg') {
            # code...
            //hapus gambar 
            unlink('img/'.$komik['sampul']);
        }
        $this->komikModel->delete($id);
        session()->setFlashdata('pesan', 'Data berhasil di Hapus.');
        return redirect()->to('/komik');
    }

    public function edit($slug)
    {
        $data = [
            'title' => 'Form Ubah Data Komik',
            'validation' => \Config\Services::validation(),
            'komik' => $this->komikModel->getKomik($slug)
        ];

        return view('komik/edit', $data);
    }


    public function update($id)
    {

        // cek Judul
        $komikLama = $this->komikModel->getKomik($this->request->getVar('slug'));
        if ($komikLama['judul'] == $this->request->getVar('judul')) {
            $rule_judul = 'required';
        } else {
            $rule_judul = 'required|is_unique[komik.judul]';
        }

        // Validasi Input
        if (!$this->validate([
            'judul' => [
                'rules' => $rule_judul,
                'errors' => [
                    'required' => '{field} Komik Harus Di Isi.',
                    'is_unique' => '{field} Komik Sudah Terdaftar.'
                ]
                ],
                'sampul' => [
                'rules' => 'max_size[sampul,1024]|is_image[sampul]|mime_in[sampul,image/jpg,image/jpeg,image/png]',
                'errors' => [
                    'max_size' => 'Gambar yang anda pilih di luar kapasitas',
                    'is_image' => 'Yang anda pilih bukan gambar',
                    'mime_in' => 'Yang anda pilih bukan gambar'
                ]
            ]
        ])) {
            // $validation = \Config\Services::validation();

            return redirect()->to('/komik/edit/' . $this->request->getVar('slug'))->withInput();
        }

        //
        $fileSampul = $this->request->getFile('sampul');

        //cek gambar, Apakah tetap
        if ($fileSampul->getError() == 4) {
            $namaSampul = $this->request->getVar('sampulLama');
        } else {
            // generete 
            $namaSampul = $fileSampul->getRandomName();
            //pindahkan gambar
            $fileSampul->move('img', $namaSampul);
            //hapus file lama
            unlink('img/'.$this->request->getVar('sampulLama'));
        }

        $slug = url_title($this->request->getVar('judul'), '-', true);
        $this->komikModel->save([
            'id' => $id,
            'judul' => $this->request->getVar('judul'),
            'slug' => $slug,
            'penulis' => $this->request->getVar('penulis'),
            'penerbit' => $this->request->getVar('penerbit'),
            'sampul' => $namaSampul
        ]);

        session()->setFlashdata('pesan', 'Data Berhasil di Ubah.');
        return redirect()->to('/komik');
    }
}