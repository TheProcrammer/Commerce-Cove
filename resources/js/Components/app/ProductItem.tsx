import { Product } from "@/types";
import { Link, useForm } from "@inertiajs/react";
import React from "react";
import CurrencyFormatter from "../core/CurrencyFormatter";

//ProductItem component is designed to display a product in a visually appealing card layout.
function ProductItem({ product }: { product: Product }): any {
    // Creates a form with default values. It keeps track of selected options (options_ids) and a quantity value.
    const form = useForm<{
        options_ids: Record<string, number>;
        quantity: number;
    }>({
        options_ids: {}, // 'options_ids' is an empty object for storing selected option IDs as numbers.
        quantity: 1, // 'quantity' starts at 1.
    });

    // adds a product to the cart by sending a request.
    const addToCart = () => {
        // Sends a request to add the product to the cart.
        form.post(route("cart.store", product.id), {
            preserveScroll: true, // Keeps the page scroll and state unchanged after submission.
            preserveState: true,
            onError: (err) => {
                console.log(err); // Logs errors if the request fails.
            },
        });
    };

    return (
        <div className="card bg-base-100 shadow-xl">
            <Link href={`/product/${product.slug}`}>
                <figure>
                    <img
                        src={product.image}
                        alt={product.title}
                        className="aspect-square object-cover"
                    />
                </figure>
            </Link>
            <div className="card-body">
                <h2 className="card-title">{product.title}</h2>
                <p>
                    from{" "}
                    <Link
                        href={route("vendor.profile", product.user.store_name)}
                        className="hover:underline"
                    >
                        {product.user.store_name}
                    </Link>
                    &nbsp;in{" "}
                    <Link
                        href={route(
                            "product.byDepartment",
                            product.department.slug
                        )}
                        className="hover:underline"
                    >
                        {product.department.name}
                    </Link>
                </p>
                <div className="card-actions items-center justify-between mt-3">
                    <button onClick={addToCart} className="btn btn-primary">
                        Add to Cart
                    </button>
                    <span className="text-2xl">
                        <CurrencyFormatter amount={product.price} />
                    </span>
                </div>
            </div>
        </div>
    );
}

export default ProductItem;
