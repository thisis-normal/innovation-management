<?php

namespace App\Filament\Resources\QuanLyDanhGiaResource\Pages;

use App\Filament\Resources\QuanLyDanhGiaResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\DB;

class ViewQuanLyDanhGia extends ViewRecord
{
    protected static string $resource = QuanLyDanhGiaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Xóa')
                ->after(function () {
                    $this->reorderRemainingDots();
                }),
        ];
    }

    protected function reorderRemainingDots(): void
    {
        $record = $this->record;
        $nam = $record->nam;
        $soDot = $record->so_dot;

        // Lấy tất cả các đợt có số lớn hơn đợt vừa xóa
        $dotsToUpdate = DB::table('dot_danh_gia')
            ->where('nam', $nam)
            ->where('so_dot', '>', $soDot)
            ->orderBy('so_dot')
            ->get();

        // Cập nhật lại số đợt
        foreach ($dotsToUpdate as $dot) {
            DB::table('dot_danh_gia')
                ->where('id', $dot->id)
                ->update(['so_dot' => $dot->so_dot - 1]);
        }
    }
}
