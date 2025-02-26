<?php

namespace App\Exports;

use App\Models\SangKien;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Filament\Actions\Exports\ExportColumn;
use Filament\Forms;
use Illuminate\Database\Eloquent\Builder;

class SangKienExporter extends Exporter
{
    protected static ?string $model = SangKien::class;

    // Sử dụng fileNamePrefix theo tài liệu chính thức
    protected static function fileNamePrefix(): string
    {
        return 'bao-cao-sang-kien';
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

    public function getFormSchema(): array
    {
        return [
            Forms\Components\Select::make('format')
                ->label('Định dạng xuất')
                ->options([
                    'csv' => 'CSV',
                    'xlsx' => 'Excel (XLSX)',
                    'pdf' => 'PDF',
                ])
                ->default('xlsx')
                ->required(),

            Forms\Components\Checkbox::make('with_trashed')
                ->label('Bao gồm các bản ghi đã xóa')
                ->hidden(fn (): bool => ! static::$model::usesSoftDeletes()),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Xuất dữ liệu báo cáo sáng kiến của bạn đã hoàn tất và đã sẵn sàng để tải xuống.';

        if ($failureCount = $export->getFailureCount()) {
            $body .= " {$failureCount} hàng không thể xuất.";
        }

        return $body;
    }

    protected function getChunkSize(): int
    {
        return 100; // Số bản ghi trong mỗi file
    }
}
