<?php defined('BASEPATH') OR exit('No direct script access allowed');

use Phoneplus\Libraries\REST_Controller;
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
class Produits extends MY_Controller {

    public $msg_not_found = 'Aucun enregitrement trouvé !';

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->model('article_model','ArticleModel');
        $this->load->model('produit_model','ProduitModel');
        $this->load->model('sousCategorie_model','SousCategorieModel');
        $this->load->model('Categorie_model','CategorieModel');
        $this->load->model('articleMeta_model','ArticleMetaModel');
        $this->load->model('marque_model','MarqueModel');
        $this->load->model('TrocMeta_model','TrocMetaModel');
        $this->load->model('boutique_model','BoutiqueModel');
        $this->load->model('Comment_model','CommentModel');

    }

    /**
    * Get produits
    * @method: GET
    * @param: {Id}
    */
    public function index_get($param='')
    {
        $produit = array();
        $msg='';

        if (empty($param)) {
            
            $produit = $this->ProduitModel->all_produit();
     
            if (empty($produit)) {
                $this->set_response([
                    'status'=>404,
                    'message'=> $this->msg_not_found
                ],
                    REST_Controller::HTTP_NOT_FOUND
                );
                return;
            } else {
                $this->set_response($produit, REST_Controller::HTTP_OK);
                $msg = 'Liste des produits récupérée avec succès !';
            }
            
        } else {
            $row = $this->ProduitModel->produit($param);            

            if (empty($row)) {
                $this->set_response([
                    'status'=>404,
                    'message'=>$this->msg_not_found
                ],
                    REST_Controller::HTTP_NOT_FOUND
                );
                return;
            } else {
                $produit = $row;
                $this->set_response($produit, REST_Controller::HTTP_OK);
                $msg = 'Produit récupéré avec succès !';
            }

        }
        $this->set_response(['status'=>200, 'message'=>$msg, 'data'=>$produit], REST_Controller::HTTP_OK);
    }

    /**
    * Liste produit  pour le site
    * @method: GET
    */
    public function list_products_get($param='')
    {
        $produit = array();
        $msg='';
        /**Verifie si le parametres n'existe pas */
        if (empty($param)) {
            foreach ($this->ProduitModel->get_products() as $row)
            {
                $images = $this->split_image($row->images);
                $images[] = $row->image;
                $row->images = $images;
                $row->rate = $this->CommentModel->get_avg_comment($row->id);
                $row->rating = $this->CommentModel->get_comment($row->id);
                $row->article = $this->get_detail_article((int)$row->article);
                $row->boutique = $this->BoutiqueModel->get_boutique($row->boutique);
                $produit[]= $row;
            }
            if (empty($produit)) {
                $this->set_response([
                    'status'=>404,
                    'message'=> $this->msg_not_found
                ],
                    REST_Controller::HTTP_NOT_FOUND
                );
                return;
            } else {
                $this->set_response($produit, REST_Controller::HTTP_OK);
                $msg = 'Liste des produits récupérée avec succès !';
            }
        }else{
            $row = $this->ProduitModel->get_products($param);
            if (empty($row)) {
                $this->set_response([
                    'status'=>404,
                    'message'=>$this->msg_not_found
                ],
                    REST_Controller::HTTP_NOT_FOUND
                );
                return;
            } else {
                $images = $this->split_image($row->images);
                $images[] = $row->image;
                $row->images = $images;
                $row->rate = $this->CommentModel->get_avg_comment($row->id);
                $row->rating = $this->CommentModel->get_comment($row->id);
                $row->article = $this->get_detail_article((int)$row->article);
                $row->boutique = $this->BoutiqueModel->get_boutique($row->boutique);
                $produit = $row;
                $this->set_response($produit, REST_Controller::HTTP_OK);
                $msg = 'Produit récupéré avec succès !';
            }
        }
        
        $this->set_response(['status'=>200, 'message'=>$msg, 'data'=>$produit], REST_Controller::HTTP_OK);
    }

    /**
     * Create New produit
     * @method: POST
     */
    public function index_post()
    {
        $this->auth();
        $date = new DateTime();
        $_POST = $this->security->xss_clean(json_decode(file_get_contents('php://input'),true));
        $this->form_validation->set_data($_POST);

        $this->form_validation->set_rules('produit_prix', 'Prix', 'trim|required|numeric');

        if ($this->form_validation->run() == FALSE){
            $message = array(
                'status'=>400,
                'error'=>$this->form_validation->error_array(),
                'message'=>validation_errors()
            );
            $this->response($message, REST_Controller::HTTP_BAD_REQUEST);
        }else{
            $data = $this->input->post();
            $photo = $this->upload_image($this->input->post('images'));
            $data['produit_main_image'] = $photo;
            $data['produit_code'] = $date->getTimestamp();
            $data['produit_modified'] = date('Y-m-d\TH:i:s.u');
            $data['produit_added'] = date('Y-m-d\TH:i:s.u');

            $outpout = $this->ProduitModel->create($data);
            if ($outpout>0 AND !empty($outpout)) {
                $message = [
                    'status'=>201,
                    'message'=>"produit ajoutée avec succes!",
                    'response'=>base_url().'/'.$outpout
                ];

                $this->response($message, REST_Controller::HTTP_CREATED);
                
            } else {
                $message = [
                    'status'=>400,
                    'message'=>"Une erreur est survenue lors de l'enregistrement!"
                ];

                $this->response($message, REST_Controller::HTTP_BAD_REQUEST);
            }
        }
    }

    /**
     * Delete produit
     * @method: DELETE
     */
    public function index_delete($id)
    {
        $this->auth();
        $id = $this->security->xss_clean($id);

        if (empty($id) AND !is_numeric($id)) {
            $this->set_response([
                'status'=>404,
                'message'=>'L\'Id de la produit n\'existe'
            ],
            REST_Controller::HTTP_NOT_FOUND);
            return;
        } else {
            $produit= [
                'produit_id'=>$id
            ];
            $outpout = $this->ProduitModel->delete($produit);
            if ($outpout>0 AND !empty($outpout)) {
                $message = [
                    'status'=>200,
                    'message'=>"produit supprimée avec succes!"
                ];

                $this->response($message, REST_Controller::HTTP_OK);
                
            } else {
                $message = [
                    'status'=>400,
                    'message'=>"Une erreur est survenue lors de l'enregistrement!"
                ];

                $this->response($message, REST_Controller::HTTP_BAD_REQUEST);
            }
        }
        
    }

    /**
     * Update produit
     * @method: PUT
     */

    public function index_put(){
        $this->auth();
        $_POST = $this->security->xss_clean(json_decode(file_get_contents('php://input'),true));
        $this->form_validation->set_data($_POST);

        $this->form_validation->set_rules('produit_id', 'produit ID', 'trim|required|numeric');
        $this->form_validation->set_rules('produit_prix', 'Prix Produit', 'trim|numeric');

        if ($this->form_validation->run() == FALSE){
            $message = array(
                'status'=>400,
                'error'=>$this->form_validation->error_array(),
                'message'=>validation_errors()
            );
            $this->response($message, REST_Controller::HTTP_BAD_REQUEST);
            return;
        }else{
            $data = $this->input->post();
            $photo = $this->upload_images($this->input->post('images'),'1595258896','phoneplus');
            $data['produit_images'] = $photo;
            $data['produit_prix'] = $this->input->post('prix',TRUE);
            $data['produit_id'] = $this->input->post('id',TRUE);
            $data['produit_modified'] = date('Y-m-d\TH:i:s.u');

            $outpout = $this->ProduitModel->update($data);
            if ($outpout>0 AND !empty($outpout)) {
                $message = [
                    'status'=>201,
                    'message'=>"produit Modifiée avec succes!"
                ];

                $this->response($message, REST_Controller::HTTP_CREATED);
                
            } else {
                $message = [
                    'status'=>400,
                    'message'=>"Une erreur est survenue lors de l'enregistrement!"
                ];

                $this->response($message, REST_Controller::HTTP_BAD_REQUEST);
            }
        }
    }

    /**
     * Formatage des données de l'article à afficher
     */
    public function get_detail_article($id){
        $article = array();
        $row = $this->ArticleModel->get_article($id);
        $row->marque = $this->MarqueModel->marque_libelle($row->marque);
        $row->categorie = $this->CategorieModel->categorie_libelle($row->categorie);
        $row->sous_categorie = $this->SousCategorieModel->sous_categorie_site($row->sous_categorie);
        $caracteristiques = $this->ArticleMetaModel->meta_by_article_id($row->id);
        $article = $row;
        $article->caracteristiques = $caracteristiques;
        return $article;

    }

    public function split_image($image){
        if(!empty($image) && strlen($image)>2){
            $image = substr($image, 1, -1);
            return explode(",",$image);
        }else{
            return $image;
        }
    }

    public function upload_image($files, $code, $subdir){
        $uploaddir = 'assets/images/produits/'+$subdir+'/'+$code;
        if(!is_dir($uploaddir)){
            mkdir($uploaddir,0775,true);
        }
        if ($files) {

            $pathinfo = $files['filename'];
            $value = $files['value'];
            $value = substr($descr[0],9);
            $value = str_replace(' ', '+', $value);
            $image = base64_decode($value);
            $ext = pathinfo($pathinfo, PATHINFO_EXTENSION);
            $file_name = 'main';
            $path = $uploaddir.$file_name.'.'.$ext;
            file_put_contents($path, $image);

            return $path;

            // $images[]=$path;
            // $pathinfo = $files['filename'];
            // $image_value = $files['value'];
            // $descr = explode(',', $image_value);
            // $toreplace = $descr[0].',';
            // $value = $descr[1];
			// $value = str_replace(' ', '+', $value);
            // $ext = pathinfo($pathinfo, PATHINFO_EXTENSION);
            // $image = base64_decode($value);
            // $file_name = 'main';
            // $image_name = $file_name.'.'.$ext;
            // $path = $uploaddir.$image_name;
            // file_put_contents($path,$image);
            // return $uploaddir.$image_name;
        } else {
            return ;
        }
        
    }

    public function upload_images($files, $code, $subdir){
        $images = '[';
        $uploaddir = 'assets/images/produits/'.$subdir.'/'.$code.'/';
        if(!is_dir($uploaddir)){
            mkdir($uploaddir,0775,true);
        }
        if ($files && is_array($files)) {
            $i = 1;
            foreach ($files as $key) {
                $pathinfo = $key['filename'];
                $value = $key['value'];
                $descr = explode(',', $value);
                $value = substr($descr[0],9);
                $value = str_replace(' ', '+', $value);
                $image = base64_decode($value);
                $ext = pathinfo($pathinfo, PATHINFO_EXTENSION);
                $file_name = 'slides_'.($i++);
                $path = $uploaddir.$file_name.'.'.$ext;
                file_put_contents($path, $image);
                $images.=$path.',';

            }
            $images = substr($images,0,-1);
            return $images.']';

        } else {
            return;
        }
        
    }


}