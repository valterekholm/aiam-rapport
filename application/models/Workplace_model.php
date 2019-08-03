<?php
class Workplace_model extends CI_Model {

	public function __construct()
	{
		$this->load->database();
	}

	public function get_workplaces_by_customer($id=FALSE){

		if($id === FALSE){
			//$query = $this->db->get('arbetsplats');
			//return $query->result_array();
		}

		$query = $this->db->get_where('arbetsplats', array('kund_id' => $id));
		return $query->row_array();
	}

	//TODO: find all occurenses of calling this function with arg set, replace with next function
	public function get_workplaces($id=FALSE){
		if($id === FALSE){
			$query = $this->db->get('arbetsplats');
			return $query->result_array();
		}
		$query = $this->db->get_where('arbetsplats', array('id' => $id));
		return $query->result_array();
	}

	public function get_workplace($id=FALSE){
		if($id === FALSE){
			return false;
		}
		$query = $this->db->get_where('arbetsplats', array('id' => $id));
		return $query->row_array();
	}

	public function get_workplaces_for_company($id=FALSE){
		error_log("get_workplaces_for_company med $id");
		if($id === FALSE){
			return false;
		}
		$sql = "select ap.* FROM arbetsplats ap LEFT JOIN kunder_arbetsplatser ka "
		."ON (ap.id = ka.id_arbetsplats) LEFT JOIN kund ON (kund.id = ka.id_kund) "
		."LEFT JOIN companies_kunder ck ON (ck.id_kund = kund.id) WHERE id_company = $id";
		$query = $this->db->query($sql);
		error_log(print_r($query->result_array(), true));
		return $query->result_array();
	}


	//in use
	//TODO: returns workplaces for any customer, with number of customers sharing a workplace
	public function get_workplaces_view2($id=FALSE){
		if($id === FALSE){
			//select select a.a, (select count(*) from personal_arbetstillfalle where id_arbetstillfalle = arbets_tillfalle.id) as antal_personal_anknyten from arbets_tillfalle left join arbetsplats on (arbetsplats_id = arbetsplats.id) order by arbets_tillfalle.datum_start"
		$sql = "select distinct arbetsplats.*, (select count(*) from kunder_arbetsplatser where id_arbetsplats = arbetsplats.id) as antal_anknytna_kunder from arbetsplats left join kunder_arbetsplatser on (arbetsplats.id = id_arbetsplats) left join kund on (kund.id = id_kund)";
		$query = $this->db->query($sql);
		return $query->result_array();
	    }
	    $sql = "select distinct arbetsplats.*, (select count(*) from kunder_arbetsplatser where id_arbetsplats = arbetsplats.id) as antal_anknytna_kunder from arbetsplats left join kunder_arbetsplatser on (arbetsplats.id = id_arbetsplats) left join kund on (kund.id = id_kund) where arbetsplats.id = '$id'";
	    $query = $this->db->query($sql);
	    return $query->row_array();
	}

