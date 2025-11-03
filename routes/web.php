<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\LeadController;
use App\Http\Controllers\Admin\FollowupController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\CustomerController;



Route::get('/', function () {
    return view('welcome');
});
/*Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');*/
Route::get('/dashboard', function () {
    /** @var \App\Models\User|\Spatie\Permission\Traits\HasRoles $user */
    $user = Auth::user();

    if ($user && $user->hasRole('Super Admin')) {
        return redirect()->route('admin.dashboard');
    } elseif ($user && $user->hasRole('employee')) {
        return redirect()->route('employee.dashboard');
    }

    return redirect('/');
})->middleware('auth')->name('dashboard');




Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
/*Route::middleware(['auth','role:Super Admin|HR'])->group(function(){
    Route::resource('users', UserController::class);
});*/
Route::middleware(['auth', 'role:Super Admin'])->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'admin'])->name('admin.dashboard');
});
Route::middleware(['auth', 'role:employee'])->group(function() {
    Route::get('/employee/dashboard', [DashboardController::class, 'employee'])->name('employee.dashboard');
});

Route::middleware('auth')->group(function(){
    Route::resource('users', UserController::class);
    Route::get('settings/data', [SettingController::class, 'getData'])->name('settings.data');
     Route::resource('settings', SettingController::class);
});

Route::middleware(['auth','role:Super Admin'])->group(function(){ 
    Route::resource('roles', RoleController::class);
    Route::resource('permissions', App\Http\Controllers\Admin\PermissionController::class)->except(['show']);
});
Route::middleware(['auth', 'role:Super Admin'])->group(function () {
    Route::get('services/data', [ServiceController::class, 'getData'])->name('services.data');
    Route::resource('services', ServiceController::class);
    Route::get('/leads/data', [LeadController::class, 'getData'])->name('leads.data');

    Route::get('/admin/services/{service}/costs', [ServiceController::class, 'getServiceCosts']);

    // AJAX: fetch costs for a set of services
/*Route::post('/admin/leads/service-costs', [LeadController::class, 'fetchServiceCosts'])
    ->name('leads.service-costs');
   // Route::get('/services/{id}/costs', [ServiceController::class, 'getServiceCosts'])->name('services.costs');
Route::get('/services/{id}/costs', [ServiceController::class, 'getServiceCosts'])
    ->name('services.costs');

      Route::resource('leads', LeadController::class);*/
      // routes/web.php
Route::post('/leads/{lead}/convert', [LeadController::class, 'convertToCustomer'])
    ->name('leads.convert')
    ->middleware(['auth']); // add role middleware if needed


    //Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    //Route::get('/customers/{id}', [CustomerController::class, 'show'])->name('customers.show');

});
Route::prefix('')->middleware(['auth','role:Super Admin'])->group(function () {
    Route::resource('leads', LeadController::class);
    Route::post('/leads/service-costs', [LeadController::class, 'fetchServiceCosts'])->name('leads.service-costs');
    Route::get('/services/{id}/costs', [ServiceController::class, 'getServiceCosts'])->name('services.costs');
    Route::get('/leads/{lead}/service/{service}/costs', [LeadController::class, 'getServiceCosts']);

});

// Customer Routes
Route::prefix('')->middleware(['auth'])->group(function () {
    
    Route::get('/customers/data', [App\Http\Controllers\Admin\CustomerController::class, 'getData'])->name('customers.data');

    // Customer listing
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');

    // Show customer details
    Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');

    // Edit customer
    Route::get('/customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit');

    // Update customer
    Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');

    // Soft delete customer
    Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');
});


Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::post('/followups', [FollowupController::class, 'store'])->name('followups.store');
    Route::get('/followups', [FollowupController::class, 'index'])->name('followups.index');
    Route::delete('/followups/{id}', [FollowupController::class, 'destroy'])->name('followups.destroy');
});


require __DIR__.'/auth.php';
