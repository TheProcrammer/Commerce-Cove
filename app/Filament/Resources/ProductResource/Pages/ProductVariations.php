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
use Faker\Provider\ar_EG\Text;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Model;

class ProductVariations extends EditRecord
{
    // Link this page to the ProductResource.
    protected static string $resource = ProductResource::class;

    protected static ?string $title = "Product Variations"; // Sets the title of the page.
    protected static ?string $navigationIcon = "heroicon-o-clipboard-document-list"; // Icon

    /**
     * Define the form schema for product images.
     *
     * Ensure this page is registered in the parent ProductResource
     * under the getPages() method and getRecordSubNavigation().
     */
    public function form(Form $form): Form
    {
        $types = $this->record->variationTypes;
        $fields = [];

        foreach ($types as $type) {
            $fields[] = TextInput::make('variation_type_' . ($type->id) . '.id')
                ->hidden();
            $fields[] = TextInput::make('variation_type_' . ($type->id) . '.name')
                ->label($type->name);
        }

        return $form
            ->schema([
                Repeater::make('variations')
                    ->label(false)
                    ->collapsible()
                    ->addable(false)
                    ->defaultItems(1)
                    ->schema([
                        Section::make()
                            ->schema($fields)
                            ->columns(3),
                        TextInput::make('quantity')
                            ->label('Quantity')
                            ->numeric(),
                        TextInput::make('price')
                            ->label('Price')
                            ->numeric(),
                    ])
                    ->columns(2)
                    ->columnspan(2)
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

    // This function modifies the form data before it is filled with values from the database or model.
    protected function mutateFormDataBeforeFill(array $data): array
    {
        //variations came from the model. Product model.
        $variations = $this->record->variations->toArray(); // Retrieves the variations associated with the current record and converts them to an array.

        // Merges the variation types of the current record with the existing variations.
        // The 'mergeCartesianWithExisting' function handles combining the variations with their types.
        $data["variations"] = $this->mergeCartesianWithExisting($this->record->variationTypes, $variations); 
        return $data; // Returns the modified data array, now including the processed variations, for further processing.
    }

    // This function generates all possible combinations of product variations based on their types 
    //(e.g., Size, Color) and options (e.g., Small, Medium, Red, Blue).
    private function cartesianProduct($variationTypes,$defaultQuantity = null, $defaultPrice = null): array
    {
        $result = [[]]; // Initialize the result array with an empty combination to start building variations.
        // Loop through each variation type (e.g., Size, Color)
        foreach ($variationTypes as $index => $variationType) { // variationTypes was get from the model. VariationType
            $temp = []; // Temporary array to store combinations for the current variation type.
            // Loop through each option of the current variation type (e.g., Small, Medium for Size)
            foreach ($variationType->options as $option) { // option was get from the model. VariationType
                // Loop through existing combinations in the result array.
                foreach ($result as $combination) {
                    // Create a new combination by adding the current option details.
                    $newCombination = $combination + [
                        'variation_type_' . ($variationType->id) => [
                            'id'=>$option->id,
                            'name'=>$option->name,
                            'label'=>$variationType->name,
                        ]
                    ];
                    $temp[] = $newCombination; // Add the new combination to the temporary array
                }
            }
            $result = $temp; // Update the result array with new combinations for the next iteration
        }
        // Add default quantity and price for each combination if it matches all variation types.
            foreach ($result as $combination) {
                if (count($combination) === count($variationTypes)){ // Ensure combination includes all variation types
                    $combination["quantity"] = $defaultQuantity; // Add default quantity
                    $combination["price"] = $defaultPrice; // Add default price
                }
            }
        return $result; // Return the final list of all combinations with variations, quantity, and price
    }

    // Merges existing variation data with price and quantity.
    private function mergeCartesianWithExisting($variationTypes, $existingData): array
    {
        // Set default values for quantity and price based on the main record.
        $defaultQuantity = $this->record->quantity;
        $defaultPrice = $this->record->price;
        // Generate all possible combinations (Cartesian Product) using the given variation types.
        $cartesianProduct = $this->cartesianProduct($variationTypes, $defaultQuantity, $defaultPrice);
        $mergedResult = []; // Initialize an empty array to store merged results.
        // Loop through each generated combination.
        foreach ($cartesianProduct as $product) {
            // Extract the IDs of all variation options (e.g., Size -> Small, Color -> Red).
            $optionIds = collect($product)
                    ->filter(fn($value, $key) => str_starts_with($key,"variation_type_")) // Filter variation keys only.
                    ->map(fn($option) => $option["id"]) // Get the ID of each option.
                    ->values() // Collect the IDs into an array.
                    ->toArray(); // Convert to a simple array.

            // Check if the combination already exists in the existing data.
            $match = array_filter($existingData, function ($existingOption) use ($optionIds) {
                return $existingOption["variation_type_option_ids"] === $optionIds;
        });

        // If a match is found in existing data, use its quantity and price.
        if (!empty($match)) {
            $existingEntry = reset($match);
            $product['id'] = $existingEntry['id']; // 
            $product ['quantity'] = $existingEntry["quantity"];
            $product ["price"] = $existingEntry["price"];

        // If no match is found, assign default quantity and price.
        } else {
            $product ["quantity"] = $defaultQuantity;
            $product ["price"] = $defaultPrice;
        }
            $mergedResult [] = $product; // Add the updated product data to the merged result.
        }
        return $mergedResult; // Return the final merged list of products.
    }

    //
    protected function mutateFormDataBeforeSave(array $data):array
    {
        $formattedData = [];
        foreach ($data['variations'] as $option) {
            $variationTypeOptionIds = [];
            foreach ($this->record->variationTypes as $i => $variationType) {
                $variationTypeOptionIds[] = $option['variation_type_' . ($variationType->id)]['id'];
            }
            
            $quantity = $option['quantity'];
            $price = $option['price'];

            $formattedData[] = [
                'id' => $option['id'],
                'variation_type_option_ids' => $variationTypeOptionIds, //
                'quantity' => $quantity,
                'price'=> $price,
            ];
        }
        $data['variations'] = $formattedData;
        return $data;
    }

    //
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $variations = $data['variations'];
        unset($data['variations']);

        // Convert variation_type_option_ids to JSON
        $variations = array_map(function ($variation) {
        $variation['variation_type_option_ids'] = json_encode($variation['variation_type_option_ids']); // Convert to JSON
        return $variation;
    }, $variations);

        // Perform upsert
        $record->variations()->upsert(
        $variations,
        ['id'], // Match existing records by ID
        ['variation_type_option_ids', 'quantity', 'price'] // Fields to update
        );

        // return parent::handleRecordUpdate($record, $data);
        return $record;
    }

    // You can also use this. 
    // protected function handleRecordUpdate(Model $record, array $data): Model
    // {
    // $variations = $data['variations'];
    // unset($data['variations']); // Remove variations from main data to handle separately

    // foreach ($variations as $variation) {
    //     // Check if variation has an ID (existing record)
    //         if (!empty($variation['id'])) {
    //         // Update the existing variation
    //         $record->variations()->where('id', $variation['id'])->update($variation);
    //         } else {
    //         // Insert new variation if ID is not provided
    //         $record->variations()->create($variation);
    //         }
    //     }

    // // return parent::handleRecordUpdate($record, $data);
    // return $record;
    // }

}