<?php
class loaddata extends CI_Controller {    
    function __construct()
    {
        parent::__construct();
        $this->load->model("pbbm_model");
        if (active_module() != 'pbbm') {
            show_404();
            exit;
        }
    }
    
    function index() {}
    
    function transaksi1()
    {
        // ob_start("ob_gzhandler");
        
        $kec_kd = (isset($_GET['kec_kd']) && is_numeric($_GET['kec_kd'])) ? $_GET['kec_kd'] : '000';
        if (get_user_kec_kd() != '000' && get_user_kec_kd() != $kec_kd)
            $kec_kd = get_user_kec_kd();
        
        $kel_kd = (isset($_GET['kel_kd']) && is_numeric($_GET['kel_kd'])) ? $_GET['kel_kd'] : '000';
        if (get_user_kel_kd() != '000' && get_user_kel_kd() != $kel_kd)
            $kec_kd = get_user_kel_kd();
        
        $buku    = (isset($_GET['buku'])) ? $_GET['buku'] : '11';
        $bukumin = $this->pbbm_model->rangebuku[substr($buku, 0, 1)][0];
        $bukumax = $this->pbbm_model->rangebuku[substr($buku, 1, 1)][1];
        
        $tglm = (isset($_GET['tglawal'])) ? $_GET['tglawal'] : date('d-m-Y');
        $tgls = (isset($_GET['tglakhir'])) ? $_GET['tglakhir'] : date('d-m-Y');
        $tglm = substr($tglm, 6, 4) . '-' . substr($tglm, 3, 2) . '-' . substr($tglm, 0, 2);
        $tgls = substr($tgls, 6, 4) . '-' . substr($tgls, 3, 2) . '-' . substr($tgls, 0, 2);
        
        $tahun_sppt1 = (isset($_GET['tahun_sppt1'])) ? $_GET['tahun_sppt1'] : date('Y');
        $tahun_sppt2 = (isset($_GET['tahun_sppt2'])) ? $_GET['tahun_sppt2'] : date('Y');
        
        $path_to_root = active_module_url();
        
        $aColumns     = array(
            'kode',
            'uraian',
            'thn_pajak_sppt',
            'pokok',
            'denda',
            'bayar',
            'tanggal',
            'nm_tp',
        );
        $sIndexColumn = "kode";
        
        $iDisplayLength = (isset($_GET['iDisplayLength']) && is_numeric($_GET['iDisplayLength'])) ? $_GET['iDisplayLength'] : 15;
        $iDisplayStart  = (isset($_GET['iDisplayStart']) && is_numeric($_GET['iDisplayStart'])) ? $_GET['iDisplayStart'] : 0;
        $iSortCol_0     = (isset($_GET['iSortCol_0']) && is_numeric($_GET['iSortCol_0'])) ? $_GET['iSortCol_0'] : 0;
        $iSortingCols   = (isset($_GET['iSortingCols']) && is_numeric($_GET['iSortingCols'])) ? $_GET['iSortingCols'] : 1;
        $sSortDir_0     = (isset($_GET['sSortDir_0'])) ? $_GET['sSortDir_0'] : "asc";
        $sEcho          = (isset($_GET['sEcho']) && is_numeric($_GET['sEcho'])) ? $_GET['sEcho'] : 1;
        $sSearch        = (isset($_GET['sSearch'])) ? $_GET['sSearch'] : "";
        $sSearch_0      = (isset($_GET['sSearch_0'])) ? $_GET['sSearch_0'] : "";
        $sSearch_1      = (isset($_GET['sSearch_1'])) ? $_GET['sSearch_1'] : "";
        $sSearch_2      = (isset($_GET['sSearch_2'])) ? $_GET['sSearch_2'] : "";
        $sSearch_3      = (isset($_GET['sSearch_3'])) ? $_GET['sSearch_3'] : "";
        $sSearch_4      = (isset($_GET['sSearch_4'])) ? $_GET['sSearch_4'] : "";
        
        /*
         * Limit
         */
        
        $sLimit     = "";
        $pageSize   = (isset($_GET['iDisplayLength']) && $_GET['iDisplayLength'] != '-1' ? $_GET['iDisplayLength'] : 15);
        $pageNumber = (isset($_GET['iDisplayStart']) && $_GET['iDisplayStart'] != '-1' ? $_GET['iDisplayStart'] : 1);
        if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1') {
            // $sLimit = "LIMIT $pageSize OFFSET $pageNumber";
        }
        
        
        /*
         * Ordering
         */
        $sOrder = "";
        if (isset($_GET['iSortCol_0'])) {
            $sOrder = "ORDER BY ";
            for ($i = 0; $i < intval($_GET['iSortingCols']); $i++) {
                if ($_GET['bSortable_' . intval($_GET['iSortCol_' . $i])] == "true") {
                    if ($aColumns[intval($_GET['iSortCol_' . $i])] == "bphtbno" || $aColumns[intval($_GET['iSortCol_' . $i])] == "tanggal") {
                        $sOrder .= $aColumns[intval($_GET['iSortCol_' . $i])] . " " . $_GET['sSortDir_' . $i] . ", ";
                    } else {
                        $sOrder .= $aColumns[intval($_GET['iSortCol_' . $i])] . ' ' . $_GET['sSortDir_' . $i] . ", ";
                    }
                }
            }
            
            $sOrder = substr_replace($sOrder, "", -2);
            if ($sOrder == "ORDER BY ") {
                $sOrder = "";
            }
        }
        
        
        /* 
         * Filtering
         * NOTE this does not match the built-in DataTables filtering which does it
         * word by word on any field. It's possible to do here, but concerned about efficiency
         * on very large tables, and MySQL's regex functionality is very limited
         */
        
        
        $where = "WHERE p.tgl_pembayaran_sppt BETWEEN '$tglm' AND '$tgls'
            AND k.kd_propinsi='" . KD_PROPINSI . "' 
            AND k.kd_dati2='" . KD_DATI2 . "' 
            AND p.thn_pajak_sppt BETWEEN '$tahun_sppt1' AND '$tahun_sppt2'
            AND k.pbb_yg_harus_dibayar_sppt between $bukumin AND $bukumax ";
        
        //die($where);    
        if ($kec_kd != "000") {
            $where .= " AND k.kd_kecamatan='$kec_kd'";
            if ($kel_kd != "000")
                $where .= " AND k.kd_kelurahan='$kel_kd'";
        }
        
        $search = '';
        if ($sSearch)
            $search .= " AND k.nm_wp_sppt ilike '%$sSearch%'";
        
        $iTotal    = 0;
        $iFiltered = 0;
        /*
        $sql_query = "SELECT count(*) c FROM sppt k 
            INNER JOIN pembayaran_sppt p 
            ON k.kd_propinsi = p.kd_propinsi
            AND k.kd_dati2 = p.kd_dati2 
            AND k.kd_kecamatan = p.kd_kecamatan 
            AND k.kd_kelurahan = p.kd_kelurahan 
            AND k.kd_blok = p.kd_blok 
            AND k.no_urut = p.no_urut 
            AND k.kd_jns_op = p.kd_jns_op 
            AND k.thn_pajak_sppt = p.thn_pajak_sppt 
            $where ";
        $qry       = $this->db->query($sql_query);
        $row       = $qry->row();
        $iTotal    = $row->c;
        
        if ($search) {
            $sql_query .= $search;
            $qry       = $this->db->query($sql_query);
            $row       = $qry->row();
            $iFiltered = $row->c;
        } else
            $iFiltered = $iTotal;
        */
        
        /*
         * Output
         */
         
        /// -- DARI SINI ..
        $fields     = explode(',', POS_FIELD);
        $pos_fld    = '';
        $pos_join   = '';
        $pos_uraian = '';
        $fs = '';
        foreach ($fields as $f) {
            $fs = $f;
            if ($f == 'kd_kanwil_bank')
                $fs = 'kd_kanwil';
            else if ($f == 'kd_kppbb_bank')
                $fs = 'kd_kppbb';
                
            $pos_fld .= "p.{$f}, ";
            $pos_join .= "p.{$f}=tp.{$fs} and ";
            $pos_uraian .= "tp.{$fs}||";
        }
        $pos_fld = substr($pos_fld, 0, -2);
        $pos_join = substr($pos_join, 0, -4);
        $pos_uraian = substr($pos_uraian, 0, -2);
        
        $tp_kd = (isset($_GET['tp_kd'])) ? $_GET['tp_kd'] : '';
        if ($tp_kd != "")
            $where .= " AND {$pos_uraian} = '{$tp_kd}'";
            
        $sql_query_r = "SELECT  
            k.kd_propinsi||'.'||k.kd_dati2||'-'||k.kd_kecamatan||'.'||k.kd_kelurahan ||'-'|| k.kd_blok ||'.'||k.no_urut||'.'|| k.kd_jns_op ||' '|| k.thn_pajak_sppt kode, 
            k.nm_wp_sppt uraian, {$pos_uraian}||':'||tp.nm_tp nm_tp, p.thn_pajak_sppt,
            (p.jml_sppt_yg_dibayar - p.denda_sppt) pokok, p.denda_sppt denda, p.jml_sppt_yg_dibayar bayar, to_char(p.tgl_pembayaran_sppt,'dd-mm-yyyy') tanggal
            FROM sppt k 
            INNER JOIN pembayaran_sppt p 
            ON k.kd_propinsi = p.kd_propinsi
            AND k.kd_dati2 = p.kd_dati2 
            AND k.kd_kecamatan = p.kd_kecamatan 
            AND k.kd_kelurahan = p.kd_kelurahan 
            AND k.kd_blok = p.kd_blok 
            AND k.no_urut = p.no_urut 
            AND k.kd_jns_op = p.kd_jns_op 
            AND k.thn_pajak_sppt = p.thn_pajak_sppt 
            LEFT JOIN tempat_pembayaran tp ON {$pos_join}
            $where $search 
            ORDER BY 1,2,3 ";
        
        $sql_query_r .= "$sOrder $sLimit";
        
        $qry = $this->db->query($sql_query_r);
        
        $output = array(
            "sEcho" => $sEcho,
            "iTotalRecords" => $iTotal,
            "iTotalDisplayRecords" => $iFiltered,
            "iDisplayStart" => $iDisplayStart,
            "iDisplayLength" => $iDisplayLength,
            
            // "SQL Query" => $sql_query_r,
            "aaData" => array()
        );
        
