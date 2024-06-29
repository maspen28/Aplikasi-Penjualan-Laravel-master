<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckPrivileges {
  public function handle(Request $request, Closure $next, $privilege) {
    // Ambil id_privileges dari user yang sedang login
    $idPrivileges = Auth::user()->id_privileges;

    // Pemeriksaan apakah user memiliki hak akses yang sesuai
    if ($idPrivileges != $privilege) {
      abort(403, 'Unauthorized action.');
    }

    return $next($request);
  }
}
