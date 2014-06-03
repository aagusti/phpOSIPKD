<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Berkas_in extends CI_Controller {

    private $isppat = FALSE;
    private $ppatkd = '';
    private $ppatnm = '';
    private $ppatid = 0;
	
	function __construct() {
		parent::__construct();
		if(!$this->session->userdata('login')) {
			$this->session->set_flashdata('msg_warning', 'Session telah kadaluarsa, silahkan login ulang.');
			redirect('login');
			exit;
		}
		
		$module = 'pelayanan';
		$this->load->library('module_auth',array('module'=>$module));

		$this->load->model(array('apps_model'));
        $this->load->model(array('berkas_in_model'));
        $this->load->model(array('ppat_user_model', 'ppat_model', 'bphtb_model', 'sspd_model'));
		
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
	}

	public function index() {
		if(!$this->module_auth->read) {
			$this->session->set_flashdata('msg_warning', $this->module_auth->msg_read);
			redirect(active_module_url(''));
		}

		$data['current'] = 'pelayanan';
		$data['apps']    = $this->apps_model->get_active_only();
		$this->load->view('vberkas_in', $data);
	}
	
	function grid() {
		$i=0;
		$responce = new stdClass();
		if($query = $this->berkas_in_model->grid()) {
			foreach($query as $row) {
				$no_berkas = $row->tahun.".".str_pad($row->kode, 2, "0", STR_PAD_LEFT).".".str_pad($row->no_urut, 6, "0", STR_PAD_LEFT);
				$ppat      = $this->ppat_model->get($row->ppat_id);
				
				$responce->aaData[$i][] = $row->id;
				$responce->aaData[$i][] = '';
				$responce->aaData[$i][] = $no_berkas;
				$responce->aaData[$i][] = $row->pengirim;
				$responce->aaData[$i][] = number_format($row->jml_berkas, 0, ',', '.');
				$responce->aaData[$i][] = date('d-m-Y', strtotime($row->tgl_terima));
				$responce->aaData[$i][] = date('d-m-Y', strtotime($row->tgl_selesai));
				$responce->aaData[$i][] = $ppat->nama;
				$responce->aaData[$i][] = $row->notes;
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


	function grid_detail() {
		$responce = new stdClass();
        $berkas_in_id = $this->uri->segment(4);
		
		if(empty($berkas_in_id)) {
			$responce->sEcho=1;
			$responce->iTotalRecords="0";
			$responce->iTotalDisplayRecords="0";
			$responce->aaData=array();	
			echo json_encode($responce);
			exit;
		}
		
		$i=0;
		$query = $this->db->query("select * from bphtb_berkas_in_det where berkas_in_id={$berkas_in_id}")->result();
		if($query) {
			foreach($query as $row) {
				$nop_thn = $row->kd_propinsi.'.'.$row->kd_dati2.'.'.$row->kd_kecamatan.'.'.$row->kd_kelurahan.'.'.$row->kd_blok.'.'.$row->no_urut.'.'.$row->kd_jns_op.'-'.$row->thn_pajak_sppt;
				$no_sspd = str_pad($row->sspd_no, 6, "0", STR_PAD_LEFT);

				$responce->aaData[$i][] = '';
				$responce->aaData[$i][] = $row->id;
				$responce->aaData[$i][] = $nop_thn;
				$responce->aaData[$i][] = number_format($row->nominal, 0, ',', '.');
				$responce->aaData[$i][] = $no_sspd;
				$responce->aaData[$i][] = '<a class="delete" href="">Hapus</a>';
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
		
	function grid_sspd() {
		$i=0;
		$responce = new stdClass();
		
		$query = $this->db->query("select * from bphtb_sspd")->result();
		if($query) {
			foreach($query as $row) {
				$nop_thn = $row->kd_propinsi.'.'.$row->kd_dati2.'.'.$row->kd_kecamatan.'.'.$row->kd_kelurahan.'.'.$row->kd_blok.'.'.$row->no_urut.'.'.$row->kd_jns_op.'-'.$row->thn_pajak_sppt;
				$no_sspd = str_pad($row->no_sspd	, 6, "0", STR_PAD_LEFT);

				$responce->aaData[$i][] = $row->id;
				$responce->aaData[$i][] = $nop_thn;
				$responce->aaData[$i][] = number_format($row->bphtb_harus_dibayarkan, 0, ',', '.');
				$responce->aaData[$i][] = $no_sspd;
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

	//admin
	private function fvalidation() {
		$this->form_validation->set_error_delimiters('<span>', '</span>');
		$this->form_validation->set_rules('tgl_terima','Tgl. Terima','required');
		$this->form_validation->set_rules('tgl_selesai','Tgl. Selesai','required');
		$this->form_validation->set_rules('pengirim','Pengirim','required|trim');
		$this->form_validation->set_rules('ppat_id','PPAT','required|numeric'); 
	}
	
	private function fpost() {
        $data['id'] = $this->input->post('id');
		$data['tahun'] = $this->input->post('tahun');
		$data['kode'] = $this->input->post('kode');
		$data['no_urut'] = $this->input->post('no_urut');
		$data['tgl_terima'] = $this->input->post('tgl_terima');
		$data['tgl_selesai'] = $this->input->post('tgl_selesai');
		$data['notes'] = $this->input->post('notes');
		$data['pengirim'] = $this->input->post('pengirim');
		$data['create_uid'] = $this->input->post('create_uid');
		$data['update_uid'] = $this->input->post('update_uid');
		$data['created'] = $this->input->post('created');
		$data['updated'] = $this->input->post('updated');
		$data['ppat_id'] = $this->input->post('ppat_id');
		
		return $data;
	}
	
	public function add() {
		if(!$this->module_auth->create) {
			$this->session->set_flashdata('msg_warning', $this->module_auth->msg_create);
			redirect(active_module_url($this->uri->segment(2)));
		}
		$data['current']     = 'pelayanan';
		$data['faction']     = active_module_url($this->uri->segment(2).'/add');
		$data['apps']        = $this->apps_model->get_active_only();
		$data['dt']          = $this->fpost();
		
        $data['ppat']   = $this->bphtb_model->get_ppat();
        $data['isppat'] = $this->isppat;
        $data['ppatid'] = $this->ppatid;
        $data['ppatnm'] = $this->ppatnm;
		
		$this->fvalidation();
		if ($this->form_validation->run() == TRUE) {
			$input_post = $this->fpost();
			$tahun   = empty($input_post['tahun']) ? date('Y') : $input_post['tahun'];
			$kode    = '1';
			$no_urut = $this->berkas_in_model->no_urut($tahun, $kode);
			$post_data = array(
				'tahun' => $tahun,
				'kode' => $kode,
				'no_urut' => (int) $no_urut,
				'tgl_terima' => empty($input_post['tgl_terima']) ? NULL : date('Y-m-d', strtotime($input_post['tgl_terima'])),
				'tgl_selesai' => empty($input_post['tgl_selesai']) ? NULL : date('Y-m-d', strtotime($input_post['tgl_selesai'])),
				'notes' => empty($input_post['notes']) ? NULL : $input_post['notes'],
				'pengirim' => empty($input_post['pengirim']) ? NULL : $input_post['pengirim'],
				'ppat_id' => empty($input_post['ppat_id']) ? NULL : $input_post['ppat_id'],
				'create_uid' => $this->session->userdata('uid'),
				'created' => date('Y-m-d'),
			);
			$berkas_in_id = $this->berkas_in_model->save($post_data);
			
			// data  detail
			$data_dtl = $this->input->post('dtDetail');
			$tambahan_data2 = array();

			if(isset($data_dtl)) {
				$i = 1;
				$data_dtl = json_decode($data_dtl, true);
				
				//hapus dulu disini
				$this->db->delete('bphtb_berkas_in_det', array('berkas_in_id' => $berkas_in_id)); 
				if(count($data_dtl['dtDetail']) > 0){
					$rd_row = array();
					foreach($data_dtl['dtDetail'] as $rows) {
						$rd_row = array (							
							'berkas_in_id'   => (int) $berkas_in_id,
							'sspd_id'        => (int) $rows[1], 
							'sspd_no'  => (int) substr($rows[4],0 ,6),  
							
							'kd_propinsi'    => substr($rows[2],0 ,2), //32.03.030.001.001.0205.0-2012
							'kd_dati2'       => substr($rows[2],3 ,2),
							'kd_kecamatan'   => substr($rows[2],6 ,3),
							'kd_kelurahan'   => substr($rows[2],10,3),
							'kd_blok'        => substr($rows[2],14,3),
							'no_urut'        => substr($rows[2],18,4),
							'kd_jns_op'      => substr($rows[2],23,1),
							'thn_pajak_sppt' => substr($rows[2],25,4),
							'nominal'        => (float) str_replace('.','', $rows[3]),
						);
						$i++;
						$tambahan_data2 = array_merge($tambahan_data2, array($rd_row));
					}
					
					//langsung ajah dah - sementara
					$this->db->insert_batch('bphtb_berkas_in_det', $tambahan_data2);
				}
			}
			
			$this->session->set_flashdata('msg_success', 'Data telah disimpan');		
			redirect(active_module_url($this->uri->segment(2)));
		}
		$this->load->view('vberkas_in_form',$data);
	}
	
	public function edit() {
		if(!$this->module_auth->update) {
			$this->session->set_flashdata('msg_warning', $this->module_auth->msg_update);
			redirect(active_module_url($this->uri->segment(2)));
		}
		$data['current']   = 'pelayanan';
		$data['faction']   = active_module_url($this->uri->segment(2).'/update');
		$data['apps']      = $this->apps_model->get_active_only();
			
        $data['ppat']   = $this->bphtb_model->get_ppat();
        $data['isppat'] = $this->isppat;
        $data['ppatid'] = $this->ppatid;
        $data['ppatnm'] = $this->ppatnm;
		
		$id = $this->uri->segment(4);
		if($id && $get = $this->berkas_in_model->get($id)) {
			$data['dt']['id'] = empty($get->id) ? NULL : $get->id;
			$data['dt']['tahun'] = empty($get->tahun) ? date('Y') : $get->tahun;
			$data['dt']['kode'] = str_pad($get->kode, 2, "0", STR_PAD_LEFT);
			$data['dt']['no_urut'] = str_pad($get->no_urut, 6, "0", STR_PAD_LEFT);
			$data['dt']['tgl_terima'] = empty($get->tgl_terima) ? NULL : date('d-m-Y', strtotime($get->tgl_terima));
			$data['dt']['tgl_selesai'] = empty($get->tgl_selesai) ? NULL : date('d-m-Y', strtotime($get->tgl_selesai));
			$data['dt']['notes'] = empty($get->notes) ? NULL : $get->notes;
			$data['dt']['pengirim'] = empty($get->pengirim) ? NULL : $get->pengirim;
			$data['dt']['create_uid'] = empty($get->create_uid) ? NULL : $get->create_uid;
			$data['dt']['update_uid'] = empty($get->update_uid) ? NULL : $get->update_uid;
			$data['dt']['created'] = empty($get->created) ? NULL : date('d-m-Y', strtotime($get->created));
			$data['dt']['updated'] = empty($get->updated) ? NULL : date('d-m-Y', strtotime($get->updated));
			$data['dt']['ppat_id'] = empty($get->ppat_id) ? NULL : $get->ppat_id;
			
			$this->load->view('vberkas_in_form',$data);
		} else {
			show_404();
		}
	}
	
	public function update() {
		if(!$this->module_auth->update) {
			$this->session->set_flashdata('msg_warning', $this->module_auth->msg_update);
			redirect(active_module_url($this->uri->segment(2)));
		}
		$data['current'] = 'pelayanan';
		$data['faction'] = active_module_url($this->uri->segment(2).'/update');
		$data['apps']    = $this->apps_model->get_active_only();
		$data['dt'] = $this->fpost();
			
        $data['ppat']   = $this->bphtb_model->get_ppat();
        $data['isppat'] = $this->isppat;
        $data['ppatid'] = $this->ppatid;
        $data['ppatnm'] = $this->ppatnm;
			
		$this->fvalidation();
		if ($this->form_validation->run() == TRUE) {
			$input_post = $this->fpost();
			$post_data = array(
				'tgl_terima' => empty($input_post['tgl_terima']) ? NULL : date('Y-m-d', strtotime($input_post['tgl_terima'])),
				'tgl_selesai' => empty($input_post['tgl_selesai']) ? NULL : date('Y-m-d', strtotime($input_post['tgl_selesai'])),
				'notes' => empty($input_post['notes']) ? NULL : $input_post['notes'],
				'pengirim' => empty($input_post['pengirim']) ? NULL : $input_post['pengirim'],
				'ppat_id' => empty($input_post['ppat_id']) ? NULL : $input_post['ppat_id'],
				'update_uid' => $this->session->userdata('uid'),
				'updated' => date('Y-m-d'),
			);
			$this->berkas_in_model->update($input_post['id'], $post_data);
			
			$berkas_in_id = $input_post['id'];
			
			// data  detail
			$data_dtl = $this->input->post('dtDetail');
			$tambahan_data2 = array();

			if(isset($data_dtl)) {
				$i = 1;
				$data_dtl = json_decode($data_dtl, true);
				
				//hapus dulu disini
				$this->db->delete('bphtb_berkas_in_det', array('berkas_in_id' => $input_post['id'])); 
				if(count($data_dtl['dtDetail']) > 0){
					$rd_row = array();
					foreach($data_dtl['dtDetail'] as $rows) {
						$rd_row = array (							
							'berkas_in_id'   => (int) $berkas_in_id,
							'sspd_id'        => (int) $rows[1], 
							'sspd_no'  => (int) substr($rows[4],0 ,6),  
							
							'kd_propinsi'    => substr($rows[2],0 ,2), //32.03.030.001.001.0205.0-2012
							'kd_dati2'       => substr($rows[2],3 ,2),
							'kd_kecamatan'   => substr($rows[2],6 ,3),
							'kd_kelurahan'   => substr($rows[2],10,3),
							'kd_blok'        => substr($rows[2],14,3),
							'no_urut'        => substr($rows[2],18,4),
							'kd_jns_op'      => substr($rows[2],23,1),
							'thn_pajak_sppt' => substr($rows[2],25,4),
							'nominal'        => (float) str_replace('.','', $rows[3]),
						);
						$i++;
						$tambahan_data2 = array_merge($tambahan_data2, array($rd_row));
					}
					
					//langsung ajah dah - sementara
					$this->db->insert_batch('bphtb_berkas_in_det', $tambahan_data2);
				}
			}
			
			$this->session->set_flashdata('msg_success', 'Data telah disimpan');
			redirect(active_module_url($this->uri->segment(2)));
		}
		$this->load->view('vberkas_in_form',$data);
	}
	
	public function delete() {
		if(!$this->module_auth->delete) {
			$this->session->set_flashdata('msg_warning', $this->module_auth->msg_delete);
			redirect(active_module_url($this->uri->segment(2)));
		}
		
		$id = $this->uri->segment(4);
		if($id && $this->berkas_in_model->get($id)) {
			$this->db->delete('bphtb_berkas_in_det', array('berkas_in_id' => $id)); 
			
			$this->berkas_in_model->delete($id);
			$this->session->set_flashdata('msg_success', 'Data telah dihapus');
			redirect(active_module_url($this->uri->segment(2)));
		} else {
			show_404();
		}
	}
	
	/* laporan */
    function lap() {
        if (!$this->module_auth->read) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_read);
            redirect(active_module_url(''));
        }
        
        $data['apps']      = $this->apps_model->get_active_only();
		$data['current']   = 'pelayanan';
        $data['judul_lap'] = 'Laporan Register Berkas Masuk';
        $data['rpt']       = "berkas_masuk";
		
		$tglawal  = date('d-m-Y');
		$tglakhir = date('d-m-Y');
        $data['tglawal']  = $tglawal;
        $data['tglakhir'] = $tglakhir;
		
        $this->load->view('vlap_berkas', $data);
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
        $rptx = $this->uri->segment(5);
        $tglm = $this->uri->segment(6);
        $tgls = $this->uri->segment(7);
		
        $tglm = substr($tglm, 6, 4) . '-' . substr($tglm, 3, 2) . '-' . substr($tglm, 0, 2);
        $tgls = substr($tgls, 6, 4) . '-' . substr($tgls, 3, 2) . '-' . substr($tgls, 0, 2);
	
		$jasper = $this->load->library('Jasper');
		$params = array(
			"startdate" => "{$tglm}",
			"enddate" => "{$tgls}",
			"logo" => base_url("assets/img/logorpt__.jpg"),
		);
		echo $jasper->cetak($rptx, $params, $type);
	}
}