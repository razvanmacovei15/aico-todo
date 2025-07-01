<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Basic Information')
                    ->description('User account details')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label('Full Name')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Enter full name'),

                                TextInput::make('email')
                                    ->label('Email Address')
                                    ->email()
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255)
                                    ->placeholder('Enter email address'),
                            ]),
                    ]),

                Section::make('Security')
                    ->description('Password and verification settings')
                    ->schema([
                        TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->dehydrated(fn ($state): bool => filled($state))
                            ->required(fn (string $context): bool => $context === 'create')
                            ->minLength(8)
                            ->confirmed()
                            ->placeholder('Enter password'),

                        TextInput::make('password_confirmation')
                            ->label('Confirm Password')
                            ->password()
                            ->dehydrated(false)
                            ->required(fn (string $context): bool => $context === 'create')
                            ->minLength(8)
                            ->placeholder('Confirm password'),
                    ]),

            Section::make('User Tasks')
                ->description('Tasks assigned to this user')
                ->schema([
                    Forms\Components\Placeholder::make('tasks_table')
                        ->content(function ($record) {
                            if (!$record || !$record->exists) {
                                return 'Save the user first to view assigned tasks.';
                            }
                            
                            $tasks = $record->tasks()->with('users')->get();
                            
                            if ($tasks->isEmpty()) {
                                return 'No tasks assigned to this user.';
                            }
                            
                            $html = '<div class="overflow-x-auto"><table class="min-w-full divide-y divide-gray-200">';
                            $html .= '<thead class="bg-gray-50"><tr>';
                            $html .= '<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>';
                            $html .= '<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>';
                            $html .= '<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>';
                            $html .= '<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>';
                            $html .= '<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>';
                            $html .= '</tr></thead>';
                            $html .= '<tbody class="bg-white divide-y divide-gray-200">';
                            
                            foreach ($tasks as $task) {
                                $html .= '<tr>';
                                $html .= '<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">' . e($task->title) . '</td>';
                                $html .= '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' . e($task->description) . '</td>';
                                $html .= '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' . e($task->due_date) . '</td>';
                                $html .= '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' . strtoupper(e($task->priority)) . '</td>';
                                $html .= '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' . ucfirst(e($task->status)) . '</td>';
                                $html .= '</tr>';
                            }
                            
                            $html .= '</tbody></table></div>';
                            
                            return new \Illuminate\Support\HtmlString($html);
                        })
                        ->columnSpanFull(),
                ])
                ->collapsible()
                ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('email'),
                Tables\Columns\TextColumn::make('created_at'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
