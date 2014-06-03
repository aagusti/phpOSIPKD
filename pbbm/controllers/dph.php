<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Dph extends CI_Controller
{
	private $module = 'dph_entri';
	private $controller = 'dph';
	
    function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('login')) {
            $this->session->set_flashdata('msg_warning', 'Session telah kadaluarsa, silahkan login ulang.');
            redirect('login');
            exit;
        }
        
		if ($this->uri->segment(2) == 'dph_posting') {
			$this->module = 'dph_posting';
			$this->controller = 'dph_posting';
		}
		
		$this->load->library('module_auth',array('module'=> $this->module));

		$this->load->model(array('apps_model'));
		$this->load->model(array('dph_model','dph_payment_model'));
	}
    
    public function index()
    {
        if (!$this->module_auth->read) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_read);
            redirect(active_module_url(''));
        }
        
        $data['current']      = 'dph';
        $data['apps']         = $this->apps_model->get_active_only();
		
        //FORM Parameter
		$tahun  = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');
        $kec_kd = (isset($_GET['kec_kd']) ? $_GET['kec_kd'] : '000');
        $kel_kd = (isset($_GET['kel_kd']) ? $_GET['kel_kd'] : '000');
		$kec_kd = (get_user_kec_kd()!='000' && get_user_kec_kd()!=$kec_kd) ? get_user_kec_kd() : $kec_kd;
		$kel_kd = (get_user_kel_kd()!='000' && get_user_kel_kd()!=$kel_kd) ? get_user_kel_kd() : $kel_kd;
		   
        $data['tahun']     = $tahun;
        $data['kec_kd']    = $kec_kd;
        $data['kel_kd']    = $kel_kd;
        
        $this->load->model('kecModel', 'kec');
        $this->load->model('kelModel', 'kel');
		$data['kecamatan'] = $this->kec->getRecord('000');
		$data['kelurahan'] = $this->kel->getRecord($kec_kd, '000');
		
        $data['data_source']    = active_module_url() . "dph/grid?tahun=$tahun&kec_kd=$kec_kd&kel_kd=$kel_kd";
		
        //Grid Standard Parameter
        $data['iDisplayLength'] = (isset($_GET['iDisplayLength']) && is_numeric($_GET['iDisplayLength'])) ? $_GET['iDisplayLength'] : 15;
        $data['iDisplayStart']  = (isset($_GET['iDisplayStart']) && is_numeric($_GET['iDisplayStart'])) ? $_GET['iDisplayStart'] : 0;
        $data['iSortingCols']   = (isset($_GET['iSortingCols']) && is_numeric($_GET['iSortingCols'])) ? $_GET['iSortingCols'] : 1;
        $data['sEcho']          = (isset($_GET['sEcho']) && is_numeric($_GET['sEcho'])) ? $_GET['sEcho'] : 1;
        $data['sSearch']        = (isset($_GET['sSearch'])) ? $_GET['sSearch'] : "";
        
        $this->load->view('vdph', $data);
    }
    
    function grid()
    {
        ob_start("ob_gzhandler");
		
        $tahun  = (isset($_GET['tahun'])) ? $_GET['tahun'] : date('Y');
        $kec_kd = (isset($_GET['kec_kd']) && is_numeric($_GET['kec_kd'])) ? $_GET['kec_kd'] : '000';
        $kel_kd = (isset($_GET['kel_kd']) && is_numeric($_GET['kel_kd'])) ? $_GET['kel_kd'] : '000';
        
        $aColumns       = array('id', 'kode_lengkap', 'nama', 'tgl_bayar', 'pokok', 'denda', 'bayar', 'status_posting', 'pokok', 'denda', 'bayar');
        $sIndexColumn   = "kode_lengkap";
        $iDisplayLength = (isset($_GET['iDisplayLength']) && is_numeric($_GET['iDisplayLength'])) ? $_GET['iDisplayLength'] : 15;
        $iDisplayLength = (int)$iDisplayLength;
        $iDisplayStart  = (isset($_GET['iDisplayStart']) && is_numeric($_GET['iDisplayStart'])) ? $_GET['iDisplayStart'] : 0;
        $iDisplayStart  = (int)$iDisplayStart;
        $iSortCol_0     = (isset($_GET['iSortCol_0']) && is_numeric($_GET['iSortCol_0'])) ? $_GET['iSortCol_0'] : 0;
        $iSortingCols   = (isset($_GET['iSortingCols']) && is_numeric($_GET['iSortingCols'])) ? $_GET['iSortingCols'] : 1;
        $sSortDir_0     = (isset($_GET['sSortDir_0'])) ? $_GET['sSortDir_0'] : "asc";
        $sEcho          = (isset($_GET['sEcho']) && is_numeric($_GET['sEcho'])) ? $_GET['sEcho'] : 1;
        $sSearch        = (isset($_GET['sSearch'])) ? $_GET['sSearch'] : "";
		
		/* Limit */
        $sLimit = "";
        if (isset($iDisplayLength) && $iDisplayStart != '-1') {
            // $sLimit = "LIMIT {$iDisplayLength} OFFSET {$iDisplayStart}";
        }

		/* Ordering */
        $sOrder = "ORDER BY kode_lengkap";
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
        $sWhere = "WHERE tahun = '{$tahun}' 
             AND d.kd_propinsi = '" . KD_PROPINSI . "' 
             AND d.kd_dati2 = '" . KD_DATI2 . "'";
			 
        if ($kec_kd != "000") {
            $sWhere .= " AND d.kd_kecamatan='$kec_kd'";
            if ($kel_kd != "000") $sWhere .= " AND d.kd_kelurahan='$kel_kd'";
        }
        
        $search = '';
        if ($sSearch) $search .= " AND (nama ilike '%$sSearch%' OR nama ilike '%$sSearch%')";
        
        /* Total Data */
        /*
        $sql = "SELECT  COUNT(*) c FROM dph d ";
        if ($sWhere) $sql .= " $sWhere";
		
        $row       = $this->db->query($sql)->row();
        $iTotal    = $row->c;
        $iFiltered = $iTotal;
        
        if ($search) {
            $sql_query_r = "SELECT  COUNT(*) c FROM dph d ";
            if ($sWhere) $sql_query_r .= " $sWhere";
            if ($search) $sql_query_r .= " $search";
            
            $row = $this->db->query($sql_query_r)->row();
            $iFiltered = $row->c;
        }
        */
        
        /*
         * Output
         */
        $sql_query_r = "SELECT d.id, d.kd_propinsi, d.kd_dati2, d.kd_kecamatan, d.kd_kelurahan, d.kode, 
			(d.kd_kecamatan||'-'||d.kd_kelurahan||'-'||d.tahun||'-'||d.kode) as kode_lengkap, 
			d.nama, d.tgl_bayar, case when d.status_posting=1 then 'Sudah' else 'Belum' end as status_posting, 
            coalesce(sum(dd.jml_yg_dibayar)-sum(dd.denda),0) pokok,
            coalesce(sum(dd.denda),0) denda, coalesce(sum(dd.jml_yg_dibayar),0) bayar
			FROM dph d left join dph_payment dd  on d.id=dd.dph_id ";
		$groupBy = " GROUP BY d.id, d.kd_propinsi, d.kd_dati2, d.kd_kecamatan, d.kd_kelurahan, d.tahun, d.kode, d.nama, d.tgl_bayar, d.status_posting ";
		
        if ($sWhere) $sql_query_r .= " $sWhere";
        if ($search) $sql_query_r .= " $search";
        $sql_query_r .= $groupBy;
        if ($sOrder) $sql_query_r .= " $sOrder";
        if ($sLimit) $sql_query_r .= " $sLimit";

		
        $qry = $this->db->query($sql_query_r);
		
        $output = array(
            "sEcho" => $sEcho,
            "iDisplayLength" => 0, //$iDisplayLength,
            "iTotalRecords" => 0, //$iTotal,
            "iTotalDisplayRecords" => 0, //$iFiltered,
            "SQL Query" => $sql_query_r,
            "aaData" => array()
        );
        
        foreach ($qry->result() as $aRow) {
            $row = array();
            for ($i = 0; $i < count($aColumns); $i++) { 
                if ($i == 3)
                    $row[] = (strtotime($aRow->tgl_bayar)) ? (string) date('d-m-Y', strtotime($aRow->tgl_bayar)) : '';
				elseif ($i == 4 || $i == 5 || $i == 6)
                    $row[] = number_format($aRow->$aColumns[$i], 0, ',', '.');
                else 
                    $row[] = $aRow->$aColumns[$i];
            }
            $output['aaData'][] = $row;
        }
        
        echo json_encode($output);
    }

    function grid_detail() // grid detail
    {
        ob_start("ob_gzhandler");
		
        $dph_id = $this->uri->segment(4);     
		
		if(empty($dph_id)) {
			$response = new stdClass;
			$responce->sEcho=1;
			$responce->iTotalRecords="0";
			$responce->iTotalDisplayRecords="0";
			$responce->aaData=array();	
			echo json_encode($responce);
			exit;
		}
		
        $aColumns = array('nop_tahun','pemilik','tanggal','pokok','denda','bayar',
			'batal',

			'kd_kecamatan', 'kd_kelurahan', 'kd_blok', 'no_urut', 'kd_jns_op', 'thn_pajak_sppt', 
			'pembayaran_ke', 'denda', 'jml_yg_dibayar', 'tgl_rekam_byr', 'nip_rekam_byr');
        
        /* Total Data */
        $sql = "SELECT  COUNT(*) c FROM dph_payment WHERE dph_id={$dph_id} ";
		
        $row       = $this->db->query($sql)->row();
        $iTotal    = $row->c;
        $iFiltered = $iTotal;
        
        /*
         * Output
         */
        $sql_query_r = "SELECT 
			dp.kd_propinsi||'.'||dp.kd_dati2||'.'||dp.kd_kecamatan||'.'||dp.kd_kelurahan||'.'||dp.kd_blok||'.'||dp.no_urut||'.'||dp.kd_jns_op||'-'||dp.thn_pajak_sppt as nop_tahun, 
			s.nm_wp_sppt as pemilik, s.tgl_jatuh_tempo_sppt as tanggal, s.pbb_yg_harus_dibayar_sppt as pokok, dp.denda as denda, s.pbb_yg_harus_dibayar_sppt + dp.denda as bayar,

			dp.kd_propinsi, dp.kd_dati2, dp.kd_kecamatan, dp.kd_kelurahan, 
			dp.kd_blok, dp.no_urut, dp.kd_jns_op, dp.thn_pajak_sppt, dp.pembayaran_ke, dp.denda, 
			dp.jml_yg_dibayar, dp.tgl_rekam_byr, dp.nip_rekam_byr
			FROM dph_payment dp 
			LEFT JOIN sppt s 
			ON dp.kd_propinsi=s.kd_propinsi 
			AND dp.kd_dati2=s.kd_dati2 
			AND dp.kd_kecamatan=s.kd_kecamatan 
			AND dp.kd_kelurahan=s.kd_kelurahan
			AND dp.kd_blok=s.kd_blok 
			AND dp.no_urut=s.no_urut 
			AND dp.kd_jns_op=s.kd_jns_op 
			AND dp.thn_pajak_sppt=s.thn_pajak_sppt
			WHERE dph_id={$dph_id} ";
				
        $qry            = $this->db->query($sql_query_r);
        $sEcho          = (isset($_GET['sEcho']) && is_numeric($_GET['sEcho'])) ? $_GET['sEcho'] : 1;
        $iDisplayLength = (isset($_GET['iDisplayLength']) && is_numeric($_GET['iDisplayLength'])) ? $_GET['iDisplayLength'] : 99;
		
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
				if ($i == 6)
                    $row[] = '<a class="delete" href="">Hapus</a>';
                elseif ($i == 2)
                    $row[] = date('d-m-Y', strtotime($aRow->$aColumns[$i]));
                elseif ($i == 18)
                    $row[] = date('Y-m-d', strtotime($aRow->$aColumns[$i]));
                elseif ($i == 3 || $i == 4 || $i == 5)
                    $row[] = number_format($aRow->$aColumns[$i], 0, ',', '.');
                else  
                    $row[] = $aRow->$aColumns[$i];
            }
            $output['aaData'][] = $row;
        }
        
        echo json_encode($output);
    }
	
	function get_nop_blok() {
		$thn = $this->uri->segment(4);
		$is_nop = ($this->uri->segment(5)==1) ? true : false;
		$r1 = $this->uri->segment(6);
		$r2 = $this->uri->segment(7);
		$r2 = !empty($r2) ? $r2 : false;
		
		$sppt = $this->dph_model->get_nop_blok($thn, $is_nop, $r1, $r2);
		if (!$sppt) return $sppt;
		
		$kirim = array();
		foreach($sppt as $data) {
			$nop_thn =  $data['kd_propinsi'].".".$data['kd_dati2'].".".$data['kd_kecamatan'].".".$data['kd_kelurahan'].".".$data['kd_blok'].".".$data['no_urut'].".".$data['kd_jns_op']."-".$data['thn_pajak_sppt'];
			$pemilik = $data['nm_wp_sppt'];
			$tanggal = $data['tgl_jatuh_tempo_sppt'];
			$pokok   = $data['pbb_yg_harus_dibayar_sppt'];
			$denda   = 0;
			$bayar   = 0;
			
			//hitung
			if (date($data['tgl_jatuh_tempo_sppt']) < date('Y-m-d')) 
				$denda = hitdenda($pokok,$data['tgl_jatuh_tempo_sppt']);
				
			$utang = $pokok + $denda;
			
			//newdata
			$newdata = array();
			$newdata['nop_thn'] = $nop_thn;
			$newdata['pemilik'] = $pemilik;
			$newdata['tanggal'] = date('d-m-Y', strtotime($tanggal));
			$newdata['pokok'] = number_format($pokok,0,",",".");
			$newdata['denda1'] = number_format($denda,0,",",".");
			$newdata['bayar'] = number_format($utang,0,",",".");
			
			// $newdata['kd_propinsi']    = $data['kd_propinsi'];
			// $newdata['kd_dati2']       = $data['kd_dati2'];
			$newdata['kd_kecamatan']   = $data['kd_kecamatan'];
			$newdata['kd_kelurahan']   = $data['kd_kelurahan'];
			$newdata['kd_blok']        = $data['kd_blok'];
			$newdata['no_urut']        = $data['no_urut'];
			$newdata['kd_jns_op']      = $data['kd_jns_op'];
			$newdata['thn_pajak_sppt'] = $data['thn_pajak_sppt'];
			$newdata['pembayaran_ke']  = '1';
			$newdata['denda']          = $denda;
			$newdata['jml_yg_dibayar'] = $utang;
			$newdata['tgl_rekam_byr']  = date('Y-m-d');
			$newdata['nip_rekam_byr']  = $this->session->userdata('nip');
			$kirim[] = $newdata;
		}
				
		echo json_encode($kirim);
	}
	
	function cek_nop_thn() {
		$nop_thn = $this->uri->segment(4);
		echo $this->dph_model->cek_nop_thn($nop_thn);
	}
	
	function get_kelurahan() {
        $this->load->model('kelModel', 'kel');
		$kec_kd = $this->uri->segment(4);
		$kelurahan = $this->kel->getRecord($kec_kd, '000');
		
		$ret = '';
		if (get_user_kel_kd() == '000') $ret = "<option value=\"000\">Semua</option>\n";
		foreach ($kelurahan as $kel) 
		{
			$selected='';
			// if ($kel->kd_kelurahan==$kel_kd) $selected=" selected";
			$ret .= "<option value=\"".$kel->kd_kelurahan."\" $selected>".$kel->nm_kelurahan."</option>\n";
		}
		
		echo $ret;
	}
	
	function get_pejabat() {
		$kec_kd = $this->uri->segment(4);
		$kel_kd = $this->uri->segment(5);
		
        $this->load->model('user_pbbms_model');
		$pejabat = $this->user_pbbms_model->get_by_kec_kel($kec_kd, $kel_kd);
		if (!$pejabat)
			$pejabat = $this->dph_model->get_user_pbbms();
			
		$ret = '';
			foreach ($pejabat as $pjb) 
				$ret .= "<option value=\"".$pjb->id."\">".$pjb->nama."</option>\n";
		
		echo $ret;
	}
	
    private function fvalidation()
    {
		$this->form_validation->set_rules('kd_kecamatan','Kecamatan','required|trim');
		$this->form_validation->set_rules('kd_kelurahan','Kelurahan','required|trim');
		$this->form_validation->set_rules('nama','Uraian','required|trim');
		$this->form_validation->set_rules('tgl_bayar','Tanggal','required');
    }
    
    private function fpost()
    {
		$data['id'] = $this->input->post('id');
		$data['kd_propinsi'] = $this->input->post('kd_propinsi');
		$data['kd_dati2'] = $this->input->post('kd_dati2');
		$data['kd_kecamatan'] = $this->input->post('kd_kecamatan');
		$data['kd_kelurahan'] = $this->input->post('kd_kelurahan');
		$data['kode'] = $this->input->post('kode');
		$data['tahun'] = $this->input->post('tahun');
		$data['nama'] = $this->input->post('nama');
		$data['tgl_bayar'] = $this->input->post('tgl_bayar');
		$data['tgl_posting'] = $this->input->post('tgl_posting');
		$data['status_posting'] = $this->input->post('status_posting');
		$data['pejabat1_id'] = $this->input->post('pejabat1_id');
		$data['pejabat2_id'] = $this->input->post('pejabat2_id');

        return $data;
    }
	
    public function add() {
		if(!$this->module_auth->create) {
			$this->session->set_flashdata('msg_warning', $this->module_auth->msg_create);
			redirect(active_module_url('dph'));
		}
		$data['current']     = 'dph';
		$data['faction']     = active_module_url($this->controller.'/add');
		$data['dt']          = $this->fpost();
        $data['apps']        = $this->apps_model->get_active_only();
		
		$data['kec_kd'] = $this->uri->segment(4);
		$data['kel_kd'] = $this->uri->segment(5);
		$data['users']  = $this->dph_model->get_user_pbbms();
		
        $this->load->model('kecModel', 'kec');
        $this->load->model('kelModel', 'kel');
		$data['kecamatan']   = $this->kec->getRecord('000');
		$data['kelurahan']   = $this->kel->getRecord($data['kec_kd'], $data['kel_kd']);
		
		$this->fvalidation();
		if ($this->form_validation->run() == TRUE) {
			$kode = $this->dph_model->last_kode();
			$input_post = $this->fpost();
			$post_data = array(
				'kd_propinsi' => KD_PROPINSI,
				'kd_dati2' => KD_DATI2,
				'kd_kecamatan' => $input_post['kd_kecamatan'],
				'kd_kelurahan' => $input_post['kd_kelurahan'],
				'kode' => $kode,
				'tahun' => $input_post['tahun'],
				'nama' => $input_post['nama'],
				'tgl_bayar' => date('Y-m-d', strtotime($input_post['tgl_bayar'])),
				'pejabat1_id' => $input_post['pejabat1_id'],
				'pejabat2_id' => $input_post['pejabat2_id'],
				// 'tgl_posting' => date('Y-m-d', strtotime($input_post['tgl_posting'])),
				// 'status_posting' => $input_post['status_posting'],
			);
			$dph_id = $this->dph_model->save($post_data);
			
			// data  detail
			$payment = $this->input->post('dtDetail');
			$tambahan_data2 = array();

			if(isset($payment)) {
				$i = 1;
				$payment = json_decode($payment, true);
				
				//hapus dulu disini
				$this->db->delete('dph_payment', array('dph_id' => $dph_id)); 
				if(count($payment['dtDetail']) > 0){
					$rd_row = array();
					foreach($payment['dtDetail'] as $rows) {
						$rd_row = array (							
							'dph_id' => $dph_id,
							'kd_propinsi' => KD_PROPINSI,
							'kd_dati2' => KD_DATI2,
							'kd_kecamatan' => $rows[7],
							'kd_kelurahan' => $rows[8],
							'kd_blok' => $rows[9],
							'no_urut' => $rows[10],
							'kd_jns_op' => $rows[11],
							'thn_pajak_sppt' => $rows[12],
							'pembayaran_ke' => $rows[13],
							'denda' => $rows[14],
							'jml_yg_dibayar' => $rows[15],
							'tgl_rekam_byr' => $rows[16],
							'nip_rekam_byr' => $rows[17],
						);
						$i++;
						$tambahan_data2 = array_merge($tambahan_data2, array($rd_row));
					}
					
					//langsung ajah dah - sementara
					$this->db->insert_batch('dph_payment', $tambahan_data2);
				}
			}
			
			$this->session->set_flashdata('msg_success', 'Data telah disimpan');		
			redirect(active_module_url().$this->controller.'?kec_kd='.$input_post['kd_kecamatan'].'&kel_kd='.$input_post['kd_kelurahan']);
		}
		$this->load->view('vdph_form',$data);
	}
	
	public function edit() {
		if(!$this->module_auth->update) {
			$this->session->set_flashdata('msg_warning', $this->module_auth->msg_update);
			redirect(active_module_url('dph'));
		}
		
		$id = $this->uri->segment(4);
		if($this->dph_model->cek_stat($id)) {
			$this->session->set_flashdata('msg_warning', 'Data ini sudah diposting, tidak dapat di edit.');
			redirect(active_module_url('dph'));
		}
		
		$data['current']   = 'dph';
		$data['faction']   = active_module_url($this->controller.'/update');
        $data['apps']      = $this->apps_model->get_active_only();
		$data['users']  = $this->dph_model->get_user_pbbms();
		
		if($id && $get = $this->dph_model->get($id)) {
			$data['dt']['id'] = $get->id;
			$data['dt']['kd_propinsi'] = $get->kd_propinsi;
			$data['dt']['kd_dati2'] = $get->kd_dati2;
			$data['kec_kd'] = $get->kd_kecamatan;
			$data['kel_kd'] = $get->kd_kelurahan;
			$data['dt']['kode'] = $get->kode;
			$data['dt']['tahun'] = $get->tahun;
			$data['dt']['nama'] = $get->nama;
			$data['dt']['tgl_bayar'] = date('d-m-Y', strtotime($get->tgl_bayar));
			$data['dt']['tgl_posting'] = date('d-m-Y', strtotime($get->tgl_posting));
			$data['dt']['status_posting'] = $get->status_posting;
			$data['dt']['pejabat1_id'] = $get->pejabat1_id;
			$data['dt']['pejabat2_id'] = $get->pejabat2_id;
			
			$this->load->model('kecModel', 'kec');
			$this->load->model('kelModel', 'kel');
			$data['kecamatan']   = $this->kec->getRecord('000');
			$data['kelurahan']   = $this->kel->getRecord($data['kec_kd'], $data['kel_kd']);
			
			$this->load->view('vdph_form',$data);
		} else {
			show_404();
		}
	}
	
	public function update() {
		if(!$this->module_auth->update) {
			$this->session->set_flashdata('msg_warning', $this->module_auth->msg_update);
			redirect(active_module_url($this->controller));
		}
		$data['current'] = 'dph';
		$data['faction'] = active_module_url($this->controller.'/update');
		$data['dt']      = $this->fpost();
        $data['apps']    = $this->apps_model->get_active_only();
				
		$this->fvalidation();
		$input_post = $this->fpost();
		if ($this->form_validation->run() == TRUE) {	
			$post_data = array(
				'kd_propinsi' => KD_PROPINSI,
				'kd_dati2' => KD_DATI2,
				'kd_kecamatan' => $input_post['kd_kecamatan'],
				'kd_kelurahan' => $input_post['kd_kelurahan'],
				'nama' => $input_post['nama'],
				'tahun' => $input_post['tahun'],
				'tgl_bayar' => date('Y-m-d', strtotime($input_post['tgl_bayar'])),
				'pejabat1_id' => $input_post['pejabat1_id'],
				'pejabat2_id' => $input_post['pejabat2_id'],
				// 'tgl_posting' => date('Y-m-d', strtotime($input_post['tgl_posting'])),
				// 'status_posting' => $input_post['status_posting'],
			);
            $this->dph_model->update($this->input->post('id'), $post_data);
			
			// data  detail
			$payment = $this->input->post('dtDetail');
			$tambahan_data2 = array();

			if(isset($payment)) {
				$i = 1;
				$payment = json_decode($payment, true);
				
				//hapus dulu disini
				$this->db->delete('dph_payment', array('dph_id' => $input_post['id'])); 
				if((int)count($payment['dtDetail']) > 0){
					$rd_row = array();
					foreach($payment['dtDetail'] as $rows) {
						$rd_row = array (							
							'dph_id' => $input_post['id'],
							'kd_propinsi' => KD_PROPINSI,
							'kd_dati2' => KD_DATI2,
							'kd_kecamatan' => $rows[7],
							'kd_kelurahan' => $rows[8],
							'kd_blok' => $rows[9],
							'no_urut' => $rows[10],
							'kd_jns_op' => $rows[11],
							'thn_pajak_sppt' => $rows[12],
							'pembayaran_ke' => $rows[13],
							'denda' => $rows[14],
							'jml_yg_dibayar' => $rows[15],
							'tgl_rekam_byr' => $rows[16],
							'nip_rekam_byr' => $rows[17],
						);
						$i++;
						$tambahan_data2 = array_merge($tambahan_data2, array($rd_row));
					}
					
					//baru simpan disini
					$this->db->insert_batch('dph_payment', $tambahan_data2);
				}
			}
			
			$this->session->set_flashdata('msg_success', 'Data telah disimpan');
			redirect(active_module_url().$this->controller.'?kec_kd='.$input_post['kd_kecamatan'].'&kel_kd='.$input_post['kd_kelurahan']);
		}
		
		$data['kec_kd'] = $input_post['kd_kecamatan'];
		$data['kel_kd'] = $input_post['kd_kelurahan'];
		$data['users']  = $this->dph_model->get_user_pbbms();
		
		$this->load->model('kecModel', 'kec');
		$this->load->model('kelModel', 'kel');
		$data['kecamatan']   = $this->kec->getRecord('000');
		$data['kelurahan']   = $this->kel->getRecord($data['kec_kd'], $data['kel_kd']);
		
		$this->load->view('vdph_form',$data);
	}
	
	public function delete() {
		if(!$this->module_auth->delete) {
			$this->session->set_flashdata('msg_warning', $this->module_auth->msg_delete);
			redirect(active_module_url($this->controller));
		}
		
		$id = $this->uri->segment(4);
		if($this->dph_model->cek_stat($id)) {
			$this->session->set_flashdata('msg_warning', 'Data ini sudah diposting, tidak dapat di hapus.');
			redirect(active_module_url('dph'));
		}
		
		if($id && $this->dph_model->get($id)) {
			$this->db->delete('dph_payment', array('dph_id' => $id)); 
			
			$this->dph_model->delete($id);
			$this->session->set_flashdata('msg_success', 'Data telah dihapus');
			redirect(active_module_url($this->controller));
		} else {
			show_404();
		}
	}
	
	public function posting() {
		header("Content-type: text/plain"); 
		header("Cache-Control: no-store, no-cache"); 
		header('Content-Disposition: attachment; filename="dph.csv"'); 
		
		$dph_id = $this->input->post('download');

		if(!empty($dph_id) && $this->dph_model->get($dph_id)) {
			if($rows = $this->dph_model->get_detail($dph_id)){
				$this->dph_model->update_stat($dph_id);
				
				$title = array('kd_propinsi', 'kd_dati2', 'kd_kecamatan', 'kd_kelurahan', 'kd_blok', 'no_urut', 'kd_jns_op', 'thn_pajak_sppt', 'pembayaran_ke', 'denda', 'jml_yg_dibayar');
				$this->csv_encode( $rows, $title ); 
			} else {
				echo "Tidak ada data";
			}
			exit;
		}
	}
	
	public function posting_old() {
		header("Content-type: text/plain"); 
		header("Cache-Control: no-store, no-cache"); 
		header('Content-Disposition: attachment; filename="dph.csv"'); 
		
		$data = $this->input->get_post('download');

		if(isset($data)) {
			$data = json_decode($data, true);
			$this->csv_encode( $data['dtTable'] ); 
			exit;
			
			/* 
			$i = 1;
			$output = array();
			if(count($data['dtTable']) > 0){
				$rd_row = array();
				foreach($data['dtTable'] as $rows) {
					$rd_row = array (							
						'Kode' => $rows[1],
						'Uraian' => $rows[2],
						'Tanggal' => $rows[3],
						'Pokok' => $rows[4],
						'Denda' => $rows[5],
						'Bayar' => $rows[6],
						'Posting' => $rows[7],
					);
					$i++;
					$output = array_merge($output, array($rd_row));
				}
				
				
				echo $this->csv_encode( $output ); 
				// echo csv_encode( $output, $aColumns ); 
			}
			echo $this->csv_encode( $data['dtTable'] ); 
			*/
		}
	}
	
	function csv_encode($aaData, $aHeaders = NULL) {
		// output headers
		if ($aHeaders) echo implode('|', $aHeaders ) . "\r\n";

		foreach ($aaData as $aRow) {
			echo implode('|', $aRow) . "\r\n";
		}
	}
}
