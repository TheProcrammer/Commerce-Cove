import { Link, usePage } from "@inertiajs/react";
import React from "react";

function DepartmentList() {
    const { departments } = usePage().props;

    return (
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
    );
}

export default DepartmentList;
