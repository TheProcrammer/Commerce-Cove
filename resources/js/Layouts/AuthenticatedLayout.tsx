// This file is responsible for rendering the authenticated layout which includes Nav bar

import Navbar from "@/Components/app/Navbar";
import { Link, usePage } from "@inertiajs/react";
import {
    PropsWithChildren,
    ReactNode,
    useEffect,
    useRef,
    useState,
} from "react";

export default function AuthenticatedLayout({
    header, // A ReactNode that represents the page header.
    children, //  The content inside this layout
}: PropsWithChildren<{ header?: ReactNode }>) {
    const props = usePage().props; // Gets the current page’s props using Inertia.js.
    const user = props.auth.user; // Retrieves the authenticated user’s details.

    const [successMessages, setSuccessMessages] = useState<any[]>([]); // Stores success messages.
    const timeoutRefs = useRef<{
        [key: number]: ReturnType<typeof setTimeout>; // Keeps track of timeout IDs to remove messages after 5 seconds.
    }>({});
    const [showingNavigationDropdown, setShowingNavigationDropdown] =
        useState(false);

    useEffect(() => {
        if (props.success.message) {
            // Checks if a success message exists
            const newMessage = {
                ...props.success,
                id: props.success.time,
            };

            // Stores the message in state.
            setSuccessMessages((prevMessages) => [newMessage, ...prevMessages]);

            const timeoutId = setTimeout(() => {
                setSuccessMessages((prevMessages) =>
                    prevMessages.filter((msg) => msg.id !== newMessage.id)
                );
                // Removes it after 5 seconds using setTimeout().
                delete timeoutRefs.current[newMessage.id];
            }, 5000);

            timeoutRefs.current[newMessage.id] = timeoutId; // Keeps track of timeouts using timeoutRefs.
        }
    }, [props.success]);

    return (
        <div className="min-h-screen bg-gray-100 dark:bg-gray-900">
            <Navbar />
            {props.error && (
                <div className="container mx-auto px-8 mt-8">
                    <div className="alert alert-error">{props.error}</div>
                </div>
            )}
            {successMessages.length > 0 && (
                <div className="toast toast-top toast-end z-[1000] mt-16">
                    {successMessages.map((msg) => (
                        <div key={msg.id} className="alert alert-success">
                            <span>{msg.message}</span>
                        </div>
                    ))}
                </div>
            )}
            <main>{children}</main>
        </div>
    );
}
