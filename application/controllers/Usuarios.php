<?php
defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';

class Usuarios extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('usuario_model');
        $this->load->helper(['jwt', 'authorization']);
        $this->load->model('usuario_model');
    }

    public function index_get()
    {
        $data = $this->verify_request();
        
        if (!empty($data)) {
            $data = $this->usuario_model->fetch_all();
            $status = REST_Controller::HTTP_OK;
            $response = $data->result_array();

            $this->response($response, $status);
        }
    }

    public function index_post()
    {
        $input_data = json_decode($this->input->raw_input_stream, true);

        $sem_nome = !array_key_exists("nome", $input_data);
        $sem_matricula = !array_key_exists("matricula", $input_data);
        $sem_senha = !array_key_exists("senha", $input_data);

        if ($sem_nome or $sem_matricula or $sem_senha) {
            header('Content-type: application/json');
            echo $error = json_encode(array("error" => "Any field is invalid!"));
            $this->response(null, REST_Controller::HTTP_BAD_REQUEST);
        } else {
            if (!array_key_exists("status", $input_data)) {
                $input_data["status"] = 1;
            }
            
            $this->usuario_model->insert($input_data);
            $this->response(null, REST_Controller::HTTP_OK);
        }
    }

    public function index_put($id)
    {
        $data = $this->usuario_model->get($id);
        
        if (empty($data)) {
            $this->response(null, REST_Controller::HTTP_NOT_FOUND);
        } else {
            $input_data = json_decode($this->input->raw_input_stream, true);
            $this->usuario_model->update($id, $input_data);
            $this->response(null, REST_Controller::HTTP_NO_CONTENT);
        }
    }

    public function index_delete($id)
    {
        $data = $this->usuario_model->get($id);
        
        if (empty($data)) {
            $this->response(null, REST_Controller::HTTP_NOT_FOUND);
        } else {
            $this->usuario_model->delete($id);
            $this->response(null, REST_Controller::HTTP_NO_CONTENT);
        }
    }

    private function verify_request()
    {
        // Get all the headers
        $headers = $this->input->request_headers();
        // Extract the token if he exists
        if (array_key_exists("Authorization", $headers)) {
            $token = $headers['Authorization'];
        } else {
            $token = "";
        }
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
}
