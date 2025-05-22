<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class VersionWidget extends Widget
{
    protected static ?int $sort = 2;

    protected static bool $isLazy = false;

    protected static string $view = 'filament.widgets.version-widget';
}
