import { Image } from "@/types";
import { useEffect, useState } from "react";

function Carousel({ images }: { images: Image[] }) {
    console.log("Images being passed to Carousel:", images); // Log the images for debugging purposes

    // Ensure selectedImage is either the first image or null if images are empty
    const [selectedImage, setSelectedImage] = useState<Image | null>(
        images.length > 0 ? images[0] : null
    );

    // Update selectedImage whenever images change
    useEffect(() => {
        if (images.length > 0) {
            setSelectedImage(images[0]);
        }
    }, [images]);

    return (
        <>
            <div className="flex items-start gap-8">
                {/* Sidebar with thumbnails */}
                <div className="flex flex-col items-center gap-2 py-2">
                    {images.map((image, i) => (
                        <button
                            // Set the clicked image as the selected image
                            onClick={() => setSelectedImage(image)}
                            // Highlight the border of the currently selected thumbnail
                            className={`border-2 hover:border-blue-500 ${
                                selectedImage?.id === image.id
                                    ? "border-blue-500" // Blue border for the selected image
                                    : "border-transparent" // Transparent border for non-selected images
                            }`}
                            key={image.id} // Unique key for React rendering
                        >
                            {/* Display the thumbnail image */}
                            <img
                                src={image.thumb}
                                alt={`Thumbnail ${i}`}
                                className="w-[50px]"
                            />
                        </button>
                    ))}
                </div>
                {/* Main carousel to display the selected image */}
                <div className="carousel w-full">
                    {selectedImage ? (
                        <img src={selectedImage.large} className="w-full" />
                    ) : (
                        <div>Loading...</div> // Placeholder for no selected image
                    )}
                </div>
            </div>
        </>
    );
}

export default Carousel;
