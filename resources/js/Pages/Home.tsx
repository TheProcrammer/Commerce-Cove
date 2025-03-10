import DepartmentList from "@/Components/app/DepartmentList";
import ProductItem from "@/Components/app/ProductItem";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { PageProps, PaginationProps, Product } from "@/types";
import { Head, Link } from "@inertiajs/react";

export default function Home({
    products, // Receiving products data
}: // Specify the props type. Check index.d.ts for specific props.
PageProps<{ products: PaginationProps<Product> }>) {
    return (
        <AuthenticatedLayout>
            <DepartmentList />
            <Head title="Welcome" />
            <div className="hero bg-base-200 min-h-screen">
                <div className="hero-content text-center">
                    <div className="max-w-md">
                        <h1 className="text-5xl font-bold">Hello there</h1>
                        <p className="py-6">
                            Provident cupiditate voluptatem et in. Quaerat
                            fugiat ut assumenda excepturi exercitationem quasi.
                            In deleniti eaque aut repudiandae et a id nisi.
                        </p>
                        <button className="btn btn-primary">Get Started</button>
                    </div>
                </div>
            </div>

            <div className="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3 p-8">
                {products.data.map((product) => (
                    <ProductItem product={product} key={product.id} /> // Render the `ProductItem` component for each product, passing `product` data as props.
                    // Use `product.id` as the unique key for React's reconciliation.
                ))}
            </div>
        </AuthenticatedLayout>
    );
}
