<?php

namespace App\Filament\Resources;

use App\Enums\RolesEnum;
use App\Filament\Resources\DepartmentResource\Pages;
use App\Filament\Resources\DepartmentResource\RelationManagers;
use App\Models\Department;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use \Filament\Facades\Filament;


class DepartmentResource extends Resource
{
    protected static ?string $model = Department::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack'; //You can change the Icon here
    //You can visit heroicons.com for more icons. 
    //when changing icons. heroicon- is a fix value, o means outline. s means solid. m means mini and
    //then the name of the icon.

    public static function form(Form $form): Form
    {
        return $form
        //Creating a fields into the /admin/departments/create
        //You can generate this by clicking Department > New Department
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->live(true)
                    ->afterStateUpdated(function (string $operation, $state, callable $set) {
                        $set('slug', Str::slug($state));
                    }),
                TextInput::make('slug')
                    ->required(),
                Checkbox::make('active')
            ]);
    }

    public static function table(Table $table): Table
    {
        //Adding a column to the /admin/departments making it searchable and sortable.
        return $table
            ->columns([
                TextColumn::make('name')
                ->searchable()
                ->sortable()
            ])
            ->defaultSort('cretated_at', 'asc')
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
        //Referencing the Category Relation Manager from the Filament to be able to establish relationship.
        //You can add components inside the CatagoriesRelationManager.
        return [
            RelationManagers\CategoriesRelationManager::class
        ];
    }

    // Pages you can add and access.
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDepartments::route('/'),
            'create' => Pages\CreateDepartment::route('/create'),
            'edit' => Pages\EditDepartment::route('/{record}/edit'),
        ];
    }

    //Only the admin can access all.
    Public static function canViewAny(): bool
    {
        $user = Filament::auth()->user();
        return $user && $user->hasRole(RolesEnum::Admin);
    }
}
