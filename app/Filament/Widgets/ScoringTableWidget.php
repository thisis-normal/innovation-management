<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class ScoringTableWidget extends Widget
{
    protected static string $view = 'filament.widgets.scoring-table-widget';

    protected int | string | array $columnSpan = 'full';
}
