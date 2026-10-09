<?php

namespace App\Controllers;

use App\Models\CustomerModel;
use App\Models\UserModel;
use App\Models\TaskModel;
use App\Models\ProductModel;
use Config\ProjectTeam;

class Pages extends BaseController
{
    // Shared data avoids repeating the same student details in multiple methods.
    private function sharedData(string $title, string $activePage): array
    {
        return [
            'title'      => $title,
            'activePage' => $activePage,
            'team'       => ProjectTeam::MEMBERS,
        ];
    }

    public function index(): string
    {
        // countAllResults() keeps both dashboard totals synchronized with MySQL.
        $data = $this->sharedData('Today', 'home');
        $today = date('Y-m-d');
        $data['today'] = $today;
        $data['tasks'] = (new TaskModel())->forDate($today);
        $data['customerCount'] = (new CustomerModel())->countAllResults();
        $data['userCount'] = (new UserModel())->countAllResults();
        $data['productCount'] = (new ProductModel())->where('is_archived', 0)->countAllResults();
        $data['saleCount'] = db_connect()->table('sales')->countAllResults();

        return view('pages/home', $data);
    }

    public function about(): string
    {
        // activePage lets the shared navigation highlight the current page.
        return view('pages/about', $this->sharedData('About', 'about'));
    }
}
