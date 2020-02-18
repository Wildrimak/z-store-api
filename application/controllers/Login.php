<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

// use Restserver\Libraries\REST_Controller;

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

class Login extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper(['jwt', 'authorization']);
        $this->load->model('usuario_model');
    }

    public function token_post()
    {
        $matricula = $this->post('matricula');
        $senha = $this->post('senha');
        echo "HAAAAAAAAAAAAAAAAAAAAAAA";
        $real_user = $this->usuario_model->get_by_matricula($matricula)[0];
        
        if ($matricula === $real_user['matricula'] && $senha === $real_user['senha']) {
            $token = AUTHORIZATION::generateToken(['username' => $real_user['matricula']]);
            $status = REST_Controller::HTTP_OK;
            $response = ['status' => $status, 'token' => $token];
            $this->response($response, $status);
        } else {
            $status = REST_Controller::HTTP_FORBIDDEN;
            $response = ['status' => $status, 'token' => 'invalid'];
            $this->response($response, $status);
        }
    }

    private function verify_request()
    {
        // Get all the headers
        $headers = $this->input->request_headers();
        // Extract the token
        $token = $headers['Authorization'];
        // Use try-catch
        // JWT library throws exception if the token is not valid
        try {
            // Validate the token
            // Successfull validation will return the decoded user data else returns false
            $data = AUTHORIZATION::validateToken($token);
            if ($data === false) {
                $status = parent::HTTP_UNAUTHORIZED;
                $response = ['status' => $status, 'msg' => 'Unauthorized Access!'];
                $this->response($response, $status);
                exit();
            } else {
                return $data;
            }
        } catch (Exception $e) {
            // Token is invalid
            // Send the unathorized access message
            $status = parent::HTTP_UNAUTHORIZED;
            $response = ['status' => $status, 'msg' => 'Unauthorized Access! '];
            $this->response($response, $status);
        }
    }

    public function get_me_data_post()
    {
        // Call the verification method and store the return value in the variable
        $data = $this->verify_request();

        if (!empty($data)) {
            // Send the return data as reponse
            $status = parent::HTTP_OK;
            $response = ['status' => $status, 'data' => $data];
            $this->response($response, $status);
        } else {
            $this->response(["error" => "Você não tem permissão para acessar a página"], REST_Controller::HTTP_UNAUTHORIZED);
        }
    }
}
