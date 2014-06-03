<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Penetapan extends CI_Controller
{
    private $my_listNumeric_Field = array('npop', 'npoptkp', 'tarif', 'terhutang', 'tarif_pengurang', 'pengurang', 'bphtb_sudah_dibayarkan', 'denda', 'bagian', 'pembagi', 'bphtb_harus_dibayarkan', 'bumi_luas', 'bumi_njop', 'bng_luas', 'bng_njop', 'njop');
    
    private $isppat = FALSE;
    private $ppatkd = '';
    private $ppatnm = '';
    private $ppatid = 0;
	
    function __construct() {
        parent::__construct();
        if (!$this->session->userdata('login')) {
            $this->session->set_flashdata('msg_warning', 'Session telah kadaluarsa, silahkan login ulang.');
            redirect('login');
            exit;
        }
        
        $module = 'penetapan';
        $this->load->library('module_auth', array(
            'module' => $module
        ));
        
        $this->load->model(array(
            'apps_model'
        ));
        $this->load->model(array(
            'bphtb_model',
            'bphtb_self_model',
            'bank_model',
			'sspd_model',
        ));
		
        $this->{'fields'} = $this->bphtb_self_model->fields_info('bphtb_validasi');
        $this->info       = ($this->fields) ? TRUE : FALSE;
    }
    
    public function index() {
        if (!$this->module_auth->read) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_read);
            redirect(active_module_url(''));
        }
        
        $data['current']      = 'penelitian';
        $data['apps']         = $this->apps_model->get_active_only();
        $data['main_content'] = '';
		
        //FORM Parameter
		$proses_id = isset($_GET['proses_id']) ? $_GET['proses_id'] : 0;
		$options = array(
			'0' => 'BELUM PROSES',
			'1' => 'SUDAH PROSES',
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
        $data['data_source']    = active_module_url() . "penetapan/grid?proses_id=$proses_id";
        
        $this->load->view('vpenetapan', $data);
    }
    
    function grid() {
        ob_start("ob_gzhandler");
		
		$proses_id = isset($_GET['proses_id']) ? $_GET['proses_id'] : 0;
        
        $aColumns = array(
			'id', 
			'tgl_transaksi', 
			'nop', 
			'wp_nama', 
			'bphtb_sudah_dibayarkan', 
			'bphtb_harus_dibayarkan',
			'ket', 
			"sk_nmr"
		);
        $sIndexColumn   = "bv.tgl_transaksi";
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
        $sWhere = "WHERE 1=1 
             AND kd_propinsi = '" . KD_PROPINSI . "' 
             AND kd_dati2 = '" . KD_DATI2 . "'";
		$sWhere .= " AND bphtb_harus_dibayarkan > 0";
		if ($proses_id == 1) 
			$sWhere .= " AND sk.kode is not null ";
		else
			$sWhere .= " AND sk.kode is null ";
        
        $search = '';
        if ($sSearch) $search .= " AND (wp_nama ilike '%$sSearch%' 
			OR bv.kd_propinsi||bv.kd_dati2||bv.kd_kecamatan||bv.kd_kelurahan||bv.kd_blok||bv.no_urut||bv.kd_jns_op ilike '%$sSearch%')";
        
        /* Total Data */
        $sql = "select count(*) c 
			from bphtb_validasi bv
			left join bphtb_sk sk on bv.id=sk.validasi_id ";
        if ($sWhere) $sql .= " $sWhere";
		
        $row       = $this->db->query($sql)->row();
        $iTotal    = $row->c;
        $iFiltered = $iTotal;
        
        if ($search) {
            $sql_query_r = "select  count(*) c 
				from bphtb_validasi bv
				left join bphtb_sk sk on bv.id=sk.validasi_id ";
            if ($sWhere) $sql_query_r .= " $sWhere";
            if ($search) $sql_query_r .= " $search";
            
            $row = $this->db->query($sql_query_r)->row();
            $iFiltered = $row->c;
        }
        
		/* Output */
        $sql_query_r = "select 
			bv.id, bv.tgl_transaksi, 
			bv.kd_propinsi||bv.kd_dati2||bv.kd_kecamatan||bv.kd_kelurahan||bv.kd_blok||bv.no_urut||bv.kd_jns_op nop, 
			bv.wp_nama, bv.bphtb_sudah_dibayarkan, bv.bphtb_harus_dibayarkan, 
			case when bphtb_harus_dibayarkan >0 then 'Kurang Bayar' when bphtb_harus_dibayarkan<0 then 'Lebih Bayar' end ket, 
			sk.tahun||'.'||lpad(sk.kode::text, 2, '0')||'.'||lpad(sk.no_urut::text, 5, '0') sk_nmr
			
			from bphtb_validasi bv
			left join bphtb_sk sk on bv.id=sk.validasi_id ";
        if ($sWhere) $sql_query_r .= " $sWhere";
        if ($search) $sql_query_r .= " $search";
        if ($sOrder) $sql_query_r .= " $sOrder";
        if ($sLimit) $sql_query_r .= " $sLimit";
        
		//die ($sql_query_r);
        $qry = $this->db->query($sql_query_r, false);
        
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
                if ($i == 1)
                    $row[] = (strtotime($aRow->tgl_transaksi)) ? (string) date('d/m/Y', strtotime($aRow->tgl_transaksi)) : '';
                elseif ($i == 4)
                    $row[] = number_format($aRow->$aColumns[$i], 0, ',', '.');
                elseif ($i == 5)
                    $row[] = number_format($aRow->$aColumns[$i], 0, ',', '.');
                else
                    $row[] = $aRow->$aColumns[$i];
            }
            $output['aaData'][] = $row;
        }
        
        echo json_encode($output);
    }
     
	private function fvalidation() {
        $this->form_validation->set_error_delimiters('<span>', '</span>');
        $this->form_validation->set_rules('ppat_id', 'PPAT', 'required');
        $this->form_validation->set_rules('wp_nama', 'Nama WP', 'required');
        $this->form_validation->set_rules('wp_npwp', 'NPWP WP', 'required');
        $this->form_validation->set_rules('wp_alamat', 'Alamat WP', 'required');
        // $this->form_validation->set_rules('wp_blok_kav', 'Blok / Kav WP', 'required');
        $this->form_validation->set_rules('wp_kelurahan', 'Kelurahan WP', 'required');
        $this->form_validation->set_rules('wp_rt', 'RT WP', 'required');
        $this->form_validation->set_rules('wp_rw', 'RW WP', 'required');
        $this->form_validation->set_rules('wp_kecamatan', 'Kecamatan WP', 'required');
        $this->form_validation->set_rules('wp_kota', 'Kota WP', 'required');
        $this->form_validation->set_rules('wp_provinsi', 'Propinsi WP', 'required');
        $this->form_validation->set_rules('wp_identitas', 'Identitas WP', 'required');
        $this->form_validation->set_rules('tgl_transaksi', 'Tanggal Transaksi', 'required');
        $this->form_validation->set_rules('kd_propinsi', 'NOP (Propinsi)', 'required');
        $this->form_validation->set_rules('kd_dati2', 'NOP (Kota / Kab)', 'required');
        $this->form_validation->set_rules('kd_kecamatan', 'NOP (Kecamatan)', 'required');
        $this->form_validation->set_rules('kd_kelurahan', 'NOP (Kelurahan)', 'required');
        $this->form_validation->set_rules('kd_blok', 'NOP (Kode Blok)', 'required');
        $this->form_validation->set_rules('no_urut', 'NOP (No.Urut)', 'required');
        $this->form_validation->set_rules('kd_jns_op', 'NOP (Jenis OP)', 'required');
        $this->form_validation->set_rules('thn_pajak_sppt', 'Tahun Pajak SPPT', 'required');
        $this->form_validation->set_rules('op_alamat', 'Alamat OP', 'required');
        $this->form_validation->set_rules('op_blok_kav', 'Blok / Kav OP', 'required');
        $this->form_validation->set_rules('op_rt', 'RT OP', 'required');
        $this->form_validation->set_rules('op_rw', 'RW OP', 'required');
        $this->form_validation->set_rules('bumi_luas', 'Luas Mumi', 'required');
        $this->form_validation->set_rules('bumi_njop', 'NJOP Bumi', 'required');
        $this->form_validation->set_rules('bng_luas', 'Luas Bangunan', 'required');
        $this->form_validation->set_rules('bng_njop', 'NJOP Bangunan', 'required');
        $this->form_validation->set_rules('njop', 'NJOP', 'required');
        $this->form_validation->set_rules('perolehan_id', 'Jenis Perolehan', 'required');
        $this->form_validation->set_rules('npop', 'NPOP', 'required');
        $this->form_validation->set_rules('npoptkp', 'NPOPTKP', 'required');
        $this->form_validation->set_rules('tarif', 'Tarif', 'required');
        $this->form_validation->set_rules('terhutang', 'Terhutang', 'required');
        $this->form_validation->set_rules('bagian', 'Bagian', 'required');
        $this->form_validation->set_rules('pembagi', 'Pembagi', 'required');
        $this->form_validation->set_rules('tarif_pengurang', 'Tarif Pengurang', 'required');
        $this->form_validation->set_rules('pengurang', 'No Pengurang', 'required');
        $this->form_validation->set_rules('bphtb_sudah_dibayarkan', 'BPHTB Sudah Dibayar', 'required');
        $this->form_validation->set_rules('denda', 'Denda', 'required');
        $this->form_validation->set_rules('bphtb_harus_dibayarkan', 'BPHTB Harus Dibayar', 'required');
        $this->form_validation->set_rules('dasar_id', 'Dasar Perhitungan', 'required');
	}
	
	private function fpost() {
        $data['id'] = $this->input->post('id');
        $data['tahun'] = $this->input->post('tahun');
		$data['kode'] = $this->input->post('kode');
        $data['no_sspd'] = $this->input->post('no_sspd');
        $data['ppat_id'] = $this->input->post('ppat_id');
        $data['wp_nama'] = $this->input->post('wp_nama');
        $data['wp_npwp'] = $this->input->post('wp_npwp');
        $data['wp_alamat'] = $this->input->post('wp_alamat');
        $data['wp_blok_kav'] = $this->input->post('wp_blok_kav');
        $data['wp_kelurahan'] = $this->input->post('wp_kelurahan');
        $data['wp_rt'] = $this->input->post('wp_rt');
        $data['wp_rw'] = $this->input->post('wp_rw');
        $data['wp_kecamatan'] = $this->input->post('wp_kecamatan');
        $data['wp_kota'] = $this->input->post('wp_kota');
        $data['wp_provinsi'] = $this->input->post('wp_provinsi');
        $data['wp_identitas'] = $this->input->post('wp_identitas');
        $data['wp_identitaskd'] = 'KTP'; //$this->input->post('wp_identitaskd');
        $data['tgl_transaksi'] = $this->input->post('tgl_transaksi');    
        $data['kd_propinsi'] = $this->input->post('kd_propinsi');
        $data['kd_dati2'] = $this->input->post('kd_dati2');
        $data['kd_kecamatan'] = $this->input->post('kd_kecamatan');
        $data['kd_kelurahan'] = $this->input->post('kd_kelurahan');
        $data['kd_blok'] = $this->input->post('kd_blok');
        $data['no_urut'] = $this->input->post('no_urut');
        $data['kd_jns_op'] = $this->input->post('kd_jns_op');
        $data['thn_pajak_sppt'] = $this->input->post('thn_pajak_sppt');
        $data['op_alamat'] = $this->input->post('op_alamat');
        $data['op_blok_kav'] = $this->input->post('op_blok_kav');
        $data['op_rt'] = $this->input->post('op_rt');
        $data['op_rw'] = $this->input->post('op_rw');
        $data['bumi_luas'] = $this->input->post('bumi_luas');
        $data['bumi_njop'] = $this->input->post('bumi_njop');
        $data['bng_luas'] = $this->input->post('bng_luas');
        $data['bng_njop'] = $this->input->post('bng_njop');
        $data['no_sertifikat'] = $this->input->post('no_sertifikat');
        $data['njop'] = $this->input->post('njop');
        $data['perolehan_id'] = $this->input->post('perolehan_id');
        $data['npop'] = $this->input->post('npop');
        $data['npoptkp'] = $this->input->post('npoptkp');
        $data['tarif'] = $this->input->post('tarif');
        $data['terhutang'] = $this->input->post('terhutang');
        $data['bagian'] = $this->input->post('bagian');
        $data['pembagi'] = $this->input->post('pembagi');
        $data['tarif_pengurang'] = $this->input->post('tarif_pengurang');
        $data['pengurang'] = $this->input->post('pengurang');
        $data['bphtb_sudah_dibayarkan'] = $this->input->post('bphtb_sudah_dibayarkan');
        $data['denda'] = $this->input->post('denda');
        //$data['restitusi'] = $this->input->post('restitusi');
        $data['bphtb_harus_dibayarkan'] = $this->input->post('bphtb_harus_dibayarkan');
        $data['status_pembayaran'] = $this->input->post('status_pembayaran');
        $data['dasar_id'] = $this->input->post('dasar_id');
        $data['header_id'] = $this->input->post('header_id');
        $data['wp_kdpos'] = $this->input->post('wp_kdpos');
        $data['keterangan'] = $this->input->post('keterangan');
        
        $data['created'] = $this->input->post('created');
        $data['create_uid'] = $this->input->post('create_uid');
		$data['updated'] = $this->input->post('updated');
        $data['update_uid'] = $this->input->post('update_uid');
        
        // dokumen pendukung
        $data['file1'] = $this->input->post('file1');
        $data['file2'] = $this->input->post('file2');
        $data['file3'] = $this->input->post('file3');
        $data['file4'] = $this->input->post('file4');
        $data['file5'] = $this->input->post('file5');
        $data['file6'] = $this->input->post('file6');
        $data['file7'] = $this->input->post('file7');
        $data['file8'] = $this->input->post('file8');
        $data['file9'] = $this->input->post('file9');
        $data['file10'] = $this->input->post('file10');
        
        $data['status_daftar'] = $this->input->post('status_daftar');
		
		$data['transno'] = $this->input->post('transno');
        
		return $data;
	}
	
    public function show() {
        if (!$this->module_auth->update) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_update);
            redirect(active_module_url($this->uri->segment(2)));
        }
		
        $data['current'] = 'penelitian';
        $data['apps']    = $this->apps_model->get_active_only();
        $data['faction'] = active_module_url('penetapan/proses');
        
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
        
        if ($id && $get = $this->bphtb_self_model->get_registered($id)) {
            $data['dt'] = array();
            $my_fields  = $this->bank_model->get_entry_fields();

			if ($sspd = $this->sspd_model->get($get->sspd_id)) {
				$data['dt']['tahun']   = $sspd->tahun;
				$data['dt']['kode']    = str_pad($sspd->kode, 2, "0", STR_PAD_LEFT);
				$data['dt']['no_sspd'] = str_pad($sspd->no_sspd, 6, "0", STR_PAD_LEFT);
			} else {
				$data['dt']['tahun']   = '';
				$data['dt']['kode']    = '';
				$data['dt']['no_sspd'] = '';
			}
			 
			if ($bank = $this->bank_model->get($get->bank_id)) {
				$data['dt']['transno'] = $bank->transno;
			} else {
				$data['dt']['transno'] = '';
			}
			
			//get
			$data['dt']['id'] = $id;
			$data['dt']['berkas_in_id'] = !isset($get->berkas_in_id) ? NULL : $get->berkas_in_id;
			$data['dt']['sspd_id'] = !isset($get->sspd_id) ? NULL : $get->sspd_id;
			$data['dt']['ppat_id'] = !isset($get->ppat_id) ? NULL : $get->ppat_id;
			$data['dt']['wp_nama'] = empty($get->wp_nama) ? NULL : $get->wp_nama;
			$data['dt']['wp_npwp'] = empty($get->wp_npwp) ? NULL : $get->wp_npwp;
			$data['dt']['wp_alamat'] = empty($get->wp_alamat) ? NULL : $get->wp_alamat;
			$data['dt']['wp_blok_kav'] = empty($get->wp_blok_kav) ? NULL : $get->wp_blok_kav;
			$data['dt']['wp_kelurahan'] = empty($get->wp_kelurahan) ? NULL : $get->wp_kelurahan;
			$data['dt']['wp_rt'] = empty($get->wp_rt) ? NULL : $get->wp_rt;
			$data['dt']['wp_rw'] = empty($get->wp_rw) ? NULL : $get->wp_rw;
			$data['dt']['wp_kecamatan'] = empty($get->wp_kecamatan) ? NULL : $get->wp_kecamatan;
			$data['dt']['wp_kota'] = empty($get->wp_kota) ? NULL : $get->wp_kota;
			$data['dt']['wp_provinsi'] = empty($get->wp_provinsi) ? NULL : $get->wp_provinsi;
			$data['dt']['wp_identitas'] = empty($get->wp_identitas) ? NULL : $get->wp_identitas;
			$data['dt']['wp_identitaskd'] = empty($get->wp_identitaskd) ? NULL : $get->wp_identitaskd;
			$data['dt']['tgl_transaksi'] = empty($get->tgl_transaksi) ? NULL : date('d-m-Y', strtotime($get->tgl_transaksi));
			$data['dt']['kd_propinsi'] = !isset($get->kd_propinsi) ? NULL : $get->kd_propinsi;
			$data['dt']['kd_dati2'] = !isset($get->kd_dati2) ? NULL : $get->kd_dati2;
			$data['dt']['kd_kecamatan'] = !isset($get->kd_kecamatan) ? NULL : $get->kd_kecamatan;
			$data['dt']['kd_kelurahan'] = !isset($get->kd_kelurahan) ? NULL : $get->kd_kelurahan;
			$data['dt']['kd_blok'] = !isset($get->kd_blok) ? NULL : $get->kd_blok;
			$data['dt']['no_urut'] = !isset($get->no_urut) ? NULL : $get->no_urut;
			$data['dt']['kd_jns_op'] = !isset($get->kd_jns_op) ? NULL : $get->kd_jns_op;
			$data['dt']['thn_pajak_sppt'] = !isset($get->thn_pajak_sppt) ? NULL : $get->thn_pajak_sppt;
			$data['dt']['op_alamat'] = empty($get->op_alamat) ? NULL : $get->op_alamat;
			$data['dt']['op_blok_kav'] = empty($get->op_blok_kav) ? NULL : $get->op_blok_kav;
			$data['dt']['op_rt'] = empty($get->op_rt) ? NULL : $get->op_rt;
			$data['dt']['op_rw'] = empty($get->op_rw) ? NULL : $get->op_rw;
			$data['dt']['bumi_luas'] = empty($get->bumi_luas) ? NULL : $get->bumi_luas;
			$data['dt']['bumi_njop'] = empty($get->bumi_njop) ? NULL : $get->bumi_njop;
			$data['dt']['bng_luas'] = empty($get->bng_luas) ? NULL : $get->bng_luas;
			$data['dt']['bng_njop'] = empty($get->bng_njop) ? NULL : $get->bng_njop;
			$data['dt']['no_sertifikat'] = empty($get->no_sertifikat) ? NULL : $get->no_sertifikat;
			$data['dt']['njop'] = empty($get->njop) ? NULL : $get->njop;
			$data['dt']['perolehan_id'] = empty($get->perolehan_id) ? NULL : $get->perolehan_id;
			$data['dt']['npop'] = empty($get->npop) ? NULL : $get->npop;
			$data['dt']['npoptkp'] = empty($get->npoptkp) ? NULL : $get->npoptkp;
			$data['dt']['tarif'] = empty($get->tarif) ? NULL : $get->tarif;
			$data['dt']['terhutang'] = empty($get->terhutang) ? NULL : $get->terhutang;
			$data['dt']['bagian'] = empty($get->bagian) ? NULL : $get->bagian;
			$data['dt']['pembagi'] = empty($get->pembagi) ? NULL : $get->pembagi;
			$data['dt']['tarif_pengurang'] = empty($get->tarif_pengurang) ? NULL : $get->tarif_pengurang;
			$data['dt']['pengurang'] = empty($get->pengurang) ? NULL : $get->pengurang;
			$data['dt']['bphtb_sudah_dibayarkan'] = empty($get->bphtb_sudah_dibayarkan) ? NULL : $get->bphtb_sudah_dibayarkan;
			$data['dt']['denda'] = empty($get->denda) ? NULL : $get->denda;
			$data['dt']['restitusi'] = empty($get->restitusi) ? NULL : $get->restitusi;
			$data['dt']['bphtb_harus_dibayarkan'] = empty($get->bphtb_harus_dibayarkan) ? NULL : $get->bphtb_harus_dibayarkan;
			$data['dt']['status_pembayaran'] = empty($get->status_pembayaran) ? NULL : $get->status_pembayaran;
			$data['dt']['dasar_id'] = empty($get->dasar_id) ? NULL : $get->dasar_id;
			$data['dt']['create_uid'] = empty($get->create_uid) ? NULL : $get->create_uid;
			$data['dt']['update_uid'] = empty($get->update_uid) ? NULL : $get->update_uid;
			$data['dt']['created'] = empty($get->created) ? NULL : date('d-m-Y', strtotime($get->created));
			$data['dt']['updated'] = empty($get->updated) ? NULL : date('d-m-Y', strtotime($get->updated));
			$data['dt']['header_id'] = empty($get->header_id) ? NULL : $get->header_id;
			$data['dt']['bpn_tgl_terima'] = empty($get->bpn_tgl_terima) ? NULL : date('d-m-Y', strtotime($get->bpn_tgl_terima));
			$data['dt']['bpn_tgl_selesai'] = empty($get->bpn_tgl_selesai) ? NULL : date('d-m-Y', strtotime($get->bpn_tgl_selesai));
			$data['dt']['wp_kdpos'] = empty($get->wp_kdpos) ? NULL : $get->wp_kdpos;
			$data['dt']['bank_id'] = empty($get->bank_id) ? NULL : $get->bank_id;
			$data['dt']['persen_pengurang_sendiri'] = empty($get->persen_pengurang_sendiri) ? NULL : $get->persen_pengurang_sendiri;
			$data['dt']['pp_nomor_pengurang_sendiri'] = empty($get->pp_nomor_pengurang_sendiri) ? NULL : $get->pp_nomor_pengurang_sendiri;

			if ($sk = $this->bphtb_self_model->get_sk_by_validasi($id))
				$data['dt']['sk_id'] = $sk->id;
			else
				$data['dt']['sk_id'] = NULL;
				
            $this->load->view('form_contents/vsspd_penetapan_form', $data);
        } else {
            show_404();
        }
    }
	
	function proses() {
        if (!$this->module_auth->update) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_update);
            redirect(active_module_url($this->uri->segment(2)));
        }
		
        $data['current'] = 'penelitian';
        $data['apps']    = $this->apps_model->get_active_only();
        $data['faction'] = active_module_url('penetapan/proses');
        
        $data['ppat']   = $this->bphtb_self_model->get_ppat();
        $data['isppat'] = $this->isppat;
        $data['ppatid'] = $this->ppatid;
        $data['ppatnm'] = $this->ppatnm;
		
        $data['perolehan'] = $this->bphtb_self_model->get_perolehan();
        $data['dasar']     = $this->bphtb_self_model->get_dasar_perhitungan();
        $data['info']      = $this->info;
        $data['fields']    = $this->fields;
        $data['mode']      = 'edit';
        
		$this->fvalidation();
		if ($this->form_validation->run() == TRUE) {
			$validasi_id = $this->input->post("id");
			
			$sspd = $this->db->query("select sspd_id from bphtb_validasi where id={$validasi_id} ");
			$sspd_id = 0;
			if ($sspd->num_rows()>0)
				$sspd_id = $sspd->row()->sspd_id;
			
			$sspd_kode = $this->bphtb_model->get_new_sspd_kode(date('Y'), '2');
			$tahun   = $sspd_kode[0];
			$kode    = $sspd_kode[1];
			$no_sspd = $sspd_kode[2];
			
            $dat = array(
                'header_id' => $sspd_id,
				'tahun'     => $tahun,
				'kode'      => $kode,
                'no_sspd'   => $no_sspd,
				
                'ppat_id' => $this->input->post('ppat_id'),
                'wp_nama' => strtoupper($this->input->post('wp_nama')),
                'wp_npwp' => strtoupper($this->input->post('wp_npwp')),
                'wp_alamat' => strtoupper($this->input->post('wp_alamat')),
                'wp_blok_kav' => strtoupper($this->input->post('wp_blok_kav')),
                'wp_kelurahan' => strtoupper($this->input->post('wp_kelurahan')),
                'wp_rt' => strtoupper($this->input->post('wp_rt')),
                'wp_rw' => strtoupper($this->input->post('wp_rw')),
                'wp_kecamatan' => strtoupper($this->input->post('wp_kecamatan')),
                'wp_kota' => strtoupper($this->input->post('wp_kota')),
                'wp_provinsi' => strtoupper($this->input->post('wp_provinsi')),
                'wp_identitas' => strtoupper($this->input->post('wp_identitas')),
                //'wp_identitaskd' => 'KTP', //$this->input->post('wp_identitaskd'),
                'wp_kdpos' => strtoupper($this->input->post('wp_kdpos')),
                'tgl_transaksi' => date('Y-m-d',strtotime($this->input->post('tgl_transaksi'))),
                'kd_propinsi' => $this->input->post('kd_propinsi'),
                'kd_dati2' => $this->input->post('kd_dati2'),
                'kd_kecamatan' => $this->input->post('kd_kecamatan'),
                'kd_kelurahan' => $this->input->post('kd_kelurahan'),
                'kd_blok' => $this->input->post('kd_blok'),
                'no_urut' => $this->input->post('no_urut'),
                'kd_jns_op' => $this->input->post('kd_jns_op'),
                'thn_pajak_sppt' => $this->input->post('thn_pajak_sppt'),
                'op_alamat' => strtoupper($this->input->post('op_alamat')),
                'op_blok_kav' => strtoupper($this->input->post('op_blok_kav')),
                'op_rt' => strtoupper($this->input->post('op_rt')),
                'op_rw' => strtoupper($this->input->post('op_rw')),
                'bumi_luas' => $this->input->post('bumi_luas'),
                'bumi_njop' => $this->input->post('bumi_njop'),
                'bng_luas' => $this->input->post('bng_luas'),
                'bng_njop' => $this->input->post('bng_njop'),
                'no_sertifikat' => $this->input->post('no_sertifikat')?$this->input->post('no_sertifikat'):'-',
                'njop' => $this->input->post('njop'),
                'perolehan_id' => $this->input->post('perolehan_id'),
                'npop' => $this->input->post('npop'),
                'npoptkp' => $this->input->post('npoptkp'),
                'tarif' => $this->input->post('tarif'),
                'terhutang' => $this->input->post('terhutang'),
                'bagian' => $this->input->post('bagian'),
                'pembagi' => $this->input->post('pembagi'),
                'tarif_pengurang' => $this->input->post('tarif_pengurang'),
                'pengurang' => $this->input->post('pengurang'),
                'bphtb_sudah_dibayarkan' => $this->input->post('bphtb_sudah_dibayarkan'),
                'denda' => $this->input->post('denda'),
                //'restitusi' => $this->input->post('restitusi'),
                'bphtb_harus_dibayarkan' => $this->input->post('bphtb_harus_dibayarkan'),
                'status_pembayaran' => 0 , //($this->input->post('status_pembayaran')=='on'?1:0),
                'dasar_id' => $this->input->post('dasar_id'),
                'keterangan' => $this->input->post('keterangan'),
                
                'file1' => $this->input->post('file1'),
                'file2' => $this->input->post('file2'),
                'file3' => $this->input->post('file3'),
                'file4' => $this->input->post('file4'),
                'file5' => $this->input->post('file5'),
                'file6' => $this->input->post('file6'),
                'file7' => $this->input->post('file7'),
                'file8' => $this->input->post('file8'),
                'file9' => $this->input->post('file9'),
                'file10' => $this->input->post('file10'),
                
                'status_daftar' => ($this->input->post('status_daftar')==2) ? 0 : $this->input->post('status_daftar'),
                
                'updated' => date('Y-m-d h:m:s'),
                'update_uid' => $this->session->userdata('uid'),
				
			);
            
            $dat    = $this->bphtb_model->set_field_number_value($this->my_listNumeric_Field, $dat);
            // $dat = $this->upload_my_file($dat);
            $this->sspd_model->save($dat);
			
			// SK 
			$id  = $this->input->post("id");
			if ($id && $get = $this->bphtb_self_model->get_registered($id)) {
				$kode    = (float)$get->bphtb_harus_dibayarkan > 0 ? 1 : 2; // 1=KB 2=LB
				$no_urut = $this->bphtb_self_model->get_last_no_urut_sk();
				
				$sk_data = array(
					'validasi_id' => $id,
					'kode' => $kode,
					'no_urut' =>$no_urut,
					'tahun' => date('Y'),
					
					'create_uid' => $this->session->userdata('user_id'),
					'created' => date('Y-m-d'),
				);
				$this->bphtb_self_model->register_sk($sk_data);
				
			}
			
			$this->session->set_flashdata('msg_success', 'Data SK Perubahan telah disimpan.');
			// redirect(active_module_url('penetapan/show/'.$id));
			redirect(active_module_url('penetapan'));
		}
		
		$data['dt'] = $this->fpost();
		$this->load->view('form_contents/vsspd_penetapan_form', $data);
	}
	
	/* report */
	function show_rpt() {
		$cls_mtd_html = $this->router->fetch_class()."/cetak/html/";
		$cls_mtd_pdf  = $this->router->fetch_class()."/cetak/pdf/";
		$data['rpt_html'] = active_module_url($cls_mtd_html. $_SERVER['QUERY_STRING']);;
		$data['rpt_pdf']  = active_module_url($cls_mtd_pdf . $_SERVER['QUERY_STRING']);;
        $this->load->view('vjasper_viewer', $data);
	}

	function cetak() {
        $type = $this->uri->segment(4);
        $id   = $this->uri->segment(5);
		$rptx = "bphtb_sk";
		
		$jasper = $this->load->library('Jasper');
		$params = array(
			"validasi_id" => (int)$id,
		);
		echo $jasper->cetak($rptx, $params, $type);
	}
}
