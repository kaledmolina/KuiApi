<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserProgressResource\Pages;
use App\Filament\Resources\UserProgressResource\RelationManagers;
use App\Models\UserProgress;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserProgressResource extends Resource
{
    protected static ?string $model = UserProgress::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationGroup = 'Gestión';
    protected static ?string $modelLabel = 'Progreso de Usuario';
    protected static ?string $pluralModelLabel = 'Progreso de Usuarios';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Forms\Components\Select::make('level_id')
                    ->relationship('level', 'name')
                    ->required(),
                Forms\Components\TextInput::make('stars')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('score')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\Toggle::make('is_completed')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Usuario')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('level.name')
                    ->label('Nivel')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('stars')
                    ->label('Estrellas')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('score')
                    ->label('Puntaje')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_completed')
                    ->label('Completado')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Fecha Actualizado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUserProgress::route('/'),
            'create' => Pages\CreateUserProgress::route('/create'),
            'edit' => Pages\EditUserProgress::route('/{record}/edit'),
        ];
    }
}
