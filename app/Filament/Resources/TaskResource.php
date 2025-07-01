<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TaskResource\Pages;
use App\Models\Task;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                ->required(),
                Forms\Components\TextInput::make('description')
                ->required(),
                Forms\Components\DatePicker::make('due_date')
                ->required(),

                Forms\Components\Select::make('users')
                    ->label('Assign Users')
                    ->multiple()
                    ->relationship('users', 'name')
                    ->required(),


                Forms\Components\Select::make('priority')
                    ->options([
                        'low' => 'LOW',
                        'medium' => 'MEDIUM',
                        'high' => 'HIGH',
                    ])->required(),
                Forms\Components\Select::make('status')
                ->options([
                    'pending' => 'Pending',
                    'completed' => 'Completed',
                    'cancelled' => 'Cancelled',
                ])

            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('users');
    }



    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable(),
                Tables\Columns\TextColumn::make('description'),
                Tables\Columns\TextColumn::make('created_at')->sortable(),
                Tables\Columns\TextColumn::make('due_date')->sortable(),

                Tables\Columns\TextColumn::make('users')
                    ->label('Assigned Users')
                    ->formatStateUsing(function ($state, $record) {
                        // $record is the Task model with loaded users relation
                        if ($record->relationLoaded('users')) {
                            return $record->users->pluck('name')->join(', ');
                        }
                        return '';
                    })
                    ->sortable(false),





                Tables\Columns\SelectColumn::make('priority')
                    ->options([
                        'low' => 'LOW',
                        'medium' => 'MEDIUM',
                        'high' => 'HIGH',
                    ]),
                Tables\Columns\SelectColumn::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),
                Tables\Filters\SelectFilter::make('priority')
                    ->options([
                        'low' => 'LOW',
                        'medium' => 'MEDIUM',
                        'high' => 'HIGH',
                    ]),
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
            'index' => Pages\ListTasks::route('/'),
            'create' => Pages\CreateTask::route('/create'),
            'edit' => Pages\EditTask::route('/{record}/edit'),
        ];
    }
}
