<?php

namespace App\Http\Controllers;

use App\Models\TeacherProfile;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

class TeacherPhotoController extends Controller
{
    public function __invoke(TeacherProfile $teacher): Response|BinaryFileResponse
    {
        if (!$teacher->photo_path) {
            abort(404);
        }

        $disk = Storage::disk('public');

        try {
            if (!$disk->exists($teacher->photo_path)) {
                abort(404);
            }

            $fullPath = $disk->path($teacher->photo_path);
            if (!is_file($fullPath) || !is_readable($fullPath)) {
                abort(404);
            }

            return response()->file($fullPath, [
                'Cache-Control' => 'public, max-age=86400',
            ]);
        } catch (FileNotFoundException|Throwable) {
            abort(404);
        }
    }
}
