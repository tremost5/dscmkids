<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivityLog;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(): View
    {
        $logs = AdminActivityLog::with('user')
            ->latest()
            ->paginate(100);

        return view('admin.audit.index', compact('logs'));
    }
}