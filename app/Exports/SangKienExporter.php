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
    protected static bool $shouldQueue = false;

    public function getFileName(Export $export): string
    {
//        return 'bao-cao-' . date('Y-m-d');
        return "sang_kien-{$export->getKey()}.xlsx";
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
        $body = 'Xuất dữ liệu sáng kiến thành công và ' . number_format($export->successful_rows) . ' dòng đã được xuất.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('dòng')->plural($failedRowsCount) . ' không thể xuất.';
        }

        return $body;
    }
    public static function getCompletedNotificationTitle (Export $export): string
    {
        return 'Xuất dữ liệu sáng kiến hoàn tất';
    }
    public function getOptions(): array
    {
        return [
            'queue' => false,
        ];
    }

}
