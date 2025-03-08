<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\TrangThaiSangKien;

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
                'mo_ta' => 'Sáng kiến sau khi được duyệt bởi trưởng phòng sẽ ở trạng thái kiểm tra, phải chờ ban nhân sự kiểm tra.',
                'order' => 4,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ma_trang_thai' => 'rejected_secretary',
                'ten_trang_thai' => 'Bị từ chối bởi ban nhân sự',
                'mo_ta' => 'Bị từ chối bởi ban nhân sự.',
                'order' => 5,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ma_trang_thai' => 'pending_council',
                'ten_trang_thai' => 'Chờ hội đồng phê duyệt',
                'mo_ta' => 'Sáng kiến đang chờ hội đồng thẩm định phê duyệt',
                'order' => 4,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ma_trang_thai' => 'rejected_council',
                'ten_trang_thai' => 'Bị từ chối bởi hội đồng',
                'mo_ta' => 'Bị từ chối bởi hội đồng',
                'order' => 7,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ma_trang_thai' => 'scoring1',
                'ten_trang_thai' => 'Đang chấm điểm vòng 1',
                'mo_ta' => 'Sáng kiến sau khi được duyệt bởi hội đồng sẽ ở trạng thái đang chấm điểm vòng 1, phải chờ từng thành viên hội đồng chấm điểm.',
                'order' => 8,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ma_trang_thai' => 'scoring2',
                'ten_trang_thai' => 'Đang chấm điểm vòng 2',
                'mo_ta' => 'Sáng kiến sau khi được chấm điểm vòng 1 sẽ ở trạng thái đang chấm điểm vòng 2, cả hội đồng thống nhất điểm quyết định.',
                'order' => 9,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ma_trang_thai' => 'approved',
                'ten_trang_thai' => 'Đã duyệt',
                'mo_ta' => 'Sáng kiến đã được duyệt và chấm điểm xong.',
                'order' => 10,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        TrangThaiSangKien::create([
            'ma_trang_thai' => 'pending_council',
            'ten_trang_thai' => 'Chờ hội đồng phê duyệt',
            'mo_ta' => 'Sáng kiến đang chờ hội đồng thẩm định phê duyệt',
            'order' => 4,
            'is_active' => true,
        ]);
    }
}
