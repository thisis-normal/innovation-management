<?php

namespace Database\Seeders;

use App\Models\DonVi;
use Illuminate\Database\Seeder;

class DonViSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $donVis = [
            ['ten_don_vi' => 'Ban quản lý dự án', 'mo_ta' => 'Ban quản lý dự án'],
            ['ten_don_vi' => 'Ban quản lý dự án 2', 'mo_ta' => 'Ban quản lý dự án 2'],
            ['ten_don_vi' => 'Ban quản lý dự án 3', 'mo_ta' => 'Ban quản lý dự án 3', 'don_vi_cha_id' => 1]
        ];

        foreach ($donVis as $donVi) {
            DonVi::query()->create($donVi);
        }
    }
}
