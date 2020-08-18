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
            
            foreach ($this->ProduitModel->all_produit() as $row)
            {
                $images = [
                    'main' =>$row->produit_main_image,
                    'slides' => $row->produit_images
                ];

                $data['id'] = (int)$row->produit_id;
                $data['code'] = $row->produit_code;
                $data['added'] = $row->produit_added;
                $data['modified'] = $row->produit_modified;
                $data['is_dispo'] = (int)$row->produit_is_dispo;
                $data['is_troc'] = (int)$row->produit_is_troc;
                $data['name'] = $row->produit_libelle;
                $data['slug'] = $row->produit_slug;
                $data['rate'] = (float)$row->produit_rate;
                $data['state'] = $row->produit_etat;
                $data['status'] = (int)$row->produit_status;             
                $data['boutique'] = $this->BoutiqueModel->boutique($row->produit_id_boutique);
                $data['quantity'] = (int)$row->produit_quantite;
                $data['qte_alert'] = (int)$row->produit_qte_alert;
                $data['price'] = (float)$row->produit_prix;
                $data['old_price'] = (float)$row->produit_prix_promo;
                $data['images'] = $images;
                $data['couleur'] = $row->produit_couleur;
                $data['article'] = $this->get_detail_article($row->produit_id_article);

                $produit[] = $data;  
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
                $images = [
                    'main' =>$row->produit_main_image,
                    'slides' => $row->produit_images
                ];

                $produit['id'] = (int)$row->produit_id;
                $produit['code'] = $row->produit_code;
                $produit['added'] = $row->produit_added;
                $produit['modified'] = $row->produit_modified;
                $produit['is_dispo'] = (int) $row->produit_is_dispo;
                $produit['is_troc'] = (int) $row->produit_is_troc;
                $produit['name'] = $row->produit_libelle;
                $produit['slug'] = $row->produit_slug;
                $produit['rate'] = (float)$row->produit_rate;
                $produit['status'] = (int) $row->produit_status;
                $produit['state'] = $row->produit_etat;
                $produit['boutique'] = $this->BoutiqueModel->boutique($row->produit_id_boutique);
                $produit['quantity'] = (int) $row->produit_quantite;
                $produit['qte_alert'] = (int) $row->qte_alert;
                $produit['price'] = (float) $row->produit_prix;
                $produit['price_old'] = (float) $row->produit_prix_promo;
                $produit['images'] = $images;
                $produit['couleur'] = $row->produit_couleur;
                $produit['article'] = $this->get_detail_article($row->produit_id_article);

                $this->set_response($produit, REST_Controller::HTTP_OK);

                $msg = 'Produit récupéré avec succès !';
            }

        }
        $this->set_response(['status'=>200, 'message'=>$msg, 'data'=>$produit], REST_Controller::HTTP_OK);
    }

    /**
    * Liste produit trocable
    * @method: GET
    */
    public function list_produit_troc_get()
    {
        $produit = array();
        $msg = '';
            
        foreach ($this->ProduitModel->all_produit_troc() as $row)
        {
            $images = [
                'main' =>$row->main_image,
                'slides' => $row->images
            ];

            $data['id'] = (int)$row->id;
            $data['code'] = $row->code;
            $data['added'] = $row->added;
            $data['modified'] = $row->modified;
            $data['is_dispo'] = (int)$row->is_dispo;
            $data['is_troc'] = (int)$row->is_troc;
            $data['status'] = (int)$row->status;
            $data['etat'] = $row->etat;
            $data['boutique'] = $row->id_boutique;
            $data['quantite'] = (int)$row->quantite;
            $data['qte_alert'] = (int)$row->qte_alert;
            $data['prix'] = (int)$row->prix;
            $data['prix_promo'] = (int)$row->prix_promo;
            $data['images'] = $images;
            $data['couleur'] = $row->couleur;
            $data['article'] = $this->get_detail_article($row->id_article);
            $data['condition_troc'] = $this->TrocMetaModel->condition_troc_by_produit_id($row->id);

            $produit[] = $data;  
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
            $msg = 'Liste des produits en troc récupérée avec succès !';
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
        // $this->form_validation->set_rules('code', 'Code', 'trim|required|is_unique[aqi_pp_produit.code]',
            // array('is_unique'=>'Ce code existe déja !')
        // );

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
        $row = $this->ArticleModel->article($id);
        $article['id'] =(int) $row->art_id;
        $article['code'] = $row->art_code;
        $article['created'] = $row->art_created_at;
        $article['updated'] = $row->art_updated_at;
        $article['libelle'] = $row->art_libelle;
        $article['status'] = (int) $row->art_status;
        $article['qte_package'] = (int)$row->art_qte_package;
        $article['marque'] = $this->MarqueModel->marque_modele($row->art_id_marque);
        $article['fabricant'] = $row->art_fabricant;
        $article['prix'] = (int) $row->art_prix;
        $article['categorie'] = $this->CategorieModel->categorie_libelle($row->art_id_categorie);
        $article['sous-categorie'] = $this->SousCategorieModel->sous_categorie_site($row->art_id_sous_categorie);
        $article['caracteristiques'] = $this->ArticleMetaModel->meta_by_article_id($row->art_id);

        return $article;

    }

    public function get_condition_troc($id){
        foreach ($this->TrocMetaModel->condition_troc_by_produit_id($id) as $row){
            $data['id'] = $row->id;
            $data['montant'] = $row->montant;
            $data['status'] = $row->status;
            $data['etat'] = $row->etat;
            $data['panne'] = $row->is_panne;
            $data['duree'] = $row->duree_vie;
            $data['couleur'] = $row->couleur;
            $data['marque'] = $this->MarqueModel->marque_modele($row->id_modele);

            $condition[]=$data;
        }
        

        return $condition;

    }

    public function get_categorie($id){
        $row = $this->SousCategorieModel->sous_categorie($id);
        $categorie['id'] = $row->id;
        $categorie['libelle'] = $row->libelle;
        $categorie['type'] = $this->CategorieModel->categorie_libelle($row->id_categorie);

        return $categorie;
    }

    public function get_marque($id){
        $row = $this->MarqueModel->marque_modele($id);
        $marque = $row;
        return $marque;
    }
    public function get_nom_produit($id){

        $meta = $this->ArticleMetaModel->get_meta_value_by_id_article($id);
        foreach ($meta as $row) {
            $nom= $row.'-';
        }
        return substr($nom,0,-1);
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

    
    public function list_produits_get($param)
    {
        $produit = array();
        $msg='';
       $produit = $this->ProduitModel->get_products($param);
        
        $this->set_response(['status'=>200, 'message'=>$msg, 'data'=>$produit], REST_Controller::HTTP_OK);
    }


}