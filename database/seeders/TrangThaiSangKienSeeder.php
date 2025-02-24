<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TrangThaiSangKienSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //insert 10 fixed data for trang_thai_sang_kien
        DB::table('trang_thai_sang_kien')->insert([
            [
                'ma_trang_thai' => 'draft',
                'ten_trang_thai' => 'Bản nháp',
                'mo_ta' => 'Sáng kiến sau khi tạo mới sẽ ở trạng thái bản nháp.',
                'order' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ma_trang_thai' => 'pending_manager',
                'ten_trang_thai' => 'Chờ trưởng bộ phận phê duyệt',
                'mo_ta' => 'Sáng kiến sau khi bấm nút "Gửi duyệt" sẽ ở trạng thái chờ duyệt, phải chờ trưởng phòng duyệt.',
                'order' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ma_trang_thai' => 'rejected_manager',
                'ten_trang_thai' => 'Bị từ chối bởi trưởng bộ phận',
                'mo_ta' => 'Bị từ chối bởi trưởng bộ phận.',
                'order' => 3,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ma_trang_thai' => 'pending_secretary',
                'ten_trang_thai' => 'Chờ ban nhân sự phê duyệt',
                'mo_ta' => 'Sáng kiến sau khi được duyệt bởi trưởng phòng sẽ ở trạng thái kiểm tra, phải chờ thư ký kiểm tra.',
                'order' => 4,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ma_trang_thai' => 'rejected_secretary',
                'ten_trang_thai' => 'Bị từ chối bởi ban nhân sự',
                'mo_ta' => 'Bị từ chối bởi ban nhân sự.',
                'order' => 3,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ma_trang_thai' => 'Reviewing',
                'ten_trang_thai' => 'Đang thẩm định',
                'mo_ta' => 'Sáng kiến sau khi được kiểm tra bởi thư ký sẽ ở trạng thái đang thẩm định, phải chờ hội đồng duyệt.',
                'order' => 5,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ma_trang_thai' => 'Scoring1',
                'ten_trang_thai' => 'Đang chấm điểm vòng 1',
                'mo_ta' => 'Sáng kiến sau khi được duyệt bởi hội đồng sẽ ở trạng thái đang chấm điểm vòng 1, phải chờ từng thành viên hội đồng chấm điểm.',
                'order' => 6,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ma_trang_thai' => 'Scoring2',
                'ten_trang_thai' => 'Đang chấm điểm vòng 2',
                'mo_ta' => 'Sáng kiến sau khi được chấm điểm vòng 1 sẽ ở trạng thái đang chấm điểm vòng 2, cả hội đồng thống nhất điểm quyết định.',
                'order' => 7,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ma_trang_thai' => 'Approved',
                'ten_trang_thai' => 'Đã duyệt',
                'mo_ta' => 'Sáng kiến đã được duyệt và chấm điểm xong.',
                'order' => 8,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
