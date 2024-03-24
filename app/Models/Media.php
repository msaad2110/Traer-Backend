<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use function App\get_user_id;

class Media extends Model
{
    use HasFactory;
    protected $fillable = [
        'document_type_id',
        'name',
        'file_name',
        'file_path',
        'created_at',
        'user_id',
        'updated_at',
        'deleted_at',
    ];

    public function document_type()
    {
        return $this->belongsTo(DocumentType::class, 'document_type_id', 'id');
    }

    protected $appends = ['file_preview_path'];

    /**
     * File preview path attribute to populate file_preview_path
     */
    public function getFilePreviewPathAttribute()
    {
        return env("APP_URL") . '/' . $this->file_path;
    }


    public static function saveMedia($request, $userId = 0)
    {
        if ($userId == 0) {
            $userId = get_user_id($request);
        }
        $timestamp = date('Y-m-d H:i:s');

        if ($request->hasFile("attachments")) {
            $files = $request->file('attachments');
            if (is_array($files)) {
                foreach ($request->file('attachments') as $file) {
                    $originalFileName = $file->getClientOriginalName();
                    $filePath = $file->store("attachments");

                    $media = new Media();
                    // $media->source_type = (int)$source_type;
                    $media->document_type_id = (int)$request->document_type_id;
                    $media->name = $originalFileName;
                    $media->file_name = $originalFileName;
                    $media->file_path = $filePath;
                    $media->created_at = $timestamp;
                    $media->user_id = (int)$userId;
                    $media->updated_at = $timestamp;
                    // $media->updated_by_id = (int)$userId;
                    if (!$media->save()) {
                        throw new Exception('Failed to save media file entry in database for: ' . $originalFileName);
                    }
                }
            } else {
                $originalFileName = $files->getClientOriginalName();
                $filePath = $files->store("attachments");

                $media = new Media();
                // $media->source_type = (int)$source_type;
                $media->document_type_id = (int)$request->document_type_id;
                $media->name = $originalFileName;
                $media->file_name = $originalFileName;
                $media->file_path = $filePath;
                $media->created_at = $timestamp;
                $media->user_id = (int)$userId;
                $media->updated_at = $timestamp;
                // $media->updated_by_id = (int)$userId;
                if (!$media->save()) {
                    throw new Exception('Failed to save media file entry in database for: ' . $originalFileName);
                }
            }
        } else {
            throw new Exception("No file attached");
        }
    }
}
