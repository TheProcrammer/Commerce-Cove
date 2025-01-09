<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use App\Enums\ProductVariationTypesEnum;

class ProductVariationType extends EditRecord
{
    // Link this page to the ProductResource.
    protected static string $resource = ProductResource::class;

    protected static ?string $title = "Variation Types"; // Sets the title of the page.
    protected static ?string $navigationIcon = "heroicon-o-queue-list"; // Icon

    /**
     * Define the form schema for product images.
     *
     * Ensure this page is registered in the parent ProductResource
     * under the getPages() method and getRecordSubNavigation().
     */
    public function form(Form $form): Form
    {
        //
        return $form
            ->schema([
                Repeater::make("variationTypes") // Creates a repeater field named 'variation_types' to handle multiple entries and it reflects as its label.
                        ->label(false) // Hides the label for the repeater field.
                        ->relationship() // Establishes a relationship between this repeater and the associated model.
                        ->collapsible() // Allows the repeater items to be collapsible for better UI organization.
                        ->defaultItems(1) // Sets the default number of items in the repeater to 1.
                        ->addActionLabel('Add New Variation Type') // Sets the label for adding new items in the repeater.
                        ->columns(2) // Displays the repeater fields in 2 columns.
                        ->columnSpan(2) // Makes the repeater span across 2 columns in the layout.
                        ->schema([ // Defines the schema for fields inside the repeater.
                                TextInput::make('name') // Adds a text input field named 'name'.
                                    ->required(),
                                Select::make('type') // Adds a dropdown (select) field named 'type'.
                                    ->options(ProductVariationTypesEnum::labels()) // Populates dropdown options using labels from the enum class.
                                    ->required(),
                                // Creates a nested repeater field named 'options'.
                                Repeater::make('options') //options defined on the VariationType model
                                    ->relationship() // Establishes a relationship for the 'options' nested repeater.
                                    ->collapsible()
                                    ->schema([
                                        TextInput::make('name') // Adds a text input field named 'name' inside 'options'.
                                            ->required()
                                            ->columnSpan(2), // How much space it takes in the layout in column.

                                        //Since this repeater is using images it should also implement HasMediaLibrary on the model.
                                        //VariationTypeOption
                                        SpatieMediaLibraryFileUpload::make('images') // Adds a file upload field for uploading images.
                                            ->image() // Restricts file uploads to image files.
                                            ->multiple() // Allows uploading multiple images.
                                            ->openable() // Enables the option to open uploaded files for preview.
                                            ->panelLayout('grid') // Displays uploaded files in a grid layout.
                                            ->collection('images') // Saves the uploaded files in a collection named 'images'.
                                            ->reorderable() // Allows reordering of uploaded files.
                                            ->appendFiles() // Enables appending additional files instead of replacing existing ones.
                                            ->preserveFilenames() // Preserves the original filenames of uploaded files.
                                            ->columnSpan(3) // Makes this field span 3 columns in the layout.
                                    ])
                                    ->columnSpan(2) // How much space it takes in the layout in column.
                            ])
            ]);
    }

    /**
     * Define header actions like delete.
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(), // Adds a delete button in the header
        ];
    }
}
