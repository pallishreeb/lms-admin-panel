import React, { useState, useEffect } from "react";
import ReactDOM from "react-dom/client";
import { Document, Page, pdfjs } from "react-pdf";
import "react-pdf/dist/esm/Page/TextLayer.css";
import "react-pdf/dist/Page/AnnotationLayer.css";
import { PDFDocument, PDFName, rgb, StandardFonts } from "pdf-lib";

import axios from "axios";
pdfjs.GlobalWorkerOptions.workerSrc = `//cdnjs.cloudflare.com/ajax/libs/pdf.js/${pdfjs.version}/pdf.worker.js`;
// Set up default headers for axios to include CSRF token
axios.defaults.headers.common["X-CSRF-TOKEN"] = document
    .querySelector('meta[name="csrf-token"]')
    .getAttribute("content");

// const api = "http://127.0.0.1:8000";
const api = "http://43.204.161.117"
function App() {
    const [numPages, setNumPages] = useState(null);
    const [pageNumber, setPageNumber] = useState(1);
    const [pdfUrl, setPdfUrl] = useState("");
    const [link, setLink] = useState("");
    const [text, setText] = useState("");
    const [editingIndex, setEditingIndex] = useState(null);
    const [annotations, setAnnotations] = useState([]);
    const [currentAnnotations, setCurrentAnnotations] = useState([]);
    const [id, setId] = useState("");
    const [imageData, setImageData] = useState(null);
    const [loading, setLoading] = useState(false);
    const [pdfLoading, setPdfLoading] = useState(false);
    useEffect(() => {
        const pathname = window.location.pathname;
        const idFromPath = extractIdFromPath(pathname);
        setId(idFromPath);
    }, []); // Runs once when the component mounts

    useEffect(() => {
        if (id) {
            fetchPdfUrl(id);
        }
    }, [id]); // Fetch PDF URL when the id changes

    useEffect(() => {
        const fetchImageData = async () => {
            try {
                const response = await axios.get(
                    `${api}/book_covers/play.png`,
                    {
                        responseType: "arraybuffer",
                    }
                );
                setImageData(response.data);
            } catch (error) {
                console.error("Error fetching image data:", error);
            }
        };

        fetchImageData();
    }, []);
    function extractIdFromPath(pathname) {
        // Assuming the URL is in the format '/books/:id'
        const parts = pathname.split("/");
        return parts[parts.length - 1];
    }

    async function fetchPdfUrl(id) {
        try {
            setPdfLoading(true);
            const response = await axios.get(`${api}/api/pdf-url/${id}`);
            if (response.data.pdf_book) {
                setPdfUrl(response.data.pdf_book);
                setPdfLoading(false);
            } else {
                console.error("PDF URL not found");
                setPdfLoading(false);
            }
        } catch (error) {
            setPdfLoading(false);
            console.error("Error fetching PDF URL:", error);
        }
    }
    useEffect(() => {
        setCurrentAnnotations(
            annotations.filter(
                (annotation) => annotation.pageNumber === pageNumber
            )
        );
    }, [annotations, pageNumber]);

    // Function to send the edited PDF to an API
    async function sendEditedPDFToAPI(pdfBytes) {
        try {
            const formData = new FormData();
            const blob = new Blob([pdfBytes], { type: "application/pdf" });
            formData.append("pdf_book", blob, "edited_pdf.pdf");
            // Replace 'YOUR_API_ENDPOINT' with your actual API endpoint
            await axios.post(`${api}/api/pdf-url/${id}`, formData, {
                headers: {
                    "Content-Type": "multipart/form-data", // Set the correct content type
                },
            });

            // console.log('PDF sent to API:', response);
        } catch (error) {
            console.error("Error sending PDF to API:", error);
        }
    }
    function onDocumentLoadSuccess({ numPages }) {
        setNumPages(numPages);
    }

    function addAnnotation() {
        if (link.trim()) {
            setAnnotations([...annotations, { text, link, pageNumber }]);
            setLink("");
            setText("");
        }
    }

    const editAnnotation = (index) => {
        // Set the editing index to the index of the annotation being edited
        setEditingIndex(index);

        // Set the text and link inputs to the values of the annotation being edited
        setText(annotations[index].text);
        setLink(annotations[index].link);
    };
    // Function to update an annotation
    const updateAnnotation = () => {
        if (link.trim() && editingIndex !== null) {
            // Update the text and link of the annotation at the editing index
            const updatedAnnotations = [...annotations];
            updatedAnnotations[editingIndex] = { text, link, pageNumber };
            setAnnotations(updatedAnnotations);

            // Clear the text and link inputs
            setText("");
            setLink("");
            setEditingIndex(null);
        }
    };

    const deleteAnnotation = (index) => {
        // Logic to delete the annotation at the specified index
        const updatedAnnotations = [...annotations];
        updatedAnnotations.splice(index, 1);
        setAnnotations(updatedAnnotations);
    };

    //function to add text with link in pdf-lib
    const createLinkAnnotationWithText = (
        page,
        x,
        y,
        text,
        uri,
        image,
        font
    ) => {
        const width = font.widthOfTextAtSize(text, 10);
        const link = page.doc.context.register(
            page.doc.context.obj({
                Type: "Annot",
                Subtype: "Link",
                Rect: [x, y - 10, x + 120, y + 65], // Adjust the rectangle dimensions as needed
                Border: rgb(1, 0, 0), // No border
                C: rgb(1, 0, 0), // Blue color
                A: {
                    Type: "Action",
                    S: "URI",
                    URI: uri,
                },
            })
        );
        // Add colored rectangle as background
        page.drawRectangle({
            x,
            y: y - 4,
            width: 120,
            height: 24,
            color: rgb(1, 1, 1),
        });
        //add logo image to pdf link
        page.drawImage(image, {
            x: x + (width / 2) - (42 / 2), // Adjust the x-coordinate to position the image after the text
            y: y + 5, // Adjust the y-coordinate to vertically center the image with the text
            width: 42,
            height: 42,
        });
        // Add text to the page
        page.drawText(text, {
            x: x + 3, // Adjust the x-coordinate to align the text within the rectangle
            y: y - 5, // Adjust the y-coordinate to align the text within the rectangle
            size: 10, // Adjust the font size as needed
            color: rgb(0, 0, 0),
            font: font,
        });

        // Return the link annotation
        return link;
    };
    // Function to download the edited PDF
    async function downloadEditedPDF() {
        if (!pdfUrl) return;

        try {
            setLoading(true);
            const response = await axios.get(`${pdfUrl}`, {
                responseType: "arraybuffer",
            });

            const pdfBytes = response.data;
            const pdfDoc = await PDFDocument.load(pdfBytes);
            // const imageBytes = await fetch(img).then(response => response.arrayBuffer());
            // const image = await pdfDoc.embedPng(imageBytes);
            const image = await pdfDoc.embedPng(imageData);
            const pages = pdfDoc.getPages();
            const annotationsByPage = {};
            const annotationCountsByPage = {};
            let currentPageNumber = -1;
            const font = await pdfDoc.embedFont(StandardFonts.Helvetica);
            annotations.forEach((annotation, index) => {
                const { text, link, pageNumber } = annotation;
                const page = pages[pageNumber - 1];
                let textX = 20; // X position for text
                let textY = 20; // Y position for text
                // Check if it's a new page
                if (pageNumber !== currentPageNumber) {
                    annotationCountsByPage[pageNumber] = 0;
                    // Reset X position for each new page
                    textX = 20;
                    textY = 20;
                    currentPageNumber = pageNumber; // Update the current page number
                } else {
                    annotationCountsByPage[pageNumber] += 1;
                    // Increment Y position for each annotation
                    textX = 20 + annotationCountsByPage[pageNumber] * 125; // Adjust as needed
                }
                const link1 = createLinkAnnotationWithText(
                    page,
                    textX,
                    textY,
                    text,
                    link,
                    image,
                    font
                );

                if (!annotationsByPage[pageNumber]) {
                    annotationsByPage[pageNumber] = [];
                }
                annotationsByPage[pageNumber].push(link1);
            });

            // Set annotations for each page
            Object.keys(annotationsByPage).forEach((pageNumber) => {
                const pageAnnotations = annotationsByPage[pageNumber];
                pages[pageNumber - 1].node.set(
                    PDFName.of("Annots"),
                    pdfDoc.context.obj(pageAnnotations)
                );
            });

            const editedPdfBytes = await pdfDoc.save();
            console.log(editedPdfBytes, pdfDoc);
            sendEditedPDFToAPI(editedPdfBytes).then(() => {
                setLoading(false);
                // Redirect to the desired URL after successful save
                window.location.href = `${api}/admin/books/${id}/edit`;
            });
        } catch (error) {
            setLoading(false);
            alert("Error in Saving PDF, Please Try Again");
            console.error("Error downloading PDF:", error);
        }
    }

    return (
        <div className="App container">
            <div className="flex gap-4">
                <div className="pdf-view">
                    <div className="header">
                        <input
                            type="text"
                            value={text}
                            onChange={(e) => setText(e.target.value)}
                            placeholder="Link Text"
                            className="input-field"
                        />
                        <input
                            type="text"
                            value={link}
                            onChange={(e) => setLink(e.target.value)}
                            placeholder="Link URL"
                            className="input-field"
                        />
                        <button
                            onClick={
                                editingIndex !== null
                                    ? updateAnnotation
                                    : addAnnotation
                            }
                            className="add-button"
                        >
                            {editingIndex !== null ? "Update" : "Add"} Link
                        </button>
                        <input
                            type="number"
                            value={pageNumber}
                            onChange={(e) =>
                                setPageNumber(parseInt(e.target.value))
                            }
                            min={1}
                            max={numPages || 1}
                            className="page-number-input"
                        />
                    </div>
                    {/* Render PDF if PDF URL is available */}
                    {pdfLoading ? (
                        <h3 className="text-center">Loading Pdf document...</h3>
                    ) : (
                        <>
                            {pdfUrl && (
                                <div className="pdf-container">
                                    <Document
                                        file={`${pdfUrl}`}
                                        onLoadSuccess={onDocumentLoadSuccess}
                                    >
                                        <Page
                                            key={pageNumber}
                                            pageNumber={pageNumber}
                                            renderAnnotationLayer={true}
                                            width={800}
                                            height={1200}
                                        />
                                    </Document>
                                </div>
                            )}
                            <div className="controls">
                                <button
                                    onClick={() =>
                                        setPageNumber(pageNumber - 1)
                                    }
                                    disabled={pageNumber <= 1}
                                    className="page-nav-button"
                                >
                                    Previous Page
                                </button>
                                <button
                                    onClick={() =>
                                        setPageNumber(pageNumber + 1)
                                    }
                                    disabled={pageNumber >= numPages}
                                    className="page-nav-button"
                                >
                                    Next Page
                                </button>
                                <button
                                    onClick={downloadEditedPDF}
                                    className="download-button save"
                                >
                                    {loading ? "Saving..." : "Save Edited PDF"}{" "}
                                </button>
                            </div>
                            <div className="page-info">
                                Page {pageNumber} of {numPages}
                            </div>
                        </>
                    )}
                </div>
                {pdfUrl && (
                    <div className="annotations-list">
                        <h2 className="mb-4">Links</h2>
                        <ul>
                            {annotations.map((annotation, index) => (
                                <li
                                    key={index}
                                    className="annotation-item flex"
                                >
                                    <div className="mr-2">
                                       
                                        <a
                                            href={annotation.link}
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            className="p-2"
                                        >
                                           <strong>{annotation.text}</strong>
                                        </a>
                                    </div>
                                    <div className="annotation-actions px-2">
                                        <button
                                            onClick={() =>
                                                editAnnotation(index)
                                            }
                                            className="mr-2 text-blue-500"
                                        >
                                            Edit
                                        </button>
                                        <button
                                            onClick={() =>
                                                deleteAnnotation(index)
                                            }
                                            className="text-red-500"
                                        >
                                            Delete
                                        </button>
                                    </div>
                                </li>
                            ))}
                        </ul>
                    </div>
                )}
            </div>
        </div>
    );
}

export default App;

if (document.getElementById("app")) {
    const Index = ReactDOM.createRoot(document.getElementById("app"));

    Index.render(
        <React.StrictMode>
            <App />
        </React.StrictMode>
    );
}
