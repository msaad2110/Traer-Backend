<?php

namespace App\Http\Controllers;

use App\Models\DocumentType;
use App\Models\Media;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\File;

use function App\get_datetime_for_db;
use function App\get_user_id;

class MediaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user_id = $request->input('user_id');
        $media = Media::whereNull('deleted_at');

        if ($request->has('user_id')) {
            $media->where('user_id', $user_id);
        }
        $media = $media->with('document_type')->get();

        return wt_api_json_success($media);
    }

    public function profile_picture(Request $request)
    {
        $validate = Validator::make(
            $request->all(),
            [
                'user_id' => 'required|exists:users,id',
            ]
        );

        if ($validate->fails()) {
            return wt_api_json_error($validate->errors()->first());
        }

        $document_type = DocumentType::where('name', 'LIKE', "%Profile Picture%")->first();
        if ($document_type == null) {
            return wt_api_json_error("No document type of Profile Picture found.");
        }

        $user_id = $request->input('user_id');
        $media = Media::whereNull('deleted_at');
        $media = $media->where('user_id', $user_id)->where('document_type_id', $document_type->id)->get();

        return wt_api_json_success($media);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {

            $action = $request->input('action');
            if ($action == 'profile-picture') {

                $validate = Validator::make(
                    $request->all(),
                    [
                        'user_id' => 'required|exists:users,id',
                        'attachments' => ['required', File::image()->max(5000)],
                    ]
                );

                if ($validate->fails()) {
                    return wt_api_json_error($validate->errors()->first());
                }

                Media::saveMedia($request);

                return wt_api_json_success(null, null, "Document Uploaded Successfully");
            } else {
                $validate = Validator::make(
                    $request->all(),
                    [
                        'user_id' => 'required|exists:users,id',
                        'document_type_id' => 'required|array|min:1',
                        // 'document_type_id.*' => 'exists:document_types,id',
                        'attachments' => 'required|array|min:1',
                        'attachments.*' => ['required']
                    ]
                );

                if ($validate->fails()) {
                    return wt_api_json_error($validate->errors()->first());
                }

                $userId = get_user_id($request);
                $timestamp = get_datetime_for_db();
                $document_type_id = $request->document_type_id;
                foreach ($request->file('attachments') as $index => $file) {
                    $originalFileName = $file->getClientOriginalName();
                    $filePath = $file->store("attachments");

                    $media = new Media();
                    $media->document_type_id = (int)$document_type_id[$index];
                    $media->name = $originalFileName;
                    $media->file_name = $originalFileName;
                    $media->file_path = $filePath;
                    $media->created_at = $timestamp;
                    $media->user_id = (int)$userId;
                    $media->updated_at = $timestamp;
                    if (!$media->save()) {
                        throw new Exception('Failed to save media file entry in database for: ' . $originalFileName);
                    }
                }

                return wt_api_json_success(null, null, "Document Uploaded Successfully");
            }
        } catch (Exception $e) {
            return wt_api_json_error($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Media  $media
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $media = Media::find($id);
        if (empty($media)) {
            return wt_api_json_success("No Record Found.");
        }
        return wt_api_json_success($media->file_preview_path);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Media  $media
     * @return \Illuminate\Http\Response
     */
    public function edit(Media $media)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Media  $media
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Media $media)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Media  $media
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {

        try {

            $media = Media::findOrFail($id);
            $media->update([
                'deleted_at' => date('Y-m-d H:i:s'),
            ]);
        } catch (Exception $e) {
            return wt_api_json_error($e->getMessage());
        }

        return wt_api_json_success("Document Successfully Deleted");
    }

    public function attachments($file_name)
    {
        $path = "attachments/" . $file_name;
        if (Storage::exists($path)) {
            return Storage::download($path);
        }
        abort(404);
    }
}
