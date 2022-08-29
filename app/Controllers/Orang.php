<?php 

namespace App\Controllers;

use App\Models\OrangModel;
use CodeIgniter\CodeIgniter;
use CodeIgniter\Exceptions\PageNotFoundException;

class Orang extends BaseController
{
    protected $orangModel;
    public function __construct()
    {
        $this->orangModel = new OrangModel();
    }
    public function index()
    {

        $currentPage = $this->request->getVar('page_orang') ? $this->request->getVar('page_orang') : 1;

        $keyword = $this->request->getVar('keyword');
        if ($keyword) {
          $orang =  $this->orangModel->search($keyword);
        } else {
            $orang = $this->orangModel;
        }

        $data = [
            'title' => 'Daftar Orang',
            'orang' => $orang->paginate(5, 'orang'),
            'pager' => $this->orangModel->pager,
            'currentPage' => $currentPage
            // 'orang' => $this->orangModel->findAll()
        ];  

        // $komikModel = new KomikModel();
        
        return view('orang/index', $data);
    }
    public function delete($id)
    {

        //gambar berdasarkan id
        

        //Cek jika gamabr bukan default.jpg
        
        $this->orangModel->delete($id);
        session()->setFlashdata('pesan', 'Data berhasil di Hapus.');
        return redirect()->to('/orang');
    }
}