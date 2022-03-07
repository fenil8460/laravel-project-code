<?php

use App\Controllers\Auth\SocialAuthLoginController;
use App\Controllers\CompanyController;
use App\Controllers\ContactController;
use App\Controllers\MessageController;
use App\Controllers\MessageTemplateController;
use App\Controllers\OrderController;
use App\Controllers\GroupController;
use App\Controllers\RegisterController;
use App\Controllers\WalletController;
use App\Controllers\SubscriptionController;
use App\Controllers\GroupContactController;
use App\Controllers\PermissionController;
use App\Controllers\AdminController;
use App\Controllers\ActivitiesController;
use App\Controllers\UserController;
use App\Controllers\WebhookController;
use App\Controllers\SafeMemoController;
use App\Controllers\TestController;
use App\Controllers\DeveloperApiController;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::get('/test1',[TestController::class,'test']);
Route::post('register', [RegisterController::class ,'register' ]);
Route::post('login', [RegisterController::class ,'login' ]);

Route::get('auth/{provider}', [SocialAuthLoginController::class , 'redirect']);
Route::get('auth/{provider}/callback', [SocialAuthLoginController::class , 'handleCallback']);
Route::post('social-login',  [RegisterController::class , 'socialLogin']);
Route::post('reset_password',[RegisterController::class ,'validatePasswordRequest' ]);
Route::post('reset_password_with_token', [RegisterController::class ,'resetPassword' ]);

Route::get('v1/accept-invite/{client_id}', [UserController::class ,'acceptInvitation']);
Route::get('v1/decline-invite/{client_id}', [UserController::class ,'declineInvitation']);
Route::post('v1/register-client-as-user', [UserController::class ,'registerClientAsUser']);
Route::get('v1/get-user-from-client/{client_id}', [UserController::class ,'getUserFromClient']);


Route::prefix('v1/admin')->group( function () {
    Route::post('/login', [RegisterController::class ,'adminLogin' ]);
    Route::post('/admin-dashboard', [RegisterController::class ,'adminBackDashboard' ]);
});

