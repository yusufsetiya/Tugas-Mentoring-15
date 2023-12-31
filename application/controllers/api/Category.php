<?php

/* Table structure for table `products` */
// CREATE TABLE `products` (
//   `id` int(10) UNSIGNED NOT NULL,
//   `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
//   `price` double NOT NULL,
//   `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
//   `updated_at` datetime DEFAULT NULL
// ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
// ALTER TABLE `products` ADD PRIMARY KEY (`id`);
// ALTER TABLE `products` MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1; COMMIT;

/**
 * Product class.
 * 
 * @extends REST_Controller
 */
require APPPATH . '/libraries/REST_Controller.php';

use Restserver\Libraries\REST_Controller;

class Category extends REST_Controller
{

    /**
     * CONSTRUCTOR | LOAD MODEL
     *
     * @return Response
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->library('Authorization_Token');
        $this->load->model('Category_model');
    }

    /**
     * SHOW | GET method.
     *
     * @return Response
     */

    public function index_get($id = 0)
    {
        $headers = $this->input->request_headers();
        if (isset($headers['Authorization'])) {
            $decodedToken = $this->authorization_token->validateToken($headers['Authorization']);
            if ($decodedToken['status']) {
                // ------- Main Logic part -------
                if (!empty($id)) {
                    $data = $this->Category_model->show($id);
                    if($data['status'] == false){
                        $this->response([
                            'message' => 'Data tidak ditemukan.',
                        ], 404);
                    }else{
                        $this->response([
                            'message' => "Sukses mengambil data",
                            'data' => $data
                        ], 200);
                    }
                } else {
                    $data = $this->Category_model->show();
                    if($data['status'] == false){
                        $this->response([
                            'message' => 'Data tidak ditemukan.',
                        ], 404);
                    }else{
                        $this->response([
                            'message' => "Sukses mengambil data",
                            'data' => $data
                        ], 200);
                    }
                }
                // ------------- End -------------
            } else {
                $this->response([
                    'message' => 'Authentikasi gagal.',
                ], 400);
            }
        } else {
            $this->response([
                'message' => 'Authentikasi gagal.',
            ], 400);
        }
    }

    /**
     * INSERT | POST method.
     *
     * @return Response
     */
    public function index_post()
    {
        $headers = $this->input->request_headers();
        if (isset($headers['Authorization'])) {
            $decodedToken = $this->authorization_token->validateToken($headers['Authorization']);
            if ($decodedToken['status']) {
                // ------- Main Logic part -------
                $input = $this->_post_args;
                // var_dump($input);die();
                if(!isset($input['category_name'])){
                    $this->response([
                        'message' => 'Data tidak lengkap.',
                    ], 400);
                }else{
                    $product_id = $this->Category_model->insert($input);
                    $data = $this->Category_model->show($product_id);
    
                    $this->response([
                        'message' => 'Sukses menambah data.',
                        'data' => $data
                    ], 200);
                }
                // ------------- End -------------
            } else {
                $this->response([
                    'message' => 'Authentikasi gagal.',
                ], 400);
            }
        } else {
            $this->response([
                'message' => 'Authentikasi gagal.',
            ], 400);
        }
    }

    /**
     * UPDATE | PUT method.
     *
     * @return Response
     */
    public function index_put($id)
    {
        $headers = $this->input->request_headers();
        if (isset($headers['Authorization'])) {
            $decodedToken = $this->authorization_token->validateToken($headers['Authorization']);
            if ($decodedToken['status']) {
                // ------- Main Logic part -------

                // $headersz = $this->input->request_headers();
                $input = $this->_put_args;
                $data['category_name'] = $input['category_name'];
                $response = $this->Category_model->update($data, $id);
                if($response > 0){
                    $data = $this->Category_model->show($id);
                    $this->response([
                        'message' => $response['message'],
                        'data' => $data
                    ], 200);
                }else{
                    $this->response([
                        'message' => 'Data tidak ter update.',
                    ], 400);
                }
                // ------------- End -------------
            } else {
                $this->response($decodedToken);
            }
        } else {
            $this->response(['Authentication failed'], 400);
        }
    }

    /**
     * DELETE method.
     *
     * @return Response
     */
    public function index_delete($id)
    {

        $headers = $this->input->request_headers();
        if (isset($headers['Authorization'])) {
            $decodedToken = $this->authorization_token->validateToken($headers['Authorization']);
            if ($decodedToken['status']) {
                // ------- Main Logic part -------
                $response = $this->Category_model->delete($id);

                $response > 0 ? $this->response([
                    'message' => 'Kategori berhasil dihapus.'
                ], 200) : 
                $this->response([
                    'message' =>'ID Kategori tidak ditemukan atau salah'
                ], 201);
                // ------------- End -------------
            } else {
                $this->response($decodedToken);
            }
        } else {
            $this->response(['Authentication failed'], 400);
        }
    }
}
