<?php

namespace Database\Seeders;

use App\Models\VaiTro;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VaiTroSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vaiTros = [
            ['ma_vai_tro' => 'user', 'ten_vai_tro' => 'User', 'mo_ta' => 'Người dùng'],
            ['ma_vai_tro' => 'admin', 'ten_vai_tro' => 'Admin', 'mo_ta' => 'Quản trị viên'],
            ['ma_vai_tro' => 'secretary', 'ten_vai_tro' => 'Secretary', 'mo_ta' => 'Thư ký'],
            ['ma_vai_tro' => 'manager', 'ten_vai_tro' => 'Manager', 'mo_ta' => 'Trưởng phòng']
        ];

        foreach ($vaiTros as $vaiTro) {
            VaiTro::query()->create($vaiTro);
        }
    }
}
