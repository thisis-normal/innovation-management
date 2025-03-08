<?php

namespace App\Filament\Resources\QuanLyDanhGiaResource\Pages;

use App\Filament\Resources\QuanLyDanhGiaResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\TextInput;

class CreateQuanLyDanhGia extends CreateRecord
{
    protected static string $resource = QuanLyDanhGiaResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->record]);
    }

    protected function mutateFormData(array $data): array
    {
        // Tìm số đợt lớn nhất trong năm hiện tại
        $maxSoDot = DB::table('dot_danh_gia')
            ->where('nam', $data['nam'])
            ->max('so_dot');

        // Nếu đã có đợt đánh giá, tăng số đợt lên 1
        if ($maxSoDot) {
            $data['so_dot'] = $maxSoDot + 1;
        } else {
            $data['so_dot'] = 1; // Nếu chưa có đợt nào, bắt đầu từ 1
        }

        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        // Kiểm tra xem đã tồn tại bản ghi với nam và so_dot này chưa
        $exists = DB::table('dot_danh_gia')
            ->where('nam', $data['nam'])
            ->where('so_dot', $data['so_dot'])
            ->exists();

        if ($exists) {
            $this->halt();
            $this->notify('danger', 'Đợt đánh giá đã tồn tại', 'Đợt đánh giá với năm ' . $data['nam'] . ' và số đợt ' . $data['so_dot'] . ' đã tồn tại. Vui lòng tạo đợt đánh giá khác.');
            return new \App\Models\DotDanhGia();
        }

        return static::getModel()::create($data);
    }

    protected function fillForm(): void
    {
        // Gọi phương thức fillForm của lớp cha để điền các giá trị mặc định
        parent::fillForm();

        // Lấy giá trị hiện tại của form
        $data = $this->form->getRawState();

        // Đảm bảo năm hiện tại được điền
        $currentYear = date('Y');
        if (!isset($data['nam']) || empty($data['nam'])) {
            $data['nam'] = $currentYear;
        }

        // Tìm số đợt lớn nhất trong năm hiện tại
        $maxSoDot = DB::table('dot_danh_gia')
            ->where('nam', $currentYear)
            ->max('so_dot');

        // Nếu đã có đợt đánh giá, tăng số đợt lên 1
        $nextSoDot = $maxSoDot ? $maxSoDot + 1 : 1;

        // Cập nhật cả năm và số đợt
        $this->form->fill([
            'nam' => $currentYear,
            'so_dot' => $nextSoDot,
        ]);
    }
}
