<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class upload_nop extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('login')) {
            $this->session->set_flashdata('msg_warning', 'Session telah kadaluarsa, silahkan login ulang.');
            redirect('login');
            exit;
        }

        if (!is_super_admin() && !isset($this->session->userdata['tpnm'])) {
            show_404();
            exit;
        }

        $module = 'POSC';
        $this->load->library('module_auth', array(
            'module' => $module
        ));

        $this->load->model(array(
            'apps_model'
        ));
        $this->load->model(array(
            'sppt_model',
            'payment_model'
        ));
    }

    public function index()
    {
    
         if (!$this->module_auth->read) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_read);
            redirect('info');
        }

        $filter         = $this->session->userdata('pos_filter');
        $filter         = isset($filter) ? $filter : '';
        $data['filter'] = $filter;
        $data['prefix'] = KD_PROPINSI . "." . KD_DATI2;
        $data['tpnm']   = isset($this->session->userdata['tpnm']) ? $this->session->userdata['tpnm'] : '';

        $data['apps']    = $this->apps_model->get_active_only();
        $data['faction'] = active_module_url('upload_nop/unggah');
        $data['current'] = 'stts';

        $this->load->view('uploadv', $data);
    }

    function simpan() {
        if (!$this->module_auth->create) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_insert);
            redirect('info');
        }

        $simpan = $_POST['data'];
        if(isset($simpan)) {
            $data = json_decode($simpan, true);

            if(count($data) > 0) {
                $saved = array();
                foreach ($data as $row) {
                    $nop = $row[0];
                    $thn_pajak_sppt = $row[1];

                    $kd_propinsi  = substr($nop, 0, 2);
                    $kd_dati2     = substr($nop, 2, 2);
                    $kd_kecamatan = substr($nop, 4, 3);
                    $kd_kelurahan = substr($nop, 7, 3);
                    $kd_blok      = substr($nop, 10, 3);
                    $no_urut      = substr($nop, 13, 4);
                    $kd_jns_op    = substr($nop, 17, 1);

                    if ($query = $this->sppt_model->get_by_nop_thn($nop, $thn_pajak_sppt)) {
                        $sisa  = (float) $query->pbb_yg_harus_dibayar_sppt - ($query->jml_sppt_yg_dibayar - (float) $query->denda_sppt);
                        $denda = 0;
                        if (date($query->tgl_jatuh_tempo_sppt) < date('Y-m-d'))
                            $denda = hitdenda($sisa, $query->tgl_jatuh_tempo_sppt);

                        $utang     = $sisa + $denda;

                        $denda_sppt          = $denda;
                        $jml_sppt_yg_dibayar = $utang;
                        $tgl_pembayaran_sppt = date('Y-m-d');
                        $tgl_rekam_byr_sppt  = date('Y-m-d');
                        $nip_rekam_byr_sppt  = $this->session->userdata('nip');

                        $pembayaran_sppt_ke  = $this->payment_model->get_pembayaran_ke($nop, $thn_pajak_sppt);

                        $data = array(
                            'kd_propinsi' => $kd_propinsi,
                            'kd_dati2' => $kd_dati2,
                            'kd_kecamatan' => $kd_kecamatan,
                            'kd_kelurahan' => $kd_kelurahan,
                            'kd_blok' => $kd_blok,
                            'no_urut' => $no_urut,
                            'kd_jns_op' => $kd_jns_op,
                            'thn_pajak_sppt' => $thn_pajak_sppt,

                            'pembayaran_sppt_ke' => $pembayaran_sppt_ke,
                            'denda_sppt' => $denda_sppt,
                            'jml_sppt_yg_dibayar' => $jml_sppt_yg_dibayar,
                            'tgl_pembayaran_sppt' => $tgl_pembayaran_sppt,
                            'tgl_rekam_byr_sppt' => $tgl_rekam_byr_sppt,
                            'nip_rekam_byr_sppt' => $nip_rekam_byr_sppt,
                            'user_id' => $this->session->userdata('userid')
                        );

                        $fields = explode(',', POS_FIELD); //seuai parameter yang ada di master konfig
                        foreach ($fields as $f) {
                            $f    = trim($f);
                            $data = array_merge($data, array(
                                trim($f) => $this->session->userdata($f)
                            ));
                        }
                        $this->payment_model->update_pmb($data);

                        $prints  = array(
                            'nop' => $nop,
                            'thn' => $thn_pajak_sppt,
                            'ke' => $pembayaran_sppt_ke
                        );
                        $saved[] = $prints;
                    }
                }
                $ret           = array();
                $ret['simpan'] = 'sukses';
                $ret['saved']  = $saved;
                echo json_encode($ret);
            }
        }
    }

	function unggah() { //upload
		if (!empty($_FILES['userfile']['name'])) {
			$this->load->library('upload');

			if(!is_array($_FILES['userfile']['name'])){
				$config['file_name'] = md5($_FILES['userfile']['name']);
			} else {
				$fn = array();
				foreach($_FILES['userfile']['name'] as $key => $value)
				{
					$fn[] = md5($value);
				}
				$config['file_name'] = $fn;
			}

			// $config['upload_path'] = dirname(__FILE__) . ('/../dokumen/');
			$config['upload_path'] = 'assets/dokumen/';
      
            $config['overwrite'] = TRUE;
            $config['encrypt_name'] = TRUE;
            $config['remove_spaces'] = TRUE;
            $config['max_size']  = 1024 * 5;
            $config['allowed_types'] = '*';
            $this->upload->initialize($config);
            
            if ($this->upload->do_multi_upload("userfile")) {
				$uploadinfo = $this->upload->get_multi_upload_data();
				// foreach ($uploadinfo as $file) { // loop over the upload data
					// $this->email->attach($file['full_path']); // attach the full path as an email attachments :D
				// }

                $param = '';
                $adata = array();
                $file = $uploadinfo[0]['full_path'];
                $myfile = fopen($file, "r") or die("Unable to open file!");
                while(!feof($myfile)) {
                    // echo fgets($myfile) . "<br>";

                    $param_n = fgets($myfile);
                    $param_x = preg_replace("/[^0-9]/","",$param_n);
                    // $param  .= "'{$param_x}',";

                    $nop = substr($param_x,0,18);
                    $thn = substr($param_x,18,4);

                    // --------------
                    if ($query = $this->sppt_model->get_by_nop_thn($nop, $thn)) {
                        $sisa  = (float) $query->pbb_yg_harus_dibayar_sppt - ($query->jml_sppt_yg_dibayar - (float) $query->denda_sppt);
                        $denda = 0;
                        if (date($query->tgl_jatuh_tempo_sppt) < date('Y-m-d'))
                            $denda = hitdenda($sisa, $query->tgl_jatuh_tempo_sppt);

                        $utang     = $sisa + $denda;
                        // $terbilang = terbilang($utang);
                        $query     = (object) array_merge((array) $query, array(
                            'found' => 1,
                            'sisa' => $sisa,
                            'denda' => $denda,
                            'utang' => $utang,
                            //'terbilang' => $terbilang
                        ));

                        $data = array(
                            $nop,
                            $thn,
                            number_format($sisa,0,',','.'),
                            number_format($denda,0,',','.'),
                            number_format($utang,0,',','.'),
                            $query->nm_wp_sppt,
                            $query->jln_wp_sppt,
                        );

                        if($utang>0) $adata[] = $data;
                    }
                    // --------------
                }
                @fclose($myfile);
                $aadata["aaData"] = $adata;

                $file = 'assets/dokumen/dtsrc.xxx';
                $dtfile = fopen($file,"w");
                echo fwrite($dtfile,json_encode($aadata));
                @fclose($dtfile);

                // echo json_encode($aadata);
				// echo json_encode(array('msg' => 'ok'));
				// echo json_encode(array('status' => 'success', 'msg' => json_encode($adata)));
				echo ' - Upload sukses.';
			} else {
				// echo 'Upload file gagal.';
                echo strip_tags($this->upload->display_errors()) .' - Upload file gagal.';
			}
		} else echo 'File tidak ditemukan.';
	}

    public function cetak() {
        $rpt = '';
        $data = $_POST['data'];
        $data = json_decode($data, true);

        if ($data!=null){
            $rpt .= "<html><head></head><body><pre>";
            $rpt .= "&nbsp;\n&nbsp;\n&nbsp;\n&nbsp;\n";

            foreach ($data as $d)
            {
                if($q = $this->payment_model->get_by_nop_thn_ke($d['nop'], $d['thn'],$d['ke'])) {
                    $rpt .= str_repeat('&nbsp;',16).$q->nm_tp."&nbsp;\n";
                    $rpt .= str_repeat('&nbsp;',26).$q->thn_pajak_sppt."&nbsp;\n";
                    $rpt .= str_repeat('&nbsp;',16).substr($q->nm_wp_sppt,0,30)."&nbsp;\n";
                    $rpt .= str_repeat('&nbsp;',23).substr($q->nm_kecamatan,0,30)."&nbsp;\n";
                    $rpt .= str_repeat('&nbsp;',23).substr($q->nm_kelurahan,0,30)."&nbsp;\n";
                    $rpt .= str_repeat('&nbsp;',16)."$q->kd_propinsi.$q->kd_dati2.$q->kd_kecamatan.$q->kd_kelurahan.$q->kd_blok-$q->no_urut.$q->kd_jns_op"."&nbsp;\n";
                    $rpt .= str_repeat('&nbsp;',25).number_format($q->jml_sppt_yg_dibayar-$q->denda_sppt,0,',','.')."&nbsp;\n";
                    $rpt .= "&nbsp;\n";
                    $rpt .= str_repeat('&nbsp;',20).date('d/m/Y',strtotime($q->tgl_jatuh_tempo_sppt))."&nbsp;\n";
                    $rpt .= "&nbsp;\n";
                    $rpt .= "&nbsp;\n";
                    $rpt .= str_repeat('&nbsp;',8) . 'TGL PEMBAYARAN    :' . str_repeat('&nbsp;',13) . date('d/m/Y',strtotime($q->tgl_pembayaran_sppt))."&nbsp;\n";
                    $rpt .= str_repeat('&nbsp;',8) . 'PEMBAYARAN        :' . str_repeat('&nbsp;',2) . 'Rp.' .str_pad(number_format($q->jml_sppt_yg_dibayar-$q->denda_sppt,0,',','.'), 18, " ", STR_PAD_LEFT)."&nbsp;\n";
                    $rpt .= str_repeat('&nbsp;',8) . 'DENDA ADMINISTRSI :' . str_repeat('&nbsp;',2) . 'Rp.' .str_pad(number_format($q->denda_sppt,0,',','.'), 18, " ", STR_PAD_LEFT)."&nbsp;\n";
                    $rpt .= str_repeat('&nbsp;',8) . 'TOTAL PEMBAYARAN  :' . str_repeat('&nbsp;',2) . 'Rp.' .str_pad(number_format($q->jml_sppt_yg_dibayar,0,',','.'), 18, " ", STR_PAD_LEFT)."&nbsp;\n";
                    $rpt .= "&nbsp;\n";
                    $rpt .= "&nbsp;\n";
                    $rpt .= "&nbsp;\n";
                    $rpt .= "&nbsp;\n";
                    $rpt .= "&nbsp;\n";

                    $sn = date('dmY',strtotime($q->tgl_pembayaran_sppt));
                    $sn.= $q->kd_propinsi.$q->kd_dati2.$q->kd_kecamatan.$q->kd_kelurahan.$q->kd_blok.$q->no_urut.$q->kd_jns_op.$q->thn_pajak_sppt;

                    $rpt .= str_repeat('&nbsp;',8) . 'SN : '. md5($sn)."&nbsp;\n";

                    $rpt .= "&nbsp;\n";
                    $rpt .= str_repeat('&nbsp;',8) . str_pad(date('d/m/Y',strtotime($q->tgl_pembayaran_sppt)),42," ",STR_PAD_RIGHT).str_pad(number_format($q->luas_bumi_sppt,0,',','.'),10," ",STR_PAD_LEFT)."&nbsp;\n";
                    $rpt .= str_repeat('&nbsp;',50) . str_pad(number_format($q->luas_bng_sppt,0,',','.'),10," ",STR_PAD_LEFT)."&nbsp;\n";
                    $rpt .= str_repeat('&nbsp;',8) . str_pad(number_format($q->jml_sppt_yg_dibayar,0,',','.'),20," ",STR_PAD_RIGHT)."&nbsp;\n";

                    // Lembar 2
                    $rpt .= "2";
                    $rpt .= "&nbsp;\n";
                    $rpt .= "&nbsp;\n";
                    $rpt .= "&nbsp;\n";
                    $rpt .= "&nbsp;\n";
                    $rpt .= str_repeat('&nbsp;',16).$q->nm_tp."&nbsp;\n";
                    $rpt .= str_repeat('&nbsp;',26).$q->thn_pajak_sppt."&nbsp;\n";
                    $rpt .= str_repeat('&nbsp;',16).substr($q->nm_wp_sppt,0,30)."&nbsp;\n";
                    $rpt .= str_repeat('&nbsp;',23).substr($q->nm_kecamatan,0,30)."&nbsp;\n";
                    $rpt .= str_repeat('&nbsp;',23).substr($q->nm_kelurahan,0,30)."&nbsp;\n";
                    $rpt .= str_repeat('&nbsp;',16)."$q->kd_propinsi.$q->kd_dati2.$q->kd_kecamatan.$q->kd_kelurahan.$q->kd_blok-$q->no_urut.$q->kd_jns_op"."&nbsp;\n";
                    $rpt .= str_repeat('&nbsp;',16).number_format($q->jml_sppt_yg_dibayar,0,',','.')."&nbsp;\n";
                    $rpt .= str_repeat('&nbsp;',16).date('d/m/Y',strtotime($q->tgl_pembayaran_sppt))."&nbsp;\n";
                    $rpt .= str_repeat('&nbsp;',16).number_format($q->jml_sppt_yg_dibayar,0,',','.')."&nbsp;\n";

                    // Lembar Bank 3
                    $rpt .= "3";
                    $rpt .= "&nbsp;\n";
                    $rpt .= "&nbsp;\n";
                    $rpt .= "&nbsp;\n";
                    $rpt .= "&nbsp;\n";
                    $rpt .= "&nbsp;\n";
                    $rpt .= "&nbsp;\n";
                    $rpt .= "&nbsp;\n";
                    $rpt .= str_repeat('&nbsp;',16).$q->nm_tp."&nbsp;\n";
                    $rpt .= str_repeat('&nbsp;',26).$q->thn_pajak_sppt."&nbsp;\n";
                    $rpt .= str_repeat('&nbsp;',16).substr($q->nm_wp_sppt,0,30)."&nbsp;\n";
                    $rpt .= str_repeat('&nbsp;',23).substr($q->nm_kecamatan,0,30)."&nbsp;\n";
                    $rpt .= str_repeat('&nbsp;',23).substr($q->nm_kelurahan,0,30)."&nbsp;\n";
                    $rpt .= str_repeat('&nbsp;',16)."$q->kd_propinsi.$q->kd_dati2.$q->kd_kecamatan.$q->kd_kelurahan.$q->kd_blok-$q->no_urut.$q->kd_jns_op"."&nbsp;\n";
                    $rpt .= str_repeat('&nbsp;',25).number_format($q->jml_sppt_yg_dibayar,0,',','.')."&nbsp;\n";
                    $rpt .= str_repeat('&nbsp;',25).date('d/m/Y',strtotime($q->tgl_pembayaran_sppt))."&nbsp;\n";
                    $rpt .= str_repeat('&nbsp;',25).number_format($q->jml_sppt_yg_dibayar,0,',','.')."&nbsp;\n";
                }
            }
            $rpt .= "</pre></font></body></html>";

            echo $rpt;
        } else echo "No Data";
    }
    
    public function cetak_bank_text() {
        $rpt = '';
        $data = $_POST['data'];
        $data = json_decode($data, true);

        if ($data!=null){
            $rpt .= "<html><head></head><body><pre>";

            foreach ($data as $d)
            {
                if($q = $this->payment_model->get_by_nop_thn_ke($d['nop'], $d['thn'],$d['ke'])) {
                
                    $rpt .= str_repeat("&nbsp;", 2) . str_pad("SURAT TANDA TERIMA SETORAN (STTS) {$q->nm_tp}",110," ",STR_PAD_BOTH);
                    $rpt .= "\n";
                    $rpt .= "\n".str_repeat("&nbsp;", 2) . "BUKTI PEMBAYARAN LUNAS {$q->nm_tp}";
                    $rpt .= "\n".str_repeat("&nbsp;", 2) . "PAJAK PBB-P2";
                    $rpt .= "\n".str_repeat("&nbsp;", 2) . "YAU0   CABANG : ";
                    $rpt .= "\n".str_repeat("&nbsp;",58) . str_pad("NOMOR SEQUENCE : ",16," ") . "       JAM TRANSAKSI : ".date('His',now());
                    $rpt .= "\n".str_repeat("&nbsp;", 2) . "TANGGAL TRANSAKSI   : ".date('d/m/Y',strtotime($q->tgl_pembayaran_sppt))." (DD/MM/YYYY)           NPWPD/NOP      : ".$q->kd_propinsi.$q->kd_dati2.$q->kd_kecamatan.$q->kd_kelurahan.$q->kd_blok.$q->no_urut.$q->kd_jns_op;
                    $rpt .= "\n".str_repeat("&nbsp;", 2) . "NOMOR TRANSAKSI     : ". str_pad(" ",34," ") . "NO URUT/KOHIR  : ";
                    $rpt .= "\n".str_repeat("&nbsp;", 2) . "KOTA/KABUPATEN      : ".LICENSE_TO;
                    $rpt .= "\n".str_repeat("&nbsp;", 2) . "TAHUN PAJAK         : {$q->thn_pajak_sppt}";
                    $rpt .= "\n".str_repeat("&nbsp;", 2) . "KODE AKUN PJK DAERAH: 41112";
                    $rpt .= "\n".str_repeat("&nbsp;", 2) . "KODE AKUN PDT DENDA : 4140701";
                    $rpt .= "\n".str_repeat("&nbsp;", 2) . "JENIS PP            : PK PERKOTAAN";
                    $rpt .= "\n".str_repeat("&nbsp;", 2) . "NAMA WAJIB PAJAK    : {$q->nm_wp_sppt}";
                    $rpt .= "\n".str_repeat("&nbsp;", 2) . "LOKASI              : {$q->jln_wp_sppt}";
                    $rpt .= "\n".str_repeat("&nbsp;", 2) . "KELURAHAN           : {$q->nm_kelurahan}";
                    $rpt .= "\n".str_repeat("&nbsp;", 2) . "KECAMATAN           : {$q->nm_kecamatan}";
                    $rpt .= "\n".str_repeat("&nbsp;", 2) . "PROPINSI            : JAWA BARAT";
                    $rpt .= "\n".str_repeat("&nbsp;", 2) . "LUAS TANAH          : ".number_format($q->luas_bumi_sppt,0,',','.')." M2      LUAS BANGUNAN : ".number_format($q->luas_bng_sppt,0,',','.')." M2";
                    $rpt .= "\n".str_repeat("&nbsp;", 2) . "TANGGAL JATUH TEMPO : ".date('Y-m-d',strtotime($q->tgl_jatuh_tempo_sppt))." (YYYY-MM-DD)";
                    $rpt .= "\n".str_repeat("&nbsp;", 2) . "URAIAN PEMBAYARAN   :";
                    $rpt .= "\n".str_repeat("&nbsp;", 2) . "POKOK PAJAK PBB-P2  : RP.".str_pad(number_format($q->jml_sppt_yg_dibayar-$q->denda_sppt,0,',','.'),20," ",STR_PAD_LEFT);
                    $rpt .= "\n".str_repeat("&nbsp;", 2) . "DENDA               : RP.".str_pad(number_format($q->denda_sppt,0,',','.'),20," ",STR_PAD_LEFT);
                    $rpt .= "\n".str_repeat("&nbsp;", 2) . "                    ------------------------- +";
                    $rpt .= "\n".str_repeat("&nbsp;", 2) . "JML SETORAN PAJAK   : RP.".str_pad(number_format($q->jml_sppt_yg_dibayar,0,',','.'),20," ",STR_PAD_LEFT);
                    $rpt .= "\n".str_repeat("&nbsp;", 2) . "TERBILANG           : ".strtoupper(terbilang($q->jml_sppt_yg_dibayar))." RUPIAH";
                    $rpt .= "\n".str_repeat("&nbsp;", 2) . "                                                               ___________________________";
                    $rpt .= "\n".str_repeat("&nbsp;", 2) . "                                                                       PETUGAS BANK";
                    $rpt .= "\n".str_repeat("&nbsp;", 2) . "SELURUH PEMERINTAH KABUPATEN/KOTA PROVINSI JAWA BARAT DAN BANTEN MENYATAKAN RESI INI SEBAGAI";
                    $rpt .= "\n".str_repeat("&nbsp;", 2) . "BUKTI PEMBAYARAN PAJAK DAERAH YANG SAH";
                    $rpt .= "\n".str_repeat("&nbsp;", 2) . "PEMBAYARAN PAJAK DAERAH DAPAT DILAKUKAN DI SELURUH JARINGAN KANTOR {$q->nm_tp} TERDEKAT";

                    $rpt .= "\n";
                    $rpt .= "\n";
                    $rpt .= "\n";
                    $rpt .= "\n";
                    $rpt .= "\n";
                }
            }
            $rpt .= "</pre></font></body></html>";

            echo $rpt;
        } else echo "No Data";
    }

    public function  cetak_pdf() {
        $data = $_POST['data'];
        $data = json_decode($data, true);
        $join = '';

		//tambahan parameter join untuk relasi tabel pembayaran sppt dgn tempat pembayaran
		if (DEF_POS_TYPE==1) {
			$join =" ps.kd_kanwil=tp.kd_kanwil AND ps.kd_kantor=tp.kd_kantor AND ps.kd_tp=tp.kd_tp ";
		} elseif (DEF_POS_TYPE==2) {
			$join =" ps.kd_kanwil_bank=tp.kd_kanwil AND ps.kd_kppbb_bank=tp.kd_kppbb AND ps.kd_bank_tunggal=tp.kd_bank_tunggal AND ps.kd_bank_persepsi=tp.kd_bank_persepsi AND  ps.kd_tp=tp.kd_tp ";
		}

        $rpt   = "stts_nop";
        $sttsno = $_POST['sttsno'];
        $rpt  .= $sttsno;

		if (count($data)>0){
            $param = '';
            foreach ($data as $d) {
                $param_n = "{$d['nop']}{$d['thn']}{$d['ke']}";
                $param_x = preg_replace("/[^0-9]/","",$param_n);
                $param_x = " ('".substr($param_x,0,2)."','".substr($param_x,2,2)."','".
                           substr($param_x,4,3)."','".substr($param_x,7,3)."','".
                           substr($param_x,10,3)."','".substr($param_x,13,4)."','".
                           substr($param_x,17,1)."','".substr($param_x,18,4)."',".
                           substr($param_x,22,1).") ";
                $param  .= "{$param_x},";
            }
            $param = substr($param, 0, -1);

            $params = array(
                "daerah" => LICENSE_TO,
                "dinas" => LICENSE_TO_SUB,
                "logo" => base_url("assets/img/logorpt__.jpg"),

                "param" => $param,
                "join" => $join,
            );

            $jasper = $this->load->library('Jasper');
            echo $jasper->cetak(POS_WIL."/{$rpt}", $params, "pdf", false);

        } else {
            echo "No Data";
        }
    }

    public function  cetak_bank() {
        $data = $_POST['data'];
        $data = json_decode($data, true);
        $join = '';

		//tambahan parameter join untuk relasi tabel pembayaran sppt dgn tempat pembayaran
		if (DEF_POS_TYPE==1) {
			$join =" ps.kd_kanwil=tp.kd_kanwil AND ps.kd_kantor=tp.kd_kantor AND ps.kd_tp=tp.kd_tp ";
		} elseif (DEF_POS_TYPE==2) {
			$join =" ps.kd_kanwil_bank=tp.kd_kanwil AND ps.kd_kppbb_bank=tp.kd_kppbb AND ps.kd_bank_tunggal=tp.kd_bank_tunggal AND ps.kd_bank_persepsi=tp.kd_bank_persepsi AND  ps.kd_tp=tp.kd_tp ";
		}

		if (count($data)>0){
            $param = '';
            foreach ($data as $d) {
                $param_n = "{$d['nop']}{$d['thn']}{$d['ke']}";
                $param_x = preg_replace("/[^0-9]/","",$param_n);
                $param_x = " ('".substr($param_x,0,2)."','".substr($param_x,2,2)."','".
                           substr($param_x,4,3)."','".substr($param_x,7,3)."','".
                           substr($param_x,10,3)."','".substr($param_x,13,4)."','".
                           substr($param_x,17,1)."','".substr($param_x,18,4)."',".
                           substr($param_x,22,1).") ";
                $param  .= "{$param_x},";
            }
            $param = substr($param, 0, -1);

            $params = array(
                "daerah" => LICENSE_TO,
                "dinas" => LICENSE_TO_SUB,
                "logo" => base_url("assets/img/logorpt__.jpg"),

                "param" => $param,
                "join" => $join,
            );

            $jasper = $this->load->library('Jasper');
            echo $jasper->cetak(POS_WIL."/stts_nop_bank", $params, "pdf", false);

        } else {
            echo "No Data";
        }
    }

}
