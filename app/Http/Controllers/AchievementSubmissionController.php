<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use App\Support\AchievementInput;
use App\Support\DashboardNotifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AchievementSubmissionController extends Controller
{
    public function __construct(private AchievementInput $achievementInput) {}

    public function index(Request $request): View
    {
        $routePrefix = $request->user()->role === 'pengurus' ? 'pengurus' : 'member';

        return view('admin.achievements.submissions', [
            'achievements' => Achievement::query()->where('created_by', $request->user()->id)->latest()->paginate(10),
            'routePrefix' => $routePrefix,
        ]);
    }

    public function create(Request $request): View
    {
        $routePrefix = $request->user()->role === 'pengurus' ? 'pengurus' : 'member';

        return view('admin.achievements.form', [
            'achievement' => new Achievement(['status' => Achievement::STATUS_PENDING]),
            'isEditing' => false,
            'isSubmission' => true,
            'routePrefix' => $routePrefix,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->achievementInput->validateAndStore($request);
        $validated['status'] = Achievement::STATUS_PENDING;
        $validated['created_by'] = $request->user()->id;
        $validated['published_at'] = null;
        Achievement::query()->create($validated);
        DashboardNotifier::admins('Prestasi menunggu persetujuan', $validated['title'].' dikirim untuk ditinjau.', route('admin.achievements.index', ['status' => 'pending']));
        $routePrefix = $request->user()->role === 'pengurus' ? 'pengurus' : 'member';
        $destination = $routePrefix === 'pengurus' ? 'pengurus.achievements.index' : 'member.achievement-submissions.index';

        return redirect()->route($destination)->with('status', 'Usulan prestasi terkirim dan menunggu persetujuan Admin.');
    }
}
