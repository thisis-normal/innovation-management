<?php

namespace App\Filament\User\Resources\ThamDinhResource\Pages;

use App\Filament\User\Resources\ThamDinhResource;
use Filament\Resources\Pages\ListRecords;

class ListThamDinh extends ListRecords
{
    protected static string $resource = ThamDinhResource::class;
    protected static ?string $title = 'Thẩm định sáng kiến';
}
