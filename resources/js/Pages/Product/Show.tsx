import Carousel from "@/Components/core/Carousel";
import CurrencyFormatter from "@/Components/core/CurrencyFormatter";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Product, variationTypeOption } from "@/types";
import { arraysAreEqual } from "@/types/helpers";
import { Head, router, useForm, usePage } from "@inertiajs/react";
import React, { useEffect, useMemo, useState } from "react";

//
function Show({
    product, // The product object containing details about the product
    variationOptions, // Each Variation Type Options ex. Color, Size should have a corresponding id.
}: //ex. Option2 Blue= 2, Option1 Small=1
{
    product: Product;
    variationOptions: number[]; // declaring the type of the variationOptions that is should be a number.
}) {
    // Create a form object using the `useForm` hook.
    // This form tracks and manages the state of user input for product options, quantity, and price.
    const form = useForm<{
        options_ids: Record<string, number>; // Represents selected option IDs, where the key is the option type, and the value is the selected option ID.
        quantity: number; // declaring the types
        price: number | null;
    }>({
        options_ids: {}, // Initial state: No options selected.
        quantity: 1, // Initial state: Default quantity is set to 1.
        price: null, // Initial state: Price is not set (null).
    });

    const { url } = usePage(); // Destructure the current page's URL from the Inertia.js usePage hook.
    // State to store the selected options for the product variations.
    // Each key in the object represents a variation type ID, and the value is the selected variation option.
    const [selectedOptions, setSelectedOptions] = useState<
        Record<number, variationTypeOption> // The record maps variation type IDs (keys)
    >([]);
    // Rendering the images of the selected options
    const images = useMemo(() => {
        for (let typeId in selectedOptions) {
            const option = selectedOptions[typeId]; // Get the selected option for the current variation type.
            if (option.images.length > 0) {
                // Check if the option has associated images.
                return option.images; // Return the images for the selected option.
            }
            return product.images; // Fallback: use the product's default images.
        }
    }, [product, selectedOptions]);
    //
    const computedProduct = useMemo(() => {
        const selectedOptionIds = Object.values(selectedOptions) // Get all selected option IDs.
            .map((op) => op.id) // Extract the IDs of the selected options.
            .sort(); // Sort the IDs to ensure the order is consistent.
        for (let variation of product.variations) {
            const optionIds = variation.variation_type_option_ids.sort(); // Sort the variation's option IDs for comparison.

            // Check if the selected option IDs match the variation's option IDs.
            if (arraysAreEqual(selectedOptionIds, optionIds)) {
                return {
                    price: variation.price, // Return the variation's price.
                    quantity:
                        variation.quantity === null // If the variation's quantity is null (unlimited stock),
                            ? Number.MAX_VALUE // set the quantity to the maximum possible value.
                            : variation.quantity, // Otherwise, use the variation's defined quantity.
                };
            }
        }
        return {
            //
            price: product.price || 0, // Fallback to product price or 0
            quantity: 0, // Default to 0 if no matching variation is found
        };
    }, [product, selectedOptions]); // Recompute whenever the product or selected options change.
    // Effect to update the selected options based on the product variations
    useEffect(() => {
        // Loop through all variation types of the product (e.g., size, color).
        for (let type of product.variationTypes) {
            // Retrieve the selected option ID for the current variation type from the `variationOptions` object.
            const selectedOptionId: number = variationOptions[type.id];
            console.log(selectedOptionId, type.options); // Log the selected option ID and all available options for debugging.
            chooseOption(
                type.id, // Pass the current variation type ID (e.g., "size" or "color").
                // Find the option that matches the selected option ID.
                // If no match is found, default to the first option available.
                type.options.find((op) => op.id == selectedOptionId) ||
                    type.options[0],
                false // Pass `false` to indicate that this option was not chosen manually by the user.
            );
        }
    }, []); // The empty dependency array ensures this effect runs only once when the component mounts.
    // Function to convert an object of option IDs into a new object with specific key-value pairs.
    const getOptionIdsMap = (newOptions: object) => {
        // Convert the `newOptions` object into a new object with specific key-value pairs.
        return Object.fromEntries(
            // Take the entries (key-value pairs) of `newOptions` as an array of [key, value].
            Object.entries(newOptions).map(([a, b]) => [a, b.id]) // Map each key (`a`) to the `id` property of the corresponding value (`b`).
        );
    };

    // updates the selected options in the state and optionally updates the URL to reflect
    // the current selection.
    const chooseOption = (
        typeId: number, // The ID of the variation type being updated (e.g., color or size)
        option: variationTypeOption, // The selected option for this variation type
        updateRouter: boolean = true // Whether to update the router with the selected options (default is true)
    ) => {
        setSelectedOptions((prevSelectedOptions) => {
            // Update the state for selected options
            // Create a new object by copying the previous selected options and adding the new one
            const newOptions = { ...prevSelectedOptions, [typeId]: option };
            // If the updateRouter flag is true, update the URL parameters with the selected options
            if (updateRouter) {
                router.get(
                    url, // The current URL
                    {
                        options: getOptionIdsMap(newOptions), // Pass the selected options as a map of type IDs to option IDs
                    },
                    {
                        preserveScroll: true, // Keep the current scroll position on the page
                        preserveState: true, // Keep the current component state
                    }
                );
            }
            return newOptions;
        });
    };
    // updates the quantity value in the form's data whenever the user selects a new quantity
    // from a dropdown menu (<select> element).
    const onQuantityChange = (ev: React.ChangeEvent<HTMLSelectElement>) => {
        // Update the "quantity" field in the form's data using the selected value
        form.setData("quantity", parseInt(ev.target.value));
    };

    // Handles adding a product to the shopping cart by sending a POST request to the server.
    const addToCart = () => {
        // Sends a POST request to the "cart.store" route with the product's ID
        form.post(route("cart.store", product.id), {
            //
            preserveScroll: true, // Keeps the current scroll position on the page after the request
            preserveState: true, // Retains the current component state after the request
            onError: (err) => {
                // Callback function to handle errors
                console.log(err); // Logs any errors returned by the server
            },
        });
    };
    // Dynamically renders the available variation types for a product  (e.g., color, size, style)
    // and their corresponding options (e.g., blue, medium, modern design).
    const renderProductVariationTypes = () => {
        // Loops through all the variation types of the product
        return product.variationTypes.map((type) => (
            <div key={type.id}>
                {/* Displays the name of the variation type */}
                <b>{type.name}</b>
                {/* Render the Images */}
                {type.type === "Image" && (
                    <div className="flex gap-2 mb-4">
                        {type.options.map((option) => (
                            <div
                                // Sets the selected option when clicked
                                onClick={() => chooseOption(type.id, option)}
                                key={option.id}
                            >
                                {option.images && (
                                    <img
                                        src={option.images[0]?.thumb}
                                        alt=""
                                        className={
                                            "w-[50px], h-[50px] " +
                                            (selectedOptions[type.id]?.id ===
                                            option.id
                                                ? "outline outline-4 outline-primary"
                                                : "")
                                        }
                                    />
                                )}
                            </div>
                        ))}
                    </div>
                )}
                {/* Handles variations of type "Radio" */}
                {type.type === "Radio" && (
                    <div className="flex join mb-4">
                        {type.options.map((option) => (
                            <input
                                onChange={() => chooseOption(type.id, option)} // Sets the selected option when changed
                                key={option.id}
                                className="join-item btn"
                                type="radio"
                                value={option.id}
                                checked={
                                    // Checks if this option is currently selected
                                    selectedOptions[type.id]?.id === option.id
                                }
                                name={"variation_type_" + type.id} // Groups radio buttons by variation type
                                aria-label={option.name}
                            />
                        ))}
                    </div>
                )}
            </div>
        ));
    };

    // A button to add the selected quantity of the product to the shopping cart.
    const renderAddToCartButton = () => {
        return (
            <div className="mb-8 flex gap-4">
                {/* Dropdown menu to select the quantity of the product to add to the cart */}
                <select
                    value={form.data.quantity}
                    onChange={onQuantityChange}
                    className="select select-bordered w-full"
                >
                    {Array.from({
                        length: Math.min(10, computedProduct.quantity), // Creates an array for quantities (up to 10 or available stock)
                    }).map((_, i) => (
                        <option key={i + 1} value={i + 1}>
                            Quantity: {i + 1}{" "}
                            {/* Displays the quantity (1 to 10 or stock limit) */}
                        </option>
                    ))}
                </select>
                {/* Button to add the selected product and quantity to the cart */}
                <button onClick={addToCart} className="btn btn-primary">
                    Add to Cart
                </button>
            </div>
        );
    };

    //  creates a mapping of selected product variation types (e.g., size, color) to their
    // corresponding selected option IDs (e.g., "Small," "Red").
    // updates the form data (form.setData)
    useEffect(() => {
        // Create a mapping of variation type IDs to their selected option IDs
        const idsMap = Object.fromEntries(
            Object.entries(selectedOptions).map(
                ([typeId, option]: [string, variationTypeOption]) => [
                    typeId, // The key is the variation type ID (e.g., color, size)
                    option.id, // The value is the selected option's ID for that variation type
                ]
            )
        );
        console.log(idsMap); // Log the mapping for debugging purposes
        form.setData("options_ids", idsMap); // Update the form data with the newly created mapping of selected options
    }, [selectedOptions]); // Re-run the effect whenever 'selectedOptions' changes

    // Renders the main content of the component
    return (
        <AuthenticatedLayout>
            <Head title={product.title} />

            <div className="container mx-auto p-8">
                <div className="grid gap-8 grid-cols-1 lg:grid-cols-12">
                    {/* Image Carousel */}
                    <div className="col-span-1 lg:col-span-7">
                        <Carousel images={images ?? []} />
                    </div>

                    {/* Product Details */}
                    <div className="col-span-1 lg:col-span-5">
                        <h1 className="text-3xl font-bold mb-4 text-gray-900">
                            {product.title}
                        </h1>

                        {/* Price Section */}
                        <div className="text-4xl font-semibold text-green-600 mb-4">
                            <CurrencyFormatter amount={computedProduct.price} />
                        </div>

                        {/* Product Variations */}
                        <div className="my-4">
                            {renderProductVariationTypes()}
                        </div>

                        {/* Stock Availability */}
                        {computedProduct.quantity != undefined &&
                            computedProduct.quantity < 10 && (
                                <div className="text-red-600 my-4">
                                    <span>
                                        Only {computedProduct.quantity} left
                                    </span>
                                </div>
                            )}

                        {/* Add to Cart Button */}
                        <div className="my-4">{renderAddToCartButton()}</div>

                        {/* About the Item */}
                        <div className="mt-8">
                            <b className="text-xl font-semibold">
                                About the Item
                            </b>
                            <div
                                className="ck-content-output" // declared in resources/css/app.css
                                dangerouslySetInnerHTML={{
                                    __html: product.description, // get description from the database
                                }}
                            ></div>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}

export default Show;
