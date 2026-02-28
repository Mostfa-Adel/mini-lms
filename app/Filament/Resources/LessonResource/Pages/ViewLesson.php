<?php

namespace App\Filament\Resources\LessonResource\Pages;

use App\Filament\Resources\LessonResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewLesson extends ViewRecord
{
    protected static string $resource = LessonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make()
                ->modalHeading('Delete lesson')
                ->modalDescription('Progress records for this lesson will also be removed. Certificates are not affected.'),
        ];
    }
}
