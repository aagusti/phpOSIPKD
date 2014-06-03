<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sspd extends CI_Controller {

	private $module = 'ppat';
	private $controller = 'sspd';
	private $current = 'ppat';
	
    private $info = FALSE;
    private $fields = FALSE;
    private $isppat = FALSE;
    private $ppatkd = '';
    private $ppatnm = '';
    private $ppatid = 0;
    
    private $my_listNumeric_Field = array('npop', 'npoptkp', 'tarif', 'terhutang', 'tarif_pengurang', 
                'pengurang', 'bphtb_sudah_dibayarkan', 'denda', 'bagian', 'pembagi', 'bphtb_harus_dibayarkan',
                'bumi_luas', 'bumi_njop', 'bng_luas', 'bng_njop', 'njop');
    
	function __construct() {
		parent::__construct();
		if(!$this->session->userdata('login')) {
			$this->session->set_flashdata('msg_warning', 'Session telah kadaluarsa, silahkan login ulang.');
			redirect('login');
			exit;
		}
		
		if ($this->uri->segment(2) == 'sspd_validasi') {
			$this->module = 'pelayanan';
			$this->controller = 'sspd_validasi';
			$this->current = 'pelayanan';
		}

		$this->load->model(array('apps_model'));
        $this->load->model(array('bphtb_model', 'sspd_model', 'ppat_model', 'ppat_user_model'));
        
        $this->fields = $this->bphtb_model->fields_info('bphtb_sspd');
        $this->info = ($this->fields)?TRUE:FALSE;
        
       
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
        $data['info']   = $this->info;
        $data['fields'] = $this->fields;
        
		$this->load->view('vsspd', $data);
	}
	
	function grid() {
		$i=0;
		$sts = $this->uri->segment(4);
        $sts = ($sts!='') ? $sts : 0;
        /* 
        if ($sts==1) {
            $query = $this->sspd_model->get_proses($this->isppat, $this->ppatid);
        } elseif ($sts==2) {
            $query = $this->sspd_model->get_selesai($this->isppat, $this->ppatid);
        } else {
            $query = $this->sspd_model->get_daftar($this->isppat, $this->ppatid);
        }
		 */
        
        if ($sts==2) {
            $query = $this->sspd_model->get_selesai($this->isppat, $this->ppatid);
        } else {
			if ($this->controller != 'sspd')
				$query = $this->sspd_model->get_daftar_sspd_validasi($this->isppat, $this->ppatid);
			else
				$query = $this->sspd_model->get_daftar_sspd($this->isppat, $this->ppatid);
        }
		
        if($query) {
			foreach($query as $row) {
				$responce->aaData[$i][]=$row->id;
                $responce->aaData[$i][]='';
                $nomor = (string)$row->no_sspd;
                // $nomor = '000000' . $nomor;
                // $nomor = substr($nomor, strlen($nomor) - 6, 6);
                $nomor = str_pad($nomor, 6, "0", STR_PAD_LEFT);
                $responce->aaData[$i][]=$row->tahun . '.' . str_pad($row->kode, 2, "0", STR_PAD_LEFT) . '.' . $nomor; 
				/* 
                if ($sts==2) {
                    if ($row->sspd_approval_id) {
                        $nomor = (string)$row->approval_no_urut;
                        // $nomor = '000000' . $nomor;
                        // $nomor = substr($nomor, strlen($nomor) - 6, 6);
						$nomor = str_pad($nomor, 6, "0", STR_PAD_LEFT);
                        $responce->aaData[$i][]=$row->approval_tahun . '.' . $row->approval_kode . '.' . $nomor; 
                    } else {
                        $responce->aaData[$i][]='-';
                    }
                }
				 */
                $responce->aaData[$i][]=$row->wp_nama;
                $responce->aaData[$i][]=$row->nomor_op;
                $responce->aaData[$i][]=$row->thn_pajak_sppt;
                $responce->aaData[$i][]=number_format($row->bphtb_harus_dibayarkan,0,',','.');
				
                $responce->aaData[$i][]=$row->status_pembayaran;
                if ($sts==2) 
                    $responce->aaData[$i][]=($row->sspd_tgl_approval)?date('d/m/Y',strtotime($row->sspd_tgl_approval)):'';
                else
					$responce->aaData[$i][]=date('d/m/Y',strtotime($row->tgl_transaksi));
				
				
                $responce->aaData[$i][]=$row->nm_ppat;
                $responce->aaData[$i][]=$row->kd_ppat;
				
                if ($row->bpn_tgl_selesai) {
                    $status = 'Selesai'; // 7;
                } else {
                    if ($row->bpn || $row->berkas_masuk) {
                        $status = 'BPN'; // 6; 
                    } else {
                        if ($row->status_pembayaran) {
                            $status = 'Dibayar'; //5;
                        } else {
                            if ($row->sspd_approval_id) {
                                $status = 'Disetujui';   //4; 'Disetujui / diterima daftar sspd oleh DPPKAD';  
                                // $status = 'Disetujui / diterima DPPKAD';   //4; 'Disetujui / diterima daftar sspd oleh DPPKAD';  
                            } else {
                                if ($row->status_daftar>0) {                   // 0 = '', 1 = prosesing, 2 = tolak, 3 = terima
                                    // $status = $row->status_daftar + 1;      // karena kode status = 1 digunakan oleh Printed, maka ditambahkan 1
                                    $status = $row->status_daftar + 1;
									if ($status == 1) $status = 'Proses';
									else if ($status == 2) $status = 'Tolak';
									else if ($status == 3) $status = 'Terima';
									else $status = '';
                                } else {
                                    if ($row->tgl_print) {
                                        $status = 'Printed'; // 1; 
                                    } else {
                                        $status = '-'; //0;
                                    }
                                }
                            }
                        }
                    }
                }
                $responce->aaData[$i][]=$status;
				$responce->aaData[$i][]=$row->keterangan;
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
        
    function format_date_error() {
        return '<blockquote><strong>Format Tanggal Transaksi salah :</strong>' .
                        '<small style="background-color: #ffe;">' .
                            '<span style="color: red;">Format Tanggal harus dd/mm/yyyy atau dd-mm-yyyy atau dd.mm.yyyy' .
                            ' dengan d = tanggal, m = bulan dan y = tahun</span>' .
                        '</small>' .
                    '</blockquote>';
    }
    
    private function upload_my_file($data) {
        $kode = $data['tahun'] . '.' . $data['kode'];
        $nomor = $data['no_sspd'];
        // $nomor = '000000' . $nomor;
        // $nomor = substr($nomor, strlen($nomor) - 6, 6);
		$nomor = str_pad($nomor, 6, "0", STR_PAD_LEFT);
        $kode .= '.' . $nomor;
        for ($i=1; $i<=10; $i++) {
            $file = $this->bphtb_model->upload_sspd_file($kode . '-doc' . (string)$i, 'attach' . (string)$i, $data['file' . (string)$i]);
            if ($file!='') {
                $data['file' . (string)$i] = $file;
            }
        }
        return $data;
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
        
		return $data;
	}
	
	public function add() {
		$this->load_auth();
		if(!$this->module_auth->create) {
			$this->session->set_flashdata('msg_warning', $this->module_auth->msg_create);
			redirect(active_module_url($this->controller));
		}
		$data['current']        = $this->current;
        $data['apps']    = $this->apps_model->get_active_only();
		$data['faction']        = active_module_url($this->controller.'/add');
        $data['ppat']           = $this->bphtb_model->get_ppat();
        
        $data['perolehan']      = $this->bphtb_model->get_perolehan();
        $data['dasar']          = $this->bphtb_model->get_dasar_perhitungan();
        
        $data['info']   = $this->info;
        $data['fields'] = $this->fields;
        $data['mode'] = 'add';
        $data['isppat'] = $this->isppat;
        $data['ppatid'] = $this->ppatid;
        $data['ppatnm'] = $this->ppatnm;
        
		$data['dt']             = $this->fpost();
        
        $data['error_date_format'] = '';
        $tgl = $this->bphtb_model->date_validation($this->input->post('tgl_transaksi'));
		
		$this->fvalidation();
		if ($this->form_validation->run() == TRUE) {
            if ($tgl=='') {
                $data['error_date_format'] = $this->format_date_error();
            } else {
				$sspd_kode = $this->bphtb_model->get_new_sspd_kode(date('Y'), '1');
                $tahun = $sspd_kode[0];
                $kode = $sspd_kode[1];
                $no_sspd = $sspd_kode[2];
                
                $dat = array(
                    'tahun' => $tahun, //$this->input->post('tahun'),
                    'kode' => $kode, //$this->input->post('kode'),
                    'no_sspd' => $no_sspd, //$this->input->post('no_sspd'),
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
                    'wp_identitaskd' => 'KTP', //$this->input->post('wp_identitaskd'),
                    'wp_kdpos' => strtoupper($this->input->post('wp_kdpos')),
                    'tgl_transaksi' => $tgl, //$this->input->post('tgl_transaksi'),
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
                    'restitusi' => 0, //$this->input->post('restitusi'),
                    'bphtb_harus_dibayarkan' => $this->input->post('bphtb_harus_dibayarkan'),
                    //'status_pembayaran' => ($this->input->post('status_pembayaran')=='on'?1:0),
                    'keterangan' => $this->input->post('keterangan'),
                    'dasar_id' => $this->input->post('dasar_id'),
                    'header_id' => $this->input->post('header_id')?$this->input->post('header_id'):0,
                    
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
                    
                    'created' => date('Y-m-d h:m:s'),
                    //'create_uid' => $this->session->userdata('username')
                    'create_uid' => $this->session->userdata('uid')             // diganti, karena kalau mengambil dari username, panjang field tidak memadai
                );
                
                $dat = $this->bphtb_model->set_field_number_value($this->my_listNumeric_Field, $dat);
                
                $dat = $this->upload_my_file($dat);
                
                $this->sspd_model->save($dat);
                
                $this->session->set_flashdata('msg_success', 'Data telah disimpan');		
                redirect(active_module_url($this->controller));
            }
        }
        $data['dt']['tgl_transaksi'] = ($this->input->post('tgl_transaksi'))?$this->input->post('tgl_transaksi'):date('d/m/Y');
        $data['dt']['kd_propinsi'] = ($this->input->post('kd_propinsi'))?$this->input->post('kd_propinsi'):KD_PROPINSI;
        $data['dt']['kd_dati2'] = ($this->input->post('kd_dati2'))?$this->input->post('kd_dati2'):KD_DATI2;
        $data['dt']['tarif'] = ($this->input->post('tarif')>-1)?$this->input->post('tarif'):5;
        $data['dt']['tarif_pengurang'] = ($this->input->post('tarif_pengurang')>-1)?$this->input->post('tarif_pengurang'):0;
        
        $data['dt'] = $this->bphtb_model->set_field_number_value($this->my_listNumeric_Field, $data['dt']);
        
        $data['dt']['nm_wp_sppt'] = '';
        $data['dt']['jln_wp_sppt'] = '';
        $data['dt']['blok_kav_no_wp_sppt'] = '';
        $data['dt']['rw_wp_sppt'] = '';
        $data['dt']['rt_wp_sppt'] = '';
        $data['dt']['kelurahan_wp_sppt'] = '';
        $data['dt']['kota_wp_sppt'] = '';
        $data['dt']['kd_pos_wp_sppt'] = '';
        $data['dt']['npwp_sppt'] = '';
        
		$this->load->view('form_contents/vsspd_form',$data);
	}
	
	public function edit() {
		$this->load_auth();
		if(!$this->module_auth->update) {
			$this->session->set_flashdata('msg_warning', $this->module_auth->msg_update);
			redirect(active_module_url($this->controller));
		}
		$data['current']        = $this->current;
        $data['apps']    = $this->apps_model->get_active_only();
        
		$data['faction']        = active_module_url($this->controller.'/update');
        $data['ppat']           = $this->bphtb_model->get_ppat();
      
        $data['perolehan']      = $this->bphtb_model->get_perolehan();
        $data['dasar']          = $this->bphtb_model->get_dasar_perhitungan();
        
        $data['info']   = $this->info;
        $data['fields'] = $this->fields;
        $data['mode'] = 'edit';
        $data['isppat'] = $this->isppat;
        $data['ppatid'] = $this->ppatid;
        $data['ppatnm'] = $this->ppatnm;
        $data['error_date_format'] = '';
        	
		$id = (int)$this->uri->segment(4);
		if($id && $get = $this->sspd_model->get($id)) {
            //die($get->status_pembayaran);
            if (($get->tgl_approval!=null) || $get->status_pembayaran!=0) {
                $this->session->set_flashdata('msg_warning', 'SSPD sudah tidak bisa diedit');
                redirect(active_module_url($this->controller));
            }
			$data['dt']['id'] = $get->id;
			$data['dt']['tahun'] = $get->tahun;
            $data['dt']['kode'] = str_pad($get->kode, 2, "0", STR_PAD_LEFT);
            $data['dt']['no_sspd'] = str_pad($get->no_sspd, 6, "0", STR_PAD_LEFT);;
            $data['dt']['ppat_id'] = $get->ppat_id;
            $data['dt']['wp_nama'] = $get->wp_nama;
            $data['dt']['wp_npwp'] = $get->wp_npwp;
            $data['dt']['wp_alamat'] = $get->wp_alamat;
            $data['dt']['wp_blok_kav'] = $get->wp_blok_kav;
            $data['dt']['wp_kelurahan'] = $get->wp_kelurahan;
            $data['dt']['wp_rt'] = $get->wp_rt;
            $data['dt']['wp_rw'] = $get->wp_rw;
            $data['dt']['wp_kecamatan'] = $get->wp_kecamatan;
            $data['dt']['wp_kota'] = $get->wp_kota;
            $data['dt']['wp_provinsi'] = $get->wp_provinsi;
            $data['dt']['wp_identitas'] = $get->wp_identitas;
            $data['dt']['wp_identitaskd'] = $get->wp_identitaskd;
            $data['dt']['wp_kdpos'] = $get->wp_kdpos;
            $data['dt']['tgl_transaksi'] = date('d/m/Y', strtotime($get->tgl_transaksi));
            $data['dt']['kd_propinsi'] = $get->kd_propinsi;
            $data['dt']['kd_dati2'] = $get->kd_dati2;
            $data['dt']['kd_kecamatan'] = $get->kd_kecamatan;
            $data['dt']['kd_kelurahan'] = $get->kd_kelurahan;
            $data['dt']['kd_blok'] = $get->kd_blok;
            $data['dt']['no_urut'] = $get->no_urut;
            $data['dt']['kd_jns_op'] = $get->kd_jns_op;
            $data['dt']['thn_pajak_sppt'] = $get->thn_pajak_sppt;
            $data['dt']['op_alamat'] = $get->op_alamat;
            $data['dt']['op_blok_kav'] = $get->op_blok_kav;
            $data['dt']['op_rt'] = $get->op_rt;
            $data['dt']['op_rw'] = $get->op_rw;
            $data['dt']['bumi_luas'] = $get->bumi_luas;
            $data['dt']['bumi_njop'] = $get->bumi_njop;
            $data['dt']['bng_luas'] = $get->bng_luas;
            $data['dt']['bng_njop'] = $get->bng_njop;
            $data['dt']['no_sertifikat'] = $get->no_sertifikat;
            $data['dt']['njop'] = $get->njop;
            $data['dt']['perolehan_id'] = $get->perolehan_id;
            $data['dt']['npop'] = $get->npop;
            $data['dt']['npoptkp'] = $get->npoptkp;
            $data['dt']['tarif'] = $get->tarif;
            $data['dt']['terhutang'] = $get->terhutang;
            $data['dt']['bagian'] = $get->bagian;
            $data['dt']['pembagi'] = $get->pembagi;
            $data['dt']['tarif_pengurang'] = $get->tarif_pengurang;
            $data['dt']['pengurang'] = $get->pengurang;
            $data['dt']['bphtb_sudah_dibayarkan'] = $get->bphtb_sudah_dibayarkan;
            $data['dt']['denda'] = $get->denda;
            $data['dt']['restitusi'] = $get->restitusi;
            $data['dt']['bphtb_harus_dibayarkan'] = $get->bphtb_harus_dibayarkan;
            $data['dt']['status_pembayaran'] = $get->status_pembayaran;
            $data['dt']['dasar_id'] = $get->dasar_id;
            $data['dt']['header_id'] = $get->header_id;
            $data['dt']['keterangan'] = $get->keterangan;
                        
            $data['dt']['updated'] = $get->updated;
            $data['dt']['update_uid'] = $get->update_uid;
            
            $data['dt']['file1'] = $get->file1;
            $data['dt']['file2'] = $get->file2;
            $data['dt']['file3'] = $get->file3;
            $data['dt']['file4'] = $get->file4;
            $data['dt']['file5'] = $get->file5;
            $data['dt']['file6'] = $get->file6;
            $data['dt']['file7'] = $get->file7;
            $data['dt']['file8'] = $get->file8;
            $data['dt']['file9'] = $get->file9;
            $data['dt']['file10'] = $get->file10;
            
            $data['dt']['status_daftar'] = $get->status_daftar;
            
            $nop = $data['dt']['kd_propinsi'] . '.' . $data['dt']['kd_dati2'] . '.' . $data['dt']['kd_kecamatan'] . '.' . $data['dt']['kd_kelurahan'] .
                 '.' . $data['dt']['kd_blok'] . '.' . $data['dt']['no_urut'] . '.' . $data['dt']['kd_jns_op'];
                 
            $data['dt'] = $this->bphtb_model->set_field_number_value($this->my_listNumeric_Field, $data['dt']);
            
            $nop = $data['dt']['kd_propinsi'] . '.' . $data['dt']['kd_dati2'] . '.' . $data['dt']['kd_kecamatan'] . '.' .
                    $data['dt']['kd_kelurahan'] . '.' . $data['dt']['kd_blok'] . '.' . $data['dt']['no_urut'] . '.' .
                    $data['dt']['kd_jns_op'];
            $wp_before = $this->bphtb_model->get_op_sppt($nop, $data['dt']['thn_pajak_sppt']);
            
            if ($wp_before) {
                $datawp = (array)$wp_before;    
                $data['dt']['nm_wp_sppt'] = $datawp['nm_wp_sppt'];
                $data['dt']['jln_wp_sppt'] = $datawp['jln_wp_sppt'];
                $data['dt']['blok_kav_no_wp_sppt'] = $datawp['blok_kav_no_wp_sppt'];
                $data['dt']['rw_wp_sppt'] = $datawp['rw_wp_sppt'];
                $data['dt']['rt_wp_sppt'] = $datawp['rt_wp_sppt'];
                $data['dt']['kelurahan_wp_sppt'] = $datawp['kelurahan_wp_sppt'];
                $data['dt']['kota_wp_sppt'] = $datawp['kota_wp_sppt'];
                $data['dt']['kd_pos_wp_sppt'] = $datawp['kd_pos_wp_sppt'];
                $data['dt']['npwp_sppt'] = $datawp['npwp_sppt'];
            } else {
                $data['dt']['nm_wp_sppt'] = '';
                $data['dt']['jln_wp_sppt'] = '';
                $data['dt']['blok_kav_no_wp_sppt'] = '';
                $data['dt']['rw_wp_sppt'] = '';
                $data['dt']['rt_wp_sppt'] = '';
                $data['dt']['kelurahan_wp_sppt'] = '';
                $data['dt']['kota_wp_sppt'] = '';
                $data['dt']['kd_pos_wp_sppt'] = '';
                $data['dt']['npwp_sppt'] = '';
            }
			
            $this->load->view('form_contents/vsspd_form',$data);
		} else {
			show_404();
		}
	}
	
	public function update() {
		$this->load_auth();
		if(!$this->module_auth->update) {
			$this->session->set_flashdata('msg_warning', $this->module_auth->msg_update);
			redirect(active_module_url($this->controller));
		}
		$data['current']        = $this->current;
        $data['apps']    = $this->apps_model->get_active_only();
		$data['faction']        = active_module_url($this->controller.'/update');
        $data['ppat']           = $this->bphtb_model->get_ppat();

        $data['perolehan']      = $this->bphtb_model->get_perolehan();
        $data['dasar']          = $this->bphtb_model->get_dasar_perhitungan();
        
        $data['info']   = $this->info;
        $data['fields'] = $this->fields;
        $data['mode'] = 'edit';
        $data['isppat'] = $this->isppat;
        $data['ppatid'] = $this->ppatid;
        $data['ppatnm'] = $this->ppatnm;
        
		$data['dt']             = $this->fpost();
        
        $tgl = $this->bphtb_model->date_validation($this->input->post('tgl_transaksi'));
        $data['error_date_format'] = '';
        if ($tgl=='') {
            $data['error_date_format'] = $this->format_date_error();
        }
        
		$this->fvalidation();
		if ($this->form_validation->run() == TRUE && $tgl!='') {
            			
            $dat = array(
                //'id' => $this->input->post('id'),
				'tahun' => $this->input->post('tahun'),
				'kode' => (int)$this->input->post('kode'),
                'no_sspd' => (int)$this->input->post('no_sspd'),
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
                'tgl_transaksi' => $tgl, //$this->input->post('tgl_transaksi'),
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
                //'status_pembayaran' => ($this->input->post('status_pembayaran')=='on'?1:0),
                'dasar_id' => $this->input->post('dasar_id'),
                'header_id' => $this->input->post('header_id'),
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
            
            $dat = $this->bphtb_model->set_field_number_value($this->my_listNumeric_Field, $dat);
            
            $dat = $this->upload_my_file($dat);
            
            $this->sspd_model->update($this->input->post('id'), $dat);
			
			/* Validasi */
			$sspd_ditolak = $this->input->post('sspd_ditolak');
			if ($this->controller == 'sspd_validasi' && empty($sspd_ditolak)) {
				$kode = '1';
				$no_urut = $this->sspd_model->no_urut_validasi($this->input->post('tahun'), $kode);
				$data_validasi = array (
					'sspd_id' => $this->input->post('id'), 
					'tahun' => $this->input->post('tahun'), 
					'kode' =>  $kode, 
					'no_urut' => $no_urut, 
					'tgl_approval' => $tgl, 
					'create_uid' => $this->session->userdata('uid'),
				);
				$this->sspd_model->save_validasi($data_validasi);
			}
			
			$this->session->set_flashdata('msg_success', 'Data telah disimpan');
			redirect(active_module_url($this->controller));
		}
        
        $data['dt'] = $this->bphtb_model->set_field_number_value($this->my_listNumeric_Field, $data['dt']);
        
        $nop = $data['dt']['kd_propinsi'] . '.' . $data['dt']['kd_dati2'] . '.' . $data['dt']['kd_kecamatan'] . '.' .
                    $data['dt']['kd_kelurahan'] . '.' . $data['dt']['kd_blok'] . '.' . $data['dt']['no_urut'] . '.' .
                    $data['dt']['kd_jns_op'];
        $wp_before = $this->bphtb_model->get_op_sppt($nop, $data['dt']['thn_pajak_sppt']);
        
        if ($wp_before) {
            $datawp = (array)$wp_before;    
            $data['dt']['nm_wp_sppt'] = $datawp['nm_wp_sppt'];
            $data['dt']['jln_wp_sppt'] = $datawp['jln_wp_sppt'];
            $data['dt']['blok_kav_no_wp_sppt'] = $datawp['blok_kav_no_wp_sppt'];
            $data['dt']['rw_wp_sppt'] = $datawp['rw_wp_sppt'];
            $data['dt']['rt_wp_sppt'] = $datawp['rt_wp_sppt'];
            $data['dt']['kelurahan_wp_sppt'] = $datawp['kelurahan_wp_sppt'];
            $data['dt']['kota_wp_sppt'] = $datawp['kota_wp_sppt'];
            $data['dt']['kd_pos_wp_sppt'] = $datawp['kd_pos_wp_sppt'];
            $data['dt']['npwp_sppt'] = $datawp['npwp_sppt'];
        } else {
            $data['dt']['nm_wp_sppt'] = '';
            $data['dt']['jln_wp_sppt'] = '';
            $data['dt']['blok_kav_no_wp_sppt'] = '';
            $data['dt']['rw_wp_sppt'] = '';
            $data['dt']['rt_wp_sppt'] = '';
            $data['dt']['kelurahan_wp_sppt'] = '';
            $data['dt']['kota_wp_sppt'] = '';
            $data['dt']['kd_pos_wp_sppt'] = '';
            $data['dt']['npwp_sppt'] = '';
        }
        
		$this->load->view('form_contents/vsspd_form',$data);
	}
	
	public function delete() {
		$this->load_auth();
		if(!$this->module_auth->delete) {
			$this->session->set_flashdata('msg_warning', $this->module_auth->msg_delete);
			redirect(active_module_url($this->controller));
		}
		
		$id = $this->uri->segment(4);
		if($id && $this->sspd_model->get($id)) {
			$this->sspd_model->delete($id);
			$this->session->set_flashdata('msg_success', 'Data telah dihapus');
			redirect(active_module_url($this->controller));
		} else {
			show_404();
		}
	}
	
	/* Validasi */
	function validasi() {
		$this->edit();
	}
	
	/* Menu Laporan SSPD */
    public function register() {
		$this->load_auth();
        if(!$this->module_auth->read) {
			$this->session->set_flashdata('msg_warning', $this->module_auth->msg_read);
			redirect(active_module_url(''));
		}

		$data['current'] = $this->current;
		$data['apps']    = $this->apps_model->get_active_only();
        $data['info']   = $this->info;
        $data['fields'] = $this->fields;
        
        $data['tgl_from'] = date('d/m/Y', strtotime('last week'));
        $data['tgl_to'] = date('d/m/Y');
        
		$this->load->view('vsspd_register', $data);
    }
    
    function grid_register() {
		$i=0;
		if($query = $this->sspd_model->get_register($this->isppat, $this->ppatid)) {
			foreach($query as $row) {
				$responce->aaData[$i][]=$row->id;
                $responce->aaData[$i][]='';
                $nomor = (string)$row->no_sspd;
                // $nomor = '000000' . $nomor;
                // $nomor = substr($nomor, strlen($nomor) - 6, 6);
                $nomor = str_pad($nomor, 6, "0", STR_PAD_LEFT);
                $responce->aaData[$i][]=$row->tahun . '.' . str_pad($row->kode, 2, "0", STR_PAD_LEFT) . '.' . $nomor; 
                $responce->aaData[$i][]=$row->wp_nama;
                $responce->aaData[$i][]=$row->nomor_op;
                $responce->aaData[$i][]=number_format($row->bphtb_harus_dibayarkan,0,',','.');
                $responce->aaData[$i][]=$row->thn_pajak_sppt;
                if ($row->bpn_tgl_selesai) {
                    $status = 7; //'Selesai'; 
                } else {
                    if ($row->bpn || $row->berkas_masuk) {
                        $status = 6; //'BPN'; 
                    } else {
                        if ($row->status_pembayaran) {
                            $status = 5; //'Dibayar';  
                        } else {
                            if ($row->sspd_approval_id) {
                                $status = 4; //'Disetujui / diterima daftar sspd oleh DPPKAD';  
                            } else {
                                if ($row->status_daftar>0) {                // 0 = '', 1 = prosesing, 2 = tolak, 3 = terima
                                    $status = $row->status_daftar + 1;      // karena kode status = 1 digunakan oleh Printed, maka ditambahkan 1
                                } else {
                                    if ($row->tgl_print) {
                                        $status = 1; //'Printed';   
                                    } else {
                                        $status = 0; //'-';    
                                    }
                                }
                            }
                        }
                    }
                }
                $responce->aaData[$i][]=$status;
                $responce->aaData[$i][]=$row->status_pembayaran;
                $responce->aaData[$i][]=$row->nm_ppat;
                $responce->aaData[$i][]=$row->kd_ppat;
                $responce->aaData[$i][]=date('Y/m/d', strtotime($row->tgl_transaksi));
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
    
	
    /* 
    public function karcis() {
        $id = $this->uri->segment(4)?$this->uri->segment(4):0;
        if ($id && $get = $this->sspd_model->get($id)) {
            $where = ' where bphtb_sspd.id=' . $id;
            $data['rpt_file'] = 'bphtb_sspd_karcis';
            $terbilang = "(" . terbilang($get->bphtb_harus_dibayarkan, 3) . " Rupiah)";
            $data['parameters'] = (object) array('kondisi' => $where, 'terbilang' => $terbilang);
            $data['id'] = $id;
            $data['pdf'] = 'pdf';
            $this->load->view('vreports', $data);
        } else {
            show_404();
        }
    }
    function report_register() {
        $from = $this->uri->segment(4);
        $to = $this->uri->segment(5);
        $src = $this->uri->segment(6);
        
        $from = $this->bphtb_model->date_validation($from);
        $to = $this->bphtb_model->date_validation($to);
        
        //$where = " where (bphtb_sspd.tgl_approval is not null) and (bphtb_sspd.tgl_transaksi between '$from' and '$to') ";
        
        $where = " where (bphtb_sspd.tgl_transaksi between '$from' and '$to') ";
        
        if ($this->isppat) {
            $where .= " and (bphtb_sspd.ppat_id=" . $this->ppatid . ") ";
        }
        
        if ($src!='') {
            $src = strtolower($src);
            $where .= " and ((lower(bphtb_ppat.kode) like '%" . $src . "%') or (lower(bphtb_ppat.nama) like '" . $src . "%') or 
                        (lower(bphtb_sspd.wp_nama) like '%" . $src . "%') or 
                        (lower(cast(bphtb_sspd.kd_propinsi || '.' || bphtb_sspd.kd_dati2 || '.' || bphtb_sspd.kd_kecamatan || '.' || 
                        bphtb_sspd.kd_kelurahan || '.' || bphtb_sspd.kd_blok || '-' || bphtb_sspd.no_urut || '.' || 
                        bphtb_sspd.kd_jns_op as varchar)) like '%" . $src . "%')) "; 
        }
        
        $order = " order by bphtb_sspd.tgl_transaksi, bphtb_sspd.ppat_id, bphtb_sspd.tahun, bphtb_sspd.kode, bphtb_sspd.no_sspd ";
        
        $data['parameters'] = (object) array('kondisi' => $where, 'order' => $order, 'daerah' => LICENSE_TO, 'ibu_kota' => LICENSE_TO_SUB);
        $data['pdf'] = 'pdf';
        $data['rpt_file'] = 'bphtb_ppat_register';
        $this->load->view('vreports', $data);
    }
    */
	
    /* memanggil report */
    /*
	public function reports() {
        $jns = $this->uri->segment(4);
        $id = $this->uri->segment(5)?$this->uri->segment(5):0;
        if (($jns && ($jns==1 || $jns==2)) && ($get = $this->sspd_model->get($id))) {
            $where = ' where bphtb_sspd.id=' . $id;
            if ($jns==1) {                              // sspd formated
                $data['rpt_file'] = 'bphtb_sspd_formated';
            } else {                                    // sspd ploted
                $data['rpt_file'] = 'bphtb_sspd_plotted';
            }
            $terbilang = terbilang($get->bphtb_harus_dibayarkan, 3) . " Rupiah";
            $data['parameters'] = (object) array('kondisi' => $where, 'terbilang' => $terbilang);
            $data['id'] = $id;
            $data['model'] = $this->sspd_model;
            $data['pdf'] = 'pdf';
            $data['update_print_state'] = true;
            $data['update_field'] = 'tgl_print';
            $data['table'] = 'bphtb_sppd';
            $this->load->view('vreports', $data);
        } else {
            show_404();
        }
    }
    */

	/* new irul */
	
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
        $id   = $this->uri->segment(6);
		
		$get = $this->sspd_model->get($id);
        $terbilang = terbilang($get->bphtb_harus_dibayarkan, 3) . " Rupiah";
		
		$jasper = $this->load->library('Jasper');
		$params = array(
			"kondisi" => " where bphtb_sspd.id = $id ",
			"terbilang" => $terbilang,
			"logo" => base_url("assets/img/logorpt__.jpg"),
			"dinas" => LICENSE_TO_SUB,
		);
		echo $jasper->cetak($rptx, $params, $type);
	}
	
	// report register
	function show_rpt_register() {
		$cls_mtd_html = $this->router->fetch_class()."/cetak_register/html/";
		$cls_mtd_pdf  = $this->router->fetch_class()."/cetak_register/pdf/";
		$data['rpt_html'] = active_module_url($cls_mtd_html. $_SERVER['QUERY_STRING']);;
		$data['rpt_pdf']  = active_module_url($cls_mtd_pdf . $_SERVER['QUERY_STRING']);;
        $this->load->view('vjasper_viewer', $data);
	}
	
    function cetak_register() {
        $type = $this->uri->segment(4);
		$rptx = $this->uri->segment(5);
        $from = $this->uri->segment(6);
        $to   = $this->uri->segment(7);
        $src  = $this->uri->segment(8);
        
        $from = $this->bphtb_model->date_validation($from);
        $to = $this->bphtb_model->date_validation($to);
        
        $where = " where (bphtb_sspd.tgl_transaksi between '$from' and '$to') ";
        
        if ($this->isppat) {
            $where .= " and (bphtb_sspd.ppat_id=" . $this->ppatid . ") ";
        }
        
        if ($src!='') {
            $src = strtolower($src);
            $where .= " and ((lower(bphtb_ppat.kode) like '%" . $src . "%') or (lower(bphtb_ppat.nama) like '" . $src . "%') or 
                        (lower(bphtb_sspd.wp_nama) like '%" . $src . "%') or 
                        (lower(cast(bphtb_sspd.kd_propinsi || '.' || bphtb_sspd.kd_dati2 || '.' || bphtb_sspd.kd_kecamatan || '.' || 
                        bphtb_sspd.kd_kelurahan || '.' || bphtb_sspd.kd_blok || '-' || bphtb_sspd.no_urut || '.' || 
                        bphtb_sspd.kd_jns_op as varchar)) like '%" . $src . "%')) "; 
        }
        
        $order = " order by bphtb_sspd.tgl_transaksi, bphtb_sspd.ppat_id, bphtb_sspd.tahun, bphtb_sspd.kode, bphtb_sspd.no_sspd ";
        		
		$jasper = $this->load->library('Jasper');
		$params = array(
			"kondisi" => $where,
			"order" => $order,
			"daerah" => LICENSE_TO,
			"ibu_kota" => LICENSE_TO_SUB,
			"dinas" => LICENSE_TO_SUB,
		);
		echo $jasper->cetak($rptx, $params, $type);
    }
}