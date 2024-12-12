<?php

namespace App\Http\Controllers;

use App\Http\Resources\LetterResource;
use App\Models\Letter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Mpdf\Mpdf;


class LetterController extends Controller
{
    public function index()
    {
        return view('pages.letters.index');
    }

    public function getData(Request $request)
    {

        $filters = $request->query('filters');
        $letters = Letter::query()

            ->where('user_id', $request->user()->id)
            ->when(isset($filters['search']), function (Builder $q) use ($filters) {
                $q->where(function (Builder $q) use ($filters) {
                    $q->whereAny(
                        [
                            'subject',
                            'body',
                            'receiver',
                            'sender'
                        ],
                        'LIKE',
                        "%" . $filters['search'] . "%"
                    );
                });
            })
            ->latest()
            ->paginate(30);

        return LetterResource::collection($letters);
    }

    public function update(Request $request, Letter $letter)
    {
        // $request->validate([
        //     'contract_id' => 'required',
        //     'from_id' => 'required',
        //     'to_id' => 'required',
        //     'type' => 'required',
        //     'title' => 'required',
        //     // 'is_completed' => 'required|boolean',
        //     'ref' => 'nullable',
        //     'content' => 'nullable',
        //     'notes' => 'nullable',
        //     'follow_ids' => 'nullable|array',
        //     'tag_ids' => 'nullable|array',
        // ]);

        $letter->update($request->all());
        return new LetterResource($letter);
    }

    public function store(Request $request)
    {
        // $request->validate([
        //     'contract_id' => 'required',
        //     'from_id' => 'required_without:to_id|nullable',
        //     'to_id' => 'required_without:from_id|nullable',
        //     'type' => 'required',
        //     'title' => 'required',
        //     'is_completed' => 'required|boolean',
        //     'ref' => 'nullable',
        //     'content' => 'nullable',
        //     'notes' => 'nullable',
        //     'follow_ids' => 'nullable|array',
        //     'tag_ids' => 'nullable|array',
        // ]);
        $letter = Letter::create($request->all());
        return new LetterResource($letter);
    }

    public function destroy(Letter $letter)
    {
        $letter->delete();
        return response()->json(['message' => 'Letter deleted successfully']);
    }

    public function originalPDF(Letter $letter)
    {
        // $mpdf = new Mpdf([
        //     'margin_bottom'            => 26,
        //     'margin_header'            => 0,
        //     'margin_footer'            => 0,
        // ]);


        $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        // Define your custom fonts directory and fonts
        $mpdf = new \Mpdf\Mpdf([
            'fontDir' => array_merge($fontDirs, [
                public_path('fonts'), // Replace with your custom fonts directory
            ]),
            'fontdata' => $fontData + [
                'sultan' => [
                    'R' => 'Sultan4-Regular.ttf', // Regular font file
                    // 'B' => 'Sultan2-Regular.ttf',   // Bold font file
                    'useOTL' => 0xFF,
                    'useKashida' => 75,
                ],
            ],
            'default_font' => 'sultan', // Set the default font if needed
        ]);

        // $mpdf->autoLangToFont = true;

        $mpdf->SetMargins(19, 19, 50);
        $mpdf->SetWatermarkImage(public_path('images/cover.jpeg'));
        $mpdf->showWatermarkImage = true;
        $mpdf->watermarkImageAlpha = 1;
        $mpdf->watermark_pos = 'P';
        $mpdf->watermark_size = 'D';
        $body = view('pages.letters.original-pdf', ['letter' => $letter]);
        $mpdf->WriteHTML($body); //should be before output directly
        $mpdf->Output('file_name.pdf', 'I');
    }

    public function normalPDF(Letter $letter)
    {
        return Pdf::view('pages.letters.normal-pdf', ['letter' => $letter])
            ->format('a4')
            ->name('your-invoice.pdf');
    }
}
