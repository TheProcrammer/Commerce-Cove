<?php

namespace App\Filament\Resources\DepartmentResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\Category;
use Faker\Provider\ar_EG\Text;
use Filament\Forms\Components\Checkbox;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CategoriesRelationManager extends RelationManager
{
    protected static string $relationship = 'categories';

    public function form(Form $form): Form
    {
        //Declaring the department variable to get its record from the database.
        $department = $this->getOwnerRecord();

        //Department/edit Create Category form
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                
                //Creates dropdown list for the parent_id on the database.
                Select::make('parent_id')
                //fetch the data from $department variable which is from the database.
                    ->options(function () use ($department) {
                        //queries the Category model to get all categories that belong to 
                        //the specified department
                        return Category::query()
                            ->where('department_id', $department->id)
                            //retrieves the name and id of each category.
                            ->pluck('name', 'id')
                            //converts the result into an array format suitable for the 
                            //dropdown options.
                            ->toArray();
                    })
                    //label for the dropdown.
                    ->label('Parent Category')
                    //preloads the data from the database. making them available 
                    //immediately when the form loads
                    ->preload()
                    ->searchable(),
                    //Creates a checkbox field named active
                    Checkbox::make('active')
            ]);
    }

    public function table(Table $table): Table
    {
        //Department/edit Categories form.
        //Shows the details of the Categories.
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                //Creating additional columns
                TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                //For the second column. Declared in category model.
                TextColumn::make('parent.name')
                    ->sortable()
                    ->searchable(),
                //To make the active or not active icon to appear
                IconColumn::make('active')
                    ->boolean()
            ])

            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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
}
