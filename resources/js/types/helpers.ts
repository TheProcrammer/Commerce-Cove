import { CartItem } from ".";

// Checks whether two arrays (arr1 and arr2) are equal.
export const arraysAreEqual = (arr1: any[], arr2: any[]) => {
    // If the arrays have different lengths, they are not equal
    if (arr1.length !== arr2.length) return false;
    // Check if each element in arr1 matches the corresponding element in arr2
    return arr1.every((value, index) => value === arr2[index]);
};

// Generates a URL for a product page with selected options in the query parameters.
export const productRoute = (item: CartItem) => {
    // Takes a CartItem and returns a product URL with options.
    const params = new URLSearchParams(); // Creates an object to store URL query parameters.
    // Loops through each option in the item.
    Object.entries(item.option_ids).forEach(([typeId, optionId]) => {
        params.append(`options[${typeId}]`, optionId + ""); // Adds each option as a query parameter in the format: options[typeId]=optionId.
    });
    return route("product.show", item.slug + "?" + params.toString()); // Returns the product URL with the options added as query parameters.
};
