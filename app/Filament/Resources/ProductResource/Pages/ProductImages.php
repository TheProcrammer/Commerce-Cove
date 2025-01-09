<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Form;

class ProductImages extends EditRecord
{
    // Link this page to the ProductResource.
    protected static string $resource = ProductResource::class;
    protected static ?string $title = "Product Images"; // Sets the title of the page.

    protected static ?string $navigationIcon = "heroicon-o-photo"; // Icon

    /**
     * Define the form schema for product images.
     *
     * Ensure this page is registered in the parent ProductResource
     * under the getPages() method and getRecordSubNavigation().
     */
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                SpatieMediaLibraryFileUpload::make('images') // images is the label. 
                    ->label(false) // Hides the label.
                    ->image() // Allow only image uploads
                    ->multiple() // Enable multiple file uploads
                    ->openable() // Allow image preview
                    ->panelLayout('grid') // Display in grid layout
                    ->collection('images') // Use the 'images' collection
                    ->reorderable() // Enable drag-and-drop reordering
                    ->appendFiles() // Append new files without replacing
                    ->preserveFilenames() // Keep original filenames
                    ->columnSpan(2), // Span 2 columns in the form layout
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
