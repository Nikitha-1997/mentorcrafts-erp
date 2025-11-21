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
use App\Http\Controllers\Admin\CustomerServiceCostController;
use App\Http\Controllers\Admin\CustomerProjectDetailController;
use App\Http\Controllers\Admin\CustomerProfileController;
use App\Http\Controllers\Admin\ProjectController;

Route::get('/', function () {
    return view('welcome');
});

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

// lead from customer
Route::post('/customers/{customer}/request-service', [CustomerController::class, 'requestNewService'])
    ->name('customers.request-service')
    ->middleware(['auth','role:Super Admin']);  

Route::middleware(['auth', 'role:Super Admin'])->group(function () {
    Route::get('/customers/{customer}/services/{service}', [CustomerController::class, 'showService'])
        ->name('customers.services.show');

    Route::get('services/data', [ServiceController::class, 'getData'])->name('services.data');
    Route::resource('services', ServiceController::class);
    Route::get('/leads/data', [LeadController::class, 'getData'])->name('leads.data');
    Route::get('/admin/services/{service}/costs', [ServiceController::class, 'getServiceCosts']);

    Route::get('/customers/{customer}/services/{service}/profile', [CustomerController::class, 'companyProfile'])
        ->name('customers.company_profile');

    Route::post('/leads/{lead}/convert', [LeadController::class, 'convertToCustomer'])
        ->name('leads.convert')
        ->middleware(['auth']);
    Route::post('/lead/{lead}/project-create', [ProjectController::class, 'createFromLead'])
    ->name('projects.fromLead')
    ->middleware('auth');
});

Route::prefix('')->middleware(['auth','role:Super Admin'])->group(function () {
    Route::resource('leads', LeadController::class);
    Route::post('/leads/service-costs', [LeadController::class, 'fetchServiceCosts'])->name('leads.service-costs');
    Route::get('/services/{id}/costs', [ServiceController::class, 'getServiceCosts'])->name('services.costs');
    Route::get('/leads/{lead}/service/{service}/costs', [LeadController::class, 'getServiceCosts']);
});

Route::prefix('')->middleware(['auth', 'role:Super Admin'])->group(function () {
    Route::get('service-costs', [CustomerServiceCostController::class, 'index'])
        ->name('service-costs.index');

    Route::post('service-costs/{id}/approve', [CustomerServiceCostController::class, 'approve'])
        ->name('service-costs.approve');
});

// Customer Routes
Route::prefix('')->middleware(['auth'])->group(function () {
    Route::get('/customers/data', [CustomerController::class, 'getData'])->name('customers.data');
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
    Route::get('/customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
    Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
    Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');
});

// Followup Routes
Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::post('/followups', [FollowupController::class, 'store'])->name('followups.store');
    Route::get('/followups', [FollowupController::class, 'index'])->name('followups.index');
    Route::delete('/followups/{id}', [FollowupController::class, 'destroy'])->name('followups.destroy');
});

// Customer Project Details
Route::middleware('auth')->group(function () {
    Route::resource('customer-project-details', CustomerProjectDetailController::class);
});
Route::get('/projects/{project}/manage', [ProjectController::class, 'manage'])
    ->name('projects.manage');



// ✅ Added CustomerProfileController Routes
Route::prefix('customers')->middleware(['auth'])->group(function () {
    Route::get('{customer}/services/{service}/profile', [CustomerProfileController::class, 'show'])
        ->name('customer.profile.show');

    Route::post('{customer}/services/{service}/profile/update', [CustomerProfileController::class, 'update'])
        ->name('customer.profile.update');
        

});
Route::post('/save-remark', [CustomerProfileController::class, 'saveRemark'])->name('save.remark');
Route::post('/update-remark', [CustomerProfileController::class, 'updateRemark'])->name('update.remark');




require __DIR__.'/auth.php';