	public function get_workplaces_view2_company($company){//TODO: select count bara på kunder till company
		$sql = "select distinct arbetsplats.*, (select count(*) from kunder_arbetsplatser join kund on (id_kund=kund.id) join companies_kunder ck2 on (kund.id=ck2.id_kund) where id_arbetsplats = arbetsplats.id and ck2.id_company=$company) as antal_anknytna_kunder from arbetsplats left join kunder_arbetsplatser on (arbetsplats.id = id_arbetsplats) left join kund on (kund.id = id_kund) left join companies_kunder ck ON (kund.id = ck.id_kund) WHERE ck.id_company = $company";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function get_workplaces_by_company_id($id=FALSE){
		error_log("get_workplaces_by_company_id");
		if($id==FALSE) return null;

		$sql = "select DISTINCT a.*, kund.namn kund from arbetsplats a left join kunder_arbetsplatser ka on (id_arbetsplats = a.id) left join kund on (kund.id = ka.id_kund) left join companies_kunder ck on (ck.id_kund = kund.id) where ck.id_company = $id";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	//return workplaces that has a satff member connected to it through a job (arbetstillfälle)
	public function get_workplaces_by_staff_member($id){
		$sql = "select * from personal_arbetstillfalle left join arbets_tillfalle on(id_arbetstillfalle = id) left join arbetsplats a on (arbetsplats_id = a.id) where id_person = $id";

		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function get_workplaces_and_first_customer($id=FALSE){
	    if($id === FALSE){
		$query = $this->db->query("select arbetsplats.id id, arbetsplats.namn, kund.namn kundnamn, gatu_adress, postnummer, trappor, land, lati, longi from kund join kunder_arbetsplatser on (kund.id = kunder_arbetsplatser.id_kund) join arbetsplats on (id_arbetsplats = arbetsplats.id)");
		return $query->result_array();
	    }
	    $query = $this->db->query("select arbetsplats.id id, arbetsplats.namn, kund.namn kundnamn, gatu_adress, postnummer, trappor, land, lati, longi from kund join kunder_arbetsplatser on (kund.id = kunder_arbetsplatser.id_kund) join arbetsplats on (id_arbetsplats = arbetsplats.id) WHERE id = '$id'");
	    return $query->row_array();
	}


	public function set_workplace()
	{
	    $this->load->helper('url');
	    $data = array(
	    'namn' => $this->input->post('name'),
	    'longi' => $this->input->post('longi'),
	    'lati' => $this->input->post('lati'),
	    'gatu_adress' => $this->input->post('street'),
	    'postnummer' => $this->input->post('postal_code'),
	    /*'trappor' => $this->input->post('stairs'),*/
	    'land' => $this->input->post('land'),
	    'time_zone' => $this->input->post('time_zone')
    );

	    if(null !== $this->input->post('stairs')){
		    $data['trappor'] = $this->input->post('stairs');
	    }
	    $result = $this->db->insert('arbetsplats', $data);
	    if($result){
		    $insert_id = $this->db->insert_id();
		    return $insert_id;
	    }
	    return false;
	}

	public function update_workplace()
	{
		$this->load->helper('url');
		$id = $this->input->post('id');
		$data = array(
			'namn' => $this->input->post('name'),
			'longi' => $this->input->post('longi'),
			'lati' => $this->input->post('lati'),
			'gatu_adress' => $this->input->post('street'),
			'postnummer' => $this->input->post('postal_code'),
			'land' => $this->input->post('land'),
			'time_zone' => $this->input->post('time_zone')
		);
		error_log("Update with: " . print_r($data, true));
		if(null !== $this->input->post('stairs')){
			$data['trappor'] = $this->input->post('stairs');
		}

		$this->db->where('id', $id);
		$res = $this->db->update('arbetsplats', $data);

		//error_log($this->db->last_query());

		return $res; 
	}

	public function delete_workplace_record($id){
	    $this->db->where('id_arbetsplats',$id);
	    $this->db->delete('kunder_arbetsplatser');//ta bort kopplingar
	    //delete from kunder_arbetsplatser where id_kund not in (select id from kund) //vid behov efterhand
	    $this->db->where('id',$id);
	    return $this->db->delete('arbetsplats');
	}

	public function delete_connection_record($id_workplace,$id_customer){
	    $array = array('id_arbetsplats'=>$id_workplace, 'id_kund'=>$id_customer);
	    $this->db->where($array);
	    return $this->db->delete('kunder_arbetsplatser');
	}

	public function get_workplaces_view1($id=FALSE){
	    if($id === FALSE){
		$query = $this->db->query("select arbetsplats.id id, arbetsplats.namn, kund.namn kundnamn, gatu_adress, postnummer, trappor, land, lati, longi from arbetsplats join kunder_arbetsplatser on (arbetsplats.id = id_arbetsplats) join kund on (kunder_arbetsplatser.id_kund = kund.id)");
		//SELECT arbetsplats.id, arbetsplats.namn, arbetsplats.gatu_adress, arbetsplats.postnummer,
		//arbetsplats.trappor, arbetsplats.land, arbetsplats.lati, arbetsplats.longi, kund.namn kund
		//FROM kund join arbetsplats on kund.id = arbetsplats.kund_id
		return $query->result_array();
	    }
	    //sql view exempel
	    /*
	    $this->db->where('id_bla',$id_bla);
	    $this->db->order_by('bla','desc');
	    $query = $this->db->get('WMY_VIEW');
	    return $query->result_array();
	    */


	    $query = $this->db->query("select arbetsplats.id id, arbetsplats.namn, kund.namn kundnamn, gatu_adress, postnummer, trappor, land, lati, longi from kund join kunder_arbetsplatser on (kund.id = kunder_arbetsplatser.id_kund) join arbetsplats on (id_arbetsplats = arbetsplats.id) WHERE id = '$id'");
	    return $query->result_array();
	}

	public function get_related_customers($id=FALSE){

		if($id!==FALSE){
			$query = $this->db->query("select * from kunder_arbetsplatser join kund on (id_kund = id) where id_arbetsplats = '$id'");
			return $query->result_array();
		}
	}

	/*Show only customers related to this company*/
	public function get_related_customers_company($id, $company){

		$query = $this->db->query("select * from kunder_arbetsplatser join kund on (id_kund = id) join companies_kunder ck on (kund.id = ck.id_kund) where id_arbetsplats = $id and ck.id_company = $company");
		return $query->result_array();
	}

	public function get_related_customers_ids($id=FALSE){

		if($id!==FALSE){
			$ids = "";
			$query = $this->db->query("select * from kunder_arbetsplatser join kund on (id_kund = id) where id_arbetsplats = '$id'");
			foreach ($query->result() as $row)
			{
				$ids .= $row->id . ",";
			}
			$ids = rtrim($ids, ',');
			return $ids;
		}
		return false;
	}

		/*Show only customers related to this company*/
	public function get_related_customers_ids_company($id, $company){
		$ids = "";
		$query = $this->db->query("select * from kunder_arbetsplatser join kund on (id_kund = id) join companies_kunder ck on (kund.id = ck.id_kund) where id_arbetsplats = $id and ck.id_company = $company");

		foreach ($query->result() as $row)
		{
			$ids .= $row->id . ",";
		}
		$ids = rtrim($ids, ',');
		return $ids;
	}

	public function get_unrelated_customers($id=FALSE){
		if($id!==FALSE){//FEL: se nedan
			$related_ids = $this->get_related_customers_ids($id);

			if(empty($related_ids)){
				$related_ids = "";
			}
			else{
				$related_ids = "and kund.id not in (" . $related_ids . ")";
			}
			$query = $this->db->query("select id, namn  from kunder_arbetsplatser right join kund on (id_kund = id) where (id_arbetsplats != $id or isnull(id_arbetsplats)) $related_ids");
			return $query->result_array();
		}
	}

	public function get_unrelated_customers_company($id=FALSE, $company){
		if($id!==FALSE){
			$related_ids = $this->get_related_customers_ids_company($id, $company);
			if(empty($related_ids)){
				$related_ids = "";
			}
			else{
				$related_ids = "and kund.id not in (" . $related_ids . ")";
			}
			$query = $this->db->query("select * from kunder_arbetsplatser right join kund on (id_kund = id) right join companies_kunder ck on (kund.id = ck.id_kund) where (id_arbetsplats != $id or isnull(id_arbetsplats)) $related_ids and ck.id_company = $company");
			return $query->result_array();
		}
	}

	public function add_customer($kund=FALSE, $workplace=FALSE){
		error_log("add_customer med $kund $workplace");
		if($kund==FALSE && $workplace==FALSE){
			error_log("Ska kolla post");
			$this->load->helper('url');
			$data = array(
				'id_kund' => $this->input->post('customer'),
				'id_arbetsplats' => $this->input->post('workplace')
			);
		}
		else{
			error_log("Fick argument");
			/*called internally?*/
			$data = array(
				'id_kund' => $kund,
				'id_arbetsplats' => $workplace
			);
		}
		error_log(print_r($data, true));
		return $this->db->insert('kunder_arbetsplatser', $data);
	}
}
?>
