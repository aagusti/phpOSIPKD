<?php
class kelModel extends CI_Model {

  var $tables ='ref_kecamatan';
  
	function __construct()
	{
		parent::__construct();
	}

	function getRecord($kec='000',$kel='000')
	{
	  $sql="select * from ref_kelurahan 
 		      where kd_propinsi='".KD_PROPINSI."' and kd_dati2='".KD_DATI2."' and kd_kecamatan='$kec' ";
 		if ($kel!='000')
 		    $sql.=" and kd_kelurahan='$kel'";
 		$qry=$this->db->query($sql);
  	return $qry->result();
	}
	
}
 