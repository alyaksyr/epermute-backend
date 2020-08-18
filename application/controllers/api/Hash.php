<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
// require APPPATH . '/libraries/REST_Controller.php';
use Phoneplus\Libraries\REST_Controller;
/**
 * Keys Controller
 * This is a basic Key Management REST controller to make and delete keys
 *
 * @package         CodeIgniter
 * @subpackage      Rest Server
 * @category        Controller
 * @author          Phil Sturgeon, Chris Kacerguis
 * @license         MIT
 * @link            https://github.com/chriskacerguis/codeigniter-restserver
 */
class Hash extends MY_Controller {

    public function index_get()

    {
        // $timeTarget = 0.05; // 50 millisecondes

        // $cost = 8;
        $pwd = "test";
        for($i=0; $i<1000; $i++){
			$pass_in_md5 = md5($pwd);
			$pass_in_sh = sha1($pass_in_md5);
			$pass = $pass_in_sh;
        }
        echo $pass;
        $passe = password_hash($pwd, PASSWORD_BCRYPT);
        // return $pass;
        // do {
        //     $cost++;
        //     $start = microtime(true);
        //     password_hash($pass, PASSWORD_BCRYPT, ["cost" => 10]);
        //     $end = microtime(true);
        // } while (($end - $start) < $timeTarget);

        
        
        echo $passe;
       // echo "Valeur de 'cost' la plus appropriée : " . $cost;

    }

}
