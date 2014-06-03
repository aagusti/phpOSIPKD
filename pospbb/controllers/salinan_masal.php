<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class salinan_masal extends CI_Controller {

	function __construct() {
		parent::__construct();
		if(!$this->session->userdata('login')) {
			$this->session->set_flashdata('msg_warning', 'Session telah kadaluarsa, silahkan login ulang.');
			redirect('login');
			exit;
		}

        if(!is_super_admin() && !isset($this->session->userdata['tpnm'])) {
            show_404();
            exit;
        }

		$module = 'POSSmasal';
		$this->load->library('module_auth',array('module'=>$module));

		$this->load->model(array('apps_model'));
		$this->load->model(array('sppt_model','payment_model'));
        set_time_limit (0);
    }

	public function index() {
		if(!$this->module_auth->read) {
			$this->session->set_flashdata('msg_warning', $this->module_auth->msg_read);
			redirect('info');
		}

		$filter = $this->session->userdata('pos_filter');
		$filter = isset($filter) ? $filter : '';
        $data['prefix'] = KD_PROPINSI.".".KD_DATI2.".";
        $data['tpnm'] = isset($this->session->userdata['tpnm']) ? $this->session->userdata['tpnm'] : '';

		$data['filter']  = $filter;

		$data['apps']    = $this->apps_model->get_active_only();
		$data['faction'] = active_module_url('salinan_masal/cetak');
        $data['tgl_awal'] = date('d-m-Y');
        $data['tgl_akhir'] = date('d-m-Y');
		$data['current'] = 'stts';

		$this->load->view('salinan_masalv', $data);
	}

	public function cari() {
		if(!$this->module_auth->read) {
			$this->session->set_flashdata('msg_warning', $this->module_auth->msg_read);
			redirect('info');
		}

        $result = FALSE;
        $search_by = $this->uri->segment(4);
        if ($search_by == 'nop') {
            $range1 = $this->uri->segment(5);
            $range2 = $this->uri->segment(6);
            $thn    = $this->uri->segment(7);
            $result = $this->payment_model->get_salinan_masal_by_nop($range1, $range2, $thn);
        } elseif ($search_by == 'tgl') {
            $tgl1 = date('Y-m-d', strtotime($this->uri->segment(5)));
            $tgl2 = date('Y-m-d', strtotime($this->uri->segment(6)));
            $result = $this->payment_model->get_salinan_masal_by_tgl($tgl1, $tgl2);
        }

		if($result) {
		    $output = array(
			    'found' => 1,
				//'sEcho' => intval($sEcho),
				'iTotalRecords' => $result['tot_rows']+1,
				'iTotalDisplayRecords' => $result['num_rows']+1,
				'sql' => $result['sql'],
				'aaData' => array()
			);

			foreach ($result['query'] as $data) {
                $row = array();
                $row[] = $data['kode'];
				$row[] = $data['thn_pajak_sppt'];
				$row[] = number_format($data['jml_sppt_yg_dibayar'],0,',','.');
				$row[] = number_format($data['pembayaran_sppt_ke'],0,',','.');
				$row[] = date('d-m-Y', strtotime($data['tgl_pembayaran_sppt']));
				$row[] = $data['nm_wp_sppt'];
				$row[] = $data['jln_wp_sppt'];
				$row[] = '<a class="delete" href="">Hapus</a>';

				$row[] = $data['nm_tp'];
				$row[] = $data['thn_pajak_sppt'];
				$row[] = $data['nm_wp_sppt'];
				$row[] = $data['nm_kecamatan'];
				$row[] = $data['nm_kelurahan'];
				$row[] = $data['kode'];
				$row[] = $data['jml_sppt_yg_dibayar'];
				$row[] = $data['denda_sppt'];
				$row[] = $data['tgl_jatuh_tempo_sppt'];
				$row[] = $data['tgl_pembayaran_sppt'];
				$row[] = $data['jml_sppt_yg_dibayar'];

				$row[] = $data['luas_bumi_sppt'];
				$row[] = $data['luas_bng_sppt'];

				$output['aaData'][] = $row;
			}

			echo json_encode($output);
		} else {
		    $output = array(
			    'found' => 0,
				'iTotalRecords' => 0,
				'iTotalDisplayRecords' => 0,
				'aaData' => array()
			);
			echo json_encode($output);
		}
	}

    function cetak() {
        $cetak = $this->input->post('dtCetak');
        $tambahan_data2 = array();

        if(isset($cetak)) {
            $i = 1;
            $j = json_decode($cetak, true);

            if(count($j['dtCetak']) > 0)
                echo $this->load->view('stts2v', $j);
        }
    }

    public function  cetak_pdf() {
		//tambahan parameter join untuk relasi tabel pembayaran sppt dgn tempat pembayaran 
        $join = '';
		if (DEF_POS_TYPE==1) {
			$join =" ps.kd_kanwil=tp.kd_kanwil AND ps.kd_kantor=tp.kd_kantor AND ps.kd_tp=tp.kd_tp ";
		} elseif (DEF_POS_TYPE==2) {
			$join =" ps.kd_kanwil_bank=tp.kd_kanwil AND ps.kd_kppbb_bank=tp.kd_kppbb AND ps.kd_bank_tunggal=tp.kd_bank_tunggal AND ps.kd_bank_persepsi=tp.kd_bank_persepsi AND  ps.kd_tp=tp.kd_tp ";
		} 
		
        $rpt   = "stts_nop";
        $sttsno = $_POST['sttsno'];
        $rpt  .= $sttsno;
        
        $cetak = $this->input->post('data'); 
        if(isset($cetak)) {
            $data = json_decode($cetak, true);

            if(count($data) > 0) {
                $param = ''; 
                foreach ($data as $d) {
                    // $param_n = "{$d['nop']}{$d['thn']}{$d['ke']}";
                    $param_n = "{$d[0]}{$d[1]}{$d[3]}";
                    $param_x = preg_replace("/[^0-9]/","",$param_n);
                    $param_x = " ('".substr($param_x,0,2)."','".substr($param_x,2,2)."','".
                               substr($param_x,4,3)."','".substr($param_x,7,3)."','".
                               substr($param_x,10,3)."','".substr($param_x,13,4)."','".
                               substr($param_x,17,1)."','".substr($param_x,18,4)."',".
                               substr($param_x,22,1).") ";
                    $param  .= " {$param_x},";
                }
                $param = substr($param, 0, -1);
                 //echo $param; exit;
                
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
    }
    
    public function  cetak_bank() {
		//tambahan parameter join untuk relasi tabel pembayaran sppt dgn tempat pembayaran 
        $join = '';
		if (DEF_POS_TYPE==1) {
			$join =" ps.kd_kanwil=tp.kd_kanwil AND ps.kd_kantor=tp.kd_kantor AND ps.kd_tp=tp.kd_tp ";
		} elseif (DEF_POS_TYPE==2) {
			$join =" ps.kd_kanwil_bank=tp.kd_kanwil AND ps.kd_kppbb_bank=tp.kd_kppbb AND ps.kd_bank_tunggal=tp.kd_bank_tunggal AND ps.kd_bank_persepsi=tp.kd_bank_persepsi AND  ps.kd_tp=tp.kd_tp ";
		} 
		
        $cetak = $this->input->post('data'); 
        if(isset($cetak)) {
            $data = json_decode($cetak, true);

            if(count($data) > 0) {
                $param = ''; 
                foreach ($data as $d) {
                    // $param_n = "{$d['nop']}{$d['thn']}{$d['ke']}";
                    $param_n = "{$d[0]}{$d[1]}{$d[3]}";
                    $param_x = preg_replace("/[^0-9]/","",$param_n);
                    $param_x = " ('".substr($param_x,0,2)."','".substr($param_x,2,2)."','".
                               substr($param_x,4,3)."','".substr($param_x,7,3)."','".
                               substr($param_x,10,3)."','".substr($param_x,13,4)."','".
                               substr($param_x,17,1)."','".substr($param_x,18,4)."',".
                               substr($param_x,22,1).")";                    
                    $param  .= "{$param_x},";
                }
                $param = substr($param, 0, -1);
                 //echo $param; exit;                 
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
}
