<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Penerimaan extends CI_Controller
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
        
        $module = 'penerimaan';
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
        
        $data['current']      = 'penerimaan';
        $data['apps']         = $this->apps_model->get_active_only();
        $data['pagetitle']    = 'OpenSIPKD';
        $data['title']        = 'Transaksi Pembayaran';
        $data['main_content'] = '';
		
        //Grid Standard Parameter
        $data['iDisplayLength'] = (isset($_GET['iDisplayLength']) && is_numeric($_GET['iDisplayLength'])) ? $_GET['iDisplayLength'] : 15;
        $data['iDisplayStart']  = (isset($_GET['iDisplayStart']) && is_numeric($_GET['iDisplayStart'])) ? $_GET['iDisplayStart'] : 0;
        $data['iSortingCols']   = (isset($_GET['iSortingCols']) && is_numeric($_GET['iSortingCols'])) ? $_GET['iSortingCols'] : 1;
        $data['sEcho']          = (isset($_GET['sEcho']) && is_numeric($_GET['sEcho'])) ? $_GET['sEcho'] : 1;
        $data['sSearch']        = (isset($_GET['sSearch'])) ? $_GET['sSearch'] : "";
        $data['data_source']    = active_module_url() . "penerimaan/grid";
        
        $this->load->view('vpenerimaan', $data);
    }
    
	
    function grid()
    {
		$i=0;
		$responce = new stdClass();
		$query = $this->penerimaan_model->grid();
		if($query) {
			foreach($query as $row) {
				$nop_thn = $row->kd_propinsi.'.'.$row->kd_dati2.'.'.$row->kd_kecamatan.'.'.$row->kd_kelurahan.'.'.$row->kd_blok.'.'.$row->no_urut.'.'.$row->kd_jns_op.'-'.$row->thn_pajak_sppt;
				
				$responce->aaData[$i][] = $row->id;
				$responce->aaData[$i][] = $row->transno;
				$responce->aaData[$i][] = date('d-m-Y', strtotime($row->tanggal));
				$responce->aaData[$i][] = $nop_thn;
				$responce->aaData[$i][] = $row->wp_nama;
				$responce->aaData[$i][] = number_format($row->bayar, 0, ',', '.');
				$responce->aaData[$i][] = $row->no_tagihan;
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
        $this->form_validation->set_rules('perolehan_id', 'Jenis Perolehan', 'required|numeric');
        $this->form_validation->set_rules('tanggal', 'Tgl. Transaksi', 'required');
        $this->form_validation->set_rules('transno', 'No. Transaksi', 'required');
        $this->form_validation->set_rules('wp_identitas', 'WP Identitas', 'required');
        $this->form_validation->set_rules('wp_nama', 'WP Nama', 'required');
        $this->form_validation->set_rules('wp_alamat', 'WP Alamat', 'required');
		
		$this->form_validation->set_rules('kd_propinsi','Kode Propinsi SPPT','required|trim');
		$this->form_validation->set_rules('kd_dati2','Kode Dati2 SPPT','required|trim');
		$this->form_validation->set_rules('kd_kecamatan','Kode Kecamatan SPPT','required|trim');
		$this->form_validation->set_rules('kd_kelurahan','Kode Kelurahan SPPT','required|trim');
		$this->form_validation->set_rules('kd_blok','Kd Blok SPPT','required|trim');
		$this->form_validation->set_rules('no_urut','No Urut SPPT','required|trim');
		$this->form_validation->set_rules('kd_jns_op','Kode Jenis SPPT','required|trim');
		$this->form_validation->set_rules('thn_pajak_sppt','Tahun Pajak  SPPT','required|trim');
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
		
        return $data;
    }
    
    public function add()
    {
        if (!$this->module_auth->create) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_create);
            redirect(active_module_url($this->uri->segment(2)));
        }
        
        $this->fvalidation();
        if ($this->form_validation->run() == TRUE) {
            $input_post = $this->fpost();
			$nop = $input_post['kd_propinsi'].$input_post['kd_dati2'].$input_post['kd_kecamatan'].$input_post['kd_kelurahan'].$input_post['kd_blok'].$input_post['no_urut'].$input_post['kd_jns_op'];
			
			$no_tagihan = '';
			if (!empty($input_post['no_sspd'])) {
				$no_tagihan = $input_post['tahun'].'.'.$input_post['kode'].'.'.str_pad((int)$input_post['no_sspd'], 6, '0', STR_PAD_LEFT);
			}
				
            $post_data  = array(
				// 'id' => empty($input_post['id']) ? NULL : $input_post['id'],
				// 'bphtbjeniskd' => empty($input_post['bphtbjeniskd']) ? NULL : $input_post['bphtbjeniskd'],
				'nop' => $nop,
				'bphtbjeniskd' => empty($input_post['perolehan_id']) ? NULL : $input_post['perolehan_id'],
				'no_tagihan' => $no_tagihan,
				
				'tahun' => date('Y', strtotime($input_post['tanggal'])),
				'tanggal' => empty($input_post['tanggal']) ? NULL : date('Y-m-d', strtotime($input_post['tanggal'])),
				'jam' => date('h:i:s', time()),
				'seq' => 0,
				'transno' => empty($input_post['transno']) ? NULL : $input_post['transno'],
				// 'cabang' => empty($input_post['cabang']) ? NULL : $input_post['cabang'],
				// 'users' => empty($input_post['users']) ? NULL : $input_post['users'],
				'bankid' => 0,
				'txs' => '',
				'sspd_id' => empty($input_post['sspd_id']) ? NULL : $input_post['sspd_id'],
				
				// 'kd_propinsi' => $input_post['kd_propinsi'],
				// 'kd_dati2' => $input_post['kd_dati2'],
				// 'kd_kecamatan' => $input_post['kd_kecamatan'],
				// 'kd_kelurahan' => $input_post['kd_kelurahan'],
				// 'kd_blok' => $input_post['kd_blok'],
				// 'no_urut' => $input_post['no_urut'],
				// 'kd_jns_op' => $input_post['kd_jns_op'],
				'thn_pajak_sppt' => $input_post['thn_pajak_sppt'],
				
				'wp_nama' => empty($input_post['wp_nama']) ? NULL : $input_post['wp_nama'],
				'wp_alamat' => empty($input_post['wp_alamat']) ? NULL : $input_post['wp_alamat'],
				'wp_blok_kav' => empty($input_post['wp_blok_kav']) ? NULL : $input_post['wp_blok_kav'],
				'wp_rt' => empty($input_post['wp_rt']) ? NULL : $input_post['wp_rt'],
				'wp_rw' => empty($input_post['wp_rw']) ? NULL : $input_post['wp_rw'],
				'wp_kelurahan' => empty($input_post['wp_kelurahan']) ? NULL : $input_post['wp_kelurahan'],
				'wp_kecamatan' => empty($input_post['wp_kecamatan']) ? NULL : $input_post['wp_kecamatan'],
				'wp_kota' => empty($input_post['wp_kota']) ? NULL : $input_post['wp_kota'],
				'wp_provinsi' => empty($input_post['wp_provinsi']) ? NULL : $input_post['wp_provinsi'],
				'wp_kdpos' => empty($input_post['wp_kdpos']) ? NULL : $input_post['wp_kdpos'],
				'wp_identitas' => empty($input_post['wp_identitas']) ? NULL : $input_post['wp_identitas'],
				'wp_identitaskd' => empty($input_post['wp_identitaskd']) ? NULL : $input_post['wp_identitaskd'],
				'wp_npwp' => empty($input_post['wp_npwp']) ? NULL : $input_post['wp_npwp'],
				'notaris' => empty($input_post['notaris']) ? NULL : $input_post['notaris'],
				'bumi_luas' => empty($input_post['bumi_luas']) ? 0 : $input_post['bumi_luas'],
				'bumi_njop' => empty($input_post['bumi_njop']) ? 0 : $input_post['bumi_njop'],
				'bng_luas' => empty($input_post['bng_luas']) ? 0 : $input_post['bng_luas'],
				'bng_njop' => empty($input_post['bng_njop']) ? 0 : $input_post['bng_njop'],
				'npop' => empty($input_post['npop']) ? 0 : $input_post['npop'],
				'bayar' => empty($input_post['bayar']) ? 0 : $input_post['bayar'],
				'denda' => empty($input_post['denda']) ? 0 : $input_post['denda'],
				'catatan' => empty($input_post['catatan']) ? NULL : $input_post['catatan'],
				// 'kd_kanwil' => empty($input_post['kd_kanwil']) ? NULL : $input_post['kd_kanwil'],
				// 'kd_kantor' => empty($input_post['kd_kantor']) ? NULL : $input_post['kd_kantor'],
				// 'kd_bank_tunggal' => empty($input_post['kd_bank_tunggal']) ? NULL : $input_post['kd_bank_tunggal'],
				// 'kd_bank_persepsi' => empty($input_post['kd_bank_persepsi']) ? NULL : $input_post['kd_bank_persepsi'],
				'is_validated' => 0,
            );
            
            $this->penerimaan_model->save($post_data);
			
			
			//update stat pmb
			$sspd_id = $input_post['sspd_id'];
			if (!empty($sspd_id)) {
				$upd_sspd = array(
					'status_pembayaran' => 1,
					'bphtb_sudah_dibayarkan' => $input_post['bayar'],
					'denda' => $input_post['denda'],
				);
				$this->db->update('bphtb_sspd', $upd_sspd, array('id'=>$sspd_id));
			}
			
            $this->session->set_flashdata('msg_success', 'Data telah disimpan');
            redirect(active_module_url($this->uri->segment(2)));
        }
        
        $data['current'] = 'penerimaan';
        $data['apps']    = $this->apps_model->get_active_only();
        $data['faction'] = active_module_url('penerimaan/add');
        
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
		
		$data['dt']['tgl_transaksi']  = date('d-m-Y');
		$data['dt']['bpn_tgl_terima'] = date('d-m-Y');
		
		$this->load->view('form_contents/vpenerimaan_form', $data);
    }
    
    public function edit()
    {
        if (!$this->module_auth->update) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_update);
            redirect(active_module_url($this->uri->segment(2)));
        }
        $data['current'] = 'penerimaan';
        $data['apps']    = $this->apps_model->get_active_only();
        $data['faction'] = active_module_url('penerimaan/update');
        
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
			
			/* 
			if ($sspd = $this->sspd_model->get($get->sspd_id)) {
				$data['dt']['tahun']   = $sspd->tahun;
				$data['dt']['kode']    = str_pad($sspd->kode, 2, "0", STR_PAD_LEFT);
				$data['dt']['no_sspd'] = str_pad($sspd->no_sspd, 6, "0", STR_PAD_LEFT);
			}
            */
			
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
			$data['dt']['bumi_luas'] = empty($get->bumi_luas) ? 0 : $get->bumi_luas;
			$data['dt']['bumi_njop'] = empty($get->bumi_njop) ? 0 : $get->bumi_njop;
			$data['dt']['bng_luas'] = empty($get->bng_luas) ? 0 : $get->bng_luas;
			$data['dt']['bng_njop'] = empty($get->bng_njop) ? 0 : $get->bng_njop;
			$data['dt']['npop'] = empty($get->npop) ? 0 : $get->npop;
			$data['dt']['bayar'] = empty($get->bayar) ? 0 : $get->bayar;
			$data['dt']['no_tagihan'] = empty($get->no_tagihan) ? NULL : $get->no_tagihan;
			$data['dt']['denda'] = empty($get->denda) ? 0 : $get->denda;
			$data['dt']['catatan'] = empty($get->catatan) ? NULL : $get->catatan;
			$data['dt']['kd_kanwil'] = empty($get->kd_kanwil) ? NULL : $get->kd_kanwil;
			$data['dt']['kd_kantor'] = empty($get->kd_kantor) ? NULL : $get->kd_kantor;
			$data['dt']['kd_bank_tunggal'] = empty($get->kd_bank_tunggal) ? NULL : $get->kd_bank_tunggal;
			$data['dt']['kd_bank_persepsi'] = empty($get->kd_bank_persepsi) ? NULL : $get->kd_bank_persepsi;
			$data['dt']['is_validated'] = empty($get->is_validated) ? 0 : $get->is_validated;
			
			// $data['dt']['bphtbjeniskd'] = empty($get->bphtbjeniskd) ? NULL : $get->bphtbjeniskd;
			$data['dt']['perolehan_id'] = $get->bphtbjeniskd;

			
            $this->load->view('form_contents/vpenerimaan_form', $data);
        } else {
            show_404();
        }
    }
    
    public function update()
    {
        if (!$this->module_auth->create) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_create);
            redirect(active_module_url($this->uri->segment(2)));
        }
        
        $this->fvalidation();
        if ($this->form_validation->run() == TRUE) {
            $input_post = $this->fpost();
			$nop = $input_post['kd_propinsi'].$input_post['kd_dati2'].$input_post['kd_kecamatan'].$input_post['kd_kelurahan'].$input_post['kd_blok'].$input_post['no_urut'].$input_post['kd_jns_op'];
			$no_tagihan = '';
			if (!empty($input_post['no_sspd'])) {
				$no_tagihan = $input_post['tahun'].'.'.$input_post['kode'].'.'.str_pad((int)$input_post['no_sspd'], 6, '0', STR_PAD_LEFT);
			}
			
            $post_data  = array(
				// 'id' => empty($input_post['id']) ? NULL : $input_post['id'],
				// 'bphtbjeniskd' => empty($input_post['bphtbjeniskd']) ? NULL : $input_post['bphtbjeniskd'],
				'nop' => $nop,
				'bphtbjeniskd' => empty($input_post['perolehan_id']) ? NULL : $input_post['perolehan_id'],
				'no_tagihan' => $no_tagihan,
				
				'tahun' => date('Y', strtotime($input_post['tanggal'])),
				'tanggal' => empty($input_post['tanggal']) ? NULL : date('Y-m-d', strtotime($input_post['tanggal'])),
				'jam' => date('h:i:s', time()),
				'seq' => 0,
				'transno' => empty($input_post['transno']) ? NULL : $input_post['transno'],
				// 'cabang' => empty($input_post['cabang']) ? NULL : $input_post['cabang'],
				// 'users' => empty($input_post['users']) ? NULL : $input_post['users'],
				'bankid' => 0,
				'txs' => '',
				'sspd_id' => empty($input_post['sspd_id']) ? NULL : $input_post['sspd_id'],
				
				// 'kd_propinsi' => $input_post['kd_propinsi'],
				// 'kd_dati2' => $input_post['kd_dati2'],
				// 'kd_kecamatan' => $input_post['kd_kecamatan'],
				// 'kd_kelurahan' => $input_post['kd_kelurahan'],
				// 'kd_blok' => $input_post['kd_blok'],
				// 'no_urut' => $input_post['no_urut'],
				// 'kd_jns_op' => $input_post['kd_jns_op'],
				'thn_pajak_sppt' => $input_post['thn_pajak_sppt'],
				
				'wp_nama' => empty($input_post['wp_nama']) ? NULL : $input_post['wp_nama'],
				'wp_alamat' => empty($input_post['wp_alamat']) ? NULL : $input_post['wp_alamat'],
				'wp_blok_kav' => empty($input_post['wp_blok_kav']) ? NULL : $input_post['wp_blok_kav'],
				'wp_rt' => empty($input_post['wp_rt']) ? NULL : $input_post['wp_rt'],
				'wp_rw' => empty($input_post['wp_rw']) ? NULL : $input_post['wp_rw'],
				'wp_kelurahan' => empty($input_post['wp_kelurahan']) ? NULL : $input_post['wp_kelurahan'],
				'wp_kecamatan' => empty($input_post['wp_kecamatan']) ? NULL : $input_post['wp_kecamatan'],
				'wp_kota' => empty($input_post['wp_kota']) ? NULL : $input_post['wp_kota'],
				'wp_provinsi' => empty($input_post['wp_provinsi']) ? NULL : $input_post['wp_provinsi'],
				'wp_kdpos' => empty($input_post['wp_kdpos']) ? NULL : $input_post['wp_kdpos'],
				'wp_identitas' => empty($input_post['wp_identitas']) ? NULL : $input_post['wp_identitas'],
				'wp_identitaskd' => empty($input_post['wp_identitaskd']) ? NULL : $input_post['wp_identitaskd'],
				'wp_npwp' => empty($input_post['wp_npwp']) ? NULL : $input_post['wp_npwp'],
				'notaris' => empty($input_post['notaris']) ? NULL : $input_post['notaris'],
				'bumi_luas' => empty($input_post['bumi_luas']) ? 0 : $input_post['bumi_luas'],
				'bumi_njop' => empty($input_post['bumi_njop']) ? 0 : $input_post['bumi_njop'],
				'bng_luas' => empty($input_post['bng_luas']) ? 0 : $input_post['bng_luas'],
				'bng_njop' => empty($input_post['bng_njop']) ? 0 : $input_post['bng_njop'],
				'npop' => empty($input_post['npop']) ? 0 : $input_post['npop'],
				'bayar' => empty($input_post['bayar']) ? 0 : $input_post['bayar'],
				'denda' => empty($input_post['denda']) ? 0 : $input_post['denda'],
				'catatan' => empty($input_post['catatan']) ? NULL : $input_post['catatan'],
				// 'kd_kanwil' => empty($input_post['kd_kanwil']) ? NULL : $input_post['kd_kanwil'],
				// 'kd_kantor' => empty($input_post['kd_kantor']) ? NULL : $input_post['kd_kantor'],
				// 'kd_bank_tunggal' => empty($input_post['kd_bank_tunggal']) ? NULL : $input_post['kd_bank_tunggal'],
				// 'kd_bank_persepsi' => empty($input_post['kd_bank_persepsi']) ? NULL : $input_post['kd_bank_persepsi'],
				// 'is_validated' => empty($input_post['is_validated']) ? NULL : $input_post['is_validated'],
            );
            
            $this->penerimaan_model->update($input_post['id'], $post_data);
			
			//update stat pmb
			$sspd_id = $input_post['sspd_id'];
			if (!empty($sspd_id)) {
				$upd_sspd = array(
					'status_pembayaran' => 1,
					'bphtb_sudah_dibayarkan' => $input_post['bayar'],
					'denda' => $input_post['denda'],
				);
				$this->db->update('bphtb_sspd', $upd_sspd, array('id'=>$sspd_id));
			}
			
            $this->session->set_flashdata('msg_success', 'Data telah disimpan');
            redirect(active_module_url($this->uri->segment(2)));
        }
        
        $data['current'] = 'penerimaan';
        $data['apps']    = $this->apps_model->get_active_only();
        $data['faction'] = active_module_url('penerimaan/update');
        
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
        
        $id = $this->input->post('id');
        
		$data['dt']['tgl_transaksi']  = date('d-m-Y');
		$data['dt']['bpn_tgl_terima'] = date('d-m-Y');
		
        $this->load->view('form_contents/vpenerimaan_form', $data);
    }
    
	public function delete() {
		if(!$this->module_auth->delete) {
			$this->session->set_flashdata('msg_warning', $this->module_auth->msg_delete);
			redirect(active_module_url($this->uri->segment(2)));
		}
		
		$id = $this->uri->segment(4);
		if($id && $this->penerimaan_model->get($id)) {
			$this->penerimaan_model->delete($id);
			$this->session->set_flashdata('msg_success', 'Data telah dihapus');
			redirect(active_module_url($this->uri->segment(2)));
		} else {
			show_404();
		}
	}
	
	//////// --------
	public function get_sspd() {
		$thn    = (int) $this->uri->segment(4);
		$kd     = (int) $this->uri->segment(5);
		$sspdno = (int) $this->uri->segment(6);
		
		$sql = "SELECT s.*, p.nama notaris FROM bphtb_sspd s
			LEFT JOIN bphtb_ppat p ON p.id=s.ppat_id
			WHERE s.tahun='{$thn}' AND s.kode='{$kd}' AND s.no_sspd='{$sspdno}'";
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
