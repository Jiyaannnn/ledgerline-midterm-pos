<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PosSeeder extends Seeder
{
    public function run(): void
    {
        $jianPassword = (string) getenv('MIDTERM_INITIAL_PASSWORD');
        $howardPassword = (string) getenv('MIDTERM_HOWARD_PASSWORD');
        $gerardPassword = (string) getenv('MIDTERM_GERARD_PASSWORD');
        $professorPassword = (string) getenv('MIDTERM_PROFESSOR_PASSWORD');
        if (strlen($jianPassword) < 12 || strlen($howardPassword) < 12 || strlen($gerardPassword) < 12 || strlen($professorPassword) < 12) {
            throw new \RuntimeException('Set MIDTERM_INITIAL_PASSWORD, MIDTERM_HOWARD_PASSWORD, MIDTERM_GERARD_PASSWORD, and MIDTERM_PROFESSOR_PASSWORD to passwords of at least 12 characters.');
        }
        if (count(array_unique([$jianPassword, $howardPassword, $gerardPassword, $professorPassword])) !== 4) {
            throw new \RuntimeException('All four initial passwords must be different.');
        }
        // One shared timestamp keeps the demonstration records consistent.
        $createdAt = '2026-09-24 08:00:00';

        // insertBatch() efficiently writes all five customer arrays to MySQL.
        $this->db->table('customers')->insertBatch([
            ['full_name' => 'Angelo Pineda', 'email' => 'anpineda@fit.edu.ph', 'phone' => '09074144816', 'created_at' => $createdAt],
            ['full_name' => 'Richmon Miguel', 'email' => 'rbmiguel@fit.edu.ph', 'phone' => '09927918909', 'created_at' => $createdAt],
            ['full_name' => 'Amiel Jamito', 'email' => 'amiel.jamito@example.invalid', 'phone' => '09084589753', 'created_at' => $createdAt],
            ['full_name' => 'Sean Macaldo', 'email' => 'sean.macaldo@example.invalid', 'phone' => '09615331576', 'created_at' => $createdAt],
            ['full_name' => 'Tristan Cachapero', 'email' => 'tbcachapero@fit.edu.ph', 'phone' => '09760997496', 'created_at' => $createdAt],
        ]);

        // The three group accounts use different private passwords; other sample staff can be reset in the UI.
        $this->db->table('users')->insertBatch([
            ['username' => 'jian', 'full_name' => 'Jian Edward Acob', 'avatar' => 'assets/images/avatars/jian.svg', 'password' => password_hash($jianPassword, PASSWORD_DEFAULT), 'created_at' => $createdAt],
            ['username' => 'ivicencio', 'full_name' => 'Isaiah Vicencio', 'avatar' => 'assets/images/avatars/isaiah.svg', 'password' => password_hash(bin2hex(random_bytes(24)), PASSWORD_DEFAULT), 'created_at' => $createdAt],
            ['username' => 'abarcelona', 'full_name' => 'Aaron Barcelona', 'avatar' => 'assets/images/avatars/aaron.svg', 'password' => password_hash(bin2hex(random_bytes(24)), PASSWORD_DEFAULT), 'created_at' => $createdAt],
            ['username' => 'howard', 'full_name' => 'Howard Mac Callanta', 'avatar' => 'assets/images/avatars/howard.svg', 'password' => password_hash($howardPassword, PASSWORD_DEFAULT), 'created_at' => $createdAt],
            ['username' => 'gerard', 'full_name' => 'Gerard Eric Doroja', 'avatar' => 'assets/images/avatars/gerard.svg', 'password' => password_hash($gerardPassword, PASSWORD_DEFAULT), 'created_at' => $createdAt],
            ['username' => 'professor', 'full_name' => 'Professor (Demo Access)', 'avatar' => 'assets/images/avatars/professor.svg', 'password' => password_hash($professorPassword, PASSWORD_DEFAULT), 'created_at' => $createdAt],
        ]);
    }
}
