<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\CrudController;
use App\Http\Controllers\Admin\IconController;
use App\Http\Controllers\Admin\ItemController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\SliderController;
use App\Http\Controllers\Admin\EnquiryController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\JobcardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Admin\ActivityController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\ExpenseController;
use App\Http\Controllers\Admin\ExtraActivityController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MaintenanceController;
use App\Http\Controllers\Admin\SocietySetupController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\RazorpayPaymentController;
use App\Http\Controllers\Admin\ItemcategoryController;

//End of use statements

Route::middleware(['auth', 'admin', 'preventBackHistory'])->group(function () {
    Route::prefix('admin')->name('admin.')->group(function () {
     

        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
        Route::get('dashboard/flats/{flat}', [DashboardController::class, 'flatProfile'])->name('dashboard.flats.show');

        Route::get('profile', [ProfileController::class, 'show'])->name('profile.show');



        // Society expenses by month/year
        Route::get('expenses', [ExpenseController::class, 'index'])->name('expenses.index');
        Route::post('expenses', [ExpenseController::class, 'store'])->name('expenses.store');
        Route::get('expenses/{expense}', [ExpenseController::class, 'show'])->name('expenses.show');
        Route::delete('expenses/{expense}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');
        Route::post('expenses/{expense}/items', [ExpenseController::class, 'storeItem'])->name('expenses.items.store');
        Route::delete('expenses/{expense}/items/{item}', [ExpenseController::class, 'destroyItem'])->name('expenses.items.destroy');

        // Maintenance collection by month/year
        Route::get('maintenance', [MaintenanceController::class, 'index'])->name('maintenance.index');
        Route::post('maintenance', [MaintenanceController::class, 'store'])->name('maintenance.store');
        Route::get('maintenance/{maintenance}/pdf/{list}', [MaintenanceController::class, 'downloadListPdf'])
            ->where('list', 'paid|unpaid')
            ->name('maintenance.pdf-list');
        Route::get('maintenance/{maintenance}', [MaintenanceController::class, 'show'])->name('maintenance.show');
        Route::delete('maintenance/{maintenance}', [MaintenanceController::class, 'destroy'])->name('maintenance.destroy');
        Route::patch('maintenance/{maintenance}/payments/{payment}', [MaintenanceController::class, 'updateStatus'])->name('maintenance.payments.update');

        // Extra activity collections, not tied to month/year
        Route::get('extra-activities', [ExtraActivityController::class, 'index'])->name('extra-activities.index');
        Route::post('extra-activities', [ExtraActivityController::class, 'store'])->name('extra-activities.store');
        Route::get('extra-activities/{extraActivity}/pdf/{list}', [ExtraActivityController::class, 'downloadListPdf'])
            ->where('list', 'paid|unpaid')
            ->name('extra-activities.pdf-list');
        Route::get('extra-activities/{extraActivity}', [ExtraActivityController::class, 'show'])->name('extra-activities.show');
        Route::delete('extra-activities/{extraActivity}', [ExtraActivityController::class, 'destroy'])->name('extra-activities.destroy');
        Route::patch('extra-activities/{extraActivity}/payments/{payment}', [ExtraActivityController::class, 'updateStatus'])->name('extra-activities.payments.update');

        // Society structure (wings → floors → flats), scoped per user
        Route::get('society-setup/wings', [SocietySetupController::class, 'wings'])->name('society-setup.wings');
        Route::post('society-setup/wings', [SocietySetupController::class, 'storeWings'])->name('society-setup.wings.store');
        Route::post('society-setup/flat-unit/{flat}', [SocietySetupController::class, 'updateFlatDetail'])->name('society-setup.flat-unit.update');
        Route::get('society-setup/flats', [SocietySetupController::class, 'flats'])->name('society-setup.flats');
        Route::post('society-setup/flats/generate', [SocietySetupController::class, 'generateFlats'])->name('society-setup.flats.generate');

        //Roles
        Route::resource('roles', RoleController::class);
        Route::post('roles/data', [RoleController::class, 'data'])->name('roles.data');
        Route::post('roles/list',[RoleController::class, 'list'])->name('roles.list');

        //Permissions
        Route::get('roles/{role}/permission/show',[RoleController::class, 'permissionsShow'])->name('roles.permissions.show');
        Route::post('roles/{role}/permission/update',[RoleController::class, 'permissionsUpdate'])->name('roles.permissions.update');

        //Users
        Route::resource('users', UserController::class);
        Route::post('users/data', [UserController::class, 'data'])->name('users.data');
        Route::post('users/list',[UserController::class, 'list'])->name('users.list');
        Route::post('users/change-status',[UserController::class, 'changeStatus'])->name('users.change.status');

        //Employees
        Route::resource('employees', EmployeeController::class);
        Route::post('employees/data', [EmployeeController::class, 'data'])->name('employees.data');
        Route::post('employees/list',[EmployeeController::class, 'list'])->name('employees.list');
        Route::post('employees/change-status',[EmployeeController::class, 'changeStatus'])->name('employees.change.status');


        // ---------------------------------------------------------------
        // Laravel V3 updated routes ::
        // ---------------------------------------------------------------

        //Enquiries
        Route::resource('enquiries', EnquiryController::class);
        Route::post('enquiries/data', [EnquiryController::class, 'data'])->name('enquiries.data');
        Route::post('enquiries/list',[EnquiryController::class, 'list'])->name('enquiries.list');

        // Slider
        Route::resource('sliders', SliderController::class);
        Route::post('sliders/data', [SliderController::class, 'data'])->name('sliders.data');
        Route::post('sliders/list',[SliderController::class, 'list'])->name('sliders.list');
        Route::post('sliders/change-status',[SliderController::class, 'changeStatus'])->name('sliders.change.status');

 
        // Setting
        Route::resource('settings', SettingController::class);
        Route::post('settings/data', [SettingController::class, 'data'])->name('settings.data');
        Route::post('settings/list',[SettingController::class, 'list'])->name('settings.list');
        Route::post('settings/change-status',[SettingController::class, 'changeStatus'])->name('settings.change.status');
        Route::post('settings/get/data', [SettingController::class, 'data'])->name('settings.get.data');
        Route::post('settings/get/data/page', [SettingController::class, 'getDataPage'])->name('settings.get.data.page');
        Route::post('settings/update/data/page', [SettingController::class, 'updateDataPage'])->name('settings.update.data.page'); 
                           
  

         // Activity Logs
        Route::resource('activities', ActivityController::class); 
        Route::post('activities/data', [ActivityController::class, 'data'])->name('activities.data'); 
        Route::post('activities/list', [ActivityController::class, 'list'])->name('activities.list'); 


        //End of File
    });
});