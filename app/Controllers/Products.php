<?php
namespace App\Controllers;

use App\Models\ProductModel;
use App\Libraries\ImageStorage;
use CodeIgniter\Exceptions\PageNotFoundException;

class Products extends BaseController
{
    public function index(): string
    {
        return view('products/index', ['title' => 'Products', 'activePage' => 'products',
            'products' => (new ProductModel())->where('is_archived', 0)->orderBy('id', 'DESC')->findAll()]);
    }

    public function new(): string { return $this->form(null, [], []); }
    public function create() { return $this->save(null); }
    public function edit(int $id): string
    {
        $product = (new ProductModel())->find($id);
        if ($product === null) throw PageNotFoundException::forPageNotFound();
        return $this->form($product, $product, []);
    }
    public function update(int $id) { return $this->save($id); }

    public function archive(int $id)
    {
        $model = new ProductModel();
        if ($model->find($id) === null) throw PageNotFoundException::forPageNotFound();
        $model->update($id, ['is_archived' => 1]);
        return redirect()->to(site_url('products'))->with('success', 'Product archived; past sales remain available.');
    }

    private function form(?array $product, array $values, array $errors): string
    {
        return view('products/form', ['title' => $product ? 'Edit Product' : 'New Product',
            'activePage' => 'products', 'product' => $product, 'values' => $values, 'errors' => $errors]);
    }

    private function save(?int $id)
    {
        $model = new ProductModel();
        $product = $id === null ? null : $model->find($id);
        if ($id !== null && $product === null) throw PageNotFoundException::forPageNotFound();
        $values = ['name' => trim((string) $this->request->getPost('name')),
            'price' => trim((string) $this->request->getPost('price')),
            'stock_quantity' => trim((string) $this->request->getPost('stock_quantity'))];
        $rules = ['name' => 'required|max_length[100]', 'price' => 'required|decimal|greater_than_equal_to[0.01]|less_than_equal_to[99999999.99]',
            'stock_quantity' => 'required|integer|greater_than_equal_to[0]'];
        $errors = [];
        if (! $this->validateData($values, $rules)) $errors = $this->validator->getErrors();
        $upload = $this->request->getFile('image');
        $hasUpload = $upload !== null && $upload->getError() !== UPLOAD_ERR_NO_FILE;
        if ($hasUpload && ! $this->validateData([], ['image' => 'uploaded[image]|is_image[image]|mime_in[image,image/jpeg,image/png]|max_size[image,2048]'])) {
            $errors = array_merge($errors, $this->validator->getErrors());
        }
        if ($errors !== []) return $this->form($product, $values, $errors);
        if ($hasUpload) {
            try {
                $values['image'] = (new ImageStorage())->store($upload, 'products', 960, 720);
            } catch (\Throwable $e) {
                log_message('error', 'Product image preparation failed.');
                return $this->form($product, $values, ['image' => 'The image could not be stored. Choose another JPG or PNG.']);
            }
        }
        if ($id === null) {
            $values['created_at'] = date('Y-m-d H:i:s');
            $model->insert($values);
        } else {
            $model->update($id, $values);
            if ($hasUpload && ! empty($product['image'])) {
                // Replacing an image releases its previous database or local file storage.
                (new ImageStorage())->delete($product['image']);
            }
        }
        return redirect()->to(site_url('products'))->with('success', $id ? 'Product updated.' : 'Product created.');
    }
}
