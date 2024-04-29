<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Addons extends MY_Controller {
	public function __construct(){
		parent::__construct();
		$this->load_global();
		$this->load->model('addons_model','addons');
	}

	public function add(){
		$this->permission_check('addons_add');
		$data=$this->data;
		$data['page_title']=$this->lang->line('addons');
		$this->load->view('addons', $data);
	}

	//ITS FROM POP UP MODAL
	public function add_addons_modal(){
		$this->form_validation->set_rules('addons', 'addons Name', 'trim|required');
		if ($this->form_validation->run() == TRUE) {
			$result=$this->addons->verify_and_save();
			//fetch latest item details
			$res=array();
			$query=$this->db->query("select id,addons_name from db_addonss order by id desc limit 1");
			$res['id']=$query->row()->id;
			$res['addons']=$query->row()->addons_name;
			$res['result']=$result;
			
			echo json_encode($res);

		} 
		else {
			echo "Please Fill Compulsory(* marked) Fields.";
		}
	}
	//END

	public function newaddons(){
		// print_r($_POST);die;
		$this->form_validation->set_rules('addons', 'addons', 'trim|required');
	

		if ($this->form_validation->run() == TRUE) {
			
			$result=$this->addons->verify_and_save();
			echo $result;
		} else {
			echo "Please Enter addons name.";
		}
	}
	public function update($id){
		$this->permission_check('addons_edit');
		$data=$this->data;

		$this->load->model('addons_model');
		$result=$this->addons_model->get_details($id,$data);
		$data=array_merge($data,$result);
		$data['page_title']=$this->lang->line('addons');
		$this->load->view('addons', $data);
	}
	public function update_addons(){
		$this->form_validation->set_rules('addons', 'addons', 'trim|required');
		$this->form_validation->set_rules('q_id', '', 'trim|required');

		if ($this->form_validation->run() == TRUE) {
			$this->load->model('addons_model');
			$result=$this->addons_model->update_addons();
			echo $result;
		} else {
			echo "Please Enter addons name.";
		}
	}
	public function view(){
		$this->permission_check('addons_view');
		$data=$this->data;
		$data['page_title']=$this->lang->line('addonss_list');
		$this->load->view('addons-view', $data);
	}

	public function ajax_list()
	{
		$list = $this->addons->get_datatables();
		
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $addons) {
			$no++;
			$row = array();
			$row[] = '<input type="checkbox" name="checkbox[]" value='.$addons->id.' class="checkbox column_checkbox" >';
			$row[] = $addons->addons_code;
			$row[] = $addons->addons_name;
			$row[] = $addons->description;

			 		if($addons->status==1){ 
			 			$str= "<span onclick='update_status(".$addons->id.",0)' id='span_".$addons->id."'  class='label label-success' style='cursor:pointer'>Active </span>";}
					else{ 
						$str = "<span onclick='update_status(".$addons->id.",1)' id='span_".$addons->id."'  class='label label-danger' style='cursor:pointer'> Inactive </span>";
					}
			$row[] = $str;			
					$str2 = '<div class="btn-group" title="View Account">
										<a class="btn btn-primary btn-o dropdown-toggle" data-toggle="dropdown" href="#">
											Action <span class="caret"></span>
										</a>
										<ul role="menu" class="dropdown-menu dropdown-light pull-right">';

											if($this->permissions('addons_edit'))
											$str2.='<li>
												<a title="Edit Record ?" href="update/'.$addons->id.'">
													<i class="fa fa-fw fa-edit text-blue"></i>Edit
												</a>
											</li>';

											if($this->permissions('addons_delete'))
											$str2.='<li>
												<a style="cursor:pointer" title="Delete Record ?" onclick="delete_addons('.$addons->id.')">
													<i class="fa fa-fw fa-trash text-red"></i>Delete
												</a>
											</li>
											
										</ul>
									</div>';			

			$row[] = $str2;
			$data[] = $row;
		}

		$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->addons->count_all(),
						"recordsFiltered" => $this->addons->count_filtered(),
						"data" => $data,
				);
		//output to json format
		echo json_encode($output);
	}

	public function update_status(){
		$this->permission_check_with_msg('addons_edit');
		$id=$this->input->post('id');
		$status=$this->input->post('status');

		$this->load->model('addons_model');
		$result=$this->addons_model->update_status($id,$status);
		return $result;
	}
	
	public function delete_addons(){
		$this->permission_check_with_msg('addons_delete');
		$id=$this->input->post('q_id');
		return $this->addons->delete_addons_from_table($id);
	}
	public function multi_delete(){
		$this->permission_check_with_msg('addons_delete');
		$ids=implode (",",$_POST['checkbox']);
		return $this->addons->delete_addonss_from_table($ids);
	}

}

