import { Config } from "ziggy-js";

// blueprint for a User. It consists what properties the object should have and what types
// those properties should be.
// the rest works the same

export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at?: string;
    stripe_account_active: boolean;
    vendor: {
        status: string;
        status_label: string;
        store_name: string;
        store_address: string;
        cover_image: string;
    };
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
    meta_title: string; // Meta title for the product page
    meta_description: string; // Meta description for the product page
    user: {
        id: number; // ID of the user who created/owns the product
        name: string; // Name of the user
        store_name: string; // Name of the user's store
    };
    department: {
        id: number; // ID of the department/category the product belongs to
        name: string; // Name of the department/category
        slug: string;
    };
    variationTypes: VariationType[]; // List of possible variation types (e.g., size, color)
    variations: Array<{
        id: number; // Unique ID for a specific product variation
        variation_type_option_ids: number[]; // Array of selected option IDs for this variation
        quantity: number; // Stock quantity of this specific variation
        price: number; // Price of this specific variation
    }>;
};

// Represents a cart item in an e-commerce system. It defines the structure of a cart
// item object to ensure type safety
export type CartItem = {
    id: number;
    product_id: number;
    title: string; // The name of the product.
    slug: string; // A URL-friendly identifier for the product
    quantity: number;
    price: number;
    image: string;
    option_ids: Record<string, number>; // A key-value pair object where: Example { "color": 2, "size": 5 }.
    options: VariationTypeOption[]; // Extract variation type options
};

// Represents a group of cart items associated with a specific user.
export type GroupedCartItems = {
    user: User; // User who owns the cart. Contains user details.
    items: CartItem[]; // Array of cart items belonging to this user.
    totalPrice: number;
    totalQuantity: number;
};

// Define a generic type `PaginationProps` that accepts a type parameter `T`.
export type PaginationProps<T> = {
    data: Array<T>; // <T> represents a generic type parameter. Generics allow this type to be flexible and reusable with different data types.
};

// Standardize the required structure of data passed to components or pages in a web application.
export type PageProps<
    T extends Record<string, unknown> = Record<string, unknown>
> = T & {
    appName: string;
    csrf_token: string;
    error: string;
    success: {
        message: string;
        time: number;
    };
    auth: {
        user: User; // The currently authenticated user, represented by a `User` type.
    };
    ziggy: Config & { location: string }; // Ex. "location": "Home Page"
    totalQuantity: number;
    totalPrice: number;
    miniCartItems: CartItem[];
    departments: Department[];
    keyword: string;
};

// Represents an individual item within an order.
export type OrderItem = {
    id: number; // Unique identifier for the order item.
    quantity: number;
    price: number;
    variation_type_option_ids: number[]; // Array of variation option IDs (e.g., size, color) selected for this item.
    product: {
        id: number;
        title: string;
        slug: string;
        description: string;
        image: string;
    };
};

// Represents an order in an e-commerce system.
export type Order = {
    id: number;
    total_price: number;
    status: string; // Current status of the order (e.g., "Pending," "Paid," "Shipped").
    created_at: string; // Timestamp when the order was placed.
    vendorUser: {
        // Vendor details associated with the order
        id: number;
        name: string;
        email: string;
        store_name: string;
        store_address: string;
    };
    orderItems: OrderItem[]; // Array of items included in this order
};

export type Vendor = {
    id: number;
    store_name: string;
    store_address: string;
};

export type Category = {
    id: number;
    name: string;
};

export type Department = {
    id: number;
    name: string;
    slug: string;
    meta_title: string;
    meta_description: string;
    categories: Category[];
};
