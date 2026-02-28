<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CertificateResource\Pages;
use App\Filament\Resources\CertificateResource\RelationManagers;
use App\Models\Certificate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CertificateResource extends Resource
{
    protected static ?string $model = Certificate::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-check';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('uuid')->disabled(),
                Forms\Components\TextInput::make('user.name')->disabled(),
                Forms\Components\TextInput::make('course.title')->disabled(),
                Forms\Components\DateTimePicker::make('issued_at')->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('uuid')->searchable(),
                Tables\Columns\TextColumn::make('user.name')->sortable(),
                Tables\Columns\TextColumn::make('course.title')->sortable(),
                Tables\Columns\TextColumn::make('issued_at')->dateTime()->sortable(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCertificates::route('/'),
            'view' => Pages\ViewCertificate::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
