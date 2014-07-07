<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class posting_bphtb extends CI_Controller {

	private $module  = 'bphtb';
	private $current = 'bphtb';
	
	function __construct() {
		parent::__construct();
		if(!$this->session->userdata('login')) {
			$this->session->set_flashdata('msg_warning', 'Session telah kadaluarsa, silahkan login ulang.');
			redirect('login');
			exit;
		}

		$this->load->model(array('apps_model'));
        $this->load->model(array('bphtb_model'));
    }

	function load_auth() {
        $this->load->library('module_auth', array('module' => $this->module));
    }

	public function index() {
		$this->load_auth();
		if(!$this->module_auth->read) {
			$this->session->set_flashdata('msg_warning', $this->module_auth->msg_read);
			redirect(active_module_url(''));
		}

		$data['current'] = $this->current;
		$data['apps']    = $this->apps_model->get_active_only();
        
		$this->load->view('vposting_bphtb', $data);
	}
	
	function grid() {
		$i=0;
        $query = $this->bphtb_model->get_daftar_sspd();
        
        if($query) {
			foreach($query as $row) {
				$responce->aaData[$i][]=$row->id;
                $responce->aaData[$i][]='';
                $nomor = (string)$row->no_sspd;
                $nomor = str_pad($nomor, 6, "0", STR_PAD_LEFT);
                $responce->aaData[$i][]=$row->tahun . '.' . str_pad($row->kode, 2, "0", STR_PAD_LEFT) . '.' . $nomor; 

                $responce->aaData[$i][]=$row->wp_nama;
                $responce->aaData[$i][]=$row->nomor_op;
                $responce->aaData[$i][]=$row->thn_pajak_sppt;
                $responce->aaData[$i][]=number_format($row->bphtb_harus_dibayarkan,0,',','.');
				
                $responce->aaData[$i][]=$row->status_pembayaran;
                $responce->aaData[$i][]=date('d/m/Y',strtotime($row->tgl_transaksi));
				
                $responce->aaData[$i][]=$row->nm_ppat;
                $responce->aaData[$i][]=$row->kd_ppat;
				
                if ($row->status_daftar==1) 
                    $status = 'Verifikasi';
                else if ($row->status_daftar==2) 
                    $status = 'Bayar';
                else if ($row->status_daftar==3) 
                    $status = 'Validasi Penelitian';
                else if ($row->status_daftar==4) 
                    $status = 'Approval';
                else if ($row->status_daftar==5) 
                    $status = 'SKPD-KB';
                else if ($row->status_daftar==6) 
                    $status = 'SKPD-LB';
                else if ($row->status_daftar==7) 
                    $status = 'BPN';
                else if ($row->status_daftar==9) 
                    $status = 'Selesai';
                else //0
                    $status = 'Draft';
                
                $responce->aaData[$i][]=$status;
				$responce->aaData[$i][]=$row->keterangan;
				$responce->aaData[$i][]="<input type='checkbox' class='cek_sspd_id' id='sspd_id' name='sspd_id' value='{$row->id}' onClick='ceklik();'>";
				$i++;
			}
		} else {
			$responce->sEcho=1;
			$responce->iTotalRecords="0";
			$responce->iTotalDisplayRecords="0";
			$responce->aaData=array();
		}
		echo json_encode($responce);
	}
}
