<?php
namespace App\Database\Seeds;
use CodeIgniter\Database\Seeder;
class MidtermProductsSeeder extends Seeder {
    public function run(): void {
        $now = date('Y-m-d H:i:s');
        // Realistic counter stock makes the checkout ready to demonstrate immediately.
        $this->db->table('products')->insertBatch([
            ['name'=>'A4 Copy Paper · 500 Sheets','price'=>'245.00','stock_quantity'=>32,'image'=>'assets/images/products/demo-1.svg','created_at'=>$now],
            ['name'=>'Black Gel Pen · 0.5 mm','price'=>'28.00','stock_quantity'=>80,'image'=>'assets/images/products/demo-2.svg','created_at'=>$now],
            ['name'=>'A5 Dotted Notebook','price'=>'125.00','stock_quantity'=>24,'image'=>'assets/images/products/demo-3.svg','created_at'=>$now],
            ['name'=>'Clear Document Folder','price'=>'18.00','stock_quantity'=>65,'image'=>'assets/images/products/demo-4.svg','created_at'=>$now],
            ['name'=>'Highlighter Set · 4 Colors','price'=>'89.00','stock_quantity'=>19,'image'=>'assets/images/products/demo-5.svg','created_at'=>$now],
            ['name'=>'Correction Tape · 5 mm','price'=>'42.00','stock_quantity'=>36,'image'=>'assets/images/products/demo-6.svg','created_at'=>$now],
        ]);
    }
}