Route::middleware(['auth:admin'])->prefix('v1/admin')->group( function () {

    //  users
    Route::get('/users',[AdminController::class,'allUsers'])->middleware(['permission:view-users']);
    Route::get('/users/ban/{user_id}',[AdminController::class,'banUser'])->middleware(['permission:create-user']);
    Route::get('/users/activate/{user_id}',[AdminController::class,'activateUser'])->middleware(['permission:create-user']);
    Route::get('/admin-users', [AdminController::class,'viewAdminUsers'])->middleware(['permission:view-users']);
    Route::post('/admin-users', [AdminController::class,'createAdminUser'])->middleware(['permission:create-user']);
    Route::get('/admin-users/{id}', [AdminController::class,'findAdminUser'])->middleware(['permission:view-users']);
    Route::put('/admin-users/{id}', [AdminController::class,'updateAdminUser'])->middleware(['permission:create-user']);
    Route::delete('/admin-users/{id}', [AdminController::class,'deleteAdminUser'])->middleware(['permission:create-user']);
    Route::get('/admin-users-permissions', [AdminController::class,'viewAdminPermissions']);
    Route::post('/users-login', [RegisterController::class,'userLoginByAdmin']);
    Route::get('/users/{id}', [AdminController::class,'getUserByAdmin'])->middleware(['permission:view-users']);

    //companies
    Route::get('/all-companies', [CompanyController::class , 'getCompany'])->middleware(['permission:manage-company']);
    Route::get('/companies/{id}', [CompanyController::class , 'getCompanyByUser'])->middleware(['permission:manage-company']);

    //group-contact
        Route::get('/contacts',[ContactController::class,'getContactLists'])->middleware(['permission:manage-contact']);
        Route::get('/contacts/{id}',[ContactController::class,'getContactByCompany']);
        Route::get('/groups',[GroupController::class,'getGroups']);
        Route::get('/group-contacts',[GroupContactController::class,'getGroupContacts']);
        Route::get('/group-contacts-company',[GroupContactController::class,'getGroupContactsCompany']);
        Route::get('/group-contacts-company/{id}',[GroupContactController::class,'getGroupContactsByCompany']);

    //wallet
    Route::prefix('wallet')->middleware(['permission:wallet-manage'])->group( function () {
        Route::post('/deposit', [AdminController::class, 'depositAmountToWallet']);
        Route::post('/withdraw', [AdminController::class, 'withdrawAmountFromWallet']);
    });

    // subscriptions
    Route::prefix('subscription')->group( function () {
        Route::post('/plan', [SubscriptionController::class, 'createPlan']);
        Route::post('/plan-feature', [SubscriptionController::class, 'addPlanFeature']);
        Route::get('/plan', [SubscriptionController::class, 'getAllPlans']);
        Route::get('/plan/{id}', [SubscriptionController::class, 'findPlan']);
        Route::post('/', [SubscriptionController::class, 'createSubscriptionByAdmin']);
        Route::get('/', [SubscriptionController::class, 'getAllCompanySubscriptionsByAdmin']);
        Route::get('/company-subscriptions', [SubscriptionController::class, 'getCompanySubscriptionsByAdmin']);
        Route::put('/plan/{id}',[SubscriptionController::class , 'updatePlan']);
        Route::delete('/plan/{id}',[SubscriptionController::class , 'destroyPlan']);
        Route::get('/plan-feature', [SubscriptionController::class, 'getAllplanFeature']);
        Route::get('/plan-feature/{id}', [SubscriptionController::class, 'findplanFeature']);
        Route::put('/plan-feature/{id}',[SubscriptionController::class , 'updateplanFeature']);
        Route::delete('/plan-feature/{id}',[SubscriptionController::class , 'destroyplanFeature']);
    });

    // Roles and Permissions
    Route::prefix('permissions')->middleware(['role:super_admin'])->group( function () {
        Route::post('/create', [PermissionController::class, 'createPermission']);
        Route::get('/', [PermissionController::class, 'getPermissions']);
        Route::get('/{id}', [PermissionController::class, 'findPermission']);
        Route::put('/{id}', [PermissionController::class, 'updatePermission']);
        Route::delete('/{id}', [PermissionController::class, 'deletePermission']);
        Route::post('/assign-many', [PermissionController::class, 'assignPermissionstoRole']);
        Route::post('/assign-single', [PermissionController::class, 'assignPermissiontoRole']);
        Route::get('/assign-all-permissions/{role_id}', [PermissionController::class, 'assignAllPermissionsToRole']);
        Route::post('/revoke-many', [PermissionController::class, 'removePermissionsFromRoles']);
        Route::post('/revoke-single', [PermissionController::class, 'removePermissionFromRoles']);
        Route::get('/revoke-all-permissions/{role_id}', [PermissionController::class, 'revokeAllPermissionsFromRole']);
    });

    Route::prefix('roles')->middleware(['role:super_admin'])->group( function () {
        Route::post('/create', [PermissionController::class, 'createRole']);
        Route::get('/', [PermissionController::class, 'getRoles']);
        Route::get('/{id}', [PermissionController::class, 'findRole']);
        Route::put('/{id}', [PermissionController::class, 'updateRole']);
        Route::delete('/{id}', [PermissionController::class, 'deleteRole']);
        Route::post('/assign-roles', [PermissionController::class, 'assignRolesToAdmins']);
        Route::post('/assign-role', [PermissionController::class, 'assignRoleToAdmin']);
        Route::get('/assign-all-roles/{user_id}', [PermissionController::class, 'assignAllRoles']);
        Route::post('/remove-roles', [PermissionController::class, 'removeRolesFromAdmins']);
        Route::post('/remove-role', [PermissionController::class, 'removeRoleFromAdmin']);
        Route::get('/remove-all-roles/{user_id}', [PermissionController::class, 'removeAllRoles']);
        Route::post('/customers/assign-roles', [PermissionController::class, 'assignRolesToCustomers']);
        Route::post('/customers/assign-role', [PermissionController::class, 'assignRoleToCustomer']);
        Route::post('/customers/remove-roles', [PermissionController::class, 'removeRolesFromCustomer']);
        Route::post('/customers/remove-role', [PermissionController::class, 'removeRoleFromCustomer']);
    });

    Route::prefix('role')->middleware(['role:super_admin'])->group( function () {
        Route::get('/assigned-permissions', [PermissionController::class, 'viewAssignedPermissions']);
    });

    //activities
    Route::get('/login-activities', [ActivitiesController::class, 'getLoginActivities']);
    Route::get('/admin-login-activities', [ActivitiesController::class, 'getAdminLoginActivities']);
    Route::get('/company-activities', [ActivitiesController::class, 'getCompanyActivities']);

    Route::get('/safe-memo', [SafeMemoController::class, 'getSafeMemo']);
    Route::post('/safe-memo', [SafeMemoController::class, 'createSafeMemo']);
    Route::get('/safe-memo/{id}', [SafeMemoController::class, 'getSafeMemoById']);

    Route::post('/logout',[RegisterController::class, 'adminLogout']);

});

