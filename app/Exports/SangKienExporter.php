<?php

namespace App\Exports;

use App\Models\SangKien;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Filament\Actions\Exports\ExportColumn;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Log;

class SangKienExporter extends Exporter
{
    protected static ?string $model = SangKien::class;

    public function getFileName(Export $export): string
    {
        try {
            return "sang_kien-{$export->getKey()}.xlsx";
        } catch (\Exception $e) {
            Log::error('Export filename error: ' . $e->getMessage());
            return 'sang_kien-export.xlsx';
        }
    }

    // Sửa phương thức này để sử dụng ExportColumn thay vì string
    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),

            ExportColumn::make('ten_sang_kien')
                ->label('Tên Sáng Kiến'),

            ExportColumn::make('mo_ta')
                ->label('Mô Tả'),

            ExportColumn::make('user.name')
                ->label('Tác Giả'),

            ExportColumn::make('donVi.ten_don_vi')
                ->label('Đơn Vị'),

            ExportColumn::make('trangThaiSangKien.ten_trang_thai')
                ->label('Trạng Thái'),

            ExportColumn::make('ket_qua')
                ->label('Xếp Loại'),

            ExportColumn::make('created_at')
                ->label('Ngày Tạo'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your institute export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
    public static function getNotifiableUser(Export $export): Authenticatable
    {
        return $export->user;
    }

    // Optional: Add this to make sure notifications are sent via database
    protected function getNotificationChannels(): array
    {
        return ['database'];
    }

}
