<?php

namespace App\Http\Controllers;

use App\Models\TeacherProfile;
use Illuminate\Http\Response;

class TeacherPhotoController extends Controller
{
    public function __invoke(TeacherProfile $teacher): Response
    {
        if (!$teacher->photo_path) {
            abort(404);
        }

        $fullPath = storage_path('app/public/'.$teacher->photo_path);
        if (!is_file($fullPath)) {
            abort(404);
        }

        return response()->file($fullPath, [
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}

