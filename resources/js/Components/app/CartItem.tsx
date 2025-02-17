import { productRoute } from "@/types/helpers";
import { Link, router, useForm } from "@inertiajs/react";
import React, { useState } from "react";
import TextInput from "../core/TextInput";
import { CartItem as CartItemType } from "@/types";

//  Represents a cart item, allowing deletion and quantity updates.
// Takes an 'item' prop of type 'CartItemType'
function CartItem({ item }: { item: CartItemType }) {
    // Initializes a form state for handling item options.
    const deleteForm = useForm({
        option_ids: item.option_ids,
    });
    const [error, setError] = useState(""); // Creates an 'error' state for handling validation errors.
    // Deletes the item from the cart while keeping the scroll position.
    const onDeleteClick = () => {
        deleteForm.delete(route("cart.destroy", item.product_id), {
            preserveScroll: true,
        });
    };
    // Clears any previous error before updating quantity.
    const handleQuantityChange = (ev: React.FocusEvent<HTMLInputElement>) => {
        setError("");
        // router.put is usually used for updating
        router.put(
            route("cart.update", item.product_id),
            {
                quantity: ev.target.value,
                option_ids: item.option_ids,
            },
            {
                preserveScroll: true,

                // Updates the item quantity in the cart. If there's an error, it sets the first error message.
                onError: (errors) => {
                    setError(Object.values(errors)[0]);
                },
            }
        );
    };
    return (
        <>
            <div key={item.id} className="flex gap-6 p-3">
                <Link
                    href={productRoute(item)}
                    className="w-32 min-w-32 min-h-32 flex justify-center self-start"
                >
                    <img
                        src={item.image}
                        alt=""
                        className="max-w-full max-h-full"
                    />
                </Link>
                <div className="flex-1 flex flex-col">
                    <div className="flex-1">
                        <h3 className="mb-3 text-sm font-semibold">
                            {item.title}
                        </h3>
                        <Link href={productRoute(item)}>{item.title}</Link>
                    </div>
                    <div className="flex justify-between items-center mt-4">
                        <div className="flex gap-2 items-center"></div>
                        <div className="text-sm">Quantity:</div>
                        <div
                            className={
                                error
                                    ? "tooltip tooltip-open tooltip-error"
                                    : ""
                            }
                            data-tip={error}
                        >
                            <TextInput
                                type="number"
                                defaultValue={item.quantity}
                                onChange={handleQuantityChange}
                                className="input-sm w-16"
                            ></TextInput>
                        </div>
                        <button
                            onClick={() => onDeleteClick()}
                            className="btn btn-sm btn-ghost"
                        >
                            Delete
                        </button>
                        <button className="btn btn-sm btn-ghost">
                            Save for later
                        </button>
                    </div>
                    <div className="text-xs">
                        {item.options.map((option) => (
                            <div key={option.id}>
                                <strong className="text-bold">
                                    {option.type.name}:
                                </strong>
                                {" " + option.name}
                            </div>
                        ))}
                    </div>
                </div>
            </div>
            <div className="divider"></div>
        </>
    );
}

export default CartItem;
