<?php
namespace App\Controllers;
use Config\ProjectTeam;
class Profile extends BaseController {
    public function index(): string {
        return view('pages/profile', ['title' => 'Project team', 'activePage' => 'profile', 'team' => ProjectTeam::MEMBERS]);
    }
}
