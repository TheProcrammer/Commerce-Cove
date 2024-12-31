<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Illuminate\database\Eloquent\Builder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Forms\Components\Select;
use App\Enums\Enum\ProductStatusEnum;
use Filament\Facades\Filament;
use App\Enums\RolesEnum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Forms\Components\Grid::make()
                ->schema([
                    //
                    TextInput::make('title')
                    ->live(true)
                    ->required()
                    ->afterStateUpdated(
                        function (string $operation, $state, callable $set) {
                            $set("slug", Str::slug($state));
                    }
                ),
                //
                    TextInput::make('slug')
                        ->required(),
                    Select::make('department_id')
                        ->relationship('department', 'name')
                        ->label(__('Department'))
                        ->preload()
                        ->searchable()
                        ->required()
                        ->reactive() //Makes field reactive to changes.
                        ->afterStateUpdated(function (callable $set) {
                            $set('category_id', null);
                        }),
                        Select::make('category_id')
                        ->relationship('category', 
                            'name',
                            //Modify the query to filter categories based on the selected department.
                            function (Builder $query, callable $get) {
                            $departmentId = $get('department_id'); // Get the selected department ID.
                            if ($departmentId){
                                //Filters categories based on the selected department ID.
                                $query->where('department_id', $departmentId);
                            }
                        })
                        ->label(__('Category'))
                        ->preload()
                        ->searchable()
                        ->required(),
                    ]),
                    //
                    Forms\Components\RichEditor::make('description')
                        ->required()
                        ->toolbarButtons([
                            'blockquote',
                            'bold',
                            'bulletList',
                            'h2',
                            'h3',
                            'italic',
                            'link',
                            'orderedList',
                            'redo',
                            'strike',
                            'table',
                            'underline',
                            'undo',
                            'unorderedList'
                        ])    
                    ->columnSpan(2),
                    //
                    TextInput::make('price')
                        ->numeric()
                        ->required(),
                    TextInput::make('quantity')
                        ->integer(),
                    Select::make('status')
                        ->options(ProductStatusEnum::labels())
                        ->default(ProductStatusEnum::Draft->value)
                        ->required(),                  
            ]);
            
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('title')
                    ->sortable()
                    ->words(10)
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->colors(ProductStatusEnum::colors()),
                TextColumn::make('department.name'), //department name
                TextColumn::make('category.name'), //category name
                TextColumn::make('created_at')
                    ->dateTime()

            ])
            ->filters([
                //
                SelectFilter::make('status')
                    ->options(ProductStatusEnum::labels()),
                SelectFilter::make('department_id')
                    ->relationship('department', 'name')

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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }

    //Vendor is the only access can view this section.
    Public static function canViewAny(): bool
    {
        $user = Filament::auth()->user();
        return $user && $user->hasRole(RolesEnum::Vendor);
    }
}
