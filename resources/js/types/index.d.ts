import { Config } from "ziggy-js";

// blueprint for a User. It consists what properties the object should have and what types
// those properties should be.
// the rest works the same

export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at?: string;
}

// Define the Image type, which represents the structure of an image associated with a product or option
export type Image = {
    id: number; // Unique identifier for the image
    thumb: string; // URL for the thumbnail-sized version of the image
    small: string; // URL for the small-sized version of the image
    large: string; // URL for the large-sized version of the image
};

export type variationTypeOption = {
    // (ex. "Red", "Blue", "Small", "Large")
    id: number;
    name: string;
    images: Image[];
    type: VariationType;
};

// Define the VariationType type, which represents the structure of a product variation type (ex. Color, Size)
export type VariationType = {
    id: number; // Unique identifier for the variation type
    name: string; // Name of the variation type (e.g., "Color", "Size")
    type: "Select" | "Radio" | "Image"; // Input type for selecting variations (dropdown, radio button, or image)
    options: variationTypeOption[]; // List of available options for this variation type
};

// Define the Product type which represents the structure of a product object
export type Product = {
    id: string; // Unique identifier for the product
    title: string; // Product name/title
    slug: string; // URL-friendly identifier for the product
    price: number; // Default price of the product
    quantity: number; // Default stock quantity of the product
    image: string; // URL for the main product image
    images: Image[]; // Array of additional product images
    short_description: string; // Brief summary of the product
    description: string; // Detailed description of the product
    user: {
        id: number; // ID of the user who created/owns the product
        name: string; // Name of the user
    };
    department: {
        id: number; // ID of the department/category the product belongs to
        name: string; // Name of the department/category
    };
    variationTypes: VariationType[]; // List of possible variation types (e.g., size, color)
    variations: Array<{
        id: number; // Unique ID for a specific product variation
        variation_type_option_ids: number[]; // Array of selected option IDs for this variation
        quantity: number; // Stock quantity of this specific variation
        price: number; // Price of this specific variation
    }>;
};

// Define a generic type `PaginationProps` that accepts a type parameter `T`.
export type PaginationProps<T> = {
    data: Array<T>; // <T> represents a generic type parameter. Generics allow this type to be flexible and reusable with different data types.
};

// Standardize the required structure of data passed to components or pages in a web application.
export type PageProps<
    T extends Record<string, unknown> = Record<string, unknown>
> = T & {
    auth: {
        user: User; // The currently authenticated user, represented by a `User` type.
    };
    ziggy: Config & { location: string }; // Ex. "location": "Home Page"
};