        $pg_pokok = 0;
        $pg_denda = 0;
        $pg_total = 0;
        foreach ($qry->result() as $aRow) {
            $row = array();
            for ($i = 0; $i < count($aColumns); $i++) {
                if ($i > 2 && $i < 5)
                    $row[] = number_format($aRow->$aColumns[$i], 0, ',', '.');
                else
                    $row[] = $aRow->$aColumns[$i];
            }
            $output['aaData'][] = $row;
            
            $pg_pokok += $aRow->$aColumns[3];
            $pg_denda += $aRow->$aColumns[4];
            $pg_total += $aRow->$aColumns[5];
            
        }
        /*
        $row = array();
        $row[]              = '';
        $row[]              = 'Jumlah Halaman';
        $row[]              = number_format($pg_pokok, 0, ',', '.');
        $row[]              = number_format($pg_denda, 0, ',', '.');
        $row[]              = number_format($pg_total, 0, ',', '.');
        $row[]              = '';
        $output['aaData'][] = $row;
        
        if ($iDisplayStart + $iDisplayLength + 1 >= $iFiltered) {
            // $sql_query_r = "SELECT  '' kode, 'TOTAL' uraian, sum(k.pbb_yg_harus_dibayar_sppt) pokok, 
            $sql_query_r = "SELECT  '' kode, 'TOTAL' uraian, sum(p.jml_sppt_yg_dibayar - p.denda_sppt) pokok, 
                sum(p.denda_sppt) denda, sum(p.jml_sppt_yg_dibayar) bayar, '' as tanggal
                FROM sppt k 
                INNER JOIN pembayaran_sppt p 
                ON k.kd_propinsi = p.kd_propinsi
                AND k.kd_dati2 = p.kd_dati2 
                AND k.kd_kecamatan = p.kd_kecamatan 
                AND k.kd_kelurahan = p.kd_kelurahan 
                AND k.kd_blok = p.kd_blok 
                AND k.no_urut = p.no_urut 
                AND k.kd_jns_op = p.kd_jns_op 
                AND k.thn_pajak_sppt = p.thn_pajak_sppt 
                $where $search ";
            
            $qry = $this->db->query($sql_query_r);
            foreach ($qry->result() as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    if ($i > 1 && $i < 5)
                        $row[] = number_format($aRow->$aColumns[$i], 0, ',', '.');
                    else
                        $row[] = $aRow->$aColumns[$i];
                }
                $output['aaData'][] = $row;
            }
        }
        */
        
        $output['pokok'] = number_format($pg_pokok, 0, ',', '.');
        $output['denda'] = number_format($pg_denda, 0, ',', '.');
        $output['total'] = number_format($pg_total, 0, ',', '.');
        
