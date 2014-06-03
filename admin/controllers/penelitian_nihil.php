<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Penelitian_nihil extends CI_Controller
{
    private $my_listNumeric_Field = array('npop', 'npoptkp', 'tarif', 'terhutang', 'tarif_pengurang', 'pengurang', 'bphtb_sudah_dibayarkan', 'denda', 'bagian', 'pembagi', 'bphtb_harus_dibayarkan', 'bumi_luas', 'bumi_njop', 'bng_luas', 'bng_njop', 'njop');
    
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
        
        $module = 'peneliti';
        $this->load->library('module_auth', array(
            'module' => $module
        ));
        
        $this->load->model(array(
            'apps_model'
        ));
        $this->load->model(array(
            'bphtb_self_model',
            'bank_model',
			'sspd_model'
            //'peneliti_model',
        ));
		
        $this->{'fields'} = $this->bphtb_self_model->fields_info('bphtb_validasi');
        $this->info       = ($this->fields) ? TRUE : FALSE;
    }
    
    public function index()
    {
        if (!$this->module_auth->read) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_read);
            redirect(active_module_url(''));
        }
        
        $data['current']      = 'penelitian';
        $data['apps']         = $this->apps_model->get_active_only();
		
        //FORM PARAMETER
		$proses_id = isset($_GET['proses_id']) ? $_GET['proses_id'] : 0;
		$options = array(
			'0' => 'BELUM PROSES',
			'1' => 'SUDAH PROSES',
		);
		$js = 'id="proses_id" class="input-medium"';
		$select = form_dropdown('proses_id', $options, $proses_id, $js);
		$select = preg_replace("/[\r\n]+/", "", $select);
		$data['select_proses'] = $select;
		
		
        //GRID STANDARD PARAMETER
        $data['iDisplayLength'] = (isset($_GET['iDisplayLength']) && is_numeric($_GET['iDisplayLength'])) ? $_GET['iDisplayLength'] : 15;
        $data['iDisplayStart']  = (isset($_GET['iDisplayStart']) && is_numeric($_GET['iDisplayStart'])) ? $_GET['iDisplayStart'] : 0;
        $data['iSortingCols']   = (isset($_GET['iSortingCols']) && is_numeric($_GET['iSortingCols'])) ? $_GET['iSortingCols'] : 1;
        $data['sEcho']          = (isset($_GET['sEcho']) && is_numeric($_GET['sEcho'])) ? $_GET['sEcho'] : 1;
        $data['sSearch']        = (isset($_GET['sSearch'])) ? $_GET['sSearch'] : "";
		
		$data['data_source']    = active_module_url() . "penelitian_nihil/grid?proses_id=$proses_id";
        
        $this->load->view('vpenelitian', $data);
    }
    
    function grid()
    {
        ob_start("ob_gzhandler");
		
		$proses_id = isset($_GET['proses_id']) ? $_GET['proses_id'] : 0;
		
        $path_to_root = active_module_url();
        
        $aColumns       = array('id', 'nomor', 'nop2', 'tgl_transaksi', 'wp_nama', 'bphtb_harus_dibayarkan', 'ppatnm');
        $sIndexColumn   = "no_sspd";
        $iDisplayLength = 9; //(isset($_GET['iDisplayLength']) && is_numeric($_GET['iDisplayLength'])) ? $_GET['iDisplayLength'] : 15;
        $iDisplayStart  = (isset($_GET['iDisplayStart']) && is_numeric($_GET['iDisplayStart'])) ? $_GET['iDisplayStart'] : 0;
        $iSortCol_0     = (isset($_GET['iSortCol_0']) && is_numeric($_GET['iSortCol_0'])) ? $_GET['iSortCol_0'] : 0;
        $iSortingCols   = (isset($_GET['iSortingCols']) && is_numeric($_GET['iSortingCols'])) ? $_GET['iSortingCols'] : 1;
        $sSortDir_0     = (isset($_GET['sSortDir_0'])) ? $_GET['sSortDir_0'] : "asc";
        $sEcho          = (isset($_GET['sEcho']) && is_numeric($_GET['sEcho'])) ? $_GET['sEcho'] : 1;
        $sSearch        = (isset($_GET['sSearch'])) ? $_GET['sSearch'] : "";
		
		/* Limit */
        $sLimit = "";
        if (isset($iDisplayLength) && $iDisplayStart != '-1') {
            $sLimit = "LIMIT $iDisplayLength OFFSET $iDisplayStart";
        }

		/* Ordering */
        $sOrder = "";
        if (isset($_GET['iSortCol_0'])) {
            $sOrder = "ORDER BY ";
            for ($i = 0; $i < intval($_GET['iSortingCols']); $i++) {
                if ($_GET['bSortable_' . intval($_GET['iSortCol_' . $i])] == "true") {
                    $sOrder .= $aColumns[intval($_GET['iSortCol_' . $i])] . ' ' . $_GET['sSortDir_' . $i] . ", ";
                }
            }
            
            $sOrder = substr_replace($sOrder, "", -2);
            
            if ($sOrder == "ORDER BY ") {
                $sOrder = "";
            }
        }
        
		/* Filtering */
        $sWhere = "WHERE (v.id is not null)::int = $proses_id 
            AND b.kd_propinsi = '" . KD_PROPINSI . "' 
            AND b.kd_dati2 = '" . KD_DATI2 . "'
			AND b.bphtb_harus_dibayarkan < 1";
        
        $search = '';
        if ($sSearch) $search .= " AND (b.wp_nama ilike '%$sSearch%' OR nop ilike '%$sSearch%')";
        
        /* Total Data */
        $sql = "SELECT  COUNT(*) c FROM bphtb_sspd b
			LEFT JOIN bphtb_validasi v ON b.id=v.sspd_id ";
        if ($sWhere) $sql .= " $sWhere";
		
        $row       = $this->db->query($sql)->row();
        $iTotal    = $row->c;
        $iFiltered = $iTotal;
        
        if ($search) {
            $sql_query_r = "SELECT  COUNT(*) c FROM bphtb_sspd b
			LEFT JOIN bphtb_validasi v ON b.id=v.sspd_id ";
            if ($sWhere) $sql_query_r .= " $sWhere";
            if ($search) $sql_query_r .= " $search";
            
            $row = $this->db->query($sql_query_r)->row();
            $iFiltered = $row->c;
        }
        
        /*
         * Output
         */
        $sql_query_r = "SELECT tahun||'.'||lpad(b.kode::text,2,'0')||'.'||lpad(b.no_sspd::text,6,'0') nomor, b.*, 
			b.kd_propinsi||'.'||b.kd_dati2||'.'||b.kd_kecamatan||'.'||b.kd_kelurahan||'.'||b.kd_blok||'.'||b.no_urut||'.'||b.kd_jns_op nop2,
			p.nama ppatnm FROM bphtb_sspd b
			LEFT JOIN bphtb_validasi v ON b.id=v.sspd_id
			LEFT JOIN bphtb_ppat p ON p.id=v.ppat_id";
        if ($sWhere) $sql_query_r .= " $sWhere";
        if ($search) $sql_query_r .= " $search";
        if ($sOrder) $sql_query_r .= " $sOrder";
        if ($sLimit) $sql_query_r .= " $sLimit";
        
        $qry = $this->db->query($sql_query_r);
        
        $output = array(
            "sEcho" => $sEcho,
            "iDisplayLength" => $iDisplayLength,
            "iTotalRecords" => $iTotal,
            "iTotalDisplayRecords" => $iFiltered,
            "SQL Query" => $sql_query_r,
            "aaData" => array()
        );
        
        foreach ($qry->result() as $aRow) {
            $row = array();
            for ($i = 0; $i < count($aColumns); $i++) {
                if ($i == 3)
                    $row[] = (strtotime($aRow->tgl_transaksi)) ? (string) date('d/m/Y', strtotime($aRow->tgl_transaksi)) : '';
                elseif ($i == 5)
                    $row[] = number_format($aRow->$aColumns[$i], 0, ',', '.');
                else
                    $row[] = $aRow->$aColumns[$i];
            }
            $output['aaData'][] = $row;
        }
        
        echo json_encode($output);
    }
    
    // --- new ---
    private function fvalidation()
    {
        //bank validation
        $this->form_validation->set_error_delimiters('<span>', '</span>');
        $this->form_validation->set_rules('id', 'id', 'required|numeric');
        $this->form_validation->set_rules('perolehan_id', 'Jenis Perolehan', 'required|numeric');
    }
    
    private function fpost()
    {
        $data['id']             = $this->input->post('id');
        $data['tanggal']        = $this->input->post('tanggal');
        $data['jam']            = $this->input->post('jam');
        $data['seq']            = $this->input->post('seq');
        $data['transno']        = $this->input->post('transno');
        $data['cabang']         = $this->input->post('cabang');
        $data['users']          = $this->input->post('users');
        $data['bankid']         = $this->input->post('bankid');
        $data['txs']            = $this->input->post('txs');
        $data['sspd_id']        = $this->input->post('sspd_id');
        $data['nop']            = $this->input->post('nop');
        $data['tahun']          = $this->input->post('tahun');
        $data['kd_propinsi']    = $this->input->post('kd_propinsi');
        $data['kd_dati2']       = $this->input->post('kd_dati2');
        $data['kd_kecamatan']   = $this->input->post('kd_kecamatan');
        $data['kd_kelurahan']   = $this->input->post('kd_kelurahan');
        $data['kd_blok']        = $this->input->post('kd_blok');
        $data['no_urut']        = $this->input->post('no_urut');
        $data['kd_jns_op']      = $this->input->post('kd_jns_op');
        $data['thn_pajak_sppt'] = $this->input->post('thn_pajak_sppt');
        $data['wp_nama']        = $this->input->post('wp_nama');
        $data['wp_alamat']      = $this->input->post('wp_alamat');
        $data['wp_blok_kav']    = $this->input->post('wp_blok_kav');
        $data['wp_rt']          = $this->input->post('wp_rt');
        $data['wp_rw']          = $this->input->post('wp_rw');
        $data['wp_kelurahan']   = $this->input->post('wp_kelurahan');
        $data['wp_kecamatan']   = $this->input->post('wp_kecamatan');
        $data['wp_kota']        = $this->input->post('wp_kota');
        $data['wp_provinsi']    = $this->input->post('wp_provinsi');
        $data['wp_kdpos']       = $this->input->post('wp_kdpos');
        $data['wp_identitas']   = $this->input->post('wp_identitas');
        $data['wp_identitaskd'] = $this->input->post('wp_identitaskd');
        $data['wp_npwp']        = $this->input->post('wp_npwp');
        $data['notaris']        = $this->input->post('notaris');
        $data['bumi_luas']      = $this->input->post('bumi_luas');
        $data['bumi_njop']      = $this->input->post('bumi_njop');
        $data['bng_luas']       = $this->input->post('bng_luas');
        $data['bng_njop']       = $this->input->post('bng_njop');
        $data['npop']           = $this->input->post('npop');
        $data['bayar']          = $this->input->post('bayar');
        $data['denda']          = $this->input->post('denda');
        $data['bphtbjeniskd']   = $this->input->post('bphtbjeniskd');
        $data['no_tagihan']     = $this->input->post('no_tagihan');
        $data['catatan']        = $this->input->post('catatan');
        $data['op_alamat']      = $this->input->post('op_alamat');
        $data['users2']         = $this->input->post('users2');
        $data['users3']         = $this->input->post('users3');
        
        
        $data['header_id']              = $this->input->post('header_id');
        $data['perolehan_id']           = $this->input->post('perolehan_id');
        $data['ppat_id']                = $this->input->post('ppat_id');
        $data['tgl_transaksi']          = $this->input->post('tgl_transaksi');
        $data['bpn_tgl_terima']         = $this->input->post('bpn_tgl_terima');
        $data['njop']                   = $this->input->post('njop');
        $data['no_sertifikat']          = $this->input->post('no_sertifikat');
        $data['tarif_pengurang']        = $this->input->post('tarif_pengurang');
        $data['pengurang']              = $this->input->post('pengurang');
        $data['bagian']                 = $this->input->post('bagian');
        $data['pembagi']                = $this->input->post('pembagi');
        $data['bphtb_harus_dibayarkan'] = $this->input->post('bphtb_harus_dibayarkan');
        $data['status_pembayaran']      = $this->input->post('status_pembayaran');
        $data['op_blok_kav']            = $this->input->post('op_blok_kav');
        $data['op_rt']                  = $this->input->post('op_rt');
        $data['op_rw']                  = $this->input->post('op_rw');
        $data['npoptkp']                = $this->input->post('npoptkp');
        $data['tarif']                  = $this->input->post('tarif');
        $data['terhutang']              = $this->input->post('terhutang');

        $data['bphtb_sudah_dibayarkan'] = $this->input->post('bphtb_sudah_dibayarkan');
        
        return $data;
    }
    
    public function register()
    {
        if (!$this->module_auth->update) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_update);
            redirect(active_module_url($this->uri->segment(2)));
        }
        $data['current'] = 'penelitian';
        $data['apps']    = $this->apps_model->get_active_only();
        $data['faction'] = active_module_url('penelitian_nihil/do_register');
        
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
        
        if ($id && $sspd = $this->sspd_model->get($id)) {
            $data['dt'] = array();
            $my_fields  = $this->bank_model->get_entry_fields();
            
            // $sspd_id    = $sspd->sspd_id;
			// if ($sspd = $this->sspd_model->get($bank->sspd_id)) {
				$data['dt']['tahun']   = $sspd->tahun;
				$data['dt']['kode']    = str_pad($sspd->kode, 2, "0", STR_PAD_LEFT);
				$data['dt']['no_sspd'] = str_pad($sspd->no_sspd, 6, "0", STR_PAD_LEFT);
                $get = $sspd;
			// } else {
				// $data['dt']['tahun']   = '';
				// $data['dt']['kode']    = '';
				// $data['dt']['no_sspd'] = '';
                // $get = $bank;
			// }
            
            $data['dt']['id'] = $id;
            // $data['dt']['bphtb_sudah_dibayarkan'] = $bank->bayar;
			// $data['dt']['transno'] = $bank->transno;
            
            //get
            $data['dt']['sspd_id']        = $get->id;
            $data['dt']['tgl_transaksi']  = date('d-m-Y');
            $data['dt']['bpn_tgl_terima'] = date('d-m-Y');
            
            $data['dt']['tanggal']                = empty($get->tanggal) ? NULL : date('d-m-Y', strtotime($get->tanggal));
            $data['dt']['txs']                    = empty($get->txs) ? NULL : $get->txs;
            $data['dt']['no_sequence']            = empty($get->no_sequence) ? NULL : $get->no_sequence;
            $data['dt']['nop']                    = empty($get->nop) ? NULL : $get->nop;
            // $data['dt']['tahun']                  = empty($get->tahun) ? NULL : $get->tahun;
            $data['dt']['wpnama']                 = empty($get->wpnama) ? NULL : $get->wpnama;
            $data['dt']['wpkelurahan']            = empty($get->wpkelurahan) ? NULL : $get->wpkelurahan;
            $data['dt']['wpkecamatan']            = empty($get->wpkecamatan) ? NULL : $get->wpkecamatan;
            $data['dt']['bayar']                  = empty($get->bayar) ? NULL : $get->bayar;
            $data['dt']['denda']                  = empty($get->denda) ? NULL : $get->denda;
            $data['dt']['perolehan_id']           = empty($get->perolehan_id) ? 1 : $get->perolehan_id;
            $data['dt']['notaris']                = empty($get->notaris) ? NULL : $get->notaris;
            $data['dt']['cabang']                 = empty($get->cabang) ? NULL : $get->cabang;
            $data['dt']['users']                  = empty($get->users) ? NULL : $get->users;
            $data['dt']['bank_id']                = empty($get->bank_id) ? NULL : $get->bank_id;
            $data['dt']['nik']                    = empty($get->nik) ? NULL : $get->nik;
            $data['dt']['berkas_in_id']           = empty($get->berkas_in_id) ? NULL : $get->berkas_in_id;
            // $data['dt']['sspd_id']                = empty($get->sspd_id) ? NULL : $get->sspd_id;
            $data['dt']['ppat_id']                = empty($get->ppat_id) ? NULL : $get->ppat_id;
            $data['dt']['wp_nama']                = empty($get->wp_nama) ? NULL : $get->wp_nama;
            $data['dt']['wp_npwp']                = empty($get->wp_npwp) ? NULL : $get->wp_npwp;
            $data['dt']['wp_alamat']              = empty($get->wp_alamat) ? NULL : $get->wp_alamat;
            $data['dt']['wp_blok_kav']            = empty($get->wp_blok_kav) ? NULL : $get->wp_blok_kav;
            $data['dt']['wp_kelurahan']           = empty($get->wp_kelurahan) ? NULL : $get->wp_kelurahan;
            $data['dt']['wp_rt']                  = empty($get->wp_rt) ? NULL : $get->wp_rt;
            $data['dt']['wp_rw']                  = empty($get->wp_rw) ? NULL : $get->wp_rw;
            $data['dt']['wp_kecamatan']           = empty($get->wp_kecamatan) ? NULL : $get->wp_kecamatan;
            $data['dt']['wp_kota']                = empty($get->wp_kota) ? NULL : $get->wp_kota;
            $data['dt']['wp_provinsi']            = empty($get->wp_provinsi) ? NULL : $get->wp_provinsi;
            $data['dt']['wp_kdpos']               = empty($get->wp_kdpos) ? NULL : $get->wp_kdpos;
            $data['dt']['wp_identitas']           = empty($get->wp_identitas) ? NULL : $get->wp_identitas;
            $data['dt']['wp_identitaskd']         = empty($get->wp_identitaskd) ? NULL : $get->wp_identitaskd;
            $data['dt']['kd_propinsi']            = !isset($get->kd_propinsi) ? NULL : $get->kd_propinsi;
            $data['dt']['kd_dati2']               = !isset($get->kd_dati2) ? NULL : $get->kd_dati2;
            $data['dt']['kd_kecamatan']           = !isset($get->kd_kecamatan) ? NULL : $get->kd_kecamatan;
            $data['dt']['kd_kelurahan']           = !isset($get->kd_kelurahan) ? NULL : $get->kd_kelurahan;
            $data['dt']['kd_blok']                = !isset($get->kd_blok) ? NULL : $get->kd_blok;
            $data['dt']['no_urut']                = !isset($get->no_urut) ? NULL : $get->no_urut;
            $data['dt']['kd_jns_op']              = !isset($get->kd_jns_op) ? NULL : $get->kd_jns_op;
            $data['dt']['thn_pajak_sppt']         = !isset($get->thn_pajak_sppt) ? NULL : $get->thn_pajak_sppt;
            $data['dt']['no_sertifikat']          = empty($get->no_sertifikat) ? NULL : $get->no_sertifikat;
            $data['dt']['npop']                   = empty($get->npop) ? NULL : $get->npop;
            $data['dt']['npoptkp']                = empty($get->npoptkp) ? NULL : $get->npoptkp;
            $data['dt']['tarif']                  = empty($get->tarif) ? 5 : $get->tarif;
            $data['dt']['terhutang']              = empty($get->terhutang) ? NULL : $get->terhutang;
            $data['dt']['bagian']                 = empty($get->bagian) ? NULL : $get->bagian;
            $data['dt']['pembagi']                = empty($get->pembagi) ? NULL : $get->pembagi;
            $data['dt']['tarif_pengurang']        = empty($get->tarif_pengurang) ? NULL : $get->tarif_pengurang;
            $data['dt']['pengurang']              = empty($get->pengurang) ? NULL : $get->pengurang;
            $data['dt']['bphtb_sudah_dibayarkan'] = empty($get->bphtb_sudah_dibayarkan) ? NULL : $get->bphtb_sudah_dibayarkan;
            $data['dt']['restitusi']              = empty($get->restitusi) ? NULL : $get->restitusi;
            $data['dt']['bphtb_harus_dibayarkan'] = empty($get->bphtb_harus_dibayarkan) ? NULL : $get->bphtb_harus_dibayarkan;
            $data['dt']['status_pembayaran']      = empty($get->status_pembayaran) ? NULL : $get->status_pembayaran;
            $data['dt']['dasar_id']               = empty($get->dasar_id) ? NULL : $get->dasar_id;
            $data['dt']['create_uid']             = empty($get->create_uid) ? NULL : $get->create_uid;
            $data['dt']['update_uid']             = empty($get->update_uid) ? NULL : $get->update_uid;
            $data['dt']['created']                = empty($get->created) ? NULL : date('d-m-Y', strtotime($get->created));
            $data['dt']['updated']                = empty($get->updated) ? NULL : date('d-m-Y', strtotime($get->updated));
            $data['dt']['header_id']              = empty($get->header_id) ? NULL : $get->header_id;
            $data['dt']['bpn_tgl_selesai']        = empty($get->bpn_tgl_selesai) ? NULL : date('d-m-Y', strtotime($get->bpn_tgl_selesai));
            $data['dt']['njop']                   = empty($get->njop) ? NULL : $get->njop;
			
			$data['dt']['op_alamat']              = empty($get->op_alamat) ? NULL : $get->op_alamat;
            $data['dt']['op_blok_kav']            = empty($get->op_blok_kav) ? NULL : $get->op_blok_kav;
            $data['dt']['op_rt']                  = empty($get->op_rt) ? NULL : $get->op_rt;
            $data['dt']['op_rw']                  = empty($get->op_rw) ? NULL : $get->op_rw;
			
			$data['dt']['bumi_luas']              = empty($get->bumi_luas) ? NULL : $get->bumi_luas;
            $data['dt']['bumi_njop']              = empty($get->bumi_njop) ? NULL : $get->bumi_njop;
            $data['dt']['bng_luas']               = empty($get->bng_luas) ? NULL : $get->bng_luas;
            $data['dt']['bng_njop']               = empty($get->bng_njop) ? NULL : $get->bng_njop;
            //-----
			
			/*
			// ngambil dari ppb.dat_objek_pajak
			$get1 = $this->bphtb_self_model->get_data_op_from_dop($get->kd_propinsi, $get->kd_dati2, $get->kd_kecamatan, $get->kd_kelurahan, $get->kd_blok, $get->no_urut, $get->kd_jns_op);
            $data['dt']['op_alamat']              = empty($get1->op_alamat) ? NULL : $get1->op_alamat;
            $data['dt']['op_blok_kav']            = empty($get1->op_blok_kav) ? NULL : $get1->op_blok_kav;
            $data['dt']['op_rt']                  = empty($get1->op_rt) ? NULL : $get1->op_rt;
            $data['dt']['op_rw']                  = empty($get1->op_rw) ? NULL : $get1->op_rw;
			
            // ngambil dari sppt
			$get2 = $this->bphtb_self_model->get_data_op_from_sppt($get->kd_propinsi, $get->kd_dati2, $get->kd_kecamatan, $get->kd_kelurahan, $get->kd_blok, $get->no_urut, $get->kd_jns_op, $get->thn_pajak_sppt);
			$data['dt']['bumi_luas']              = empty($get2->bumi_luas) ? NULL : $get2->bumi_luas;
            $data['dt']['bumi_njop']              = empty($get2->bumi_njop) ? NULL : $get2->bumi_njop;
            $data['dt']['bng_luas']               = empty($get2->bng_luas) ? NULL : $get2->bng_luas;
            $data['dt']['bng_njop']               = empty($get2->bng_njop) ? NULL : $get2->bng_njop;
			*/
			
            $this->load->view('form_contents/vsspd_penelitian_form', $data);
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
            $post_data  = array(
                'berkas_in_id' => 0,
                
				'sspd_id' => empty($input_post['id']) ? NULL : $input_post['id'],
				
                'ppat_id' => empty($input_post['ppat_id']) ? NULL : $input_post['ppat_id'],
                'wp_nama' => empty($input_post['wp_nama']) ? '-' : $input_post['wp_nama'],
                'wp_npwp' => empty($input_post['wp_npwp']) ? '' : $input_post['wp_npwp'],
                'wp_alamat' => empty($input_post['wp_alamat']) ? '' : $input_post['wp_alamat'],
                'wp_blok_kav' => empty($input_post['wp_blok_kav']) ? '' : $input_post['wp_blok_kav'],
                'wp_kelurahan' => empty($input_post['wp_kelurahan']) ? '' : $input_post['wp_kelurahan'],
                'wp_rt' => empty($input_post['wp_rt']) ? '' : $input_post['wp_rt'],
                'wp_rw' => empty($input_post['wp_rw']) ? '' : $input_post['wp_rw'],
                'wp_kecamatan' => empty($input_post['wp_kecamatan']) ? '' : $input_post['wp_kecamatan'],
                'wp_kota' => empty($input_post['wp_kota']) ? '' : $input_post['wp_kota'],
                'wp_provinsi' => empty($input_post['wp_provinsi']) ? '' : $input_post['wp_provinsi'],
                'wp_kdpos' => empty($input_post['wp_kdpos']) ? '' : $input_post['wp_kdpos'],
                'wp_identitas' => empty($input_post['wp_identitas']) ? '' : $input_post['wp_identitas'],
                'wp_identitaskd' => empty($input_post['wp_identitaskd']) ? '' : $input_post['wp_identitaskd'],
                'tgl_transaksi' => empty($input_post['tgl_transaksi']) ? date('Y-m-d') : date('Y-m-d', strtotime($input_post['tgl_transaksi'])),
                'kd_propinsi' => !isset($input_post['kd_propinsi']) ? '' : $input_post['kd_propinsi'],
                'kd_dati2' => !isset($input_post['kd_dati2']) ? '' : $input_post['kd_dati2'],
                'kd_kecamatan' => !isset($input_post['kd_kecamatan']) ? '' : $input_post['kd_kecamatan'],
                'kd_kelurahan' => !isset($input_post['kd_kelurahan']) ? '' : $input_post['kd_kelurahan'],
                'kd_blok' => !isset($input_post['kd_blok']) ? '' : $input_post['kd_blok'],
                'no_urut' => !isset($input_post['no_urut']) ? '' : $input_post['no_urut'],
                'kd_jns_op' => !isset($input_post['kd_jns_op']) ? '' : $input_post['kd_jns_op'],
                'thn_pajak_sppt' => !isset($input_post['thn_pajak_sppt']) ? '' : $input_post['thn_pajak_sppt'],
                'op_alamat' => empty($input_post['op_alamat']) ? '' : $input_post['op_alamat'],
                'op_blok_kav' => empty($input_post['op_blok_kav']) ? '' : $input_post['op_blok_kav'],
                'op_rt' => empty($input_post['op_rt']) ? '' : $input_post['op_rt'],
                'op_rw' => empty($input_post['op_rw']) ? '' : $input_post['op_rw'],
                'bumi_luas' => empty($input_post['bumi_luas']) ? 0 : $input_post['bumi_luas'],
                'bumi_njop' => empty($input_post['bumi_njop']) ? 0 : $input_post['bumi_njop'],
                'bng_luas' => empty($input_post['bng_luas']) ? 0 : $input_post['bng_luas'],
                'bng_njop' => empty($input_post['bng_njop']) ? 0 : $input_post['bng_njop'],
                'no_sertifikat' => empty($input_post['no_sertifikat']) ? '' : $input_post['no_sertifikat'],
                'njop' => empty($input_post['njop']) ? 0 : $input_post['njop'],
                'perolehan_id' => empty($input_post['perolehan_id']) ? 0 : $input_post['perolehan_id'],
                'npop' => empty($input_post['npop']) ? 0 : $input_post['npop'],
                'npoptkp' => empty($input_post['npoptkp']) ? 0 : $input_post['npoptkp'],
                'tarif' => empty($input_post['tarif']) ? 0 : $input_post['tarif'],
                'terhutang' => empty($input_post['terhutang']) ? 0 : $input_post['terhutang'],
                'bagian' => empty($input_post['bagian']) ? 0 : $input_post['bagian'],
                'pembagi' => empty($input_post['pembagi']) ? 0 : $input_post['pembagi'],
                'tarif_pengurang' => empty($input_post['tarif_pengurang']) ? 0 : $input_post['tarif_pengurang'],
                'pengurang' => empty($input_post['pengurang']) ? 0 : $input_post['pengurang'],
                'bphtb_sudah_dibayarkan' => empty($input_post['bphtb_sudah_dibayarkan']) ? 0 : $input_post['bphtb_sudah_dibayarkan'],
                'denda' => empty($input_post['denda']) ? 0 : $input_post['denda'],
                'restitusi' => empty($input_post['restitusi']) ? 0 : $input_post['restitusi'],
                'bphtb_harus_dibayarkan' => empty($input_post['bphtb_harus_dibayarkan']) ? 0 : $input_post['bphtb_harus_dibayarkan'],
                'status_pembayaran' => empty($input_post['status_pembayaran']) ? 0 : $input_post['status_pembayaran'],
                'dasar_id' => empty($input_post['dasar_id']) ? 0 : $input_post['dasar_id'],
                'header_id' => empty($input_post['header_id']) ? 0 : $input_post['header_id'],
                'create_uid' => $this->session->userdata('user_id'),
                'created' => date('Y-m-d'),
				'bank_id' => empty($input_post['id']) ? NULL : $input_post['id'],
				
                //'update_uid' => empty($input_post['update_uid']) ? 0 : $input_post['update_uid'],
                //'updated' => empty($input_post['updated']) ? NULL : date('Y-m-d', strtotime($input_post['updated'])),
                //'bpn_tgl_terima' => empty($input_post['bpn_tgl_terima']) ? NULL : date('Y-m-d', strtotime($input_post['bpn_tgl_terima'])),
                //'bpn_tgl_selesai' => empty($input_post['bpn_tgl_selesai']) ? NULL : date('Y-m-d', strtotime($input_post['bpn_tgl_selesai']))
            );
            
            $dat = $this->bphtb_self_model->set_field_number_value($this->my_listNumeric_Field, $post_data);
            
            $reg_id = $this->bphtb_self_model->register($dat);
			if ($reg_id) {
				// update status validasi di bank - langsung aja sekalian dibawah ini
				// $this->bank_model->update_state($input_post['id']);
				
				// update other field dibank        
				$upd_data_bank = array (
					'nop' => $input_post['kd_propinsi'].$input_post['kd_dati2'].$input_post['kd_kecamatan'].$input_post['kd_kelurahan'].$input_post['kd_blok'].$input_post['no_urut'].$input_post['kd_jns_op'],
					'is_validated' => 1,
				);
				$this->bank_model->update($input_post['id'], $upd_data_bank);
				
				// proses sk - dipindahin sendiri aja
				/*
				if ((float)$dat['bphtb_harus_dibayarkan'] <> 0) {
					$kode = (float)$dat['bphtb_harus_dibayarkan'] > 0 ? 1 : 2; // 1=KB 2=LB
					$no_urut = $this->bphtb_self_model->get_last_no_urut_sk();
					
					$sk_data = array(
						'validasi_id' => $reg_id,
						'kode' => $kode,
						'no_urut' =>$no_urut,
						'tahun' => date('Y'),
						
						'create_uid' => $this->session->userdata('user_id'),
						'created' => date('Y-m-d'),
					);
					$this->bphtb_self_model->register_sk($sk_data);
				}
				*/
			}
			
            $this->session->set_flashdata('msg_success', 'Data telah disimpan');
            redirect(active_module_url($this->uri->segment(2)));
        }
        
        $data['current'] = 'penelitian';
        $data['apps']    = $this->apps_model->get_active_only();
        $data['faction'] = active_module_url('penelitian_nihil/do_register');
        
        $data['ppat']      = $this->bphtb_self_model->get_ppat();
        $data['perolehan'] = $this->bphtb_self_model->get_perolehan();
        $data['dasar']     = $this->bphtb_self_model->get_dasar_perhitungan();
        $data['info']      = $this->info;
        $data['fields']    = $this->fields;
        $data['mode']      = 'edit';
        
        $data['dt'] = $this->fpost();
        
        $id = $this->input->post('id');
        
        if ($id && $bank = $this->bank_model->get($id)) {
            $my_fields = $this->bank_model->get_entry_fields();
            
            $sspd_id    = $bank->sspd_id;
            $no_tagihan = $bank->no_tagihan;
            
            $data['dt']['bphtb_sudah_dibayarkan'] = $bank->bayar;
            $data['dt']['sspd_id'] = $sspd_id;
        }
		$data['dt']['tgl_transaksi']  = date('d-m-Y');
		$data['dt']['bpn_tgl_terima'] = date('d-m-Y');
		
        $this->load->view('form_contents/vsspd_penelitian_form', $data);
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
		$get1 = $this->bphtb_self_model->get_data_op_from_dop($kd_propinsi, $kd_dati2, $kd_kecamatan, $kd_kelurahan, $kd_blok, $no_urut, $kd_jns_op);
		// ngambil dari sppt
		$get2 = $this->bphtb_self_model->get_data_op_from_sppt($kd_propinsi, $kd_dati2, $kd_kecamatan, $kd_kelurahan, $kd_blok, $no_urut, $kd_jns_op, $thn);

		$data = new stdClass;
		if (!$get1 && !$get2)
			$data = NULL;
		if ($get1 && $get2)
			$data = (object) array_merge((array) $get1, (array) $get2);		
		
        echo json_encode($data);
	}
	
	function cetak() {
        $type = $this->uri->segment(4);
        $rptx = $this->uri->segment(5);
        $tglm = $this->uri->segment(6);
        $tgls = $this->uri->segment(7);
		
		$rptx = $rptx == 1 ? "sudah_validasi" : "belum_validasi";
        $tglm = substr($tglm, 6, 4) . '-' . substr($tglm, 3, 2) . '-' . substr($tglm, 0, 2);
        $tgls = substr($tgls, 6, 4) . '-' . substr($tgls, 3, 2) . '-' . substr($tgls, 0, 2);
	
		$jasper = $this->load->library('Jasper');
		$params = array(
			"startdate" => "'{$tglm}'",
			"enddate" => "'{$tgls}'",
			"logo" => base_url("assets/img/logorpt__.jpg"),
		);
		echo $jasper->cetak($rptx, $params, $type);
	}
}
