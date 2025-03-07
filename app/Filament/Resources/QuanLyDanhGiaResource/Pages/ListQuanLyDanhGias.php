<?php

namespace App\Filament\Resources\QuanLyDanhGiaResource\Pages;

use App\Filament\Resources\QuanLyDanhGiaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\DB;

class ListQuanLyDanhGias extends ListRecords
{
    protected static string $resource = QuanLyDanhGiaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tạo mới'),
        ];
    }

    protected function getTableBulkActions(): array
    {
        return [
            // Các bulk actions khác nếu có
        ];
    }

    protected function configureDeleteAction($action): void
    {
        $action->after(function ($record) {
            $this->reorderRemainingDots($record);
        });
    }

    protected function reorderRemainingDots($record): void
    {
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
