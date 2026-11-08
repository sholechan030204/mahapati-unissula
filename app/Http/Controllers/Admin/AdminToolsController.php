<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminToolsController extends Controller
{
    private const EXPORTS = [
        'members' => ['table' => 'members', 'filename' => 'anggota-mahapati.csv', 'columns' => ['full_name', 'alam_name', 'nra', 'nim', 'membership_type', 'status', 'position', 'address', 'phone']],
        'programs' => ['table' => 'work_programs', 'filename' => 'program-kerja-mahapati.csv', 'columns' => ['title', 'description', 'planned_start_date', 'planned_end_date', 'actual_start_date', 'actual_end_date', 'status']],
        'activities' => ['table' => 'activities', 'filename' => 'kegiatan-mahapati.csv', 'columns' => ['title', 'description', 'location', 'planned_at', 'realized_at', 'status']],
        'cash' => ['table' => 'cash_transactions', 'filename' => 'transaksi-kas-mahapati.csv', 'columns' => ['transaction_date', 'type', 'amount', 'description']],
        'news' => ['table' => 'articles', 'filename' => 'berita-mahapati.csv', 'columns' => ['title', 'slug', 'category', 'excerpt', 'body', 'status', 'published_at']],
        'achievements' => ['table' => 'achievements', 'filename' => 'prestasi-mahapati.csv', 'columns' => ['title', 'category', 'description', 'achievement_date', 'level', 'status']],
    ];

    public function auditIndex(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        ]);
        $logs = AuditLog::query()->with('user:id,name,email')
            ->when(($filters['q'] ?? '') !== '', function ($query) use ($filters): void {
                $term = '%'.trim($filters['q']).'%';
                $query->where(function ($search) use ($term): void {
                    $search->where('action', 'like', $term)->orWhere('url', 'like', $term)
                        ->orWhereHas('user', fn ($userQuery) => $userQuery->where('name', 'like', $term)->orWhere('email', 'like', $term));
                });
            })
            ->when(! empty($filters['date_from']), fn ($query) => $query->whereDate('created_at', '>=', $filters['date_from']))
            ->when(! empty($filters['date_to']), fn ($query) => $query->whereDate('created_at', '<=', $filters['date_to']))
            ->latest()->paginate(20)->withQueryString();

        return view('admin.tools.audit', ['logs' => $logs, 'filters' => $filters]);
    }

    public function backupIndex(): View
    {
        $counts = [];
        foreach (self::EXPORTS as $key => $export) {
            $counts[$key] = Schema::hasTable($export['table']) ? DB::table($export['table'])->count() : 0;
        }

        return view('admin.tools.backup', ['exports' => self::EXPORTS, 'counts' => $counts]);
    }

    public function downloadBackup(): StreamedResponse
    {
        $snapshot = [
            'format' => 'mahapati-database-backup',
            'version' => 1,
            'created_at' => now()->toIso8601String(),
            'tables' => [],
        ];
        foreach ($this->backupTables() as $table) {
            $snapshot['tables'][$table] = DB::table($table)->get()->map(fn (object $row): array => (array) $row)->all();
        }
        $content = json_encode($snapshot, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);

        return response()->streamDownload(static function () use ($content): void {
            echo $content;
        }, 'mahapati-backup-'.now()->format('Y-m-d-His').'.json', ['Content-Type' => 'application/json; charset=UTF-8']);
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $dataset = $request->validate(['dataset' => ['required', Rule::in(array_keys(self::EXPORTS))]])['dataset'];
        $export = self::EXPORTS[$dataset];
        $columns = array_values(array_filter($export['columns'], fn (string $column): bool => Schema::hasColumn($export['table'], $column)));

        return response()->streamDownload(function () use ($export, $columns): void {
            $output = fopen('php://output', 'wb');
            fwrite($output, "\xEF\xBB\xBF");
            fputcsv($output, $columns);
            foreach (DB::table($export['table'])->select($columns)->orderBy('id')->cursor() as $row) {
                fputcsv($output, array_map(static function (string $column) use ($row): mixed {
                    $value = $row->{$column};
                    if (is_string($value) && preg_match('/^[\s]*[=+\-@]/u', $value) === 1) {
                        return "'".$value;
                    }

                    return $value;
                }, $columns));
            }
            fclose($output);
        }, $export['filename'], ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function restoreBackup(Request $request): RedirectResponse
    {
        $validated = $request->validate(['backup_file' => ['required', 'file', 'max:51200']]);
        try {
            $snapshot = json_decode($validated['backup_file']->get(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return back()->withErrors(['backup_file' => 'Berkas bukan JSON cadangan MAHAPATI yang valid.']);
        }

        $tables = $this->backupTables();
        if (! is_array($snapshot) || ($snapshot['format'] ?? null) !== 'mahapati-database-backup' || ($snapshot['version'] ?? null) !== 1 || ! is_array($snapshot['tables'] ?? null) || array_diff($tables, array_keys($snapshot['tables'])) !== [] || array_diff(array_keys($snapshot['tables']), $tables) !== []) {
            return back()->withErrors(['backup_file' => 'Isi berkas tidak cocok dengan format cadangan MAHAPATI atau skemanya berbeda.']);
        }
        foreach ($snapshot['tables'] as $table => $rows) {
            if (! is_array($rows) || ! array_is_list($rows)) {
                return back()->withErrors(['backup_file' => 'Struktur data cadangan tidak valid.']);
            }
            $allowedColumns = Schema::getColumnListing($table);
            foreach ($rows as $row) {
                if (! is_array($row) || array_diff(array_keys($row), $allowedColumns) !== []) {
                    return back()->withErrors(['backup_file' => 'Berkas dibuat dari versi struktur data yang berbeda.']);
                }
            }
        }

        Schema::disableForeignKeyConstraints();
        try {
            DB::transaction(function () use ($tables, $snapshot): void {
                foreach (array_reverse($tables) as $table) {
                    DB::table($table)->delete();
                }
                foreach ($tables as $table) {
                    foreach (array_chunk($snapshot['tables'][$table], 250) as $rows) {
                        if ($rows !== []) {
                            DB::table($table)->insert($rows);
                        }
                    }
                }
            });
        } finally {
            Schema::enableForeignKeyConstraints();
        }

        return redirect()->route('admin.backup.index')->with('status', 'Pemulihan selesai. Data aplikasi sekarang mengikuti cadangan yang dipilih.');
    }

    public function settings(): View
    {
        $saved = SiteSetting::values();
        $defaults = config('public_contact');
        $socialLinks = collect($defaults['social_links'])->keyBy('name');

        return view('admin.tools.settings', [
            'settings' => [
                'contact_email' => $saved['contact_email'] ?? $defaults['email'],
                'contact_whatsapp' => $saved['contact_whatsapp'] ?? '',
                'instagram_url' => $saved['instagram_url'] ?? $socialLinks->get('Instagram')['url'],
                'facebook_url' => $saved['facebook_url'] ?? $socialLinks->get('Facebook')['url'],
                'youtube_url' => $saved['youtube_url'] ?? $socialLinks->get('YouTube')['url'],
                'tiktok_url' => $saved['tiktok_url'] ?? $socialLinks->get('TikTok')['url'],
            ],
        ]);
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'contact_email' => ['required', 'email', 'max:255'],
            'contact_whatsapp' => ['nullable', 'string', 'max:30'],
            'instagram_url' => ['required', 'url', 'max:2048'],
            'facebook_url' => ['required', 'url', 'max:2048'],
            'youtube_url' => ['required', 'url', 'max:2048'],
            'tiktok_url' => ['required', 'url', 'max:2048'],
        ]);
        SiteSetting::saveValues($validated);

        return back()->with('status', 'Pengaturan kontak publik berhasil disimpan.');
    }

    public function profile(Request $request): View
    {
        return view('admin.tools.profile', ['user' => $request->user()]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'current_password' => ['nullable', 'required_with:password', 'current_password'],
            'password' => ['nullable', 'confirmed', 'min:12'],
        ]);
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        if (filled($validated['password'] ?? null)) {
            $user->password = Hash::make($validated['password']);
        }
        $user->save();

        return back()->with('status', 'Profil Admin berhasil diperbarui.');
    }

    /** @return array<int, string> */
    private function backupTables(): array
    {
        return array_values(array_filter([
            'users', 'members', 'member_change_logs', 'work_programs', 'activities', 'activity_member',
            'activity_change_requests', 'gallery_items', 'gallery_change_requests', 'cash_transactions',
            'articles', 'achievements', 'registration_settings', 'registration_applicants', 'certificates',
            'notifications', 'account_requests', 'audit_logs', 'site_settings',
        ], static fn (string $table): bool => Schema::hasTable($table)));
    }
}
