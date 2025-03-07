<?php

namespace App\Filament\Resources\QuanLyDanhGiaResource\Pages;

use App\Filament\Resources\QuanLyDanhGiaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EditQuanLyDanhGia extends EditRecord
{
    protected static string $resource = QuanLyDanhGiaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Xóa'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        // Kiểm tra xem đã tồn tại bản ghi khác với nam và so_dot này chưa
        $exists = DB::table('dot_danh_gia')
            ->where('nam', $data['nam'])
            ->where('so_dot', $data['so_dot'])
            ->where('id', '!=', $record->id)
            ->exists();

        if ($exists) {
            $this->halt();
            $this->notify('danger', 'Đợt đánh giá đã tồn tại', 'Đợt đánh giá với năm ' . $data['nam'] . ' và số đợt ' . $data['so_dot'] . ' đã tồn tại. Vui lòng chọn số đợt khác.');
            return $record;
        }

        $data['updated_by'] = auth()->id();
        $record->update($data);

        return $record;
    }
}
