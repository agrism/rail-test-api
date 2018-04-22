<?php

namespace App\Http\Controllers\Api\v1;

use App\Models\Attachment;
use App\Models\Document;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Imagick;

class DocumentsController extends Controller
{

    private $documentPath = null;

    private $documentsPerPage = 20;

    private $thumbFileFormat = 'png';

    /**
     * DocumentsController constructor.
     */
    public function __construct()
    {
        $this->documentPath = 'app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'documents' . DIRECTORY_SEPARATOR;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request)
    {

        $page = $request->page;

        if (!$page) {
            $page = 1;
        }

        $documents = Document::with('firstAttachment')->get();

        $documentCount = $documents->count();

        $pages = ceil($documentCount / $this->documentsPerPage);

        $start = ($page - 1) * $this->documentsPerPage;

        $documents = $documents->splice($start, $this->documentsPerPage)->all();

        $documents = collect(collect($documents));

        $documents->map(function ($document) {
            $document['thumb'] = url('storage/documents/' . 'thumb_' . $document->firstAttachment->file_name . '.' . $this->thumbFileFormat);
            $document['attachmentId'] = $document->firstAttachment->id;
            unset($document->firstAttachment);
        });

        return response([
            'documents' => $documents,
            'pages' => $pages
        ], 200);
    }

    /**
     * @param Request $request
     * @throws \ImagickException
     */

    public function store(Request $request)
    {
        $requestFile = $request->file('file');

        $request->validate([
                'file' => 'required|mimes:pdf'
            ]);

        $document = new Document();

        $document->name = $requestFile->getClientOriginalName();

        $document->save();

        $attachment = new Attachment();

        $fileNameArray = explode('.', $document->name);

        $fileExtension = end($fileNameArray);

        $attachment->file_name = uniqid() . '.' . $fileExtension;

        $request->file('file')->storeAs('public' . DIRECTORY_SEPARATOR . 'documents', $attachment->file_name);

        $document->attachments()->save($attachment);

        $imagick = new Imagick();

        $imagick->readImage($request->file('file')->getRealPath() . '[0]');

        $imagick->setImageFormat($this->thumbFileFormat);

        $imagick->cropThumbnailImage(200, 200);

        Storage::disk('local_public_documents')->put('thumb_' . $attachment->file_name . '.' . $this->thumbFileFormat, $imagick);
    }
}
