<?php
namespace App\Controllers;

use App\Models\CustomerModel;
use App\Models\ProductModel;

class Sales extends BaseController
{
    public function index(): string
    {
        $db = db_connect();
        // Join archived records too: a historical sale must keep its original names.
        $sales = $db->table('sales s')->select('s.*, p.name AS product_name, c.full_name AS customer_name, u.full_name AS staff_name')
            ->join('products p', 'p.id = s.product_id')->join('customers c', 'c.id = s.customer_id', 'left')
            ->join('users u', 'u.id = s.sold_by')->orderBy('s.id', 'DESC')->get()->getResultArray();
        return view('sales/index', ['title' => 'Sales History', 'activePage' => 'sales', 'sales' => $sales]);
    }

    public function new(): string { return $this->form([], []); }

    public function create()
    {
        $values = ['product_id' => (string) $this->request->getPost('product_id'),
            'customer_id' => (string) $this->request->getPost('customer_id'),
            'quantity' => (string) $this->request->getPost('quantity')];
        $rules = ['product_id' => 'required|integer|greater_than[0]',
            'customer_id' => 'permit_empty|integer|greater_than[0]',
            'quantity' => 'required|integer|greater_than[0]'];
        if (! $this->validateData($values, $rules)) return $this->form($values, $this->validator->getErrors());
        $db = db_connect();
        $productId = (int) $values['product_id'];
        $customerId = $values['customer_id'] === '' ? null : (int) $values['customer_id'];
        $quantity = (int) $values['quantity'];
        if ($customerId !== null && (new CustomerModel())->where('id', $customerId)->where('is_archived', 0)->first() === null) {
            return $this->form($values, ['customer_id' => 'Choose an active customer or leave this blank.']);
        }
        $db->transBegin();
        try {
            // Lock this product until the sale commits, preventing two checkouts from overselling it.
            $product = $db->query('SELECT id, name, price, stock_quantity FROM products WHERE id = ? AND is_archived = 0 FOR UPDATE', [$productId])->getRowArray();
            if ($product === null) { $db->transRollback(); return $this->form($values, ['product_id' => 'Choose an active product.']); }
            if ($quantity > (int) $product['stock_quantity']) { $db->transRollback(); return $this->form($values, ['quantity' => 'Only ' . $product['stock_quantity'] . ' units are available.']); }
            $priceCents = (int) round((float) $product['price'] * 100);
            $total = number_format($priceCents * $quantity / 100, 2, '.', '');
            $db->table('sales')->insert(['product_id' => $productId, 'customer_id' => $customerId,
                'sold_by' => (int) session()->get('staff_id'), 'quantity' => $quantity,
                'total_price' => $total, 'created_at' => date('Y-m-d H:i:s')]);
            $db->table('products')->where('id', $productId)->set('stock_quantity', 'stock_quantity - ' . $quantity, false)->update();
            if ($db->transStatus() === false) throw new \RuntimeException('Sale database transaction failed.');
            $db->transCommit();
        } catch (\Throwable $e) {
            $db->transRollback();
            // The user sees a safe error; production logs never include database details.
            log_message('error', 'Sale transaction failed.');
            return $this->form($values, ['sale' => 'The sale could not be recorded. Please try again.']);
        }
        return redirect()->to(site_url('sales'))->with('success', 'Sale recorded and stock updated.');
    }

    private function form(array $values, array $errors): string
    {
        return view('sales/form', ['title' => 'Record Sale', 'activePage' => 'sales', 'values' => $values,
            'errors' => $errors, 'products' => (new ProductModel())->where('is_archived', 0)->orderBy('name', 'ASC')->findAll(),
            'customers' => (new CustomerModel())->where('is_archived', 0)->orderBy('full_name', 'ASC')->findAll()]);
    }
}
