<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use PhpOffice\PhpPresentation\PhpPresentation;
use PhpOffice\PhpPresentation\IOFactory;
use Cristal\Presentation\PPTX;

class PresentationController extends Controller
{
    public function index()
    {
        return view('upload');
    }

    public function upload(Request $request)
    {
        // Get the uploaded PPTX files
        $pptxFiles = $request->file('pptx_files');

        // validation
        if (count($pptxFiles) === 0) {
            // Handle the case when no files are uploaded
            return redirect('/home')->with('error', 'No files uploaded.');
        }

        // Define the custom order of files and slides
        // dd($request->all());
        $customOrder = [];
        foreach ($request->file('pptx_files') as $key => $file) {
            $customOrder[$request->merge_order[$key]-1]=[
                'file_index' => $key,  // Index of the first uploaded file
                'slide_numbers' => explode(',', $request->slide_numbers[$key]),  // Slide numbers from the first file
            ];
        }
        // Re-arrange the array indices numerically
        ksort($customOrder);
        // dd($customOrder);
        // $customOrder = [
        //     [
        //         'file_index' => 0,  // Index of the first uploaded file
        //         'slide_numbers' => [1],  // Slide numbers from the first file
        //         'merging_order' => [1,3,4,6,8], // Slide numbers from
        //     ],
        //     [
        //         'file_index' => 1,  // Index of the second uploaded file
        //         'slide_numbers' => [1],  // Slide numbers from the second file
        //         'merging_order' => [2,5,7,9], // Slide numbers from
        //     ],
        // ];

        // Initialize the merged presentation
        // dd(storage_path('app/public/empty.pptx'));
        $mergedPPTX = new PPTX(storage_path('app/public/empty.pptx'));

        foreach ($customOrder as $order) {
            $fileIndex = $order['file_index'];
            $slideNumbers = $order['slide_numbers'];

            if (isset($pptxFiles[$fileIndex])) {
                $pptxFile = $pptxFiles[$fileIndex];
                $pptx = new PPTX($pptxFile->getRealPath());
                // dd($pptx->getSlides()[1]);
                foreach ($slideNumbers as $slideNumber) {
                    $slide = $pptx->getSlides()[$slideNumber - 1];
                    if ($slide) {
                        $mergedPPTX->addSlide($slide);
                    }
                }
            }
        }
        // Save the merged presentation
        $outputPath = storage_path('app/public/merged_presentation.pptx');
        $mergedPPTX->saveAs($outputPath);

        return redirect('/download');
    }
    public function download()
    {
        $filePath = storage_path('app/public/merged_presentation.pptx');

        return response()->download($filePath, 'merged_presentation.pptx')->deleteFileAfterSend(true);
    }
}
