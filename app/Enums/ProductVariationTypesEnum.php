<?php

namespace App\Enums;

enum ProductVariationTypesEnum: string
{
    // Define an enum with three cases for input types: Select, Radio, and Image
    case Select = "Select"; // Represents a dropdown selection option
    case Radio = "Radio"; // Represents a radio button option
    case Image = "Image"; // Represents an image selection option

    public static function labels(): array
    {
        return [
            self::Select->value => __('Select'), // Label for Select
            self::Radio->value => __('Radio'), // Label for Radio
            self::Image->value => __('Image'), // Label for Image
        ];
    }
}
