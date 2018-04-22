<?php

namespace App\Http\Controllers\Api\v1;

use App\Models\Document;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class DocumentsAttachmentController extends Controller
{

    /**
     * @param $documentId
     * @param $attachmentId
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function previews($documentId, $attachmentId)
    {
        $document = Document::with(['attachments' => function ($attachment) use ($attachmentId) {
            $attachment->where('id', $attachmentId);
        }])->find($documentId);

        if ($document) {
            $attachment = $document->attachments->first();
            if ($attachment) {

                $file = Storage::disk('local_public_documents')->get($attachment->file_name);
                return response(['file' => 'data:application/pdf;base64, ' . base64_encode($file)], 200);
            }
        }
        return response('file not found', 200);


    }
}
