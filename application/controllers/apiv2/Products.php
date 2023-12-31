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

class Products extends REST_Controller
{

    /**
     * CONSTRUCTOR | LOAD MODEL
     *
     * @return Response
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Product_model');
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    }

    /**
     * SHOW | GET method.
     *
     * @return Response
     */

    public function index_get($id = 0)
    {
        // ------- Main Logic part -------
        if (!empty($id)) {
            $data = $this->Product_model->show($id);
            if ($data['status'] == false) {
                $this->response([
                    'message' => 'Data tidak ditemukan.',
                ], 404);
            } else {
                $this->response([
                    'message' => "Sukses mengambil data",
                    'data' => $data
                ], 200);
            }
        } else {
            $data = $this->Product_model->show();
            if ($data['status'] == false) {
                $this->response([
                    'message' => 'Data tidak ditemukan.',
                ], 404);
            } else {
                $this->response([
                    'message' => "Sukses mengambil data",
                    'data' => $data
                ], 200);
            }
        }
    }

    /**
     * INSERT | POST method.
     *
     * @return Response
     */
    public function index_post()
    {
        // ------- Main Logic part -------
        $input = $this->_post_args;
        // var_dump($input);die();
        if (!isset($input['product_name']) || !isset($input['category_id'])) {
            $this->response([
                'message' => 'Data tidak lengkap.',
            ], 400);
        } else {
            $product_id = $this->Product_model->insert($input);
            if ($product_id['status'] == false) {
                $this->response([
                    'message' => $product_id['message'],
                ], 400);
            } else {
                $data = $this->Product_model->show($product_id['id']);

                $this->response([
                    'message' => 'Sukses menambah data.',
                    'data' => $data
                ], 200);
            }
        }
        // ------------- End -------------
    }

    /**
     * UPDATE | PUT method.
     *
     * @return Response
     */
    public function index_put($id = 0)
    {
        // ------- Main Logic part -------

        // $headersz = $this->input->request_headers();
        $input = $this->_put_args;
        $data['product_name'] = $input['product_name'];
        $data['category_id'] = $input['category_id'];
        $data['price'] = $input['price'];
        $data['quantity'] = $input['quantity'];
        $data['description'] = $input['description'];
        $response = $this->Product_model->update($data, $id);
        if ($response > 0 && $response['status'] == true) {
            $data = $this->Product_model->show($id);
            $this->response([
                'message' => $response['message'],
                'data' => $data
            ], 200);
        } else {
            $this->response([
                'message' => $response['message'],
            ], 400);
        }
        // ------------- End -------------
    }

    /**
     * DELETE method.
     *
     * @return Response
     */
    public function index_delete($id)
    {
        // ------- Main Logic part -------
        $response = $this->Product_model->delete($id);
        $this->db->delete('ingredients', array('product_id' => $id));

        $response > 0 ? $this->response([
            'status' => true, // 'status' => 'success
            'message' => 'Product berhasil dihapus.'
        ], 200) :
            $this->response([
                'status' => false, // 'status' => 'failed
                'message' => 'ID Product tidak ditemukan atau salah'
            ], 201);
        // ------------- End -------------
    }
}
