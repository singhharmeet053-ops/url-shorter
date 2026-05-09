<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\MemberController;


Route::get('/', [FrontController::class, 'Login'])->name('login');
Route::post('/loggedin', [FrontController::class, 'Loggedin'])->name('loggedin');
Route::post('logout', [FrontController::class, 'Logout'])->name('logout');

/* Super admin middleware start */
Route::middleware(['superadmin'])->prefix('superadmin')->group(function () {
    Route::get('/dashboard', [SuperAdminController::class, 'superAdminDashboard'])->name('superadmin.dashboard');
    Route::get('/client_invitation', [SuperAdminController::class, 'clientInvitation'])->name('superadmin.client_invitation');
    Route::get('/generated_urls', [SuperAdminController::class, 'generatedUrls'])->name('superadmin.generated_urls');
    Route::get('/client_view_data', [SuperAdminController::class, 'clientViewData'])->name('superadmin.client_view_data');
    
    Route::post('/invited_new_client', [SuperAdminController::class, 'invitedNewClient'])->name('superadmin.invited_new_client');

    Route::get('/superadmin/download_csv', [SuperAdminController::class, 'downloadCsv'])
    ->name('superadmin.download_csv');
});

/* Superadmin End */

/* Admin middleware start */
Route::middleware(['admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'adminDashboard'])->name('admin.dashboard');
    Route::get('/generate_short_url', [AdminController::class, 'generateShortUrl'])->name('admin.generate_short_url');

    Route::get('/short_urls', [AdminController::class, 'ShortUrl'])->name('admin.short_urls');
    Route::get('/team_member', [AdminController::class, 'TeamMember'])->name('admin.team_member');
    Route::get('/invite_team_member', [AdminController::class, 'InviteTeamMember'])->name('admin.invite_team_member');

    Route::post('save_invited_member', [AdminController::class, 'saveInvitedMember'])->name('admin.save_invited_member');

    Route::post('/generate_url', [AdminController::class, 'generatedUrl'])->name('admin.generate_url');

    Route::get('/{code}',[AdminController::class, 'redirectUrl']);

    Route::get('/admin/download_csv', [AdminController::class, 'downloadCsv'])
    ->name('admin.download_csv');
    
});

/* Admin End */

/* Member middleware start */
Route::middleware(['member'])->prefix('member')->group(function () {
    Route::get('/dashboard', [MemberController::class, 'memberDashboard'])->name('member.dashboard');
    Route::get('/generate_short_url', [MemberController::class, 'generateShortUrl'])->name('member.generate_short_url');
    Route::post('/generate_url', [MemberController::class, 'generatedUrl'])->name('member.generate_url');

    Route::get('/{code}',[MemberController::class, 'redirectUrl']);

    Route::get('/member/download_csv', [MemberController::class, 'downloadCsv'])
    ->name('member.download_csv');
});

/* Member End */