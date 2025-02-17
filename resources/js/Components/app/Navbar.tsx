import React, { FormEventHandler } from "react";
import { Link, useForm, usePage } from "@inertiajs/react";
import MiniCartDropdown from "./MiniCartDropdown";
import { MagnifyingGlassIcon } from "@heroicons/react/16/solid";
import { PageProps } from "@/types";

function Navbar() {
    // Destructure auth and user access using the usePage
    // const user = usePage().props.auth.user; You can do this as well to directly access the user
    const { auth, departments, keyword } = usePage().props;
    const { user } = auth;

    const searchForm = useForm<{ keyword: string }>({
        keyword: keyword || "",
    });

    const { url } = usePage();

    const onSubmit: FormEventHandler = (e) => {
        e.preventDefault();
        searchForm.get(url, {
            preserveScroll: true,
            preserveState: true,
        });
    };

    return (
        <>
            <div className="navbar bg-base-100">
                <div className="flex-1">
                    <Link href="/">
                        <img
                            className="w-24 h-auto"
                            src="/images/commerce-logo.png"
                            alt="Commerce Cove Logo"
                        />
                    </Link>
                </div>
                <div className="flex-none gap-4">
                    <div className="flex-1">
                        <form onSubmit={onSubmit} className="join flex-1">
                            <input
                                value={searchForm.data.keyword}
                                onChange={(e) =>
                                    searchForm.setData(
                                        "keyword",
                                        e.target.value
                                    )
                                }
                                className="input input-bordered join-item w-full"
                                placeholder="Search"
                            />
                            <div className="indicator">
                                <button className="btn join-item">
                                    <MagnifyingGlassIcon className="size-4" />
                                    Search
                                </button>
                            </div>
                        </form>
                    </div>

                    <MiniCartDropdown />

                    {/* Check if user is logged in to show profile dropdown, else show login/register */}
                    {user ? (
                        <div className="dropdown dropdown-end">
                            <div
                                tabIndex={0}
                                role="button"
                                className="btn btn-ghost btn-circle avatar"
                            >
                                <div className="w-10 rounded-full">
                                    <img
                                        alt="User Avatar"
                                        src="https://img.daisyui.com/images/stock/photo-1534528741775-53994a69daeb.webp"
                                    />
                                </div>
                            </div>
                            <ul
                                tabIndex={0}
                                className="menu menu-sm dropdown-content bg-base-100 rounded-box z-[1] mt-3 w-52 p-2 shadow"
                            >
                                <li>
                                    <Link
                                        href={route("profile.edit")}
                                        className="justify-between"
                                    >
                                        Profile
                                        <span className="badge">New</span>
                                    </Link>
                                </li>
                                <li>
                                    <Link
                                        href={route("logout")}
                                        method="post"
                                        as="button"
                                    >
                                        Logout
                                    </Link>
                                </li>
                            </ul>
                        </div>
                    ) : (
                        <>
                            <Link href={route("login")} className="btn">
                                Login
                            </Link>
                            <Link
                                href={route("register")}
                                className="btn btn-primary"
                            >
                                Register
                            </Link>
                        </>
                    )}
                </div>
            </div>

            <div className="navbar-center-100 border-t min-h-4">
                <div className="navbar-center hidden lg:flex">
                    <ul className="menu menu-horizontal px-1 z-20 py-0">
                        {departments.map((department) => (
                            <li key={department.id}>
                                <Link
                                    href={route(
                                        "product.byDepartment",
                                        department.slug
                                    )}
                                >
                                    {department.name}
                                </Link>
                            </li>
                        ))}
                    </ul>
                </div>
            </div>
        </>
    );
}

export default Navbar;
