import React, { useState, useEffect } from 'react';
import ReactDOM from 'react-dom/client';
import { Document, Page, pdfjs } from 'react-pdf';
import 'react-pdf/dist/esm/Page/TextLayer.css';
import 'react-pdf/dist/Page/AnnotationLayer.css';
import { PDFDocument,PDFName} from 'pdf-lib';

import axios from 'axios'; 
pdfjs.GlobalWorkerOptions.workerSrc = `//cdnjs.cloudflare.com/ajax/libs/pdf.js/${pdfjs.version}/pdf.worker.js`;
// Set up default headers for axios to include CSRF token
axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

const api = "http://127.0.0.1:8000"
function App() {
    const [numPages, setNumPages] = useState(null);
    const [pageNumber, setPageNumber] = useState(1);
    const [pdfUrl, setPdfUrl] = useState('');
    const [link, setLink] = useState('');
    const [annotations, setAnnotations] = useState([]);
    const [currentAnnotations, setCurrentAnnotations] = useState([]);
    const [id, setId] = useState('');
    const [imageData, setImageData] = useState(null);
    
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
          const response = await axios.get(`${api}/api/fetch-image`, {
            responseType: 'arraybuffer',
          });
          setImageData(response.data);
        } catch (error) {
          console.error('Error fetching image data:', error);
        }
      };
  
      fetchImageData();
    }, []);
    function extractIdFromPath(pathname) {
        // Assuming the URL is in the format '/books/:id'
        const parts = pathname.split('/');
        return parts[parts.length - 1];
    }

    async function fetchPdfUrl(id) {
        try {
            const response = await axios.get(`${api}/api/pdf-url/${id}`);
            if (response.data.pdf_book) {
                setPdfUrl(response.data.pdf_book);
            } else {
                console.error('PDF URL not found');
            }
        } catch (error) {
            console.error('Error fetching PDF URL:', error);
        }
    }
    useEffect(() => {
      setCurrentAnnotations(
        annotations.filter(annotation => annotation.pageNumber === pageNumber)
      );
    }, [annotations, pageNumber]);
  
    // Function to send the edited PDF to an API
    async function sendEditedPDFToAPI(pdfBytes) {
      try {
        const formData = new FormData();
        const blob = new Blob([pdfBytes], { type: 'application/pdf' });
        formData.append('pdf_book', blob, 'edited_pdf.pdf');
        // Replace 'YOUR_API_ENDPOINT' with your actual API endpoint
         await axios.post(`${api}/api/pdf-url/${id}`,  formData, {
          headers: {
            'Content-Type': 'multipart/form-data', // Set the correct content type
          },
        });

        // console.log('PDF sent to API:', response);
      } catch (error) {
        console.error('Error sending PDF to API:', error);
      }
    }
    function onDocumentLoadSuccess({ numPages }) {
      setNumPages(numPages);
    }
  
    function handleFileChange(event) {
      const file = event.target.files[0];
      setPdfFile(file);
    }
  
    function addAnnotation() {
      if (link.trim()) {
        setAnnotations([...annotations, { link, pageNumber }]);
        setLink('');
      }
    }

    //function to add text with link in pdf-lib
  const createLinkAnnotationWithText = (page, x, y, text, uri,image) => {
    const link = page.doc.context.register(
        page.doc.context.obj({
            Type: 'Annot',
            Subtype: 'Link',
            Rect: [x, y, x + 150, y + 20], // Adjust the rectangle dimensions as needed
            Border: [0, 0, 0], // No border
            C: [0, 0, 1], // Blue color
            A: {
                Type: 'Action',
                S: 'URI',
                URI: uri,
            },
        }),
    );

//add logo image to pdf link
    page.drawImage(image,  {
      x: x + 80, // Adjust the x-coordinate to position the image after the text
      y: y - 3, // Adjust the y-coordinate to vertically center the image with the text
      width: 24,
      height: 24,
  });
      // Add text to the page
      page.drawText(text, {
        x: x + 2, // Adjust the x-coordinate to align the text within the rectangle
        y: y + 2, // Adjust the y-coordinate to align the text within the rectangle
        size: 12, // Adjust the font size as needed
    });

    // Return the link annotation
    return link;
}; 
    // Function to download the edited PDF
    async function downloadEditedPDF() {
      if (!pdfUrl) return;
    
      try {
        const response = await axios.get(`${api}/pdf_books/${pdfUrl}`, {
          responseType: 'arraybuffer'
        });
    
        const pdfBytes = response.data;
        const pdfDoc = await PDFDocument.load(pdfBytes);
        // const imageBytes = await fetch(img).then(response => response.arrayBuffer());
        // const image = await pdfDoc.embedPng(imageBytes);
        const image = await pdfDoc.embedPng(imageData);
        const pages = pdfDoc.getPages();
        annotations.forEach((annotation) => {
          const { link, pageNumber } = annotation;
            const page = pages[pageNumber - 1];
            const textX = 20; // X position for text
            const textY = 20; // Y position for text
            const link1 = createLinkAnnotationWithText(page, textX, textY, 'Explore here', link,image);
            page.node.set(PDFName.of('Annots'), pdfDoc.context.obj([link1]));        
        });
    
        const editedPdfBytes = await pdfDoc.save();
        console.log(editedPdfBytes,pdfDoc)
        sendEditedPDFToAPI(editedPdfBytes).then(() => {
          // Redirect to the desired URL after successful save
         window.location.href = `${api}/admin/books/${id}/edit`;
        })
      } catch (error) {
        console.error('Error downloading PDF:', error);
      }
    }
 
    return (
      <div className="App">
        <div className="header">
          <input
            type="text"
            value={link}
            onChange={(e) => setLink(e.target.value)}
            placeholder="Link URL"
            className="input-field"
          />
          <button onClick={addAnnotation} className="add-button">Add Annotation</button>
          <input
            type="number"
            value={pageNumber}
            onChange={(e) => setPageNumber(parseInt(e.target.value))}
            min={1}
            max={numPages || 1}
            className="page-number-input"
          />
        </div>
        {/* Render PDF if PDF URL is available */}
        {pdfUrl && (
          <div className="pdf-container">
            <Document
              file={`${api}/pdf_books/${pdfUrl}`}
              onLoadSuccess={onDocumentLoadSuccess}
            >
              <Page key={pageNumber} pageNumber={pageNumber} renderAnnotationLayer={true} width={800} height={1200} />
            </Document>
            <div className="annotations">
              {currentAnnotations.map((annotation, index) => (
                <div className="annotation" key={index}>
                  <a href={annotation.link} target="_blank" rel="noopener noreferrer">{annotation.link}</a>
                </div>
              ))}
            </div>
          </div>
        )}
        <div className="controls">
          <button onClick={() => setPageNumber(pageNumber - 1)} disabled={pageNumber <= 1} className="page-nav-button">
            Previous Page
          </button>
          <button onClick={() => setPageNumber(pageNumber + 1)} disabled={pageNumber >= numPages} className="page-nav-button">
            Next Page
          </button>
          <button onClick={downloadEditedPDF} className="download-button save">Save Edited PDF</button>
        </div>
        <div className="page-info">
          Page {pageNumber} of {numPages}
        </div>
      </div>
    );
  }
  
  export default App;

if (document.getElementById('app')) {
    const Index = ReactDOM.createRoot(document.getElementById("app"));

    Index.render(
        <React.StrictMode>
            <App/>
        </React.StrictMode>
    )
}
