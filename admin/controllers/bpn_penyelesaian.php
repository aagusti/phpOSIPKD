<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Bpn_penyelesaian extends CI_Controller
{
    private $isppat = FALSE;
    private $ppatkd = '';
    private $ppatnm = '';
    private $ppatid = 0;
	
    function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('login')) {
            $this->session->set_flashdata('msg_warning', 'Session telah kadaluarsa, silahkan login ulang.');
            redirect('login');
            exit;
        }
        
        $module = 'bpn';
        $this->load->library('module_auth', array(
            'module' => $module
        ));
        
        $this->load->model(array(
            'apps_model'
        ));
        $this->load->model(array(
            'bphtb_self_model',
            'bank_model',
            'ppat_user_model',
			'ppat_model',
			'penerimaan_model',
			'sspd_model',
        ));
		
        $row=$this->ppat_user_model->getkode($this->session->userdata('uid'));
        if ($row){
            $this->session->set_userdata('isppat',true);
            $this->session->set_userdata('ppatkd',$row->kode);
            $this->isppat = TRUE;
            $this->ppatkd = $row->kode;
            $ppat = $this->ppat_model->get_id_nama($this->ppatkd);
            $this->ppatid = $ppat[0];       // untuk relationship
            $this->ppatnm = $ppat[1];       // untuk menampilkan di entry sspd dgn login ppat
        }else{
            $this->session->set_userdata('isppat',false);
            $this->session->set_userdata('ppatkd',false);
        }
		
        $this->{'fields'} = $this->bphtb_self_model->fields_info('bphtb_validasi');
        $this->info       = ($this->fields) ? TRUE : FALSE;
    }
    
    public function index()
    {
        if (!$this->module_auth->read) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_read);
            redirect(active_module_url(''));
        }
        
        $data['current']      = 'bpn';
        $data['apps']         = $this->apps_model->get_active_only();
        $data['pagetitle']    = 'OpenSIPKD';
        $data['title']        = 'Transaksi Pembayaran';
        $data['main_content'] = '';
		
        //FORM PARAMETER
		$proses_id = isset($_GET['proses_id']) ? $_GET['proses_id'] : 1;
		$options = array(
			'1' => 'BELUM PROSES',
			'2' => 'SUDAH PROSES',
		);
		$js = 'id="proses_id" class="input-medium"';
		$select = form_dropdown('proses_id', $options, $proses_id, $js);
		$select = preg_replace("/[\r\n]+/", "", $select);
		$data['select_proses'] = $select;
		
        //Grid Standard Parameter
        $data['iDisplayLength'] = (isset($_GET['iDisplayLength']) && is_numeric($_GET['iDisplayLength'])) ? $_GET['iDisplayLength'] : 15;
        $data['iDisplayStart']  = (isset($_GET['iDisplayStart']) && is_numeric($_GET['iDisplayStart'])) ? $_GET['iDisplayStart'] : 0;
        $data['iSortingCols']   = (isset($_GET['iSortingCols']) && is_numeric($_GET['iSortingCols'])) ? $_GET['iSortingCols'] : 1;
        $data['sEcho']          = (isset($_GET['sEcho']) && is_numeric($_GET['sEcho'])) ? $_GET['sEcho'] : 1;
        $data['sSearch']        = (isset($_GET['sSearch'])) ? $_GET['sSearch'] : "";
        $data['data_source']    = active_module_url() . "bpn_penyelesaian/grid/{$proses_id}";
        
        $this->load->view('vbpn_penyelesaian', $data);
    }
    
    function grid()
    {
		$proses_id = $this->uri->segment(4);
		$status =  TRUE; //($proses_id > 0);
		
		$i=0;
		$responce = new stdClass();
		$query = $this->penerimaan_model->grid_bpn($status, $proses_id);
		if($query) {
			foreach($query as $row) {
				$nop_thn = $row->kd_propinsi.'.'.$row->kd_dati2.'.'.$row->kd_kecamatan.'.'.$row->kd_kelurahan.'.'.$row->kd_blok.'.'.$row->no_urut.'.'.$row->kd_jns_op.'-'.$row->thn_pajak_sppt;
				
				$responce->aaData[$i][] = $row->id;
				$responce->aaData[$i][] = $row->transno;
				$responce->aaData[$i][] = !empty($row->tanggal) ? date('d-m-Y', strtotime($row->tanggal)) : '';
				$responce->aaData[$i][] = $nop_thn;
				$responce->aaData[$i][] = $row->wp_nama;
				$responce->aaData[$i][] = number_format($row->bayar, 0, ',', '.');
				$responce->aaData[$i][] = !empty($row->tgl_proses) ? date('d-m-Y', strtotime($row->tgl_proses)) : '';
				$responce->aaData[$i][] = !empty($row->tgl_selesai) ? date('d-m-Y', strtotime($row->tgl_selesai)) : '';
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
	    
    // --- new ---
    private function fvalidation()
    {
        //bank validation
        $this->form_validation->set_error_delimiters('<span>', '</span>');
        // $this->form_validation->set_rules('perolehan_id', 'Jenis Perolehan', 'required|numeric');
        // $this->form_validation->set_rules('tanggal', 'Tgl. Transaksi', 'required');
        // $this->form_validation->set_rules('transno', 'No. Transaksi', 'required');
        // $this->form_validation->set_rules('wp_identitas', 'WP Identitas', 'required');
        // $this->form_validation->set_rules('wp_nama', 'WP Nama', 'required');
        // $this->form_validation->set_rules('wp_alamat', 'WP Alamat', 'required');
		
		// $this->form_validation->set_rules('kd_propinsi','Kode Propinsi SPPT','required|trim');
		// $this->form_validation->set_rules('kd_dati2','Kode Dati2 SPPT','required|trim');
		// $this->form_validation->set_rules('kd_kecamatan','Kode Kecamatan SPPT','required|trim');
		// $this->form_validation->set_rules('kd_kelurahan','Kode Kelurahan SPPT','required|trim');
		// $this->form_validation->set_rules('kd_blok','Kd Blok SPPT','required|trim');
		// $this->form_validation->set_rules('no_urut','No Urut SPPT','required|trim');
		// $this->form_validation->set_rules('kd_jns_op','Kode Jenis SPPT','required|trim');
		// $this->form_validation->set_rules('thn_pajak_sppt','Tahun Pajak  SPPT','required|trim');
		
        $this->form_validation->set_rules('tgl_proses', 'Tgl. Proses', 'required');
    }
    
    private function fpost()
    {
		$data['id'] = $this->input->post('id');
		$data['tanggal'] = $this->input->post('tanggal');
		$data['jam'] = $this->input->post('jam');
		$data['seq'] = $this->input->post('seq');
		$data['transno'] = $this->input->post('transno');
		$data['cabang'] = $this->input->post('cabang');
		$data['users'] = $this->input->post('users');
		$data['bankid'] = $this->input->post('bankid');
		$data['txs'] = $this->input->post('txs');
		$data['sspd_id'] = $this->input->post('sspd_id');
		// $data['nop'] = $this->input->post('nop');
		$data['kd_propinsi'] = $this->input->post('kd_propinsi');
		$data['kd_dati2'] = $this->input->post('kd_dati2');
		$data['kd_kecamatan'] = $this->input->post('kd_kecamatan');
		$data['kd_kelurahan'] = $this->input->post('kd_kelurahan');
		$data['kd_blok'] = $this->input->post('kd_blok');
		$data['no_urut'] = $this->input->post('no_urut');
		$data['kd_jns_op'] = $this->input->post('kd_jns_op');
		$data['thn_pajak_sppt'] = $this->input->post('thn_pajak_sppt');
		$data['wp_nama'] = $this->input->post('wp_nama');
		$data['wp_alamat'] = $this->input->post('wp_alamat');
		$data['wp_blok_kav'] = $this->input->post('wp_blok_kav');
		$data['wp_rt'] = $this->input->post('wp_rt');
		$data['wp_rw'] = $this->input->post('wp_rw');
		$data['wp_kelurahan'] = $this->input->post('wp_kelurahan');
		$data['wp_kecamatan'] = $this->input->post('wp_kecamatan');
		$data['wp_kota'] = $this->input->post('wp_kota');
		$data['wp_provinsi'] = $this->input->post('wp_provinsi');
		$data['wp_kdpos'] = $this->input->post('wp_kdpos');
		$data['wp_identitas'] = $this->input->post('wp_identitas');
		$data['wp_identitaskd'] = $this->input->post('wp_identitaskd');
		$data['wp_npwp'] = $this->input->post('wp_npwp');
		$data['notaris'] = $this->input->post('notaris');
		
		$data['bumi_luas'] = $this->to_decimal($this->input->post('bumi_luas'));
		$data['bumi_njop'] = $this->to_decimal($this->input->post('bumi_njop'));
		$data['bng_luas'] = $this->to_decimal($this->input->post('bng_luas'));
		$data['bng_njop'] = $this->to_decimal($this->input->post('bng_njop'));
		$data['npop'] = $this->to_decimal($this->input->post('npop'));
		$data['bayar'] = $this->to_decimal($this->input->post('bayar'));
		$data['denda'] = $this->to_decimal($this->input->post('denda'));
		
		$data['bphtbjeniskd'] = $this->input->post('bphtbjeniskd');
		$data['no_tagihan'] = $this->input->post('no_tagihan');
		$data['catatan'] = $this->input->post('catatan');
		$data['kd_kanwil'] = $this->input->post('kd_kanwil');
		$data['kd_kantor'] = $this->input->post('kd_kantor');
		$data['kd_bank_tunggal'] = $this->input->post('kd_bank_tunggal');
		$data['kd_bank_persepsi'] = $this->input->post('kd_bank_persepsi');
		$data['is_validated'] = $this->input->post('is_validated');
		
		$data['perolehan_id'] = $this->input->post('perolehan_id');
		
		$data['tahun'] = $this->input->post('tahun');
		$data['kode'] = $this->input->post('kode');
		$data['no_sspd'] = $this->input->post('no_sspd');
		
		$data['tgl_proses'] = $this->input->post('tgl_proses');
		$data['tgl_selesai'] = $this->input->post('tgl_selesai');
        
        return $data;
    }
    
    public function register()
    {
        if (!$this->module_auth->update) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_update);
            redirect(active_module_url($this->uri->segment(2)));
        }
        $data['current'] = 'bpn';
        $data['apps']    = $this->apps_model->get_active_only();
        $data['faction'] = active_module_url('bpn_penyelesaian/do_register');
        
        $data['ppat']   = $this->bphtb_self_model->get_ppat();
        $data['isppat'] = $this->isppat;
        $data['ppatid'] = $this->ppatid;
        $data['ppatnm'] = $this->ppatnm;
		
        $data['perolehan'] = $this->bphtb_self_model->get_perolehan();
        $data['dasar']     = $this->bphtb_self_model->get_dasar_perhitungan();
        $data['info']      = $this->info;
        $data['fields']    = $this->fields;
        $data['mode']      = 'edit';
        
        $id = $this->uri->segment(4);
        
        if ($id && $terima = $this->penerimaan_model->get($id)) {
            $data['dt'] = array();
            $my_fields  = $this->bank_model->get_entry_fields();
			
			$get = $terima;
						
			$no_tagihan = '';
			if (!empty($get->no_tagihan)) {
				$nt = explode('.', $get->no_tagihan);
				$data['dt']['tahun']   = $nt[0];
				$data['dt']['kode']    = $nt[1];
				$data['dt']['no_sspd'] = $nt[2];
			}
			
            //get
			$data['dt']['id'] = empty($get->id) ? NULL : $get->id;
			$data['dt']['tanggal'] = empty($get->tanggal) ? NULL : date('d-m-Y', strtotime($get->tanggal));
			$data['dt']['jam'] = empty($get->jam) ? NULL : date('d-m-Y', strtotime($get->jam));
			$data['dt']['seq'] = empty($get->seq) ? NULL : $get->seq;
			$data['dt']['transno'] = empty($get->transno) ? NULL : $get->transno;
			$data['dt']['cabang'] = empty($get->cabang) ? NULL : $get->cabang;
			$data['dt']['users'] = empty($get->users) ? NULL : $get->users;
			$data['dt']['bankid'] = empty($get->bankid) ? NULL : $get->bankid;
			$data['dt']['txs'] = empty($get->txs) ? NULL : $get->txs;
			$data['dt']['sspd_id'] = empty($get->sspd_id) ? NULL : $get->sspd_id;
			// $data['dt']['nop'] = empty($get->nop) ? NULL : $get->nop;
			$data['dt']['tahun'] = empty($get->tahun) ? NULL : $get->tahun;
			$data['dt']['kd_propinsi'] = empty($get->kd_propinsi) ? NULL : $get->kd_propinsi;
			$data['dt']['kd_dati2'] = empty($get->kd_dati2) ? NULL : $get->kd_dati2;
			$data['dt']['kd_kecamatan'] = empty($get->kd_kecamatan) ? NULL : $get->kd_kecamatan;
			$data['dt']['kd_kelurahan'] = empty($get->kd_kelurahan) ? NULL : $get->kd_kelurahan;
			$data['dt']['kd_blok'] = empty($get->kd_blok) ? NULL : $get->kd_blok;
			$data['dt']['no_urut'] = empty($get->no_urut) ? NULL : $get->no_urut;
			$data['dt']['kd_jns_op'] = $get->kd_jns_op;
			$data['dt']['thn_pajak_sppt'] = empty($get->thn_pajak_sppt) ? NULL : $get->thn_pajak_sppt;
			$data['dt']['wp_nama'] = empty($get->wp_nama) ? NULL : $get->wp_nama;
			$data['dt']['wp_alamat'] = empty($get->wp_alamat) ? NULL : $get->wp_alamat;
			$data['dt']['wp_blok_kav'] = empty($get->wp_blok_kav) ? NULL : $get->wp_blok_kav;
			$data['dt']['wp_rt'] = empty($get->wp_rt) ? NULL : $get->wp_rt;
			$data['dt']['wp_rw'] = empty($get->wp_rw) ? NULL : $get->wp_rw;
			$data['dt']['wp_kelurahan'] = empty($get->wp_kelurahan) ? NULL : $get->wp_kelurahan;
			$data['dt']['wp_kecamatan'] = empty($get->wp_kecamatan) ? NULL : $get->wp_kecamatan;
			$data['dt']['wp_kota'] = empty($get->wp_kota) ? NULL : $get->wp_kota;
			$data['dt']['wp_provinsi'] = empty($get->wp_provinsi) ? NULL : $get->wp_provinsi;
			$data['dt']['wp_kdpos'] = empty($get->wp_kdpos) ? NULL : $get->wp_kdpos;
			$data['dt']['wp_identitas'] = empty($get->wp_identitas) ? NULL : $get->wp_identitas;
			$data['dt']['wp_identitaskd'] = empty($get->wp_identitaskd) ? NULL : $get->wp_identitaskd;
			$data['dt']['wp_npwp'] = empty($get->wp_npwp) ? NULL : $get->wp_npwp;
			$data['dt']['notaris'] = empty($get->notaris) ? NULL : $get->notaris;
			$data['dt']['bumi_luas'] = empty($get->bumi_luas) ? NULL : $get->bumi_luas;
			$data['dt']['bumi_njop'] = empty($get->bumi_njop) ? NULL : $get->bumi_njop;
			$data['dt']['bng_luas'] = empty($get->bng_luas) ? NULL : $get->bng_luas;
			$data['dt']['bng_njop'] = empty($get->bng_njop) ? NULL : $get->bng_njop;
			$data['dt']['npop'] = empty($get->npop) ? NULL : $get->npop;
			$data['dt']['bayar'] = empty($get->bayar) ? NULL : $get->bayar;
			$data['dt']['no_tagihan'] = empty($get->no_tagihan) ? NULL : $get->no_tagihan;
			$data['dt']['denda'] = empty($get->denda) ? NULL : $get->denda;
			$data['dt']['catatan'] = empty($get->catatan) ? NULL : $get->catatan;
			$data['dt']['kd_kanwil'] = empty($get->kd_kanwil) ? NULL : $get->kd_kanwil;
			$data['dt']['kd_kantor'] = empty($get->kd_kantor) ? NULL : $get->kd_kantor;
			$data['dt']['kd_bank_tunggal'] = empty($get->kd_bank_tunggal) ? NULL : $get->kd_bank_tunggal;
			$data['dt']['kd_bank_persepsi'] = empty($get->kd_bank_persepsi) ? NULL : $get->kd_bank_persepsi;
			$data['dt']['is_validated'] = empty($get->is_validated) ? NULL : $get->is_validated;
			
			// $data['dt']['bphtbjeniskd'] = empty($get->bphtbjeniskd) ? NULL : $get->bphtbjeniskd;
			$data['dt']['perolehan_id'] = $get->bphtbjeniskd;

			$bpn =  $this->db->query("SELECT tgl_proses FROM bphtb_bpn WHERE bank_id={$get->id}");
			if ($bpn->num_rows() > 0)
				$data['dt']['tgl_proses'] = $bpn->row()->tgl_proses;
			else 
				$data['dt']['tgl_proses'] = '';
			
            $this->load->view('form_contents/vbpn_penyelesaian_form', $data);
        } else {
            show_404();
        }
    }
    
    public function do_register()
    {
        if (!$this->module_auth->create) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_create);
            redirect(active_module_url($this->uri->segment(2)));
        }
        
        $this->fvalidation();
        if ($this->form_validation->run() == TRUE) {		
            $input_post = $this->fpost();
			$id = $input_post['id'];
			
			// $validasi = $this->db->query("SELECT id FROM bphtb_validasi WHERE bank_id={$id}");
			// $validasi_id = $validasi->num_rows() > 0 ? $validasi->id : NULL;
			
			if($bank = $this->penerimaan_model->get($id)) {
				$post_data = array (
					// 'bank_id' => $bank->id, 
					// 'sspd_id' => $bank->sspd_id,
					// 'validasi_id' => $validasi_id,
					// 'tgl_proses' => date('Y-m-d', strtotime($input_post['tgl_proses'])),
					'tgl_selesai' => date('Y-m-d', strtotime($input_post['tgl_selesai'])),
					'status' => 2,
				);
				
				$bpn =  $this->db->query("SELECT id FROM bphtb_bpn WHERE bank_id={$id}");
				if ($bpn->num_rows() > 0)
					$bpn_id = $bpn->row()->id;
				else {
					$this->session->set_flashdata('msg_success', 'Data gagal diperbaharui.');
					redirect(active_module_url($this->uri->segment(2)));
				}
				
				$this->db->update('bphtb_bpn',$post_data, array("id" => $bpn_id));
				
				$this->session->set_flashdata('msg_success', 'Data telah disimpan');
				redirect(active_module_url($this->uri->segment(2)));
			}
			$this->session->set_flashdata('msg_success', 'apakah');
        }
        
        $data['current'] = 'bpn';
        $data['apps']    = $this->apps_model->get_active_only();
        $data['faction'] = active_module_url('bpn_penyelesaian/do_register');
        
        $data['ppat']   = $this->bphtb_self_model->get_ppat();
        $data['isppat'] = $this->isppat;
        $data['ppatid'] = $this->ppatid;
        $data['ppatnm'] = $this->ppatnm;
		
        $data['perolehan'] = $this->bphtb_self_model->get_perolehan();
        $data['dasar']     = $this->bphtb_self_model->get_dasar_perhitungan();
        $data['info']      = $this->info;
        $data['fields']    = $this->fields;
        $data['mode']      = 'edit';
        
        $data['dt'] = $this->fpost();
		
        $this->load->view('form_contents/vbpn_penyelesaian_form', $data);
    }
   
	//////// --------
	public function get_sspd() {
		$thn    = (int) $this->uri->segment(4);
		$kd     = (int) $this->uri->segment(5);
		$sspdno = (int) $this->uri->segment(6);
		
		$sql = "SELECT * FROM bphtb_sspd WHERE tahun='{$thn}' AND kode='{$kd}' AND no_sspd='{$sspdno}'";
		$query = $this->db->query($sql)->row();
		
		if ($query) 
			echo json_encode($query);
		else
			echo json_encode(array());
	}	
		
	function get_typeahead_wp_ktp() {
        $xktp = $this->uri->segment(4);
        $data = $this->bphtb_self_model->get_typeahead_wp_ktp($xktp);
        echo json_encode($data);
    }
	
	function get_wp() {
        $id = $this->uri->segment(4);
        $data = $this->bphtb_self_model->get_wp($id);
        echo json_encode($data);
	}
	
    function get_npoptkp() {
        $id = $this->uri->segment(4) ? $this->uri->segment(4) : 0;
        if($data = $this->bphtb_self_model->get_npoptkp($id)) {
			$result =  $data;
		} else {
            $result['npoptkp'] = 0;
            $result['tarif_pengurang'] = 0;
		}
		echo json_encode($result);
	}

	function get_op() {
		$kd_propinsi  = $this->uri->segment(4);
		$kd_dati2     = $this->uri->segment(5);
		$kd_kecamatan = $this->uri->segment(6);
		$kd_kelurahan = $this->uri->segment(7);
		$kd_blok      = $this->uri->segment(8);
		$no_urut      = $this->uri->segment(9);
		$kd_jns_op    = $this->uri->segment(10);
		$thn          = $this->uri->segment(11);
		
		// ngambil dari ppb.dat_objek_pajak
		// $get1 = $this->bphtb_self_model->get_data_op_from_dop($kd_propinsi, $kd_dati2, $kd_kecamatan, $kd_kelurahan, $kd_blok, $no_urut, $kd_jns_op);
		// ngambil dari sppt
		$get2 = $this->bphtb_self_model->get_data_op_from_sppt($kd_propinsi, $kd_dati2, $kd_kecamatan, $kd_kelurahan, $kd_blok, $no_urut, $kd_jns_op, $thn);

		// if (!$get1 && !$get2)
			// $data = NULL;
		// if ($get1 && $get2)
			// $data = (object) array_merge((array) $get1, (array) $get2);		
		
        // echo json_encode($data);
        echo json_encode($get2);
	}
	
    function to_decimal($str_val, $ret_val = NULL)
    {
        $val = $str_val;
        $val = str_replace(".", "", $val);
        $val = str_replace(",", ".", $val);
        return $val != '' ? $val : (!empty($ret_val) ? $ret_val : 0);
    }
}