        echo json_encode($output);
    }
    
    function transaksi2()
    {
        // ob_start("ob_gzhandler");
        
        $buku        = (isset($_GET['buku'])) ? $_GET['buku'] : '11';
        $bukumin     = $this->pbbm_model->rangebuku[substr($buku, 0, 1)][0];
        $bukumax     = $this->pbbm_model->rangebuku[substr($buku, 1, 1)][1];
        $tahun_sppt1 = (isset($_GET['tahun_sppt1'])) ? $_GET['tahun_sppt1'] : date('Y');
        $tahun_sppt2 = (isset($_GET['tahun_sppt2'])) ? $_GET['tahun_sppt2'] : date('Y');
        $kec_kd      = (isset($_GET['kec_kd']) && is_numeric($_GET['kec_kd'])) ? $_GET['kec_kd'] : '000';
        if (get_user_kec_kd() != '000' && get_user_kec_kd() != $kec_kd)
            $kec_kd = get_user_kec_kd();
        
        $kel_kd = (isset($_GET['kel_kd']) && is_numeric($_GET['kel_kd'])) ? $_GET['kel_kd'] : '000';
        
        if (get_user_kel_kd() != '000' && get_user_kel_kd() != $kel_kd)
            $kec_kd = get_user_kel_kd();
        
        $path_to_root = active_module_url();
        
        $aColumns     = array(
            'kode',
            'uraian',
            'thn_pajak_sppt',
            'pokok',
            'denda',
            'bayar'
        );
        $sIndexColumn = "kode";
        
        $iDisplayLength = (isset($_GET['iDisplayLength']) && is_numeric($_GET['iDisplayLength'])) ? $_GET['iDisplayLength'] : 15;
        $iDisplayStart  = (isset($_GET['iDisplayStart']) && is_numeric($_GET['iDisplayStart'])) ? $_GET['iDisplayStart'] : 0;
        
        $iSortCol_0   = (isset($_GET['iSortCol_0']) && is_numeric($_GET['iSortCol_0'])) ? $_GET['iSortCol_0'] : 0;
        $iSortingCols = (isset($_GET['iSortingCols']) && is_numeric($_GET['iSortingCols'])) ? $_GET['iSortingCols'] : 1;
        $sSortDir_0   = (isset($_GET['sSortDir_0'])) ? $_GET['sSortDir_0'] : "asc";
        
        $sEcho = (isset($_GET['sEcho']) && is_numeric($_GET['sEcho'])) ? $_GET['sEcho'] : 1;
        
        $sSearch   = (isset($_GET['sSearch'])) ? $_GET['sSearch'] : "";
        $sSearch_0 = (isset($_GET['sSearch_0'])) ? $_GET['sSearch_0'] : "";
        $sSearch_1 = (isset($_GET['sSearch_1'])) ? $_GET['sSearch_1'] : "";
        $sSearch_2 = (isset($_GET['sSearch_2'])) ? $_GET['sSearch_2'] : "";
        $sSearch_3 = (isset($_GET['sSearch_3'])) ? $_GET['sSearch_3'] : "";
        $sSearch_4 = (isset($_GET['sSearch_4'])) ? $_GET['sSearch_4'] : "";
        
        $tglm = (isset($_GET['tglawal'])) ? $_GET['tglawal'] : date('d-m-Y');
        $tgls = (isset($_GET['tglakhir'])) ? $_GET['tglakhir'] : date('d-m-Y');
        
        $tglm = substr($tglm, 6, 4) . '-' . substr($tglm, 3, 2) . '-' . substr($tglm, 0, 2);
        $tgls = substr($tgls, 6, 4) . '-' . substr($tgls, 3, 2) . '-' . substr($tgls, 0, 2);
        
        /*
         * Limit
         */
        
        $sLimit = "";
        if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1') {
            // $sLimit = "LIMIT " . $_GET['iDisplayLength'] . " OFFSET " . $_GET['iDisplayStart'];
        }
        
        
        /*
         * Ordering
         */
        $sOrder = "";
        if (isset($_GET['iSortCol_0'])) {
            $sOrder = "ORDER BY ";
            for ($i = 0; $i < intval($_GET['iSortingCols']); $i++) {
                if ($_GET['bSortable_' . intval($_GET['iSortCol_' . $i])] == "true") {
                    if ($aColumns[intval($_GET['iSortCol_' . $i])] == "bphtbno" || $aColumns[intval($_GET['iSortCol_' . $i])] == "tanggal") {
                        $sOrder .= $aColumns[intval($_GET['iSortCol_' . $i])] . " " . $_GET['sSortDir_' . $i] . ", ";
                    } else {
                        $sOrder .= $aColumns[intval($_GET['iSortCol_' . $i])] . ' ' . $_GET['sSortDir_' . $i] . ", ";
                    }
                }
            }
            
            $sOrder = substr_replace($sOrder, "", -2);
            if ($sOrder == "ORDER BY ") {
                $sOrder = "";
            }
        }
        
        /* 
         * Filtering
         * NOTE this does not match the built-in DataTables filtering which does it
         * word by word on any field. It's possible to do here, but concerned about efficiency
         * on very large tables, and MySQL's regex functionality is very limited
         */
        $where = "WHERE p.tgl_pembayaran_sppt BETWEEN '$tglm' AND '$tgls'
            AND p.kd_propinsi='" . KD_PROPINSI . "' 
            AND p.kd_dati2='" . KD_DATI2 . "' 
            AND p.thn_pajak_sppt BETWEEN '$tahun_sppt1' AND '$tahun_sppt2'
            AND k.pbb_yg_harus_dibayar_sppt between $bukumin AND $bukumax ";
        
        if ($kec_kd != "000")
            $where .= " AND p.kd_kecamatan='$kec_kd'";
        if ($kel_kd != "000")
            $where .= " AND p.kd_kelurahan='$kel_kd'";
        
        $search = '';
        if ($sSearch)
            $search .= " AND tp.nm_tp ilike '%$sSearch%'";
        
        $iTotal    = 0;
        $iFiltered = 0;
        
        /// -- DARI SINI ..
        $fields     = explode(',', POS_FIELD);
        $pos_fld    = '';
        $pos_join   = '';
        $pos_uraian = '';
        $fs = '';
        foreach ($fields as $f) {
            $fs = $f;
            if ($f == 'kd_kanwil_bank')
                $fs = 'kd_kanwil';
            else if ($f == 'kd_kppbb_bank')
                $fs = 'kd_kppbb';
                
            $pos_fld .= "p.{$f}, ";
            $pos_join .= "p.{$f}=tp.{$fs} and ";
            $pos_uraian .= "tp.{$fs}||";
        }
        $pos_fld = substr($pos_fld, 0, -2);
        $pos_join = substr($pos_join, 0, -4);
        $pos_uraian = substr($pos_uraian, 0, -2);
        
        $tp_kd = (isset($_GET['tp_kd'])) ? $_GET['tp_kd'] : '';
        if ($tp_kd != "")
            $where .= " AND {$pos_uraian} = '{$tp_kd}'";
            
        /*
        // $sql_query = "SELECT count(*) c FROM  (SELECT DISTINCT p.kd_kanwil, p.kd_kantor, p.kd_tp, tp.nm_tp, p.tgl_pembayaran_sppt 
        $sql_query = "SELECT count(*) c FROM  (SELECT DISTINCT {$pos_fld}, tp.nm_tp, p.tgl_pembayaran_sppt 
            FROM pembayaran_sppt p 
            LEFT JOIN tempat_pembayaran tp 
            -- ON p.kd_kanwil=tp.kd_kanwil and p.kd_kantor=tp.kd_kantor AND p.kd_tp=tp.kd_tp 
            ON {$pos_join}
            INNER JOIN SPPT k ON k.kd_propinsi = p.kd_propinsi
            AND k.kd_dati2 = p.kd_dati2 
            AND k.kd_kecamatan = p.kd_kecamatan 
            AND k.kd_kelurahan = p.kd_kelurahan 
            AND k.kd_blok = p.kd_blok 
            AND k.no_urut = p.no_urut 
            AND k.kd_jns_op = p.kd_jns_op 
            AND k.thn_pajak_sppt = p.thn_pajak_sppt 

            $where AND (1=1)
            )z";
        $qry       = $this->db->query($sql_query);
        $row       = $qry->row();
        $iTotal    = $row->c;
        
        if ($search) {
            $sql_query = str_replace('AND (1=1)', $search, $sql_query);
            $qry       = $this->db->query($sql_query);
            $row       = $qry->row();
            $iFiltered = $row->c;
        } else
            $iFiltered = $iTotal;
        */
        
        /*
         * Output
         */
        // $sql_query_r = "SELECT  tgl_pembayaran_sppt kode,tp.kd_kanwil||tp.kd_kantor||tp.kd_tp||':'||tp.nm_tp uraian, 
        $sql_query_r = "SELECT  tgl_pembayaran_sppt kode,{$pos_uraian}||':'||tp.nm_tp uraian, p.thn_pajak_sppt,
            sum(p.jml_sppt_yg_dibayar - p.denda_sppt)  pokok, sum(p.denda_sppt) denda, 
            sum(p.jml_sppt_yg_dibayar) bayar
            FROM sppt k 
            INNER JOIN pembayaran_sppt p 
            ON k.kd_propinsi = p.kd_propinsi
            AND k.kd_dati2 = p.kd_dati2 
            AND k.kd_kecamatan = p.kd_kecamatan 
            AND k.kd_kelurahan = p.kd_kelurahan 
            AND k.kd_blok = p.kd_blok 
            AND k.no_urut = p.no_urut 
            AND k.kd_jns_op = p.kd_jns_op 
            AND k.thn_pajak_sppt = p.thn_pajak_sppt 
            LEFT JOIN tempat_pembayaran tp ON {$pos_join}
            $where $search 
            GROUP BY 1,2,3
            ORDER BY 1,2,3 ";
        $sql_query_r .= "$sOrder $sLimit";
        
        $output = array(
            "sEcho" => $sEcho,
            "iTotalRecords" => $iTotal,
            "iTotalDisplayRecords" => $iFiltered,
            "iDisplayStart" => $iDisplayStart,
            "iDisplayLength" => $iDisplayLength,
            // "SQL Query" => $sql_query_r,
            "aaData" => array()
        );
        
        $pg_pokok = 0;
        $pg_denda = 0;
        $pg_total = 0;
        
        $qry = $this->db->query($sql_query_r);
        foreach ($qry->result() as $aRow) {
            $row = array();
            for ($i = 0; $i < count($aColumns); $i++) {
                if ($i > 2)
                    $row[] = number_format($aRow->$aColumns[$i], 0, ',', '.');
                elseif($i == 0)
                    $row[] = date('d-m-Y', strtotime($aRow->$aColumns[$i]));
                else
                    $row[] = $aRow->$aColumns[$i];
            }
            
            $pg_pokok += $aRow->$aColumns[3];
            $pg_denda += $aRow->$aColumns[4];
            $pg_total += $aRow->$aColumns[5];
            
            $output['aaData'][] = $row;
        }
        /*
        $row                = array();
        $row[]              = '';
        $row[]              = 'Jumlah Halaman';
        $row[]              = number_format($pg_pokok, 0, ',', '.');
        $row[]              = number_format($pg_denda, 0, ',', '.');
        $row[]              = number_format($pg_total, 0, ',', '.');
        $output['aaData'][] = $row;
        
        if ($iDisplayStart + $iDisplayLength + 1 >= $iFiltered) {
            $sql_query_r = "SELECT  '' kode, 'TOTAL' uraian, 
                -- sum(k.pbb_yg_harus_dibayar_sppt)  pokok, sum(p.denda_sppt) denda, 
                sum(p.jml_sppt_yg_dibayar - p.denda_sppt)  pokok, sum(p.denda_sppt) denda, 
                sum(p.jml_sppt_yg_dibayar) bayar
                FROM sppt k 
                INNER JOIN pembayaran_sppt p 
                ON k.kd_propinsi = p.kd_propinsi
                AND k.kd_dati2 = p.kd_dati2 
                AND k.kd_kecamatan = p.kd_kecamatan 
                AND k.kd_kelurahan = p.kd_kelurahan 
                AND k.kd_blok = p.kd_blok 
                AND k.no_urut = p.no_urut 
                AND k.kd_jns_op = p.kd_jns_op 
                AND k.thn_pajak_sppt = p.thn_pajak_sppt 
                LEFT JOIN tempat_pembayaran tp 
                -- ON p.kd_kanwil=tp.kd_kanwil and p.kd_kantor=tp.kd_kantor AND p.kd_tp=tp.kd_tp
                ON {$pos_join}
                $where $search ";
            
            $qry = $this->db->query($sql_query_r);
            foreach ($qry->result() as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    if ($i > 1)
                        $row[] = number_format($aRow->$aColumns[$i], 0, ',', '.');
                    else
                        $row[] = $aRow->$aColumns[$i];
                }
                $output['aaData'][] = $row;
            }
        }
        */
        
        $output['pokok'] = number_format($pg_pokok, 0, ',', '.');
        $output['denda'] = number_format($pg_denda, 0, ',', '.');
        $output['total'] = number_format($pg_total, 0, ',', '.');
        
        echo json_encode($output);
    }
    
    function tranmonths()
    {
        // ob_start("ob_gzhandler");
        
        
        $buku        = (isset($_GET['buku'])) ? $_GET['buku'] : '11';
        $bukumin     = $this->pbbm_model->rangebuku[substr($buku, 0, 1)][0];
        $bukumax     = $this->pbbm_model->rangebuku[substr($buku, 1, 1)][1];
        $tahun       = (isset($_GET['tahun'])) ? $_GET['tahun'] : date('Y');
        $tahun_sppt1 = (isset($_GET['tahun_sppt1'])) ? $_GET['tahun_sppt1'] : date('Y');
        $tahun_sppt2 = (isset($_GET['tahun_sppt2'])) ? $_GET['tahun_sppt2'] : date('Y');
        $kec_kd      = (isset($_GET['kec_kd']) && is_numeric($_GET['kec_kd'])) ? $_GET['kec_kd'] : '000';
        $kel_kd      = (isset($_GET['kel_kd']) && is_numeric($_GET['kel_kd'])) ? $_GET['kel_kd'] : '000';
        
        if (get_user_kec_kd() != '000' && get_user_kec_kd() != $kec_kd)
            $kec_kd = get_user_kec_kd();
        
        if (get_user_kel_kd() != '000' && get_user_kel_kd() != $kel_kd)
            $kec_kd = get_user_kel_kd();
        
        $path_to_root = active_module_url();
        
        $aColumns     = array(
            'kode',
            'uraian',
            'thn_pajak_sppt',
            'pokok',
            'denda',
            'bayar'
        );
        $sIndexColumn = "kode";
        
        $iDisplayLength = (isset($_GET['iDisplayLength']) && is_numeric($_GET['iDisplayLength'])) ? $_GET['iDisplayLength'] : 15;
        $iDisplayStart  = (isset($_GET['iDisplayStart']) && is_numeric($_GET['iDisplayStart'])) ? $_GET['iDisplayStart'] : 0;
        
        $iSortCol_0   = (isset($_GET['iSortCol_0']) && is_numeric($_GET['iSortCol_0'])) ? $_GET['iSortCol_0'] : 0;
        $iSortingCols = (isset($_GET['iSortingCols']) && is_numeric($_GET['iSortingCols'])) ? $_GET['iSortingCols'] : 1;
        $sSortDir_0   = (isset($_GET['sSortDir_0'])) ? $_GET['sSortDir_0'] : "asc";
        
        $sEcho = (isset($_GET['sEcho']) && is_numeric($_GET['sEcho'])) ? $_GET['sEcho'] : 1;
        
        $sSearch   = (isset($_GET['sSearch'])) ? $_GET['sSearch'] : "";
        $sSearch_0 = (isset($_GET['sSearch_0'])) ? $_GET['sSearch_0'] : "";
        $sSearch_1 = (isset($_GET['sSearch_1'])) ? $_GET['sSearch_1'] : "";
        $sSearch_2 = (isset($_GET['sSearch_2'])) ? $_GET['sSearch_2'] : "";
        $sSearch_3 = (isset($_GET['sSearch_3'])) ? $_GET['sSearch_3'] : "";
        $sSearch_4 = (isset($_GET['sSearch_4'])) ? $_GET['sSearch_4'] : "";
        
        /*
         * Limit
         */
        
        $sLimit = "";
        if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1') {
            // $sLimit = "LIMIT " . $_GET['iDisplayLength'] . " OFFSET " . $_GET['iDisplayStart'];
        }
        
        
        /*
         * Ordering
         */
        $sOrder = "";
        if (isset($_GET['iSortCol_0'])) {
            $sOrder = "ORDER BY ";
            for ($i = 0; $i < intval($_GET['iSortingCols']); $i++) {
                if ($_GET['bSortable_' . intval($_GET['iSortCol_' . $i])] == "true") {
                    if ($aColumns[intval($_GET['iSortCol_' . $i])] == "bphtbno" || $aColumns[intval($_GET['iSortCol_' . $i])] == "tanggal") {
                        $sOrder .= $aColumns[intval($_GET['iSortCol_' . $i])] . " " . $_GET['sSortDir_' . $i] . ", ";
                    } else {
                        $sOrder .= $aColumns[intval($_GET['iSortCol_' . $i])] . ' ' . $_GET['sSortDir_' . $i] . ", ";
                    }
                }
            }
            
            $sOrder = substr_replace($sOrder, "", -2) . ", kode";
            if ($sOrder == "ORDER BY ") {
                $sOrder = "";
            }
        }
        
        
        /* 
         * Filtering
         * NOTE this does not match the built-in DataTables filtering which does it
         * word by word on any field. It's possible to do here, but concerned about efficiency
         * on very large tables, and MySQL's regex functionality is very limited
         */
        
        
        $search = '';
        if ($sSearch)
            $search .= " AND tp.nm_tp ilike '%$sSearch%'";
        
        $where = "WHERE extract(year FROM p.tgl_pembayaran_sppt)= $tahun 
            AND p.kd_propinsi='" . KD_PROPINSI . "' 
            AND p.kd_dati2='" . KD_DATI2 . "'
            AND k.pbb_yg_harus_dibayar_sppt between $bukumin AND $bukumax 
            AND p.thn_pajak_sppt between '$tahun_sppt1' AND '$tahun_sppt2' ";
        if ($kec_kd != "000")
            $where .= " AND p.kd_kecamatan='$kec_kd'";
        if ($kel_kd != "000")
            $where .= " AND p.kd_kelurahan='$kel_kd'";
        
        // POS_FIELD
        $fields     = explode(',', POS_FIELD);
        $pos_fld    = '';
        $pos_join   = '';
        $pos_uraian = '';
        $fs = '';
        foreach ($fields as $f) {
            $fs = $f;
            if ($f == 'kd_kanwil_bank')
                $fs = 'kd_kanwil';
            else if ($f == 'kd_kppbb_bank')
                $fs = 'kd_kppbb';

            $pos_fld .= "p.{$f}, ";
            $pos_join .= "p.{$f}=tp.{$fs} and ";
            $pos_uraian .= "tp.{$fs}||";
        }
        $pos_fld = substr($pos_fld, 0, -2);
        $pos_join = substr($pos_join, 0, -4);
        $pos_uraian = substr($pos_uraian, 0, -2);
        
        $tp_kd = (isset($_GET['tp_kd'])) ? $_GET['tp_kd'] : '';
        if ($tp_kd != "")
            $where .= " AND {$pos_uraian} = '{$tp_kd}'";
            
        /*
        $iTotal    = 0;
        $iFiltered = 0;

        $sql_query = "SELECT count(*) c FROM  (
            -- SELECT DISTINCT p.kd_kanwil, p.kd_kantor, p.kd_tp, tp.nm_tp, extract(month FROM p.tgl_pembayaran_sppt) 
            SELECT DISTINCT {$pos_fld}, tp.nm_tp, extract(month FROM p.tgl_pembayaran_sppt) 
            FROM pembayaran_sppt p 
            LEFT JOIN tempat_pembayaran tp 
            -- ON p.kd_kanwil=tp.kd_kanwil and p.kd_kantor=tp.kd_kantor AND p.kd_tp=tp.kd_tp 
            ON {$pos_join}
            INNER JOIN sppt k ON k.kd_propinsi = p.kd_propinsi
            AND k.kd_dati2 = p.kd_dati2 
            AND k.kd_kecamatan = p.kd_kecamatan 
            AND k.kd_kelurahan = p.kd_kelurahan 
            AND k.kd_blok = p.kd_blok 
            AND k.no_urut = p.no_urut 
            AND k.kd_jns_op = p.kd_jns_op 
            AND k.thn_pajak_sppt = p.thn_pajak_sppt 

            $where AND (1=1) )a";
        $qry       = $this->db->query($sql_query);
        $row       = $qry->row();
        $iTotal    = $row->c;
        
        if ($search) {
            $sql_query = str_replace('AND (1=1)', $search, $sql_query);
            $qry       = $this->db->query($sql_query);
            $row       = $qry->row();
            $iFiltered = $row->c;
        } else
            $iFiltered = $iTotal;
        */
        
        /*
         * Output
         */
        $sql_query_r = "SELECT  Extract(month FROM tgl_pembayaran_sppt) kode,
            {$pos_uraian}||':'||tp.nm_tp uraian, p.thn_pajak_sppt,
            sum(p.jml_sppt_yg_dibayar - p.denda_sppt)  pokok, 
            sum(p.denda_sppt) denda, sum(p.jml_sppt_yg_dibayar) bayar
            FROM sppt k 
            INNER JOIN pembayaran_sppt p 
            ON k.kd_propinsi = p.kd_propinsi
            AND k.kd_dati2 = p.kd_dati2 
            AND k.kd_kecamatan = p.kd_kecamatan 
            AND k.kd_kelurahan = p.kd_kelurahan 
            AND k.kd_blok = p.kd_blok 
            AND k.no_urut = p.no_urut 
            AND k.kd_jns_op = p.kd_jns_op 
            AND k.thn_pajak_sppt = p.thn_pajak_sppt 
            LEFT JOIN tempat_pembayaran tp ON {$pos_join}
            $where $search
            GROUP BY 1,2,3
            ORDER BY 1,2,3 ";
        $sql_query_r .= "$sOrder $sLimit";
        
        $output = array(
            "sEcho" => $sEcho,
            // "iTotalRecords" => $iTotal,
            // "iTotalDisplayRecords" => $iFiltered,
            // "SQL Query" => $sql_query_r,
            "aaData" => array()
        );
        
        $qry      = $this->db->query($sql_query_r);
        $pg_pokok = 0;
        $pg_denda = 0;
        $pg_total = 0;
        foreach ($qry->result() as $aRow) {
            $row = array();
            for ($i = 0; $i < count($aColumns); $i++) {
                if ($i == 0) {
                    $row[] = $aRow->$aColumns[$i] . ':' . namabulan($aRow->$aColumns[$i]);
                } else if ($i > 2) {
                    $row[] = number_format($aRow->$aColumns[$i], 0, ',', '.');
                } else
                    $row[] = $aRow->$aColumns[$i];
            }
            $pg_pokok += $aRow->$aColumns[3];
            $pg_denda += $aRow->$aColumns[4];
            $pg_total += $aRow->$aColumns[5];
            
            $output['aaData'][] = $row;
        }
        /*
        $row                = array();
        $row[]              = '';
        $row[]              = 'TOTAL';
        $row[]              = number_format($pg_pokok, 0, ',', '.');
        $row[]              = number_format($pg_denda, 0, ',', '.');
        $row[]              = number_format($pg_total, 0, ',', '.');
        $output['aaData'][] = $row;
        */
        
        $output['pokok'] = number_format($pg_pokok, 0, ',', '.');
        $output['denda'] = number_format($pg_denda, 0, ',', '.');
        $output['total'] = number_format($pg_total, 0, ',', '.');
        
        echo json_encode($output);
    }
    
    function realisasi()
    {
        ob_start("ob_gzhandler");
       
        $path_to_root = active_module_url();
        
        $aColumns     = array(
            'kode',
            'uraian',
            'sppt1',
            'amount1',
            'sppt2',
            'amount2',
            'sppt3',
            'amount3',
            'sppt4',
            'amount4',
            'prsn1',
            'sppt5',
            'amount5',
            'prsn2'
        );
        $sIndexColumn = "kode";
        
        $iDisplayLength = (isset($_GET['iDisplayLength']) && is_numeric($_GET['iDisplayLength'])) ? $_GET['iDisplayLength'] : 15;
        $iDisplayStart  = (isset($_GET['iDisplayStart']) && is_numeric($_GET['iDisplayStart'])) ? $_GET['iDisplayStart'] : 0;
        $iSortCol_0     = (isset($_GET['iSortCol_0']) && is_numeric($_GET['iSortCol_0'])) ? $_GET['iSortCol_0'] : 0;
        $iSortingCols   = (isset($_GET['iSortingCols']) && is_numeric($_GET['iSortingCols'])) ? $_GET['iSortingCols'] : 1;
        $sEcho          = (isset($_GET['sEcho']) && is_numeric($_GET['sEcho'])) ? $_GET['sEcho'] : 1;
        $sSearch        = (isset($_GET['sSearch'])) ? $_GET['sSearch'] : "";
        $sSearch_0      = (isset($_GET['sSearch_0'])) ? $_GET['sSearch_0'] : "";
        $sSearch_1      = (isset($_GET['sSearch_1'])) ? $_GET['sSearch_1'] : "";
        $sSearch_2      = (isset($_GET['sSearch_2'])) ? $_GET['sSearch_2'] : "";
        $sSearch_3      = (isset($_GET['sSearch_3'])) ? $_GET['sSearch_3'] : "";
        $sSearch_4      = (isset($_GET['sSearch_4'])) ? $_GET['sSearch_4'] : "";
        $sSortDir_0     = (isset($_GET['sSortDir_0'])) ? $_GET['sSortDir_0'] : "asc";
        
        /*
         * Limit
         */
        
        $sLimit  = "";
        $sSearch = "";
        $search  = '';
        /*
         * Ordering
         */
        $sOrder  = "";
        
        /* Filtering
         * NOTE this does not match the built-in DataTables filtering which does it
         * word by word on any field. It's possible to do here, but concerned about efficiency
         * on very large tables, and MySQL's regex functionality is very limited
         */
        $sWhere = "";
        
        $tahun    = (isset($_GET['tahun'])) ? $_GET['tahun'] : date('Y');
        $buku     = (isset($_GET['buku'])) ? $_GET['buku'] : '11';
        $kec_kd   = (isset($_GET['kec_kd']) && is_numeric($_GET['kec_kd'])) ? $_GET['kec_kd'] : '000';
        $kel_kd   = (isset($_GET['kel_kd']) && is_numeric($_GET['kel_kd'])) ? $_GET['kel_kd'] : '000';
        $nop_kd   = (isset($_GET['nop_kd']) && is_numeric($_GET['nop_kd'])) ? $_GET['kel_kd'] : '000000000000000000';
        $tglawal  = (isset($_GET['tglawal'])) ? $_GET['tglawal'] : date('d-m-Y');
        $tglakhir = (isset($_GET['tglakhir'])) ? $_GET['tglakhir'] : date('d-m-Y');
        
        $tglm = substr($tglawal, 6, 4) . '-' . substr($tglawal, 3, 2) . '-' . substr($tglawal, 0, 2);
        $tgls = substr($tglakhir, 6, 4) . '-' . substr($tglakhir, 3, 2) . '-' . substr($tglakhir, 0, 2);
        
        if (get_user_kec_kd() != '000' && get_user_kec_kd() != $kec_kd)
            $kec_kd = get_user_kec_kd();
        if (get_user_kel_kd() != '000' && get_user_kel_kd() != $kel_kd)
            $kec_kd = get_user_kel_kd();
        
        /*
         * SQL queries
         * Get data to display
         */
        
        if ($kec_kd == '000') {
            $iDisplayLength = 0;
            $sql_query_c = "SELECT COUNT(*) AS c FROM ref_kecamatan ";
            $sql_query_r = $this->pbbm_model->qry_realisasi_kec($tahun, $tglm, $tgls, $buku);
        } else if ($kel_kd == '000') {
            $iDisplayLength = 0;
            $sql_query_c = "SELECT COUNT(*) AS c FROM ref_kelurahan k 
                WHERE k.kd_propinsi='" . KD_PROPINSI . "' 
                AND k.kd_dati2='" . KD_DATI2 . "'
                AND k.kd_kecamatan='$kec_kd'";
            $sql_query_r = $this->pbbm_model->qry_realisasi_kel($tahun, $tglm, $tgls, $kec_kd, $buku);
        } else if ($nop_kd = '000000000000000000') {
            //$sLimit  = "LIMIT  $iDisplayLength OFFSET $iDisplayStart";
            $sSearch = (isset($_GET['sSearch'])) ? $_GET['sSearch'] : "";
            if ($sSearch)
                $search .= " AND a.nm_wp_sppt ilike '%$sSearch%' ";
            $bukumin = $this->pbbm_model->rangebuku[substr($buku, 0, 1)][0];
            $bukumax = $this->pbbm_model->rangebuku[substr($buku, 1, 1)][1];
            $search .= " AND a.pbb_yg_harus_dibayar_sppt between $bukumin AND $bukumax";
            $sql_query_c = "
                SELECT COUNT(*) AS c 
                FROM sppt a
                WHERE a.kd_propinsi='" . KD_PROPINSI . "' 
                AND a.kd_dati2='" . KD_DATI2 . "' 
                AND a.kd_kecamatan='$kec_kd' 
                AND a.kd_kelurahan='$kel_kd'
                AND a.thn_pajak_sppt='$tahun' ";
            
            $sql_query_r = $this->pbbm_model->qry_realisasi_op($tahun, $tglm, $tgls, $kec_kd, $kel_kd, $buku);
            
            if ($search) {
                $sql_query_r = str_replace('AND (1=1)', $search, $sql_query_r);
            }
        }
        
        /*
         * Output
         */
         /*
        $qry    = $this->db->query($sql_query_c);
        $row    = $qry->row();
        $iTotal = $row->c;
        if ($search) {
            $qry            = $this->db->query($sql_query_c . $search);
            $row            = $qry->row();
            $iFilteredTotal = $row->c;
        } else
            $iFilteredTotal = $iTotal;
        */
        
        $output = array(
            "sEcho" => $sEcho,
            "iTotalRecords" => 0, //$iTotal,
            "iTotalDisplayRecords" => 0, //$iFilteredTotal,
            // "SQL Query" => $sql_query_r,
            "aaData" => array()
        );
        $nsppt1=0; $nsppt2=0;$nsppt3=0; $nsppt4 =0; $nsppt5 =0;
        $amount1=0; $amount2=0; $amount3=0; $amount4=0; $amount5=0;
        
        $qry = $this->db->query($sql_query_r . " $sOrder $sLimit");
        foreach ($qry->result() as $aRow) {
            $row = array();
            for ($i = 0; $i < count($aColumns); $i++) {
                if ($i == 1) {
                    if ($kec_kd == '000')
                        $row[] = "<a href=\"" . active_module_url() . "realisasi?tahun=$tahun&buku=$buku&tgawal=$tglawal&tglakhir=$tglakhir&kec_kd=" . substr($aRow->kode, -3) . "\">" . $aRow->$aColumns[$i] . "</a>";
                    else if ($kel_kd == '000')
                        $row[] = "<a href=\"" . active_module_url() . "realisasi?tahun=$tahun&buku=$buku&tgawal=$tglawal&tglakhir=$tglakhir&kec_kd=$kec_kd&kel_kd=" . substr($aRow->kode, -3) . "\">" . $aRow->$aColumns[$i] . "</a>";
                    else if ($nop_kd == '000000000000000000')
                        $row[] = "<a href=\"" . active_module_url() . "op?&nop_kd=" . $aRow->kode . "\">" . $aRow->$aColumns[$i] . "</a>";
                    
                } else if ($aColumns[$i] == 'sppt4'){
                    $row[] = number_format($aRow->sppt2 + $aRow->sppt3, 0, ',', '.');
                    $nsppt4 += $aRow->sppt2 + $aRow->sppt3;
                } else if ($aColumns[$i] == 'amount4'){
                    $row[] = number_format($aRow->amount2 + $aRow->amount3, 0, ',', '.');
                    $amount4 += $aRow->amount2 + $aRow->amount3;
                } else if ($aColumns[$i] == 'prsn1') {
                    if ($aRow->amount1 > 0)
                        $row[] = number_format(($aRow->amount2 + $aRow->amount3) / $aRow->amount1 * 100, 2, ',', '.');
                    else
                        $row[] = number_format(0, 0, ',', '.');
                } else if ($aColumns[$i] == 'sppt5'){
                
                    $row[] = number_format($aRow->sppt1 - $aRow->sppt2 - $aRow->sppt3, 0, ',', '.');
                    $nsppt5 += $aRow->sppt1 - $aRow->sppt2 - $aRow->sppt3;
 
                } else if ($aColumns[$i] == 'amount5'){
                    $row[] = number_format($aRow->amount1 - $aRow->amount2 - $aRow->amount3, 0, ',', '.');
                    $amount5 += $aRow->amount1 - $aRow->amount2 - $aRow->amount3;

                } else if ($aColumns[$i] == 'prsn2') {
                    if ($aRow->amount1 > 0)
                        $row[] = number_format(($aRow->amount1 - $aRow->amount2 - $aRow->amount3) / $aRow->amount1 * 100, 2, ',', '.');
                    else
                        $row[] = number_format(0, 0, ',', '.');
                } else if ($i > 1)
                    $row[] = number_format($aRow->$aColumns[$i], 0, ',', '.');
                else{
                    $row[] = $aRow->$aColumns[$i];
                }
            }
            $nsppt1+=$aRow->sppt1;
            $nsppt2+=$aRow->sppt2;
            $nsppt3+=$aRow->sppt3;
            $amount1+=$aRow->amount1;
            $amount2+=$aRow->amount2;
            $amount3+=$aRow->amount3;
            
            $output['aaData'][] = $row;
        }
        
        /*
        //JUMLAH HALAMAN
        $row = array();
        $row[] = '&nbsp';
        $row[] = "JUMLAH";
        $row[] = number_format($nsppt1, 0, ',', '.');
        $row[] = number_format($amount1, 0, ',', '.');
        $row[] = number_format($nsppt2, 0, ',', '.');
        $row[] = number_format($amount2, 0, ',', '.');
        $row[] = number_format($nsppt3, 0, ',', '.');
        $row[] = number_format($amount3, 0, ',', '.');
        $row[] = number_format($nsppt4, 0, ',', '.');
        $row[] = number_format($amount4, 0, ',', '.');
        $row[] = number_format(($amount1>0?$amount4/$amount1*100:0), 2, ',', '.');
        $row[] = number_format($nsppt5, 0, ',', '.');
        $row[] = number_format($amount5, 0, ',', '.');
        $row[] = number_format(($amount1>0?$amount5/$amount1*100:0), 2, ',', '.');
        $output['aaData'][] = $row;
            
        //Buat Sub Total Halaman
        $sql_query_t = "SELECT '' kode, 'JUMLAH' uraian, SUM(sppt1) sppt1, sum(amount1) amount1, 
                          sum(sppt2)sppt2, sum(amount2)amount2, 
                          sum(sppt3)sppt3, sum(amount3)amount3,
                          sum(sppt4)sppt4, sum(amount4)amount4, 
                          sum(sppt5)sppt5, sum(amount5)amount5 
          FROM ($sql_query_r $sOrder $sLimit) a 
          GROUP BY 1,2 ";
          
        //Buat Total 
        if ($nop_kd == '000000000000000000' && $iFilteredTotal <= ($iDisplayLength + $iDisplayStart)) {
            $sql_query_t = " SELECT '' kode, 'TOTAL' uraian, SUM(sppt1) sppt1, sum(amount1) amount1, 
                          sum(sppt2)sppt2, sum(amount2)amount2, 
                          sum(sppt3)sppt3, sum(amount3)amount3,
                          sum(sppt4)sppt4, sum(amount4)amount4, 
                          sum(sppt5)sppt5, sum(amount5)amount5 
                FROM ($sql_query_r) a 
                GROUP BY 1,2
                ";
        
            $qry = $this->db->query($sql_query_t);
            
            foreach ($qry->result() as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    if ($aColumns[$i] == 'sppt4')
                        $row[] = number_format($aRow->sppt2 + $aRow->sppt3, 0, ',', '.');
                    else if ($aColumns[$i] == 'amount4')
                        $row[] = number_format($aRow->amount2 + $aRow->amount3, 0, ',', '.');
                    else if ($aColumns[$i] == 'prsn1') {
                        if ($aRow->amount1 > 0)
                            $row[] = number_format(($aRow->amount2 + $aRow->amount3) / $aRow->amount1 * 100, 2, ',', '.');
                        else
                            $row[] = number_format(0, 2, ',', '.');
                    } else if ($aColumns[$i] == 'sppt5')
                        $row[] = number_format($aRow->sppt1 - $aRow->sppt2 - $aRow->sppt3, 0, ',', '.');
                    else if ($aColumns[$i] == 'amount5')
                        $row[] = number_format($aRow->amount1 - $aRow->amount2 - $aRow->amount3, 0, ',', '.');
                    else if ($aColumns[$i] == 'prsn2') {
                        if ($aRow->amount1 > 0)
                            $row[] = number_format(($aRow->amount1 - $aRow->amount2 - $aRow->amount3) / $aRow->amount1 * 100, 2, ',', '.');
                        else
                            $row[] = number_format(0, 2, ',', '.');
                    } else if ($i > 1)
                        $row[] = number_format($aRow->$aColumns[$i], 0, ',', '.');
                    else
                        $row[] = $aRow->$aColumns[$i];
                }
                $output['aaData'][] = $row;
            }
        }
        */
        
        $output['nsppt1'] = number_format($nsppt1, 0, ',', '.');
        $output['amount1'] = number_format($amount1, 0, ',', '.');
        $output['nsppt2'] = number_format($nsppt2, 0, ',', '.');
        $output['amount2'] = number_format($amount2, 0, ',', '.');
        $output['nsppt3'] = number_format($nsppt3, 0, ',', '.');
        $output['amount3'] = number_format($amount3, 0, ',', '.');
        $output['nsppt4'] = number_format($nsppt4, 0, ',', '.');
        $output['amount4'] = number_format($amount4, 0, ',', '.');
        $output['persen1'] = number_format(($amount1>0?$amount4/$amount1*100:0), 2, ',', '.');
        $output['nsppt5'] = number_format($nsppt5, 0, ',', '.');
        $output['amount5'] = number_format($amount5, 0, ',', '.');
        $output['persen2'] = number_format(($amount1>0?$amount5/$amount1*100:0), 2, ',', '.');
        
        echo json_encode($output);
    }
    
    function lb()
    {
        // ob_start("ob_gzhandler");
        //$this->load->model("pbbm_model");
        
        $path_to_root = active_module_url();
        
        $aColumns     = array(
            'kode',
            'uraian',
            'sppt1',
            'amount1',
            'amount2',
            'amount3'
        );
        $sIndexColumn = "kode";
        
        $iDisplayLength = (isset($_GET['iDisplayLength']) && is_numeric($_GET['iDisplayLength'])) ? $_GET['iDisplayLength'] : 15;
        $iDisplayStart  = (isset($_GET['iDisplayStart']) && is_numeric($_GET['iDisplayStart'])) ? $_GET['iDisplayStart'] : 0;
        $iSortCol_0     = (isset($_GET['iSortCol_0']) && is_numeric($_GET['iSortCol_0'])) ? $_GET['iSortCol_0'] : 0;
        $iSortingCols   = (isset($_GET['iSortingCols']) && is_numeric($_GET['iSortingCols'])) ? $_GET['iSortingCols'] : 1;
        $sEcho          = (isset($_GET['sEcho']) && is_numeric($_GET['sEcho'])) ? $_GET['sEcho'] : 1;
        $sSearch        = (isset($_GET['sSearch'])) ? $_GET['sSearch'] : "";
        $sSearch_0      = (isset($_GET['sSearch_0'])) ? $_GET['sSearch_0'] : "";
        $sSearch_1      = (isset($_GET['sSearch_1'])) ? $_GET['sSearch_1'] : "";
        $sSearch_2      = (isset($_GET['sSearch_2'])) ? $_GET['sSearch_2'] : "";
        $sSearch_3      = (isset($_GET['sSearch_3'])) ? $_GET['sSearch_3'] : "";
        $sSearch_4      = (isset($_GET['sSearch_4'])) ? $_GET['sSearch_4'] : "";
        $sSortDir_0     = (isset($_GET['sSortDir_0'])) ? $_GET['sSortDir_0'] : "asc";
        
        /*
         * Limit
         */
        
        $sLimit  = "";
        $sSearch = "";
        $search  = '';
        /*
         * Ordering
         */
        $sOrder  = "";
        /*if(isset($_GET['iSortCol_0'])){
        $sOrder = "ORDER BY ";
        for($i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ ){
        if($_GET['bSortable_'.intval($_GET['iSortCol_'.$i])] == "true" ){
        if($aColumns[ intval( $_GET['iSortCol_'.$i] ) ]=="bphtbno" || $aColumns[ intval( $_GET['iSortCol_'.$i] ) ]=="tanggal"){
        $sOrder .= $aColumns[ intval( $_GET['iSortCol_'.$i] ) ]." ". $_GET['sSortDir_'.$i] .", ";
        }else{
        $sOrder .= $aColumns[ intval( $_GET['iSortCol_'.$i] ) ].' '. $_GET['sSortDir_'.$i] .", ";
        }
        }
        }
        
        $sOrder = substr_replace($sOrder, "", -2);
        if($sOrder == "ORDER BY "){
        $sOrder = "";
        }
        }*/
        
        
        /* Filtering
         * NOTE this does not match the built-in DataTables filtering which does it
         * word by word on any field. It's possible to do here, but concerned about efficiency
         * on very large tables, and MySQL's regex functionality is very limited
         */
        $sWhere = "";
        
        $tahun  = (isset($_GET['tahun'])) ? $_GET['tahun'] : date('Y');
        $kec_kd = (isset($_GET['kec_kd']) && is_numeric($_GET['kec_kd'])) ? $_GET['kec_kd'] : '000';
        $kel_kd = (isset($_GET['kel_kd']) && is_numeric($_GET['kel_kd'])) ? $_GET['kel_kd'] : '000';
        $nop_kd = (isset($_GET['nop_kd']) && is_numeric($_GET['nop_kd'])) ? $_GET['kel_kd'] : '000000000000000000';
        
        if (get_user_kec_kd() != '000' && get_user_kec_kd() != $kec_kd)
            $kec_kd = get_user_kec_kd();
        if (get_user_kel_kd() != '000' && get_user_kel_kd() != $kel_kd)
            $kec_kd = get_user_kel_kd();
        
        /*
         * SQL queries
         * Get data to display
         */
        
        if ($kec_kd == '000') {
            $sql_query_c = "SELECT COUNT(*) AS c FROM ref_kecamatan ";
            $sql_query_r = $this->pbbm_model->qry_realisasi_lb_kec($tahun);
        } else if ($kel_kd == '000') {
            $sql_query_c = "SELECT COUNT(*) AS c FROM ref_kelurahan k 
                WHERE k.kd_propinsi='" . KD_PROPINSI . "' 
                AND k.kd_dati2='" . KD_DATI2 . "'
                AND k.kd_kecamatan='$kec_kd'";
            $sql_query_r = $this->pbbm_model->qry_realisasi_lb_kel($tahun, $kec_kd);
        }
        
        else if ($nop_kd = '000000000000000000') {
            // $sLimit  = "LIMIT  $iDisplayLength OFFSET $iDisplayStart";
            $sSearch = (isset($_GET['sSearch'])) ? $_GET['sSearch'] : "";
            if ($sSearch)
                $search .= " AND a.nm_wp_sppt ilike '%$sSearch%' ";
            
            
            
            $sql_query_r = $this->pbbm_model->qry_realisasi_lb_op($tahun, $kec_kd, $kel_kd);
            
            if ($search) {
                $sql_query_r = str_replace('AND (1=1)', $search, $sql_query_r);
            }
        }
        
        $sql_query_c = "SELECT COUNT(*) AS c FROM ($sql_query_r) z";
        
        /*
         * Output
         */
         /*
        $qry    = $this->db->query($sql_query_c);
        $row    = $qry->row();
        $iTotal = $row->c;
        if ($search) {
            $qry            = $this->db->query($sql_query_c . $search);
            $row            = $qry->row();
            $iFilteredTotal = $row->c;
        } else
            $iFilteredTotal = $iTotal;
        */
        
        $output   = array(
            "sEcho" => $sEcho,
            "iTotalRecords" => 0, //$iTotal,
            "iTotalDisplayRecords" => 0, //$iFilteredTotal,
            "iDisplayStart" => 0, //$iDisplayStart,
            // "SQL Query" => $sql_query_r,
            "aaData" => array()
        );
        //die($sql_query_r." $sOrder $sLimit");
        $qry      = $this->db->query($sql_query_r . " $sOrder $sLimit");
        $pg_sppt  = 0;
        $pg_pokok = 0;
        $pg_denda = 0;
        $pg_total = 0;
        
        foreach ($qry->result() as $aRow) {
            $row = array();
            for ($i = 0; $i < count($aColumns); $i++) {
                if ($i == 1) {
                    if ($kec_kd == '000')
                        $row[] = "<a href=\"" . active_module_url() . "lb?tahun=$tahun&kec_kd=" . substr($aRow->kode, -3) . "\">" . $aRow->$aColumns[$i] . "</a>";
                    else if ($kel_kd == '000')
                        $row[] = "<a href=\"" . active_module_url() . "lb?tahun=$tahun&kec_kd=$kec_kd&kel_kd=" . substr($aRow->kode, -3) . "\">" . $aRow->$aColumns[$i] . "</a>";
                    else if ($nop_kd == '000000000000000000')
                        $row[] = "<a href=\"" . active_module_url() . "op?&nop_kd=" . $aRow->kode . "\">" . $aRow->$aColumns[$i] . "</a>";
                    
                } else if ($aColumns[$i] == 'amount3') {
                    $row[] = number_format($aRow->amount1 - $aRow->amount2, 0, ',', '.');
                } else if ($i > 1)
                    $row[] = number_format($aRow->$aColumns[$i], 0, ',', '.');
                else
                    $row[] = $aRow->$aColumns[$i];
            }
            $pg_sppt += $aRow->$aColumns[2];
            $pg_pokok += $aRow->$aColumns[3];
            $pg_denda += $aRow->$aColumns[4];
            $pg_total += $aRow->$aColumns[5];
            $output['aaData'][] = $row;
        }
        
        /*
        //Buat Total Halaman
        $row                = array();
        $row[]              = '';
        $row[]              = 'Jumlah Halaman';
        $row[]              = number_format($pg_sppt, 0, ',', '.');
        $row[]              = number_format($pg_pokok, 0, ',', '.');
        $row[]              = number_format($pg_denda, 0, ',', '.');
        $row[]              = number_format($pg_total, 0, ',', '.');
        $output['aaData'][] = $row;
        
        if ($iFilteredTotal <= ($iDisplayLength + $iDisplayStart)) {
            $sql_query_t = "SELECT '' kode, 'TOTAL' uraian, SUM(sppt1) sppt1, sum(amount1) amount1, 
                sum(amount2)amount2
                FROM ($sql_query_r) a 
                GROUP BY 1,2 ";
            $qry         = $this->db->query($sql_query_t);
            
            foreach ($qry->result() as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    if ($aColumns[$i] == 'amount3') {
                        $row[] = number_format($aRow->amount1 - $aRow->amount2, 0, ',', '.');
                    } else if ($i > 1)
                        $row[] = number_format($aRow->$aColumns[$i], 0, ',', '.');
                    else
                        $row[] = $aRow->$aColumns[$i];
                }
                $output['aaData'][] = $row;
            }
        }
        */
        
        $output['sppt']  = number_format($pg_sppt, 0, ',', '.');
        $output['pokok'] = number_format($pg_pokok, 0, ',', '.');
        $output['denda'] = number_format($pg_denda, 0, ',', '.');
        $output['total'] = number_format($pg_total, 0, ',', '.');
        
        echo json_encode($output);
    }
    
    
    function kb()
    {
        ob_start("ob_gzhandler");
        //$this->load->model("pbbm_model");
        
        $path_to_root = active_module_url();
        
        $aColumns     = array(
            'kode',
            'uraian',
            'sppt1',
            'amount1',
            'amount2',
            'amount3'
        );
        $sIndexColumn = "kode";
        
        $iDisplayLength = (isset($_GET['iDisplayLength']) && is_numeric($_GET['iDisplayLength'])) ? $_GET['iDisplayLength'] : 15;
        $iDisplayStart  = (isset($_GET['iDisplayStart']) && is_numeric($_GET['iDisplayStart'])) ? $_GET['iDisplayStart'] : 0;
        $iSortCol_0     = (isset($_GET['iSortCol_0']) && is_numeric($_GET['iSortCol_0'])) ? $_GET['iSortCol_0'] : 0;
        $iSortingCols   = (isset($_GET['iSortingCols']) && is_numeric($_GET['iSortingCols'])) ? $_GET['iSortingCols'] : 1;
        $sEcho          = (isset($_GET['sEcho']) && is_numeric($_GET['sEcho'])) ? $_GET['sEcho'] : 1;
        $sSearch        = (isset($_GET['sSearch'])) ? $_GET['sSearch'] : "";
        $sSearch_0      = (isset($_GET['sSearch_0'])) ? $_GET['sSearch_0'] : "";
        $sSearch_1      = (isset($_GET['sSearch_1'])) ? $_GET['sSearch_1'] : "";
        $sSearch_2      = (isset($_GET['sSearch_2'])) ? $_GET['sSearch_2'] : "";
        $sSearch_3      = (isset($_GET['sSearch_3'])) ? $_GET['sSearch_3'] : "";
        $sSearch_4      = (isset($_GET['sSearch_4'])) ? $_GET['sSearch_4'] : "";
        $sSortDir_0     = (isset($_GET['sSortDir_0'])) ? $_GET['sSortDir_0'] : "asc";
        
        /*
         * Limit
         */
        
        $sLimit  = "";
        $sSearch = "";
        $search  = '';
        /*
         * Ordering
         */
        $sOrder  = "";
        /*if(isset($_GET['iSortCol_0'])){
        $sOrder = "ORDER BY ";
        for($i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ ){
        if($_GET['bSortable_'.intval($_GET['iSortCol_'.$i])] == "true" ){
        if($aColumns[ intval( $_GET['iSortCol_'.$i] ) ]=="bphtbno" || $aColumns[ intval( $_GET['iSortCol_'.$i] ) ]=="tanggal"){
        $sOrder .= $aColumns[ intval( $_GET['iSortCol_'.$i] ) ]." ". $_GET['sSortDir_'.$i] .", ";
        }else{
        $sOrder .= $aColumns[ intval( $_GET['iSortCol_'.$i] ) ].' '. $_GET['sSortDir_'.$i] .", ";
        }
        }
        }
        
        $sOrder = substr_replace($sOrder, "", -2);
        if($sOrder == "ORDER BY "){
        $sOrder = "";
        }
        }*/
        
        
        /* Filtering
         * NOTE this does not match the built-in DataTables filtering which does it
         * word by word on any field. It's possible to do here, but concerned about efficiency
         * on very large tables, and MySQL's regex functionality is very limited
         */
        $sWhere = "";
        
        $tahun  = (isset($_GET['tahun'])) ? $_GET['tahun'] : date('Y');
        $kec_kd = (isset($_GET['kec_kd']) && is_numeric($_GET['kec_kd'])) ? $_GET['kec_kd'] : '000';
        $kel_kd = (isset($_GET['kel_kd']) && is_numeric($_GET['kel_kd'])) ? $_GET['kel_kd'] : '000';
        $nop_kd = (isset($_GET['nop_kd']) && is_numeric($_GET['nop_kd'])) ? $_GET['kel_kd'] : '000000000000000000';
        
        if (get_user_kec_kd() != '000' && get_user_kec_kd() != $kec_kd)
            $kec_kd = get_user_kec_kd();
        if (get_user_kel_kd() != '000' && get_user_kel_kd() != $kel_kd)
            $kec_kd = get_user_kel_kd();
        
        /*
         * SQL queries
         * Get data to display
         */
        
        if ($kec_kd == '000') {
            $sql_query_c = "SELECT COUNT(*) AS c FROM ref_kecamatan ";
            $sql_query_r = $this->pbbm_model->qry_realisasi_kb_kec($tahun);
        } else if ($kel_kd == '000') {
            $sql_query_c = "SELECT COUNT(*) AS c FROM ref_kelurahan k 
                WHERE k.kd_propinsi='" . KD_PROPINSI . "' 
                AND k.kd_dati2='" . KD_DATI2 . "'
                AND k.kd_kecamatan='$kec_kd'";
            $sql_query_r = $this->pbbm_model->qry_realisasi_kb_kel($tahun, $kec_kd);
        }
        
        else if ($nop_kd = '000000000000000000') {
            // $sLimit  = "LIMIT  $iDisplayLength OFFSET $iDisplayStart";
            $sSearch = (isset($_GET['sSearch'])) ? $_GET['sSearch'] : "";
            if ($sSearch)
                $search .= " AND a.nm_wp_sppt ilike '%$sSearch%' ";
            
            
            
            $sql_query_r = $this->pbbm_model->qry_realisasi_kb_op($tahun, $kec_kd, $kel_kd);
            
            if ($search) {
                $sql_query_r = str_replace('AND (1=1)', $search, $sql_query_r);
            }
        }
        
        /*
         * Output
         */
        /*
        $sql_query_c = "SELECT COUNT(*) AS c FROM ($sql_query_r) z";
        $qry    = $this->db->query($sql_query_c);
        $row    = $qry->row();
        $iTotal = $row->c;
        if ($search) {
            $qry            = $this->db->query($sql_query_c . $search);
            $row            = $qry->row();
            $iFilteredTotal = $row->c;
        } else
            $iFilteredTotal = $iTotal;
        */
        
        $output   = array(
            "sEcho" => $sEcho,
            "iTotalRecords" => 0, //$iTotal,
            "iTotalDisplayRecords" => 0, //$iFilteredTotal,
            "iDisplayStart" => 0, //$iDisplayStart,
            // "SQL Query" => $sql_query_r,
            "aaData" => array()
        );
        //die($sql_query_r." $sOrder $sLimit");
        $qry      = $this->db->query($sql_query_r . " $sOrder $sLimit");
        $pg_sppt  = 0;
        $pg_pokok = 0;
        $pg_denda = 0;
        $pg_total = 0;
        
        foreach ($qry->result() as $aRow) {
            $row = array();
            for ($i = 0; $i < count($aColumns); $i++) {
                if ($i == 1) {
                    if ($kec_kd == '000')
                        $row[] = "<a href=\"" . active_module_url() . "kb?tahun=$tahun&kec_kd=" . substr($aRow->kode, -3) . "\">" . $aRow->$aColumns[$i] . "</a>";
                    else if ($kel_kd == '000')
                        $row[] = "<a href=\"" . active_module_url() . "kb?tahun=$tahun&kec_kd=$kec_kd&kel_kd=" . substr($aRow->kode, -3) . "\">" . $aRow->$aColumns[$i] . "</a>";
                    else if ($nop_kd == '000000000000000000')
                        $row[] = "<a href=\"" . active_module_url() . "op?&nop_kd=" . $aRow->kode . "\">" . $aRow->$aColumns[$i] . "</a>";
                    
                } else if ($aColumns[$i] == 'amount3') {
                    $row[] = number_format($aRow->amount1 - $aRow->amount2, 0, ',', '.');
                } else if ($i > 1)
                    $row[] = number_format($aRow->$aColumns[$i], 0, ',', '.');
                else
                    $row[] = $aRow->$aColumns[$i];
            }
            $pg_sppt += $aRow->$aColumns[2];
            $pg_pokok += $aRow->$aColumns[3];
            $pg_denda += $aRow->$aColumns[4];
            $pg_total += $aRow->$aColumns[5];
            $output['aaData'][] = $row;
        }
        
        /*
        //Buat Total Halaman
        $row                = array();
        $row[]              = '';
        $row[]              = 'Jumlah Halaman';
        $row[]              = number_format($pg_sppt, 0, ',', '.');
        $row[]              = number_format($pg_pokok, 0, ',', '.');
        $row[]              = number_format($pg_denda, 0, ',', '.');
        $row[]              = number_format($pg_total, 0, ',', '.');
        $output['aaData'][] = $row;
        
        if ($iFilteredTotal <= ($iDisplayLength + $iDisplayStart)) {
            $sql_query_t = "SELECT '' kode, 'TOTAL' uraian, SUM(sppt1) sppt1, sum(amount1) amount1, 
                sum(amount2)amount2
                FROM ($sql_query_r) a 
                GROUP BY 1,2 ";
            $qry         = $this->db->query($sql_query_t);
            
            foreach ($qry->result() as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    if ($aColumns[$i] == 'amount3') {
                        $row[] = number_format($aRow->amount1 - $aRow->amount2, 0, ',', '.');
                    } else if ($i > 1)
                        $row[] = number_format($aRow->$aColumns[$i], 0, ',', '.');
                    else
                        $row[] = $aRow->$aColumns[$i];
                }
                $output['aaData'][] = $row;
            }
        }
        */
        
        $output['sppt']  = number_format($pg_sppt, 0, ',', '.');
        $output['pokok'] = number_format($pg_pokok, 0, ',', '.');
        $output['denda'] = number_format($pg_denda, 0, ',', '.');
        $output['total'] = number_format($pg_total, 0, ',', '.');
        
        echo json_encode($output);
    }
    
    function piutang()
    {
        // ob_start("ob_gzhandler");
        //$this->load->model("pbbm_model");
        $tahun  = (isset($_GET['tahun']) && is_numeric($_GET['tahun'])) ? $_GET['tahun'] : date('Y');
        $tahun2 = (isset($_GET['tahun2']) && is_numeric($_GET['tahun2'])) ? $_GET['tahun2'] : date('Y');
        $buku   = (isset($_GET['buku'])) ? $_GET['buku'] : '44';
        $kec_kd = (isset($_GET['kec_kd']) && is_numeric($_GET['kec_kd'])) ? $_GET['kec_kd'] : '000';
        $kel_kd = (isset($_GET['kel_kd']) && is_numeric($_GET['kel_kd'])) ? $_GET['kel_kd'] : '000';
        $nop_kd = (isset($_GET['nop_kd']) && is_numeric($_GET['nop_kd'])) ? $_GET['nop_kd'] : '000000000000000000';
        
        $bukumin = $this->pbbm_model->rangebuku[substr($buku, 0, 1)][0];
        $bukumax = $this->pbbm_model->rangebuku[substr($buku, 1, 1)][1];
        //      $search.=" AND a.pbb_yg_harus_dibayar_sppt between $bukumin AND $bukumax";
        
        /*Cek User Auth*/
        $kec_kd = (get_user_kec_kd() != '000' && get_user_kec_kd() != $kec_kd) ? get_user_kec_kd() : $kec_kd;
        $kel_kd = (get_user_kel_kd() != '000' && get_user_kel_kd() != $kel_kd) ? get_user_kel_kd() : $kel_kd;
        
        
        $path_to_root = active_module_url();
        
        $aColumns     = array(
            'kode',
            'uraian',
            'transaksi',
            'amount'
        );
        $sIndexColumn = "kode";
        
        $pageSize   = (isset($_GET['iDisplayLength']) && $_GET['iDisplayLength'] != '-1' ? $_GET['iDisplayLength'] : 15);
        $pageNumber = (isset($_GET['iDisplayStart']) && $_GET['iDisplayStart'] != '-1' ? $_GET['iDisplayStart'] : 0);
        
        $iSortCol_0   = (isset($_GET['iSortCol_0']) && is_numeric($_GET['iSortCol_0'])) ? $_GET['iSortCol_0'] : 0;
        $iSortingCols = (isset($_GET['iSortingCols']) && is_numeric($_GET['iSortingCols'])) ? $_GET['iSortingCols'] : 1;
        $sEcho        = (isset($_GET['sEcho']) && is_numeric($_GET['sEcho'])) ? $_GET['sEcho'] : 1;
        $sSearch      = (isset($_GET['sSearch'])) ? $_GET['sSearch'] : "";
        /*$sSearch_0 		= (isset($_GET['sSearch_0'])) ? $_GET['sSearch_0'] : "";
        $sSearch_1 		= (isset($_GET['sSearch_1'])) ? $_GET['sSearch_1'] : "";
        $sSearch_2 		= (isset($_GET['sSearch_2'])) ? $_GET['sSearch_2'] : "";
        $sSearch_3 		= (isset($_GET['sSearch_3'])) ? $_GET['sSearch_3'] : "";
        $sSearch_4 		= (isset($_GET['sSearch_4'])) ? $_GET['sSearch_4'] : "";
        $sSortDir_0 	= (isset($_GET['sSortDir_0'])) ? $_GET['sSortDir_0'] : "asc";*/
        
        /*
         * Limit
         */
        $sLimit = "";
        if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1') {
            // $sLimit = "LIMIT $pageSize OFFSET $pageNumber";
        }
        
        
        /* Ordering
         */
        $sOrder = "";
        /* if (isset($_GET['iSortCol_0'])) {
        $sOrder = "ORDER BY ";
        for ($i = 0; $i < intval($_GET['iSortingCols']); $i++) {
        if ($_GET['bSortable_' . intval($_GET['iSortCol_' . $i])] == "true") {
        if ($aColumns[intval($_GET['iSortCol_' . $i])] == "bphtbno" || $aColumns[intval($_GET['iSortCol_' . $i])] == "tanggal") {
        $sOrder .= $aColumns[intval($_GET['iSortCol_' . $i])] . " " . $_GET['sSortDir_' . $i] . ", ";
        } else {
        $sOrder .= $aColumns[intval($_GET['iSortCol_' . $i])] . ' ' . $_GET['sSortDir_' . $i] . ", ";
        }
        }
        }
        
        $sOrder = substr_replace($sOrder, "", -2);
        if ($sOrder == "ORDER BY ") {
        $sOrder = "";
        }
        } */
        
        
        /* Filtering
         * NOTE this does not match the built-in DataTables filtering which does it
         * word by word on any field. It's possible to do here, but concerned about efficiency
         * on very large tables, and MySQL's regex functionality is very limited
         */
        $sWhere = "";
        
        /* Individual column filtering */
        
        
        /*
         * SQL queries
         * Get data to display
         */
        
        if ($kec_kd == '000') {
            $sql_query_c = "SELECT COUNT(*) AS C FROM ref_kecamatan ";
            $sql_query_r = $this->pbbm_model->qry_piutang_kec($tahun, $tahun2, $buku);
        } else if ($kel_kd == '000') {
            $sql_query_c = "SELECT COUNT(*) AS c 
                FROM ref_kelurahan k 
                WHERE k.kd_propinsi='" . KD_PROPINSI . "' 
                AND k.kd_dati2='" . KD_DATI2 . "'
                AND k.kd_kecamatan='$kec_kd'";
            $sql_query_r = $this->pbbm_model->qry_piutang_kel($tahun, $tahun2, $buku, $kec_kd);
            
        } else if ($nop_kd = '000000000000000000') {
            $sql_query_c = "SELECT COUNT(*) AS c 
                FROM (SELECT k.kd_propinsi, k.kd_dati2, k.kd_kecamatan, k.kd_kelurahan, k.kd_blok, k.no_urut
                ,k.kd_jns_op, k.thn_pajak_sppt, k.pbb_yg_harus_dibayar_sppt 
                FROM    sppt k 
                LEFT JOIN pembayaran_sppt p
                ON k.kd_propinsi = p.kd_propinsi 
                AND k.kd_dati2 = p.kd_dati2 
                AND k.kd_kecamatan = p.kd_kecamatan
                AND k.kd_kelurahan = p.kd_kelurahan
                AND k.kd_blok = p.kd_blok 
                AND k.no_urut = p.no_urut 
                AND k.kd_jns_op = p.kd_jns_op
                AND k.thn_pajak_sppt = p.thn_pajak_sppt 

                WHERE k.kd_propinsi='" . KD_PROPINSI . "' 
                AND k.kd_dati2='" . KD_DATI2 . "' 
                AND k.kd_kecamatan='$kec_kd' 
                AND k.kd_kelurahan='$kel_kd'
                AND k.thn_pajak_sppt BETWEEN '$tahun' AND  '$tahun2' 
                AND k.pbb_yg_harus_dibayar_sppt BETWEEN $bukumin AND $bukumax 
                GROUP BY k.kd_propinsi, k.kd_dati2, k.kd_kecamatan, k.kd_kelurahan, k.kd_blok, k.no_urut
                ,k.kd_jns_op, k.thn_pajak_sppt, k.pbb_yg_harus_dibayar_sppt 
                HAVING k.pbb_yg_harus_dibayar_sppt > SUM(coalesce(p.jml_sppt_yg_dibayar,0)-coalesce(p.denda_sppt,0))
                )a";
            $sql_query_r = $this->pbbm_model->qry_piutang_op($tahun, $tahun2, $buku, $kec_kd, $kel_kd);
            
        }
        
        /* Total data set length */
        /*
        $qry            = $this->db->query($sql_query_c);
        $row            = $qry->row();
        $iTotal         = $row->c;
        $iFilteredTotal = $iTotal; //$iTotal;
        
        if ($sWhere != "") {
            $row            = $qry->row();
            $iFilteredTotal = $row->c;
        }
        */
        
        /*
         * Output
         */
        $output = array(
            "sEcho" => $sEcho,
            "iTotalRecords" => 0, //$iTotal,
            "iTotalDisplayRecords" => 0, //$iFilteredTotal,
            // "SQL Query" => $sql_query_r,
            "aaData" => array()
        );
        
        $qry = $this->db->query("$sql_query_r $sLimit");
        $pg_sppt =0;
        $pg_pokok =0;
            
        foreach ($qry->result() as $aRow) {
            $row = array();
            for ($i = 0; $i < count($aColumns); $i++) {
                if ($i == 1) {
                    if ($kec_kd == '000')
                        $row[] = "<a href=\"" . active_module_url() . "piutang?tahun=$tahun&buku=$buku&kec_kd=" . substr($aRow->kode, -3) . "\">" . $aRow->$aColumns[$i] . "</a>";
                    else if ($kel_kd == '000')
                        $row[] = "<a href=\"" . active_module_url() . "piutang?tahun=$tahun&buku=$buku&kec_kd=$kec_kd&kel_kd=" . substr($aRow->kode, -3) . "\">" . $aRow->$aColumns[$i] . "</a>";
                    else if ($nop_kd == '000000000000000000')
                        $row[] = "<a href=\"" . active_module_url() . "op?&nop_kd=" . substr($aRow->kode,0,24) . "\">" . $aRow->$aColumns[$i] . "</a>";
                }
                
                else if ($i > 1)
                    $row[] = number_format($aRow->$aColumns[$i], 0, ',', '.');
                else
                    $row[] = $aRow->$aColumns[$i];
                        
            }
            $pg_sppt += $aRow->$aColumns[2];
            $pg_pokok += $aRow->$aColumns[3];
            
            $output['aaData'][] = $row;
        }
        
        /*
        //Buat Total Halaman
        $sql_query_t = "SELECT '' kode, 'JUMLAH' uraian, SUM(transaksi) transaksi, sum(amount) amount
            FROM ($sql_query_r $sOrder $sLimit) a 
            GROUP BY 1,2 ";
        if ($iFilteredTotal <= ($pageNumber + $pageSize)) {
            $sql_query_t .= "UNION 
                SELECT '' kode, 'TOTAL' uraian, SUM(transaksi) transaksi, sum(amount) amount
                FROM ($sql_query_r) a 
                GROUP BY 1,2 ";
        }
        $qry = $this->db->query($sql_query_t);
        
        foreach ($qry->result() as $aRow) {
            $row = array();
            for ($i = 0; $i < count($aColumns); $i++) {
                if ($i > 1)
                    $row[] = number_format($aRow->$aColumns[$i], 0, ',', '.');
                else
                    $row[] = $aRow->$aColumns[$i];
            }
            $output['aaData'][] = $row;
        }
        */
        $output['sppt']  = number_format($pg_sppt, 0, ',', '.');
        $output['pokok'] = number_format($pg_pokok, 0, ',', '.');
        
        echo json_encode($output);
    }
    
}
