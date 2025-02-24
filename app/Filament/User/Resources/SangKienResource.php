<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\SangKienResource\Pages;
use App\Filament\User\Resources\SangKienResource\RelationManagers;
use App\Models\SangKien;
use App\Models\TaiLieuSangKien;
use App\Models\TrangThaiSangKien;
use Exception;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use ZipArchive;

class SangKienResource extends Resource
{
    protected static ?string $model = SangKien::class;
    protected static ?string $navigationLabel = 'Sáng Kiến'; // Custom label in sidebar
    protected static ?string $pluralModelLabel = 'Sáng Kiến'; // Used for breadcrumbs
    protected static ?string $modelLabel = 'Sáng Kiến'; // Used in forms & buttons
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $slug = 'sang-kien';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('ten_sang_kien')->label('Tên sáng kiến')->required()->columnSpan('full'),
                RichEditor::make('hien_trang')->label('Hiện trạng')->disableToolbarButtons(['attachFiles', 'link'])->required()->columnSpan('full'),
                RichEditor::make('mo_ta')->label('Mô tả')
                    ->disableToolbarButtons(['attachFiles', 'link'])
                    ->required()
                    ->columnSpan('full'),
                RichEditor::make('ket_qua')->label('Kết quả')->disableToolbarButtons(['attachFiles', 'link'])->required()->columnSpan('full'),
                FileUpload::make('files')
                    ->disk('public')
                    ->label('File')
                    ->multiple()
                    ->maxFiles(5)
                    ->acceptedFileTypes([
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // DOC, DOCX
                        'application/pdf', // PDF
                        'application/vnd.ms-excel',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' // XLS, XLSX
                    ])
                    ->directory('innovation-files')
                    ->downloadable()
                    ->openable()
                    ->required()
                    ->columnSpan('full')
                    ->maxSize(10 * 1024) // 10 MB
                    ->helperText('Chỉ chấp nhận các loại file: DOC, DOCX, PDF, XLS, XLSX. Dung lượng tối đa 10MB/file.')
                    ->afterStateHydrated(function ($state, callable $set, $record) {
                        if ($record) {
                            $set('files', TaiLieuSangKien::query()->where('sang_kien_id', $record->id)
                                ->pluck('file_path')
                                ->toArray());
                        }
                    })
                    ->validationMessages([
                        'files.max' => 'Số lượng file tối đa là 5.',
                        'files.acceptedFileTypes' => 'Chỉ chấp nhận các loại file: DOC, DOCX, PDF, XLS, XLSX.',
                        'files.maxSize' => 'Dung lượng tối đa 10MB/file.',
                    ]),
                Hidden::make('ma_tac_gia')->default(Auth::id()),
            ]);
    }

    /**
     * @throws Exception
     */
    public static function table(Table $table): Table
    {
        return $table
            ->query(
                SangKien::query()
                    ->where('ma_tac_gia', Auth::id())
            )
            ->columns([
                TextColumn::make('ten_sang_kien')->label('Tên sáng kiến')->searchable()->sortable(),
                TextColumn::make('hien_trang')->label('Hiện trạng')->searchable()->sortable()->limit(50)->state(fn ($record) => strip_tags($record->hien_trang)),
                TextColumn::make('mo_ta')->label('Mô tả')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->state(fn ($record) => strip_tags($record->mo_ta)),
                TextColumn::make('ket_qua')->label('Kết quả')->searchable()->sortable()->limit(50)->state(fn ($record) => strip_tags($record->ket_qua)),
                TextColumn::make('user.name')->label('Tác giả')->searchable()->sortable(),

                TextColumn::make('taiLieuSangKien.file_path')
                    ->label('File đính kèm')
                    ->limit(20)
                    ->state(function ($record) {
                        // Check if relationship exists and has items
                        if (!$record->taiLieuSangKien || $record->taiLieuSangKien->isEmpty()) {
                            return 'Không có file nào được tải lên.';
                        }
                        // Return the array of file paths directly
                        return $record->taiLieuSangKien->pluck('file_path')->toArray();
                    })
                    ->listWithLineBreaks(),

                TextColumn::make('trangThaiSangKien.ten_trang_thai')
                    ->label('Trạng thái')
                    ->badge()
                    ->color(fn ($record) => match ($record->trangThaiSangKien->ma_trang_thai) {
                        'draft' => 'gray', // Neutral gray for drafts
                        'Pending' => 'amber', // Amber (yellow-orange) for pending actions
                        'Checking' => 'calm-blue', // Calm blue for checking
                        'Reviewing' => 'indigo', // Indigo for reviewing
                        'Scoring1' => 'lime', // Bright lime green for initial scoring
                        'Scoring2' => 'emerald', // Rich emerald green for secondary scoring
                        'Approved' => 'green', // Vibrant green for approved items
                        default => 'red', // Bold red for rejected or unknown states
                    }),
            ])
            ->filters([
                Filter::make('Search')
                    ->query(fn (Builder $query, $value) => $query->where('title', 'like', "%$value%")),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Chỉnh sửa')->visible(fn ($record) => $record->trangThaiSangKien->ma_trang_thai == 'draft'),
                Tables\Actions\DeleteAction::make()->label('Xóa'),
                Action::make('Download')
                    ->label('Tải xuống')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->visible(function ($record) {
                        // Check if the relationship exists and has items
                        return $record->taiLieuSangKien && $record->taiLieuSangKien->isNotEmpty();
                    })
                    ->action(function ($record) {
                        try {
                            // Check if relationship exists and has items
                            if (!$record->taiLieuSangKien || $record->taiLieuSangKien->isEmpty()) {
                                Notification::make()
                                    ->title('Không có tệp để tải xuống')
                                    ->warning()
                                    ->send();
                                return null;
                            }
                            // Create a new zip archive
                            $zip = new ZipArchive();
                            $zipName = 'innovation-files-' . $record->id . '.zip';
                            $zip->open(storage_path('app/public/' . $zipName), ZipArchive::CREATE | ZipArchive::OVERWRITE);
                            // Add each file to the archive
                            foreach ($record->taiLieuSangKien as $file) {
                                $zip->addFile(storage_path('app/public/' . $file->file_path), $file->file_path);
                            }
                            $zip->close();
                            // Return the zip file
                            Notification::make()
                                ->title('Tải xuống thành công')
                                ->success()
                                ->send();
                            // Return the zip file
                            return response()->download(storage_path('app/public/' . $zipName))->deleteFileAfterSend();
                        } catch (Exception $e) {
                            return Notification::make()
                                ->title('Tải xuống thất bại')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                Action::make('Submit')
                    ->label('Gửi duyệt')
                    ->icon('heroicon-o-check-circle')
                    ->visible(function ($record) {
                        return $record->trangThaiSangKien->ma_trang_thai == 'draft';
                    })
                    ->action(function ($record) {
                        //get id from trang_thai_sang_kien table where ma_trang_thai = 'Pending'
                        $trangThaiId = TrangThaiSangKien::query()->where('ma_trang_thai', 'Pending')->first()->id;
                        //update ma_trang_thai_sang_kien in sang_kien table
                        $record->update(['ma_trang_thai_sang_kien' => $trangThaiId]);
                        Notification::make()
                            ->title('Đã chuyển sáng kiến sang trạng thái chờ duyệt')
                            ->success()
                            ->send();
                    }),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label('Thêm mới sáng kiến'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
//            TaiLieuSangKienRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSangKiens::route('/'),
            'create' => Pages\CreateSangKien::route('/create'),
            'edit' => Pages\EditSangKien::route('/{record}/edit'),
        ];
    }
}
