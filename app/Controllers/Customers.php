<?php

namespace App\Controllers;

use App\Models\CustomerModel;

class Customers extends BaseController
{
    public function index(): string
    {
        // Creating the Model gives this controller access to the customers table.
        $customerModel = new CustomerModel();

        // Query Builder sorts by id, while findAll() retrieves every customer row.
        return view('customers/index', [
            'title'      => 'Customer Accounts',
            'activePage' => 'customers',
            'customers'  => $customerModel->where('is_archived', 0)->orderBy('id', 'ASC')->findAll(),
        ]);
    }

    public function new(): string
    {
        return view('customers/form', ['title' => 'New Customer', 'activePage' => 'customers', 'customer' => null, 'errors' => [], 'values' => []]);
    }

    public function create()
    {
        return $this->saveCustomer(null);
    }

    public function edit(int $id): string
    {
        $customer = (new CustomerModel())->find($id);
        if ($customer === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        return view('customers/form', ['title' => 'Edit Customer', 'activePage' => 'customers', 'customer' => $customer, 'errors' => [], 'values' => $customer]);
    }

    public function update(int $id)
    {
        return $this->saveCustomer($id);
    }

    public function remove(int $id)
    {
        $model = new CustomerModel();
        if ($model->find($id) === null) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        // Customers with sales are archived so the historical receipt keeps its name.
        if (db_connect()->table('sales')->where('customer_id', $id)->countAllResults() > 0) {
            $model->update($id, ['is_archived' => 1]);
            $message = 'Customer removed from the active directory; past sales remain intact.';
        } else {
            $model->delete($id);
            $message = 'Customer deleted.';
        }
        return redirect()->to(site_url('customers'))->with('success', $message);
    }

    private function saveCustomer(?int $id)
    {
        $model = new CustomerModel();
        $customer = $id === null ? null : $model->find($id);
        if ($id !== null && $customer === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        $values = [
            'full_name' => trim((string) $this->request->getPost('full_name')),
            'email' => trim((string) $this->request->getPost('email')),
            'phone' => trim((string) $this->request->getPost('phone')),
        ];
        // Validation happens before the database changes, so invalid entries can be redisplayed.
        $rules = ['full_name' => 'required|max_length[100]', 'email' => 'required|valid_email|max_length[100]', 'phone' => 'permit_empty|max_length[20]'];
        if (! $this->validateData($values, $rules)) {
            return view('customers/form', ['title' => $id ? 'Edit Customer' : 'New Customer', 'activePage' => 'customers', 'customer' => $customer, 'errors' => $this->validator->getErrors(), 'values' => $values]);
        }
        if ($id === null) {
            $values['created_at'] = date('Y-m-d H:i:s');
            $model->insert($values);
        } else {
            $model->update($id, $values);
        }
        return redirect()->to(site_url('customers'))->with('success', $id ? 'Customer updated.' : 'Customer created.');
    }
}
