<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(auth()->check() && auth()->user()->hasAnyRole(['root', 'admin']), 403);

        $query = AuditLog::with('user')->latest();

        if ($action = $request->input('action')) {
            $query->where('action', 'like', $action . '%');
        }
        if ($group = $request->input('group')) {
            $query->where('group_name', $group);
        }
        if ($search = $request->input('search')) {
            $query->where(function ($builder) use ($search) {
                $builder->where('setting_key', 'like', '%' . $search . '%')
                    ->orWhere('action', 'like', '%' . $search . '%');
            });
        }

        $logs = $query->paginate(40)->withQueryString();
        $groups = AuditLog::query()->whereNotNull('group_name')->distinct()->orderBy('group_name')->pluck('group_name');

        return view('integrations.audit-logs', compact('logs', 'groups'));
    }
}
