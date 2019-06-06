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
class Produits extends REST_Controller {

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

    }

    /**
    * Get article
    * @method: GET
    * @param: {Id}
    */
    public function index_get($param='')
    {
        $produit = array();

        if (empty($param)) {
            
            foreach ($this->ProduitModel->all_produit() as $row)
            {
                $data['id'] = $row->id;
                $data['added'] = $row->added;
                $data['modified'] = $row->modified;
                $data['disponible'] = $row->is_dispo;
                $data['trocable'] = $row->is_troc;
                $data['status'] = $row->status;
                $data['boutique'] = $row->id_boutique;
                $data['quantite'] = $row->quantite;
                $data['quantite_alerte'] = $row->qte_alert;
                $data['prix'] = $row->prix;
                $data['images'] = $row->images;
                $data['couleur'] = $row->couleur;
                $data['article'] = $this->get_detail_article($row->id);

                $produit[] = $data;  
            }     
            if (empty($produit)) {
                $this->set_response([
                    'status'=>false,
                    'message'=> $this->msg_not_found
                ],
                    REST_Controller::HTTP_NOT_FOUND
                );
            } else {
                $this->set_response($produit, REST_Controller::HTTP_OK);
            }
            
        } else {
            $row = $this->ProduitModel->produit($param);
            $produit['id'] = $row->id;
            $produit['added'] = $row->added;
            $produit['modified'] = $row->modified;
            $produit['disponible'] = $row->is_dispo;
            $produit['trocable'] = $row->is_troc;
            $produit['status'] = $row->status;
            $produit['boutique'] = $row->id_boutique;
            $produit['quantite'] = $row->quantite;
            $produit['quantite_alerte'] = $row->qte_alert;
            $produit['prix'] = $row->prix;
            $produit['images'] = $row->images;
            $produit['couleur'] = $row->couleur;
            $produit['article'] = $this->get_detail_article($row->id);
            

            if (empty($produit)) {
                $this->set_response([
                    'status'=>false,
                    'message'=>$this->msg_not_found
                ],
                    REST_Controller::HTTP_NOT_FOUND
                );
            } else {
                $this->set_response($produit, REST_Controller::HTTP_OK);
            }

        }
        $this->set_response($produit, REST_Controller::HTTP_OK);
    }

    /**
     * Create New produit
     * @method: POST
     */
    public function index_post()
    {
        $_POST = $this->security->xss_clean(json_decode(file_get_contents('php://input'),true));
        $this->form_validation->set_data($_POST);

        $this->form_validation->set_rules('raison_social', 'RaisonSocial', 'trim|required');
        $this->form_validation->set_rules('mobile', 'Mobile', 'trim|required');
        $this->form_validation->set_rules('description', 'Description', 'trim|required');

        if ($this->form_validation->run() == FALSE){
            $message = array(
                'status'=>false,
                'error'=>$this->form_validation->error_array(),
                'message'=>validation_errors()
            );
            $this->response($message, REST_Controller::HTTP_BAD_REQUEST);
        }else{
            $data = $this->input->post();
            $data['modified'] = date('Y-m-d\TH:i:s.u');
            $data['created'] = date('Y-m-d\TH:i:s.u');
            $data['code'] = time();

            $outpout = $this->ProduitModel->create($data);
            if ($outpout>0 AND !empty($outpout)) {
                $message = [
                    'status'=>true,
                    'message'=>"produit ajoutée avec succes!"
                ];

                $this->response($message, REST_Controller::HTTP_CREATED);
                
            } else {
                $message = [
                    'status'=>false,
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
        $id = $this->security->xss_clean($id);

        if (empty($id) AND !is_numeric($id)) {
            $this->set_response([
                'status'=>FALSE,
                'message'=>'L\'Id de la produit n\'existe'
            ],
            REST_Controller::HTTP_NOT_FOUND);
        } else {
            $produit= [
                'id'=>$id
            ];
            $outpout = $this->ProduitModel->delete($produit);
            if ($outpout>0 AND !empty($outpout)) {
                $message = [
                    'status'=>true,
                    'message'=>"produit supprimée avec succes!"
                ];

                $this->response($message, REST_Controller::HTTP_OK);
                
            } else {
                $message = [
                    'status'=>false,
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
        $_POST = $this->security->xss_clean(json_decode(file_get_contents('php://input'),true));
        $this->form_validation->set_data($_POST);

        $this->form_validation->set_rules('id', 'produit ID', 'trim|required|numeric');
        $this->form_validation->set_rules('raison_social', 'RaisonSocial', 'trim|required');
        $this->form_validation->set_rules('mobile', 'Mobile', 'trim|required');
        $this->form_validation->set_rules('description', 'Description', 'trim|required');

        if ($this->form_validation->run() == FALSE){
            $message = array(
                'status'=>false,
                'error'=>$this->form_validation->error_array(),
                'message'=>validation_errors()
            );
            $this->response($message, REST_Controller::HTTP_BAD_REQUEST);
        }else{
            $data = $this->input->post();
            $data['id'] = $this->input->post('id',TRUE);
            $data['modified'] = date('Y-m-d\TH:i:s.u');

            $outpout = $this->ProduitModel->update($data);
            if ($outpout>0 AND !empty($outpout)) {
                $message = [
                    'status'=>true,
                    'message'=>"produit Modifiée avec succes!"
                ];

                $this->response($message, REST_Controller::HTTP_CREATED);
                
            } else {
                $message = [
                    'status'=>false,
                    'message'=>"Une erreur est survenue lors de l'enregistrement!"
                ];

                $this->response($message, REST_Controller::HTTP_BAD_REQUEST);
            }
        }
    }

    /**
    * Get article
    * @method: GET
    * @param: {Id}
    */
    public function produits_get($param='')
    {
        $produit= array();

        if (empty($param)) {
            
            foreach ($this->ProduitModel->all_produit() as $row)
            {
                $data['id'] = $row->id;
                $data['code'] = $this->get_detail_article($row->id)['code'];
                $data['libelle'] = $this->get_detail_article($row->id)['libelle'];
                $data['marque'] = $this->get_detail_article($row->id)['marque'];
                $data['disponible'] = $row->is_dispo;
                $data['trocable'] = $row->is_troc;
                $data['status'] = $row->status;
                $data['boutique'] = $row->id_boutique;
                $data['quantite'] = $row->quantite;
                $data['alert'] = $row->qte_alert;
                $data['prix'] = $row->prix;
                $data['images'] = $row->images;
                $data['couleur'] = $row->couleur;
                $data['caracteristiques'] = $this->ArticleMetaModel->meta_by_article_id($row->id_article);

                $produit[] = $data;  
            }     
            if (empty($produit)) {
                $this->set_response([
                    'status'=>false,
                    'message'=> $this->msg_not_found
                ],
                    REST_Controller::HTTP_NOT_FOUND
                );
            } else {
                $this->set_response($produit, REST_Controller::HTTP_OK);
            }
            
        } else {
            $row = $this->ProduitModel->produit($param);
            $produit['id'] = $row->id;
            $produit['code'] = $this->get_detail_article($row->id)['code'];
            $produit['libelle'] = $this->get_detail_article($row->id)['libelle'];
            $produit['marque'] = $this->get_detail_article($row->id)['marque'];
            $produit['disponible'] = $row->is_dispo;
            $produit['trocable'] = $row->is_troc;
            $produit['status'] = $row->status;
            $produit['boutique'] = $row->id_boutique;
            $produit['quantite'] = $row->quantite;
            $produit['alert'] = $row->qte_alert;
            $produit['prix'] = $row->prix;
            $produit['images'] = $row->images;
            $produit['couleur'] = $row->couleur;
            $produit['caracteristiques'] = $this->ArticleMetaModel->meta_by_article_id($row->id_article);
            

            if (empty($produit)) {
                $this->set_response([
                    'status'=>false,
                    'message'=>$this->msg_not_found
                ],
                    REST_Controller::HTTP_NOT_FOUND
                );
            } else {
                $this->set_response($produit, REST_Controller::HTTP_OK);
            }

        }
        $this->set_response($produit, REST_Controller::HTTP_OK);
    }

    public function get_detail_article($id){
        $row = $this->ArticleModel->article($id);
        $article['id'] = $row->id;
        $article['code'] = $row->code;
        $article['created'] = $row->created_at;
        $article['updated'] = $row->updated_at;
        $article['libelle'] = $row->libelle;
        $article['status'] = $row->status;
        $article['etat'] = $row->etat;
        $article['qte_package'] = $row->qte_package;
        $article['marque'] = $this->get_marque($row->id_marque);
        $article['fabricant'] = $row->fabricant;
        $article['categorie'] = $this->get_categorie($row->id_sous_categorie);
        $article['caracteristiques'] = $this->ArticleMetaModel->meta_by_article_id($row->id);

        return $article;

    }

    public function get_categorie($id){
        $row = $this->SousCategorieModel->sous_categorie($id);
        $categorie['id'] = $row->id;
        $categorie['libelle'] = $row->libelle;
        $categorie['type'] = $this->CategorieModel->categorie_libelle($row->id_categorie);

        return $categorie;
    }

    public function get_marque($id){
        $row = $this->MarqueModel->marque_libelle($id);
        $marque = $row;
        return $marque;
    }
}