<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Phoneplus\Libraries\REST_Controller;
require APPPATH .'libraries/REST_Controller.php';
require APPPATH .'libraries/Format.php';

/**
 * This is an example of a few basic user interaction methods you could use
 * all done with a hardcoded array
 *
 * @package         CodeIgniter
 * @subpackage      Rest Server
 * @category        Controller
 * @author          AQUICK'INTL
 * @license         MIT
 * @link            https://www.aquickintl.com
 */
class Marques extends REST_Controller {

    protected $marque_table = 'aqi_pp_marque';
    public $msg_not_found = 'Aucun enregitrement trouvé !';

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->database();

    }

    public function index_get($param='')
    {

        if (empty($param)) {
            $data = $this->db->get($this->marque_table)->result();
            if (empty($data)) {
                $this->set_response([
                    'status'=>false,
                    'message'=> $this->msg_not_found
                ],
                    REST_Controller::HTTP_NOT_FOUND
                );
            } else {
                $this->set_response($data, REST_Controller::HTTP_OK);
            }
            
        } else {
            $this->db->select('*');
            $this->db->from($this->marque_table);
            $this->db->where('id',$param);
            $this->db->or_where('code',$param);
            $data = $this->db->get()->row_array();

            if (empty($data)) {
                $this->set_response([
                    'status'=>false,
                    'message'=>$this->msg_not_found
                ],
                    REST_Controller::HTTP_NOT_FOUND
                );
            } else {
                $this->set_response($data, REST_Controller::HTTP_OK);
            }

        }
        $this->set_response($data, REST_Controller::HTTP_OK);
    }

    public function index_post()
    {
        
        $code = $this->input->post('code');
        if ($this->valid_code($code)) {

            $_POST = json_decode(file_get_contents('php://input'),true);
            $data = $this->input->post();
            $this->db->insert($this->marque_table,$data);

            $this->set_response([
                'status'=>REST_Controller::HTTP_CREATED,
                'message'=>'marque créée avec succès.'
            ],
            REST_Controller::HTTP_CREATED
        );

        } else {
            $this->set_response([
                'status'=>REST_Controller::HTTP_BAD_REQUEST,
                'message'=>'Ce code existe deja !'
            ],
                REST_Controller::HTTP_BAD_REQUEST
            );

        }
    }

    public function index_put($id)
    {

        $_POST = json_decode(file_get_contents('php://input'),true);
        $data = $this->put();
        $this->db->update($this->marque_table, $data, array('id'=>$id));

        $this->set_response(['marque modifiée avec succès.'], REST_Controller::HTTP_CREATED);
    }

    public function index_delete($id)
    {
        $this->db->delete($this->marque_table, array('id'=>$id));

        $this->set_response(['marque supprimée avec succès.'], REST_Controller::HTTP_OK);
    }

    public function valid_code($code)
    {
        $qry = $this->db->get_where($this->marque_table, array('code'=>$code));
        return ($qry->num_rows() <= 0)? true: false;              
    }

}