<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RecordAdminAuditLog
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $routeName = $request->route()?->getName();
        $isMutation = in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'], true);
        $isDataDownload = in_array($routeName, ['admin.backup.download', 'admin.backup.csv'], true);
        if (! $request->user()?->isAdmin() || (! $isMutation && ! $isDataDownload) || $response->getStatusCode() >= 400) {
            return $response;
        }

        $action = $this->describeAction($routeName, $request->method());

        AuditLog::query()->create([
            'user_id' => $request->user()->id,
            'action' => $action,
            'route_name' => $routeName,
            'http_method' => $request->method(),
            'url' => '/'.ltrim($request->path(), '/'),
            'status_code' => $response->getStatusCode(),
            'ip_address' => $request->ip(),
            'user_agent' => mb_substr((string) $request->userAgent(), 0, 1000),
        ]);

        return $response;
    }

    private function describeAction(?string $routeName, string $method): string
    {
        $name = $routeName ?? 'admin.unknown';
        $subject = match (true) {
            str_contains($name, 'members') || str_contains($name, 'domiciles') => 'data anggota',
            str_contains($name, 'cash-transactions') => 'transaksi kas',
            str_contains($name, 'work-programs') => 'program kerja',
            str_contains($name, 'activities') => 'kegiatan',
            str_contains($name, 'gallery') => 'galeri',
            str_contains($name, 'news') => 'berita',
            str_contains($name, 'achievements') => 'prestasi',
            str_contains($name, 'registration') || str_contains($name, 'account-requests') => 'pendaftaran atau pengajuan akun',
            str_contains($name, 'certificates') => 'sertifikat',
            str_contains($name, 'settings') => 'pengaturan website',
            str_contains($name, 'profile') => 'profil Admin',
            str_contains($name, 'backup') => 'cadangan atau ekspor data',
            default => 'data MAHAPATI',
        };

        if ($method === 'GET') {
            return str_contains($name, '.csv') ? 'Mengunduh ekspor CSV '.$subject : 'Mengunduh cadangan '.$subject;
        }
        $verb = match ($method) {
            'POST' => str_contains($name, 'approve') || str_contains($name, 'setujui') ? 'Menyetujui' : (str_contains($name, 'reject') || str_contains($name, 'tolak') ? 'Menolak' : (str_contains($name, 'restore') ? 'Memulihkan' : 'Menambahkan atau memproses')),
            'PUT', 'PATCH' => 'Memperbarui',
            'DELETE' => 'Menghapus',
            default => 'Mengubah',
        };

        return mb_substr($verb.' '.$subject, 0, 120);
    }
}