Route::middleware(['auth:api'])->prefix('v1')->group( function () {
    Route::prefix('companies')->group( function () {
        Route::get('/', [CompanyController::class , 'index']);
        Route::post('/', [CompanyController::class , 'create']);
        Route::get('/{id}', [CompanyController::class , 'show']);
        Route::put('/{id}',[CompanyController::class , 'update']);
        Route::delete('/{id}',[CompanyController::class , 'destroy']);
        Route::post('/companyActivities',[CompanyController::class , 'storeCompanyActivities']);
        Route::get('{id}/notifications',[CompanyController::class , 'viewNotifications']);
    });

    //clients
    Route::prefix('clients')->group( function () {
        Route::get('/', [UserController::class , 'showClientInvites']);
        Route::post('/', [UserController::class , 'createClient']);
        Route::post('/send-invitation', [UserController::class , 'sendInvitation']);
        Route::delete('/{id}',[UserController::class , 'deleteClient']);
        // Route::get('/{id}', [UserController::class , 'show']);
        // Route::put('/{id}',[UserController::class , 'update']);
        // Route::post('/companyActivities',[UserController::class , 'storeCompanyActivities']);
        Route::post('/permissions', [UserController::class , 'setClientPermissions']);
        Route::get('/permissions', [UserController::class , 'viewPermissions']);
        Route::get('/permissions/{id}', [UserController::class , 'viewPermissionsById']);
        Route::put('/permissions/{id}', [UserController::class , 'editPermissionsById']);
    });
    //user
    Route::prefix('user')->group( function () {
        Route::get('/', [RegisterController::class , 'show']);
        Route::put('/', [RegisterController::class , 'update']);
    });

    //activities
    Route::get('/company-activities', [ActivitiesController::class, 'getCompanyActivitiesByUser']);

    Route::middleware('switch_company')->group( function () {


        Route::post('/availableNumbers', [OrderController::class, 'searchAvailableNumbers']);
        Route::post('/orders', [OrderController::class, 'createOrder']);
        Route::get('/orders', [OrderController::class, 'getOrders']);
        Route::get('/inservice-numbers', [OrderController::class, 'getInserviceNumbers']);
        Route::get('/inservice-numbers/{company_id}', [OrderController::class, 'getInserviceNumbers']);
        Route::post('/disconnects', [OrderController::class, 'disconnectNumber']);
        Route::get('/disconnects/{disconnectid}', [OrderController::class, 'getDisconnectedNumbers']);
        Route::get('/all-disconnected-numbers', [OrderController::class, 'getAllDisconnectedNumbers']);
        Route::post('/reconnect/{telephone_number}', [OrderController::class, 'createOrder']);

        //contacts
        Route::post('/contacts',[ContactController::class,'createContact']);
        Route::post('/contacts-import',[ContactController::class,'importContact']);
        Route::get('/companies-contact',[ContactController::class,'getCompanyContact']);
        Route::get('/user-contact',[ContactController::class,'getUserContact']);

        //groups
        Route::post('/groups',[GroupController::class,'createGroup']);
        Route::get('/companies-group',[GroupController::class,'getCompanyGroup']);
        Route::get('/user-group',[GroupController::class,'getUserGroup']);

        //group contact
        Route::post('/group-contacts',[GroupContactController::class,'addContactsToGroup']);
        Route::get('/user-group-contact',[GroupContactController::class,'getUserGroupContact']);
        Route::get('/company-group-contact',[GroupContactController::class,'getCompanyGroupContact']);


        //Wallet
        Route::prefix('wallet')->group( function () {
            Route::get('/balance', [WalletController::class, 'getCompanyWalletBalance']);
            Route::post('/deposit', [WalletController::class, 'depositAmountToWallet']);
            Route::post('/withdraw', [WalletController::class, 'withdrawAmountFromWallet']);
        });

        // Subscription
        Route::prefix('subscription')->group( function () {
            Route::get('/plan', [SubscriptionController::class, 'getAllPlans']);
            Route::get('/plan/{id}', [SubscriptionController::class, 'findPlan']);
            Route::get('/plan-features', [SubscriptionController::class, 'getPlanFeatures']);
            Route::post('/', [SubscriptionController::class, 'createSubscription']);
            Route::get('/company-subscriptions', [SubscriptionController::class, 'getCompanySubscriptions']);

        });

        //message-template
        Route::get('/message-template', [MessageTemplateController::class , 'getMessageTemplates']);
        Route::post('/message-template', [MessageTemplateController::class , 'create']);
        Route::get('/message-template/{id}', [MessageTemplateController::class , 'show']);
        Route::put('/message-template/{id}',[MessageTemplateController::class , 'update']);
        Route::delete('/message-template/{id}',[MessageTemplateController::class , 'destroy']);

        Route::post('/sms', [MessageController::class , 'sendSMS']);
        Route::post('/sms-template', [MessageController::class , 'sendSMSByMessageTemplate']);
        Route::get('/sms_sent', [MessageController::class , 'getSentMessages']);
        Route::get('/search-sent-sms', [MessageController::class , 'searchSentMessages']);
        Route::get('/search-received-sms', [MessageController::class , 'searchReceivedMessages']);
        Route::get('/sms_received', [MessageController::class , 'getReceivedMessages']);

         //webhook calls
         Route::get('/webhook',[ WebhookController::class , 'getWebhook']);
         Route::delete('/webhook/{id}',[ WebhookController::class , 'deleteWebhook']);

    });


    Route::middleware('auth:api_token')->prefix('developer')->group( function () {
       
        Route::get('/all-companies', [DeveloperApiController::class , 'getCompany']);
        Route::get('/inservice-numbers', [DeveloperApiController::class, 'getAllInserviceNumbers']);
        Route::get('/all-disconnected-numbers', [DeveloperApiController::class, 'getDisconnectedNumbersByDeveloper']);
        Route::get('/orders', [DeveloperApiController::class, 'getOrdersByDeveloper']);
        Route::post('/orders', [DeveloperApiController::class, 'createOrder']);
        Route::get('/inservice-numbers/{company_id}', [DeveloperApiController::class, 'getInserviceNumbers']);
        Route::post('/disconnects', [DeveloperApiController::class, 'disconnectNumberByDeveloper']);
        Route::post('/reconnect/{telephone_number}', [DeveloperApiController::class, 'createOrder']);
        Route::get('/disconnects/{phone_numbers}', [DeveloperApiController::class, 'findDisconnectedNumbersByDeveloper']);

        //message
        Route::post('/sms', [DeveloperApiController::class , 'sendSMS']);
        Route::get('/sms_sent', [DeveloperApiController::class , 'getSentMessages']);
        Route::get('/search-sent-sms', [DeveloperApiController::class , 'searchSentMessages']);
        Route::get('/search-received-sms', [DeveloperApiController::class , 'searchReceivedMessages']);
        Route::get('/sms_received', [DeveloperApiController::class , 'getReceivedMessages']);
    });
    Route::post('/logout',[RegisterController::class, 'logout']);
});



Route::post('/webhook/sms-receive',[ MessageController::class , 'webhookSmsReceive']);

Route::webhooks('webhook-receiving-url');




