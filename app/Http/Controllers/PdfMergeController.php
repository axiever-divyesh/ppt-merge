<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Webklex\PDFMerger\PDFMerger;
class PdfMergeController extends Controller
{
    public function index()
    {
        return view('pdf_merger');
    }
    //

    public function mergePdf(Request $request)
    {
        // Get the uploaded PPTX files
        $pdf_file = $request->file('pdf_file');

        // validation
        if (count($pdf_file) === 0) {
            // Handle the case when no files are uploaded
            return redirect('/pdf_merger')->with('error', 'No files uploaded.');
        }

        // Initialize the PDF merger
        $pdf = new PDFMerger(app('files'));

        // Define the custom order of files and slides
        $customOrder = [];
        foreach ($request->file('pdf_file') as $key => $file) {
            $customOrder[$request->merge_order[$key] - 1] = [
                'pdf_file' => $key, // Index of the first uploaded file
                'slide_numbers' => explode(',', $request->slide_numbers[$key]), // Slide numbers from the first file
            ];
        }
        // Re-arrange the array indices numerically
        ksort($customOrder);
        foreach ($customOrder as $order) {
            $fileIndex = $order['pdf_file'];
            $slideNumbers = isset($order['slide_numbers']) ? $order['slide_numbers'] : ['all'];

            if (isset($pdf_file[$fileIndex])) {
                $pdfFile = $pdf_file[$fileIndex];
                $pdf_path = $pdfFile->getRealPath();
                // foreach ($slideNumbers as $slideNumber) {
                $slide = $pdf_path;
                if ($slideNumbers[0] == 'all' || $slideNumbers[0] == '') {
                    $pdf->addPDF($slide, 'all');
                } else {
                    $pdf->addPDF($slide, $slideNumbers);
                }
                // }
            }
        }
        // Merge uploaded PDF files
        // foreach ($request->file('pdf_file') as $pdfFile) {
        //     $pdf->addPDF($pdfFile->path(), 'all');
        // }

        // Set the output file path for the merged PDF
        $outputPath = storage_path('app/public/merged.pdf');

        // Merge the PDF files and save the result
        $pdf->merge();
        $pdf->save($outputPath);

        // Optionally, you can return a response or a download link
        return response()
            ->download($outputPath)
            ->deleteFileAfterSend(true);
    }

    public function mergePdf_old_working()
    {
        // Initialize the PDF merger
        $pdf = new PDFMerger(app('files'));

        // List of PDF files to merge
        $pdfFiles = [
            storage_path('app/public/1.pdf'),
            storage_path('app/public/2.pdf'),
            // Add more PDF files as needed
        ];

        // Loop through each PDF file and add it to the merger
        foreach ($pdfFiles as $pdfFile) {
            $pdf->addPDF($pdfFile, 'all');
        }

        // Set the output file path for the merged PDF
        $outputPath = storage_path('app/public/merged.pdf');

        // Merge the PDF files and save the result
        // $pdf->merge('file', $outputPath);

        // Merge the PDF files and save the result
        $pdf->merge();
        $pdf->save($outputPath);

        // Optionally, you can return a response or a download link
        return response()
            ->download($outputPath)
            ->deleteFileAfterSend(true);
    }
}
