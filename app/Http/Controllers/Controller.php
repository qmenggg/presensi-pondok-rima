<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * Base Controller with role-based permission helpers
 */
abstract class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Role definitions for each feature
     * Note: pengasuh hanya bisa read saja di semua fitur
     */
    protected $rolePermissions = [
        // Feature => [roles that can access]
        'santri.read' => ['admin', 'pengasuh', 'pengurus'],
        'santri.write' => ['admin'],
        'kamar.read' => ['admin', 'pengasuh', 'pengurus'],
        'kamar.write' => ['admin'],
        'user.read' => ['admin', 'pengasuh'],
        'user.write' => ['admin'],
        'kegiatan.read' => ['admin', 'pengasuh', 'pengurus', 'asatid'],
        'kegiatan.write' => ['admin'],
        'tapel.read' => ['admin', 'pengasuh'],
        'tapel.write' => ['admin'],
        'izin.read' => ['admin', 'pengasuh', 'pengurus', 'asatid'],
        'izin.write' => ['admin', 'pengurus'],
        'izin.approve' => ['admin'],
        'laporan.read' => ['admin', 'pengasuh'],
        'laporan.export' => ['admin', 'pengasuh'],
        'rekap.read' => ['admin', 'pengasuh', 'pengurus', 'asatid'],
        'rekap.write' => ['admin', 'pengurus', 'asatid'],
        'rekap.approve' => ['admin'],
        'absensi.read' => ['admin', 'pengasuh', 'pengurus', 'asatid'],
        'absensi.write' => ['admin', 'pengurus', 'asatid'],
    ];

    /**
     * Check if current user has permission for a feature
     * 
     * @param string $permission Permission key (e.g., 'santri.write')
     * @return bool
     */
    protected function hasPermission(string $permission): bool
    {
        $user = auth()->user();
        
        if (!$user) {
            return false;
        }

        // Admin always has access
        if ($user->role === 'admin') {
            return true;
        }

        $allowedRoles = $this->rolePermissions[$permission] ?? [];
        return in_array($user->role, $allowedRoles);
    }

    /**
     * Abort if user doesn't have permission
     * 
     * @param string $permission Permission key
     * @param string $message Custom error message
     */
    protected function requirePermission(string $permission, string $message = null): void
    {
        if (!$this->hasPermission($permission)) {
            abort(403, $message ?? 'Anda tidak memiliki akses untuk fitur ini.');
        }
    }

    /**
     * Get current user's role
     */
    protected function getUserRole(): ?string
    {
        return auth()->user()?->role;
    }

    /**
     * Check if current user is admin
     */
    protected function isAdmin(): bool
    {
        return $this->getUserRole() === 'admin';
    }

    /**
     * Check if current user is pengasuh
     */
    protected function isPengasuh(): bool
    {
        return $this->getUserRole() === 'pengasuh';
    }

    /**
     * Check if current user is pengurus
     */
    protected function isPengurus(): bool
    {
        return $this->getUserRole() === 'pengurus';
    }

    /**
     * Check if current user is asatid
     */
    protected function isAsatid(): bool
    {
        return $this->getUserRole() === 'asatid';
    }
}
