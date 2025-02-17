import InputError from "@/Components/core/InputError";
import InputLabel from "@/Components/core/InputLabel";
import Modal from "@/Components/core/Modal";
import PrimaryButton from "@/Components/core/PrimaryButton";
import SecondaryButton from "@/Components/core/SecondaryButton";
import TextInput from "@/Components/core/TextInput";
import { User } from "@/types";
import { useForm, usePage } from "@inertiajs/react";
import React, { ChangeEvent, FormEventHandler, useState } from "react";

export default function VendorDetails({
    className = "",
}: {
    className?: string;
}): any {
    // State variables for managing UI interactions
    const [showBecomeVendorConfirmation, setShowBecomeVendorConfirmation] =
        useState(false); // Controls the visibility of the modal
    const [successMessage, setSuccessMessage] = useState(""); // Stores the success message

    // Fetch authenticated user and CSRF token from Laravel Inertia
    const user: User = usePage().props.auth.user;
    const token: string = usePage().props.csrf_token;

    // Form handling with useForm hook from Inertia.js
    const { data, setData, errors, post, processing, recentlySuccessful } =
        useForm({
            store_name:
                user.vendor?.store_name ||
                user.name.toLowerCase().replace(/\s+/g, "-"), // Default store name based on user's name
            store_address: user.vendor?.store_address,
        });

    // Function to handle store name change
    const OnStoreNameChange: (ev: ChangeEvent<HTMLInputElement>) => void = (
        ev: React.ChangeEvent<HTMLInputElement>
    ) => {
        setData(
            "store_name",
            ev.target.value.toLowerCase().replace(/\s+/g, "-") // Formats store name into a URL-friendly string
        );
    };

    // Function to close the modal
    const closeModal = () => {
        setShowBecomeVendorConfirmation(false);
    };

    // Function to handle vendor registration
    const becomeVendor: FormEventHandler = (ev) => {
        ev.preventDefault();

        post(route("vendor.store"), {
            preserveScroll: true, // Prevents page from scrolling to top after submission
            onSuccess: () => {
                closeModal();
                setSuccessMessage("You are now a vendor!");
            },
            onError: (errors) => {}, // Handle validation errors if needed
        });
    };

    // Function to handle vendor details update
    const updateVendor: FormEventHandler = (ev) => {
        ev.preventDefault();

        post(route("vendor.store"), {
            preserveScroll: true,
            onSuccess: () => {
                closeModal();
                setSuccessMessage("Your details have been updated.");
            },
            onError: (errors) => {},
        });
    };

    return (
        <section className={className}>
            {recentlySuccessful && (
                <div className="toast toast-top toast-end">
                    <div className="alert alert-success">
                        <span>{successMessage}</span>
                    </div>
                </div>
            )}
            <header>
                <h2 className="flex justify-between mb-8 text-lg font-medium text-gray-900 dark:text-gray-100">
                    Vendor details
                    {user.vendor?.status === "pending" && (
                        <span className={"badge badge-warning"}>
                            {user.vendor.status_label}
                        </span>
                    )}
                    {user.vendor?.status === "rejected" && (
                        <span className={"badge badge-error"}>
                            {user.vendor.status_label}
                        </span>
                    )}
                    {user.vendor?.status === "approved" && (
                        <span className={"badge badge-success"}>
                            {user.vendor.status_label}
                        </span>
                    )}
                </h2>
            </header>
            <div>
                {!user.vendor && (
                    <PrimaryButton
                        onClick={(ev) => setShowBecomeVendorConfirmation(true)}
                        disabled={processing}
                    >
                        Become a Vendor
                    </PrimaryButton>
                )}
                {user.vendor && (
                    <>
                        <form onSubmit={updateVendor}>
                            <div className="mb-4">
                                <InputLabel htmlFor="name" value="Store Name" />
                                <TextInput
                                    id="name"
                                    className="mt-1 block w-full"
                                    value={data.store_name}
                                    onChange={OnStoreNameChange}
                                    required
                                    isFocused
                                    autoComplete="name"
                                />

                                <InputError
                                    className="mt-2"
                                    message={errors.store_name}
                                />
                            </div>
                            <div>
                                <InputLabel
                                    htmlFor="name"
                                    value="Store Address"
                                />
                                <textarea
                                    className="textarea textarea-bordered w-full mt-1"
                                    value={data.store_address}
                                    onChange={(e) =>
                                        setData("store_address", e.target.value)
                                    }
                                    placeholder="Enter Your Store Address"
                                ></textarea>
                                <InputError
                                    className="mt-2"
                                    message={errors.store_address}
                                />
                            </div>
                            <div className="flex items-center gap-4">
                                <PrimaryButton disabled={processing}>
                                    Update
                                </PrimaryButton>
                            </div>
                        </form>
                        <form
                            action={route("stripe.connect")}
                            method="post"
                            className={"my-8"}
                        >
                            <input type="hidden" name="_token" value={token} />
                            {user.stripe_account_active && (
                                <div
                                    className={
                                        "text-center text-gray-600 my-4 text-sm"
                                    }
                                >
                                    You are successfully connected to Stripe
                                </div>
                            )}
                            <button
                                className="btn btn-primary w-full"
                                disabled={user.stripe_account_active}
                            >
                                Connect to Stripe
                            </button>
                        </form>
                    </>
                )}
            </div>
            <Modal show={showBecomeVendorConfirmation} onClose={closeModal}>
                <form onSubmit={becomeVendor} className="p-8">
                    <h2 className="text-lg font-medium text-gray-900 dark:text-gray-100">
                        Are you sure you want to become a Vendor?
                    </h2>
                    <div className="mt-6 flex justify-end">
                        <SecondaryButton onClick={closeModal}>
                            Cancel
                        </SecondaryButton>
                        <PrimaryButton className="ms-3" disabled={processing}>
                            Confirm
                        </PrimaryButton>
                    </div>
                </form>
            </Modal>
        </section>
    );
}
