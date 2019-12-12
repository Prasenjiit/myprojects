<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
Route::get('install', ['as' => 'welcome','uses' => 'WelcomeController@welcome']);
Route::get('requirements', ['as' => 'requirements','uses' => 'RequirementsController@requirements']);
Route::get('permissions', ['as' => 'permissions','uses' => 'PermissionsController@permissions']);
Route::get('environment', ['as' => 'environment','uses' => 'EnvironmentController@environmentMenu']);
Route::get('environmentWizard', ['as' => 'environmentWizard','uses' => 'EnvironmentController@environmentWizard']);
//Route::get('environmentClassic', ['as' => 'environmentClassic','uses' => 'EnvironmentController@environmentClassic']);
Route::post('environmentSaveWizard', ['as' => 'environmentSaveWizard','uses' => 'EnvironmentController@saveWizard']);
//Route::post('environmentSaveClassic', ['as' => 'environmentSaveClassic','uses' => 'EnvironmentController@saveClassic']);
Route::get('CheckDB', ['as' => 'CheckDB','uses' => 'EnvironmentController@CheckDB']);
Route::get('database', ['as' => 'database','uses' => 'DatabaseController@database']);
Route::get('importTable',['as' => 'imprttbl', 'uses' => 'ImportTableController@importTable']);
Route::get('final',['as' => 'final', 'uses' => 'FinalController@finish']);

Route::get('/response',function(){
	return view('pages/documents/response');
});
Route::post('emailDuplication', ['as' => 'emailDuplication', 'before'=> 'csrf', 'uses' => 'UsersController@emailDuplication']);

Route::group(array('before' => 'auth'), function()
{
  Route::post('selectType', ['as' => 'selectType', 'before'=> 'csrf', 'uses' => 'AjaxController@dtview']);
  Route::post('documentsAdvSearch', ['as'=>'documentsAdvSearch', 'before'=>'csrf', 'uses'=>'SearchController@searchadvncdoc']);
  Route::post('publishSelected', ['as' => 'publishSelected', 'before'=> 'csrf', 'uses' => 'AjaxController@moveAll']);
  Route::post('link_to_doc_filter', ['as' => 'link_to_doc_filter', 'before'=> 'csrf', 'uses' => 'AjaxController@link_to_doc_filter']);
  Route::post('link_to_doc_save', ['as' => 'link_to_doc_save', 'before'=> 'csrf', 'uses' => 'AjaxController@link_to_doc_save']);
});
Route::group(['middleware' => ['pathmiddleware','licensemiddleware']], function ()
{
//annotation
Route::post('/lib/html5/annotationHandler.php','DocumentsController@annotehandler');
Route::get('/lib/html5/annotationHandler.php','DocumentsController@annotehandler');

Route::get('/', 'HomeController@index');

Route::get('getDeptment','HomeController@getDept');

//serial key activate request
Route::get('key_activate_req','LoginController@activateKeyRequest');
//modules 
Route::post('ModulesSave', ['as' => 'ModulesSave', 'before'=> 'csrf', 'uses' => 'AccountController@modulesSave']);
Route::get('modules','AccountController@modules');
Route::get('mod_update_req','AccountController@mod_update_req');

Route::auth();
//To get and update geolocation if user login failed
Route::get('auth_location_details',['as'=> 'auth_location_details', 'before'=> 'csrf', 'uses'=> 'LoginController@auth_location_details']);

Route::get('auth_location_details_no_conn',['as'=> 'auth_location_details_no_conn', 'before'=> 'csrf', 'uses'=> 'LoginController@auth_location_details_no_conn']);

Route::get('/home', 'HomeController@index');
Route::get('/pdfedit', 'PDFController@index');
Route::patch('home', ['as' => 'home', 'before'=> 'csrf', 'uses' => 'HomeController@index']);
Route::get('/logout', [ 'as'=>'logout', 'uses'=>'Auth\AuthController@logout']);
Route::get('login', ['as' => 'login', 'before'=> 'csrf', 'uses' => 'LoginController@index']);
Route::post('login', ['as' => 'login', 'before'=> 'csrf', 'uses' => 'LoginController@loginProcess']);

Route::get('getsecuritySettings', 'PasswordController@getsecuritySettings');// get security message for tooltips
Route::get('getexpiryMessage', 'PasswordController@getexpiryMessage');// get security message for expiry date
Route::get('reset', 'PasswordController@reset');
Route::post('resetSubmit', 'PasswordController@resetSubmit');


//nspl
Route::resource('dimentioncombinations','DimentionCombinationController');
Route::resource('CBMaster','CashBankMasterController');
Route::any('/CBMaster/update/{id}','CashBankMasterController@update');

/* vishnu 26-11-19 */
Route::get('annotation/annotation', 'AnnotationController@listall');
Route::get('/annotation', 'AnnotationController@listall');
Route::get('/annotation/{id}/search', 'AnnotationController@search');
Route::delete('/annotation/{id}/deleteAll', 'AnnotationController@deleteAll');

Route::get('/annotation/{id}', 'AnnotationController@index');
Route::post('annotation', 'AnnotationController@store');
Route::put('annotation/{id}', 'AnnotationController@update');
Route::delete('annotation/{id}', 'AnnotationController@delete');

Route::get('/stamp/{pdf}/{page}', 'StampController@index');
Route::post('/stamp', 'StampController@store');
Route::put('/stamp/{id}', 'StampController@update');
Route::delete('/stamp/{id}', 'StampController@delete');

/* end vishnu 26-11-19 */

//backup and restore functions 
Route::get('backup',['as'=> 'backup', 'before'=> 'csrf', 'uses'=> 'BckpRestreController@index']);
Route::post('BackupProcess',['as'=> 'BackupProcess', 'before'=> 'csrf', 'uses'=> 'BckpRestreController@dobackup']);
Route::get('restoreProcess',['as'=> 'restoreProcess', 'before'=> 'csrf', 'uses'=> 'BckpRestreController@doRestore']);
Route::post('backupDeleteZip', ['as' => 'backupDeleteZip', 'before'=> 'csrf', 'uses' => 'BckpRestreController@DeleteZip']);
Route::post('backupRestoreZip', ['as' => 'backupRestoreZip', 'before'=> 'csrf', 'uses' => 'BckpRestreController@RestoreZip']);
Route::get('DownloadBckup/{filename}', ['as' => 'getBckupZip', 'before'=> 'csrf', 'uses' => 'BckpRestreController@getZip']);
Route::post('removebackupDocument', ['as' => 'removebackupDocument', 'before'=> 'csrf', 'uses' => 'BckpRestreController@removeDocument']);
Route::post('deleteSelectedbckup', ['as' => 'deleteSelectedbckup', 'before'=> 'csrf', 'uses' => 'BckpRestreController@deleteAll']);

// Setting
Route::get('settings',['as'=> 'settings', 'before'=> 'csrf', 'uses'=> 'SettingsController@index']);
Route::get('getSettings',['as'=> 'getSettings', 'before'=> 'csrf', 'uses'=> 'SettingsController@getSettings']);
Route::get('getSettingsAuth',['as'=> 'getSettingsAuth', 'before'=> 'csrf', 'uses'=> 'Auth\AuthController@getSettingsAuth']);
Route::get('checkUserLockStatus',['as'=> 'checkUserLockStatus', 'before'=> 'csrf', 'uses'=> 'Auth\AuthController@checkUserLockStatus']);
Route::get('distroySession',['as'=> 'distroySession', 'before'=> 'csrf', 'uses'=> 'Auth\AuthController@distroySession']);
Route::post('settingsSave', ['as' => 'settingsSave', 'before'=> 'csrf', 'uses' => 'SettingsController@save']);
Route::post('SaveAccountLockOutSettings', ['as' => 'SaveAccountLockOutSettings', 'before'=> 'csrf', 'uses' => 'SettingsController@SaveAccountLockOutSettings']);

//bookmark
Route::get('saveBookmark',['as'=> 'saveBookmark', 'before'=> 'csrf', 'uses'=> 'DocumentsController@saveBookmark']);
Route::get('dleteBookmark',['as'=> 'dleteBookmark', 'before'=> 'csrf', 'uses'=> 'DocumentsController@deleteBookmark']);
Route::get('getBookmark',['as'=> 'getBookmark', 'before'=> 'csrf', 'uses'=> 'DocumentsController@getBookmark']);

// Test email in smtp setting
Route::post('testEmail',['as'=> 'testEmail', 'before'=> 'csrf', 'uses'=> 'SettingsController@testEmail']);

Route::get('account', ['as' => 'account', 'before'=> 'csrf', 'uses' => 'AccountController@index']);
Route::get('updatedata', ['as' => 'updatedata', 'before'=> 'csrf', 'uses' => 'AccountController@updatedata']);

// Get user permission using ajax<-- common function -->
Route::get('getUserPermission',['as'=> 'getUserPermission', 'before'=> 'csrf', 'uses'=> 'UsersController@getUserPermission']);
// Get user notice messages
Route::get('getUserMessages',['as'=> 'userMessage', 'before'=> 'csrf', 'uses'=> 'UsersController@getUserMessages']);
Route::get('getUserAvailable',['as'=> 'getUserAvailable', 'before'=> 'csrf', 'uses'=> 'UsersController@getUserAvailable']);
// Stack Master
Route::get('stacks',['as'=> 'stacks', 'before'=> 'csrf', 'uses'=> 'StacksController@index']);
Route::post('stackSave/{id}', ['as' => 'stackSave', 'before'=> 'csrf', 'uses' => 'StacksController@save']);
Route::get('stacksList', ['as'=> 'stacksList', 'before'=> 'csrf', 'uses'=> 'StacksController@stacksList']);
Route::get('stacksEdit/{id}', ['as' => 'stacksEdit', 'before'=> 'csrf', 'uses' => 'StacksController@edit']);
Route::post('stacksDuplication', ['as' => 'stacksDuplication', 'before'=> 'csrf', 'uses' => 'StacksController@duplication']);
Route::post('stacksDelete', ['as' => 'stacksDelete', 'before'=> 'csrf', 'uses' => 'StacksController@delete']);

//submenu 
//Route::get('documentsListStck/{id}', ['as'=> 'documentsListStck', 'before'=> 'csrf', 'uses'=> 'StacksController@documentsList']);
//Route::get('documentsListDept/{id}', ['as'=> 'documentsListDept', 'before'=> 'csrf', 'uses'=> 'DepartmentsController@documentsList']);
//Route::get('documentTypeList/{id}', ['as'=> 'documentTypeList', 'before'=> 'csrf', 'uses'=> 'DocumentTypesController@documentsList']);

//Route::get('deptListview', ['as'=> 'deptListview', 'before'=> 'csrf', 'uses'=> 'DepartmentsController@documentsListView']); 
//Route::get('stckListview', ['as'=> 'stckListview', 'before'=> 'csrf', 'uses'=> 'StacksController@documentsListView']); 

Route::get('docsList/{id}', ['as'=> 'docsList', 'before'=> 'csrf', 'uses'=> 'DocumentTypesController@documentsList']);
Route::get('dctypeListview', ['as'=> 'dctypeListview', 'before'=> 'csrf', 'uses'=> 'DocumentTypesController@documentsListView']);

Route::post('blkCheckout', ['as' => 'blkCheckout', 'before'=> 'csrf', 'uses' => 'DocumentsController@blkCheckout']);
// Audit Master
Route::get('audits', ['as' => 'audits', 'before'=> 'csrf', 'uses' => 'AuditsController@index']);
Route::get('auditsList', ['as' => 'auditsList', 'before'=> 'csrf', 'uses' => 'AuditsController@showAll']);
Route::get('getAllAudits', ['as' => 'getAllAudits', 'before'=> 'csrf', 'uses' => 'AuditsController@getAllAudits']);
Route::post('auditsAdvSearch', ['as'=>'auditsAdvSearch', 'before'=>'csrf', 'uses'=>'AuditsController@searchadvncaud']);
Route::get('auditsSearchRecords', ['as'=>'auditsSearchRecords', 'before'=>'csrf', 'uses'=>'AuditsController@auditsSearchRecords']);
Route::post('viewOnAudit',['as'=>'viewOnAudit','before'=>'csrf','uses'=>'AuditsController@viewOnAudit']);
Route::post('searchNotAccess',['as'=>'searchNotAccess','before'=>'csrf','uses'=>'AuditsController@searchNotAccess']);
Route::post('clearAudit',['as' => 'clearAudit','before'=>'csrf', 'uses' => 'AuditsController@clearAudit']);

// TagWords
Route::get('tagWords',['as'=> 'tagWords', 'before'=> 'csrf', 'uses'=> 'TagWordsController@index']);
Route::get('saveCategory',['as'=> 'saveCategory', 'before'=> 'csrf', 'uses'=> 'TagWordsController@saveCategory']);
Route::get('getCatgory',['as'=> 'getCatgory', 'before'=> 'csrf', 'uses'=> 'TagWordsController@getAllCatgory']);
Route::get('updateCategory',['as'=> 'updateCategory', 'before'=> 'csrf', 'uses'=> 'TagWordsController@updateCategory']);
Route::get('deleteCat',['as'=> 'deleteCat', 'before'=> 'csrf', 'uses'=> 'TagWordsController@deleteCat']);
Route::get('addTag',['as'=> 'addTag', 'before'=> 'csrf', 'uses'=> 'TagWordsController@addTag']);
Route::get('updateTag',['as'=> 'updateTag', 'before'=> 'csrf', 'uses'=> 'TagWordsController@updateTag']);
Route::get('deleteTag',['as'=> 'deleteTag', 'before'=> 'csrf', 'uses'=> 'TagWordsController@deleteTag']);
Route::get('getTags',['as'=> 'getTags', 'before'=> 'csrf', 'uses'=> 'TagWordsController@getTags']);


//Department Routing
Route::get('departments',['as'=> 'departments', 'before'=> 'csrf', 'uses'=> 'DepartmentsController@index']);
Route::get('departmentsList', ['as'=> 'departmentsList', 'before'=> 'csrf', 'uses'=> 'DepartmentsController@departmentsList']);
Route::post('departmentSave/{id}', ['as' => 'departmentSave', 'before'=> 'csrf', 'uses' => 'DepartmentsController@save']);
Route::get('departmentEdit/{id}', ['as' => 'departmentEdit', 'before'=> 'csrf', 'uses' => 'DepartmentsController@edit']);
Route::post('departmentDuplication', ['as' => 'departmentDuplication', 'before'=> 'csrf', 'uses' => 'DepartmentsController@duplication']);
Route::post('departmentDelete', ['as' => 'departmentDelete', 'before'=> 'csrf', 'uses' => 'DepartmentsController@check']);

Route::get('showUser/{id}/{name}', ['as' => 'showUser', 'before'=> 'csrf', 'uses' => 'UsersController@show']);


Route::post('documentGroupDelete', ['as' => 'documentGroupDelete', 'before'=> 'csrf', 'uses' => 'DepartmentsController@delete']);

//Application audit Log
Route::get('audit', ['as' => 'audit', 'before'=> 'csrf', 'uses' => 'AuditController@index']);

//menu toggle save for the user 
Route::post('menuToggle', ['as' => 'menuToggle', 'before'=> 'csrf', 'uses' => 'menuToggleController@status']);
Route::post('viewmenuToggle', ['as' => 'viewmenuToggle', 'before'=> 'csrf', 'uses' => 'menuToggleController@showcookies']);

Route::post('srchToggle', ['as' => 'srchToggle', 'before'=> 'csrf', 'uses' => 'DocumentsController@advcsrchstatus']);
Route::post('viewsrchToggle', ['as' => 'viewsrchToggle', 'before'=> 'csrf', 'uses' => 'DocumentsController@showcookies']);


//Usermanagement routing
Route::get('users', ['as' => 'users', 'before'=> 'csrf', 'uses' => 'UsersController@index']);
Route::get('userEdit/{id}', ['as' => 'userEdit', 'before'=> 'csrf', 'uses' => 'UsersController@edit']);
Route::get('userHistory/{id}', ['as' => 'userHistory', 'before'=> 'csrf', 'uses' => 'UsersController@history']);
Route::post('userDelete', ['as' => 'userDelete', 'before'=> 'csrf', 'uses' => 'UsersController@delete']);
Route::post('userSave/{id}', ['as' => 'userSave', 'before'=> 'csrf', 'uses' => 'UsersController@save']);
Route::post('usersList', ['as' => 'usersList', 'before'=> 'csrf', 'uses' => 'UsersController@usersList']);
Route::post('userDuplication', ['as' => 'userDuplication', 'before'=> 'csrf', 'uses' => 'UsersController@duplication']);

Route::post('userLogout', ['as' => 'userLogout', 'before'=> 'csrf', 'uses' => 'UsersController@logout']);
Route::post('userUnlock', ['as' => 'userUnlock', 'before'=> 'csrf', 'uses' => 'UsersController@unlock']);


//Document Type Routing
Route::get('documentTypes',['as'=> 'documentTypes', 'before'=> 'csrf', 'uses'=> 'DocumentTypesController@index']);
Route::get('documentTypeList', ['as'=> 'documentTypeList', 'before'=> 'csrf', 'uses'=> 'DocumentTypesController@documentTypeList']);
Route::post('documentTypeSave/{id}', ['as' => 'documentTypeSave', 'before'=> 'csrf', 'uses' => 'DocumentTypesController@save']);
Route::get('documentTypeEdit/{id}', ['as' => 'documentTypeEdit', 'before'=> 'csrf', 'uses' => 'DocumentTypesController@edit']);
Route::post('documentTypeDelete', ['as' => 'documentTypeDelete', 'before'=> 'csrf', 'uses' => 'DocumentTypesController@delete']);
Route::post('documentTypeDuplication', ['as' => 'documentTypeDuplication', 'before'=> 'csrf', 'uses' => 'DocumentTypesController@duplication']);
Route::post('stackDelete', ['as' => 'stackDelete', 'before'=> 'csrf', 'uses' => 'StacksController@delete']);
Route::post('stackFieldDelete', ['as' => 'stackFieldDelete', 'before'=> 'csrf', 'uses' => 'StacksController@stackFieldDelete']);
Route::post('stackDuplication', ['as' => 'stackDuplication', 'before'=> 'csrf', 'uses' => 'StacksController@duplication']);

Route::post('documentTypeFieldDelete', ['as' => 'documentTypeFieldDelete', 'before'=> 'csrf', 'uses' => 'DocumentTypesController@documentTypeFieldDelete']);
Route::get('documentTypeColDelete/{name}', ['as' => 'documentTypeColDelete', 'before'=> 'csrf', 'uses' => 'DocumentTypesController@deleteColumn']);
//documement column type check 
Route::get('documentCheck', ['as' => 'documentCheck', 'before'=> 'csrf', 'uses' => 'DocumentTypesController@checkdoc']);
// checking documt has any sub datas while delete 
Route::get('hasChild', ['as' => 'hasChild', 'before'=> 'csrf', 'uses' => 'DocumentTypesController@hasChild']);
Route::post('optionDelete',['uses'=>'DocumentTypesController@optionDelete']);
Route::post('optionChange',['uses'=>'DocumentTypesController@optionChange']);

Route::post('stackoptionDelete',['uses'=>'StacksController@optionDelete']);
Route::post('stackoptionChange',['uses'=>'StacksController@optionChange']);


//Document Type Column Routing
Route::get('documentTypeColumn',['as'=> 'documentTypeColumn', 'before'=> 'csrf', 'uses'=> 'DocumentTypeColumnController@index']);
Route::get('documentTypeColumnList', ['as'=> 'documentTypeColumnList', 'before'=> 'csrf', 'uses'=> 'DocumentTypeColumnController@documentTypeColumnList']);
Route::post('documentTypeColumnSave/{id}', ['as' => 'documentTypeColumnSave', 'before'=> 'csrf', 'uses' => 'DocumentTypeColumnController@save']);
Route::get('documentTypeColumnEdit/{id}', ['as' => 'documentTypeColumnEdit', 'before'=> 'csrf', 'uses' => 'DocumentTypeColumnController@edit']);
Route::post('documentTypeColumnDelete', ['as' => 'documentTypeColumnDelete', 'before'=> 'csrf', 'uses' => 'DocumentTypeColumnController@delete']);
Route::post('documentTypeColumnDuplication', ['as' => 'documentTypeColumnDuplication', 'before'=> 'csrf', 'uses' => 'DocumentTypeColumnController@duplication']);

//Documents Routing
Route::get('editFile/{id}', ['as' => 'editFile', 'before'=> 'csrf', 'uses' => 'DocumentsController@editFile']);

Route::get('documents', ['as'=> 'documents', 'before'=> 'csrf', 'uses'=> 'DocumentsController@index']);
//document save route
Route::post('documentsSave/{id}', ['as' => 'documentsSave', 'before'=> 'csrf', 'uses' => 'DocumentsController@save']);
Route::post('documentsSaveAll/{status}/{id?}', ['as' => 'documentsSaveAll', 'before'=> 'csrf', 'uses' => 'DocumentsController@saveAll']);
// advance search from the popup
//Route::post('documentsAdvSearch', ['as'=>'documentsAdvSearch', 'before'=>'csrf', 'uses'=>'DocumentsController@searchadvncdoc']);
Route::post('searchadv',['as'=> 'searchadv', 'before'=> 'csrf', 'uses'=> 'DocumentsController@searchadv']);
// Save criteris
Route::post('saveCriteria', ['as'=>'saveCriteria', 'before'=>'csrf', 'uses'=>'DocumentsController@saveCriteria']);
//get download
Route::get('getDownload/{id}', ['as'=> 'getDownload', 'before'=> 'csrf', 'uses'=> 'DocumentsController@getDownload']);
//view pdf files
Route::get('documentManagementView', ['as' => 'documentManagementView', 'uses' => 'DocumentsController@fileView']);
//pdf file thumb view
Route::get('webviewer', ['as' => 'webviewer', 'uses' => 'DocumentsController@fileView1']);

//view cad files
Route::get('cadView', ['as' => 'cadView', 'before'=> 'csrf', 'uses' => 'DocumentsController@cadView']);

Route::post('editDocument/{id}', ['as' => 'editDocument', 'before'=> 'csrf', 'uses' => 'DocumentsController@editdoc']);//edit
Route::post('checkout/{id}', ['as' => 'checkout', 'before'=> 'csrf', 'uses' => 'DocumentsCheckoutController@checkoutdoc']);//edit
Route::post('editDoc/{id}', ['as' => 'editDoc', 'before'=> 'csrf', 'uses' => 'DocumentsCheckoutController@editdoc']);//edit
Route::get('doctypeSettings', 'DocumentsCheckoutController@getsecuritySettings');// get security message for tooltips
Route::get('doctypeSettingsEdit', 'DocumentsCheckoutController@getsecuritySettingsEdit');

//List Views
//Route::get('selectType', 'DocumentsController@dtview');//before datatable serverside
//Route::post('selectType', ['as' => 'selectType', 'before'=> 'csrf', 'uses' => 'DocumentsController@dtview']);//new after server side datatable
// Common url for get document more details
Route::get('documentMoreDetails/{id}',['as'=> 'documentMoreDetails', 'before'=> 'csrf', 'uses'=> 'DocumentsController@docMoreDetails']);
// Common url for get get document type sublist by document type ids
Route::get('getDocumentTypeSublis',['as'=> 'getDocumentTypeSublist', 'before'=> 'csrf', 'uses'=> 'DocumentsController@getDocumentTypeSublist']);

//Add new document
Route::post('loadStack', ['as' => 'loadStack', 'before'=> 'csrf', 'uses' => 'DocumentsController@loadStack']);
Route::post('loadStackonAdvanceSearch', ['as' =>'loadStackonAdvanceSearch', 'before'=>'csrf', 'uses' => 'DocumentsController@loadStackonAdvanceSearch']);
Route::post('addNewDocument/{id}', ['as' => 'addNewDocument', 'before'=> 'csrf', 'uses' => 'DocumentsController@savefile']);    
Route::get('documentsListview', ['as'=> 'documentsListview', 'before'=> 'csrf', 'uses'=> 'DocumentsController@documentsListview']);
Route::post('documentsKeywords', ['as'=> 'documentsKeywords', 'before'=> 'csrf', 'uses'=> 'DocumentsController@getKeywords']);
Route::post('documentsSubList', ['as'=> 'documentsSubList', 'before'=> 'csrf', 'uses'=> 'DocumentsController@documentsSubList']);
Route::post('documentsSubListSrch', ['as'=> 'documentsSubListSrch', 'before'=> 'csrf', 'uses'=> 'DocumentsController@documentsSubListSrch']);
Route::post('documentsSubListEdit', ['as'=> 'documentsSubListEdit', 'before'=> 'csrf', 'uses'=> 'DocumentsController@documentsSubListEdit']);
Route::post('getDocumentIndexFields', ['as'=> 'getDocumentIndexFields', 'before'=> 'csrf', 'uses'=> 'DocumentsController@getDocumentIndexFields']);
Route::post('documentsSubListCheckout', ['as'=> 'documentsSubListCheckout', 'before'=> 'csrf', 'uses'=> 'DocumentsCheckoutController@documentsSubListCheckout']);
Route::get('documentAdd', ['as' => 'documentAdd', 'before'=> 'csrf', 'uses' => 'DocumentsController@add']);
Route::get('documentEdit', ['as' => 'documentEdit', 'uses' => 'DocumentsController@edituploadfile']);
Route::post('documentsEditAll', ['as' => 'documentsEditAll', 'before'=> 'csrf', 'uses' => 'DocumentsController@edituploadfilesall']);
Route::post('documentsTagwords', ['as'=> 'documentsTagwords', 'before'=> 'csrf', 'uses'=> 'DocumentsController@getTagwords']);
Route::get('documentsNoteSave', ['as' => 'documentsNoteSave', 'before'=> 'csrf', 'uses' => 'DocumentsController@saveNote']);
Route::get('saveNote', ['as' => 'saveNote', 'before'=> 'csrf', 'uses' => 'DocumentsController@saveNotePopup']);
Route::get('tempdocumentsNoteSave', ['as' => 'tempdocumentsNoteSave', 'before'=> 'csrf', 'uses' => 'DocumentsController@tempsaveNote']);
Route::get('documentsNote', ['as' => 'documentsNote', 'before'=> 'csrf', 'uses' => 'DocumentsController@getNote']);
Route::get('documentHistory/{id}', ['as' => 'documentHistory', 'before'=> 'csrf', 'uses' => 'DocumentsController@history']);
// Get dociment type columns getDocTypeColumn
Route::get('getDocTypeColumn', ['as' => 'getDocTypeColumn', 'before'=> 'csrf', 'uses' => 'DocumentsController@getDocTypeColumn']);
Route::get('documensMoreDetails', ['as' => 'documensMoreDetails', 'before'=> 'csrf', 'uses' => 'DocumentsController@moreDetails']);
Route::get('documentsMoreDetails', ['as' => 'documentsMoreDetails', 'before'=> 'csrf', 'uses' => 'DocumentsController@moreDetails']);
Route::get('documentsMoreDetails_relate', ['as' => 'documentsMoreDetails_relate', 'before'=> 'csrf', 'uses' => 'DocumentsController@moreDetails_relate']);
Route::post('commentAdd', ['as' => 'commentAdd', 'before'=> 'csrf', 'uses' => 'DocumentsController@saveComment']);
Route::get('download', ['as' => 'download', 'before'=> 'csrf', 'uses' => 'DocumentsController@download']);
Route::get('chkoutdownload', ['as' => 'chkoutdownload', 'before'=> 'csrf', 'uses' => 'DocumentsController@downloadCheckout']);
//find duplicates
Route::get('documentDuplicates', ['as' => 'documentDuplicates', 'before'=> 'csrf', 'uses' => 'DocumentsController@findDuplicates']);
Route::get('fileDuplicates', ['as' => 'fileDuplicates', 'before'=> 'csrf', 'uses' => 'DocumentsController@fileDuplicates']);

Route::get('documentManagementEdit', ['as' => 'documentManagementEdit', 'before'=> 'csrf', 'uses' => 'DocumentsController@edit']);
Route::post('documentDelete', ['as' => 'documentDelete', 'before'=> 'csrf', 'uses' => 'DocumentsController@deleteDocument']);
Route::post('deleteDocumentTemp', ['as' => 'deleteDocument', 'before'=> 'csrf', 'uses' => 'DocumentsController@deleteTemp']);
Route::post('documentsAdvSrchSubList', ['as'=> 'documentsAdvSrchSubList', 'before'=> 'csrf', 'uses'=> 'DocumentsController@documentsSrchSubList']);
Route::get('documentAdvanceSearch/{name?}', ['as' => 'documentAdvanceSearch', 'before'=> 'csrf', 'uses' => 'DocumentsController@advancesearch']);
Route::get('checkoutDocument', ['as' => 'checkoutDocument','uses' => 'DocumentsCheckoutController@checkout']);
Route::get('editAllDocument', ['as' => 'editAllDocument','uses' => 'DocumentsCheckoutController@editDocument']);
Route::post('checkinDocument', ['as' => 'checkinDocument', 'before'=> 'csrf', 'uses' => 'DocumentsCheckoutController@checkin']);
Route::post('discardDocumentDraft', ['as' => 'discardDocumentDraft', 'before'=> 'csrf', 'uses' => 'DocumentsCheckoutController@discard_draft']);
Route::post('discardDocumentPublished', ['as' => 'discardDocumentPublished', 'before'=> 'csrf', 'uses' => 'DocumentsCheckoutController@discard_published']);
Route::post('discardPublished', ['as' => 'discardPublished', 'before'=> 'csrf', 'uses' => 'DocumentsCheckoutController@discardAll']);
Route::post('draftDocument', ['as' => 'draftdDocument', 'before'=> 'csrf', 'uses' => 'DocumentsCheckoutController@draft']);
Route::get('editcheckoutDocument', ['as' => 'editcheckoutDocument','uses' => 'DocumentsCheckoutController@edit']);
Route::post('cancelCheckout', ['as' => 'cancelCheckout', 'before'=> 'csrf', 'uses' => 'DocumentsCheckoutController@cancel']);
Route::post('docnoDuplication', ['as' => 'docnoDuplication', 'before'=> 'csrf', 'uses' => 'DocumentsController@docnoDuplication']);
Route::post('docnameDuplication', ['as' => 'docnameDuplication', 'before'=> 'csrf', 'uses' => 'DocumentsController@docnameDuplication']);

Route::get('clear',['as'=>'clear','before'=>'csrf','uses'=>'DocumentsController@docClear']);
Route::post('columnList', ['as'=> 'columnList', 'before'=> 'csrf', 'uses'=> 'DocumentsController@columnsList']);
Route::get('documentSearchType/{id}',['as'=>'documentSearchType','before'=>'csrf','uses'=>'DocumentsController@wrkspacesrchdoc']);

Route::get('showDocument/{id}/{name}',['as'=> 'showDocument', 'before'=> 'csrf', 'uses'=> 'DocumentTypesController@showdoc']);
Route::get('showDocuments/{id}/{name}',['as'=> 'showDocuments', 'before'=> 'csrf', 'uses'=> 'DocumentsController@showdep']);
Route::post('documentSearch', ['as' => 'documentSearch', 'before'=> 'csrf', 'uses' => 'DocumentsController@searchdoc']);
Route::get('/register',function(){
	return view('auth/register');
});
Route::post('dropzone/uploadFiles', 'DocumentsController@uploadFiles');
Route::post('dropzone/bckpUploadFiles', 'DocumentsController@bckpUploadFiles');
Route::post('dropzone/uploadFiles2', 'DocumentsController@uploadFiles2');
Route::get('session', 'DocumentsController@session_set');
Route::get('wrkspacesrchdoc', ['as' => 'wrkspacesrchdoc', 'before'=> 'csrf', 'uses' => 'DocumentsController@wrkspacesrchdoc']);
Route::get('uploadFileEdit', ['as' => 'uploadFileEdit', 'before'=> 'csrf', 'uses' => 'DocumentsController@uploadEdit']);
Route::post('deleteSelected', ['as' => 'deleteSelected', 'before'=> 'csrf', 'uses' => 'DocumentsController@deleteAll']);
Route::post('deletePublished', ['as' => 'deletePublished', 'before'=> 'csrf', 'uses' => 'DocumentsController@deleteAllPublished']);
Route::post('uploadFileEdit', ['as' => 'uploadFileEdit', 'before'=> 'csrf', 'uses' => 'DocumentsController@uploadEdit']);
/*Route::post('publishSelected', ['as' => 'publishSelected', 'before'=> 'csrf', 'uses' => 'DocumentsController@moveAll']);*/
Route::post('publishChecked', ['as' => 'publishChecked', 'before'=> 'csrf', 'uses' => 'DocumentsController@moveCheckedAll']);
Route::post('draftSelected', ['as' => 'draftSelected', 'before'=> 'csrf', 'uses' => 'DocumentsController@moveAsDraft']);
Route::post('removeDocument', ['as' => 'removeDocument', 'before'=> 'csrf', 'uses' => 'DocumentsController@removeDocument']);
Route::post('removeDocumentTemp', ['as' => 'removeDocumentTemp', 'before'=> 'csrf', 'uses' => 'DocumentsController@removeDocumentTemp']);
Route::post('removeDocumentOnNavigation', ['as' => 'removeDocumentOnNavigation', 'before'=> 'csrf', 'uses' => 'DocumentsController@removeDocumentOnNavigation']);
//Import and export
Route::get('importFile', ['as' => 'importFile', 'before'=> 'csrf', 'uses' => 'ImportController@importView']);
Route::post('import_process', ['as' => 'import_process', 'before'=> 'csrf', 'uses' => 'ImportController@processImport']);
Route::post('import_parse', ['as' => 'import_parse', 'before'=> 'csrf', 'uses' => 'ImportController@parseImport']);

Route::get('downloadSample', 'ImportController@getDownloadSample');

Route::get('downloadSampledoc/{type}', 'ImportController@getDownloadSampledoc');

Route::post('importExcel', 'ImportController@importExcel');

Route::post('selectTypeimport', 'ImportController@session_Type');

Route::get('exportSample', 'ImportController@get_export');

Route::get('exportError', 'ImportController@get_error');

Route::get('exportclear',['as'=>'clear','before'=>'csrf','uses'=>'ImportController@export_clear']);

Route::post('doctypeSelectedImport','ImportController@getDownloadSampledoc');

Route::get('export','ImportController@exportview');

// 404 page not found
Route::get('pageNotFound',['as'=>'pageNotFound','uses'=>'HomeController@pageNotFound']);
// Token mismatch error
Route::get('tokenMismatchAuth',['as'=>'tokenMismatchAuth','uses'=>'Auth\AuthController@tokenMismatchAuth']);//If not logged in
Route::get('tokenMismatch',['as'=>'tokenMismatch','uses'=>'HomeController@tokenMismatch']);// If logged in

Route::get('relatedsearch/{id}',['as'=>'relatedsearch','uses'=>'DocumentsController@relatedsearch']);
Route::post('relateResult','DocumentsController@relateResult');
Route::post('relateResultTag','DocumentsController@relateResultTag');
Route::post('relateResultPrevious','DocumentsController@relateResult_previous');
//more details of previous versions
Route::get('documentsMoreDetailsPrevious', ['as' => 'documentsMoreDetailsPrevious', 'before'=> 'csrf', 'uses' => 'DocumentsController@moreDetailsPrevious']);
//delete from history table
Route::post('documentDeleteHistory', ['as' => 'v', 'before'=> 'csrf', 'uses' => 'DocumentsController@deleteDocumentHistory']);
//Delete all previous
Route::post('deletePrevious', ['as' => 'deletePrevious', 'before'=> 'csrf', 'uses' => 'DocumentsController@deleteAllPrevious']);
// Delete saved criteria
Route::get('deleteSavedSearch', ['as' => 'deleteSavedSearch', 'before'=> 'csrf', 'uses' => 'DocumentsController@deleteSavedSearch']); 

// FAQ
Route::get('faqs', ['as' => 'faq', 'before'=> 'csrf', 'uses' => 'FaqController@index']); 
Route::post('faqSave', ['as' => 'faqSave', 'before'=> 'csrf', 'uses' => 'FaqController@faqSave']); 
Route::get('faqEdit', ['as' => 'faqEdit', 'before'=> 'csrf', 'uses' => 'FaqController@faqEdit']); 
Route::get('deleteFaq', ['as' => 'deleteFaq', 'before'=> 'csrf', 'uses' => 'FaqController@deleteFaq']);  

// Updated work flow
// Save workflow history when change stage
Route::post('saveWorkflowHistory', ['as' => 'workFlow', 'before'=> 'csrf', 'uses' => 'WorkflowController@saveWorkflowHistory']); 

Route::post('clearAudit',['as' => 'clearAudit','before'=>'csrf', 'uses' => 'AuditsController@clearAudit']);
Route::post('passwordFailure_insertAudit',['as' => 'passwordFailure_insertAudit','before'=>'csrf', 'uses' => 'AuditsController@insertPasswordFailure']);
Route::post('AuditDelete_Notifications',['as' => 'AuditDelete_Notifications','before'=>'csrf', 'uses' => 'AuditsController@AuditDelete_Notifications']);
// To validate logged user password to approve and delete audits
Route::post('deleteAudits',['as'=>'deleteAudits','before'=>'csrf','uses'=>'AuditsController@deleteAudits']);
// Delete audits request table
Route::post('deleteAuditsRequest',['as'=>'deleteAuditsRequest','before'=>'csrf','uses'=>'AuditsController@deleteAuditsRequest']);

// Save dashboard widget postions
Route::post('saveWidgetPostion',['as'=>'saveWidgetPostion','before'=>'csrf','uses'=>'HomeController@saveWidgetPostion']);
//view united
Route::get('listview', ['as' => 'listview', 'before'=> 'csrf', 'uses' => 'DocumentsController@uploadAllView']);
//data table save
Route::post('dbManager',['as'=>'dbManager','before'=>'csrf','uses'=>'AuditsController@dbManager']);

//workflow

Route::get('workFlow', ['as' => 'workFlow', 'before'=> 'csrf', 'uses' => 'WorkflowController@index']);
Route::post('WorkflowSave/{id}', ['as' => 'WorkflowSave', 'before'=> 'csrf', 'uses' => 'WorkflowController@workflowsave']);
Route::post('WorkflowSelectSave/{id}', ['as' => 'WorkflowSelectSave', 'before'=> 'csrf', 'uses' => 'WorkflowController@WorkflowSelectSave']);
Route::post('renameNewStage', ['as' => 'renameNewStage', 'before'=> 'csrf', 'uses' => 'WorkflowController@renameNewStage']);
Route::post('workflowDelete', ['as' => 'workflowDelete', 'before'=> 'csrf', 'uses' => 'WorkflowController@workflowDelete']);
Route::post('workflowReArrangeStages', ['as' => 'workflowReArrangeStages', 'before'=> 'csrf', 'uses' => 'WorkflowController@workflowReArrangeStages']);
Route::post('addNewStage', ['as' => 'addNewStage', 'before'=> 'csrf', 'uses' => 'WorkflowController@addNewStage']);
Route::post('workflowStageDelete', ['as' => 'workflowStageDelete', 'before'=> 'csrf', 'uses' => 'WorkflowController@workflowStageDelete']);
Route::get('workflowEdit/{id}', ['as' => 'workflowEdit', 'before'=> 'csrf', 'uses' => 'WorkflowController@workflowEdit']);
Route::get('addviewWorkflow', ['as' => 'addviewWorkflow', 'before'=> 'csrf', 'uses' => 'WorkflowController@addviewWorkflow']);
Route::get('selectStages', ['as' => 'selectStages', 'before'=> 'csrf', 'uses' => 'WorkflowController@selectStages']);

//workflow by faisal START
Route::get('viewworkflow/{work_flow_id}',['as'=> 'viewworkflow', 'before'=> 'csrf', 'uses'=> 'WorkflowController@view_workflow']);
Route::post('workflow_activity_save',['as'=> 'workflow_activity_save', 'before'=> 'csrf', 'uses'=> 'WorkflowController@workflow_activity_save']);
Route::post('get_workflow_activity',['as'=> 'get_workflow_activity', 'before'=> 'csrf', 'uses'=> 'WorkflowController@get_workflow_activity']);
Route::post('change_workflow_stage',['as'=> 'change_workflow_stage', 'before'=> 'csrf', 'uses'=> 'WorkflowController@change_workflow_stage']);
Route::post('workflow_stages',['as'=> 'workflow_stages', 'before'=> 'csrf', 'uses'=> 'WorkflowController@workflow_stages']);
Route::post('add_to_workflow',['as'=> 'add_to_workflow', 'before'=> 'csrf', 'uses'=> 'WorkflowController@add_to_workflow']);
Route::get('get_obejects',['as'=> 'get_obejects', 'before'=> 'csrf', 'uses'=> 'WorkflowController@get_obejects']);
Route::get('get_users_list',['as'=> 'get_users_list', 'before'=> 'csrf', 'uses'=> 'WorkflowController@get_users_list']);
Route::get('workflow_activity_delete',['as'=> 'workflow_activity_delete', 'before'=> 'csrf', 'uses'=> 'WorkflowController@workflow_activity_delete']);
Route::get('workflow_complete',['as'=> 'workflow_complete', 'before'=> 'csrf', 'uses'=> 'WorkflowController@workflow_complete']);
Route::post('get_workflow_docs',['as'=> 'get_workflow_docs', 'before'=> 'csrf', 'uses'=> 'WorkflowController@get_workflow_docs']);
Route::post('saveActivityPostion',['as'=> 'saveActivityPostion', 'before'=> 'csrf', 'uses'=> 'WorkflowController@saveActivityPostion']);
Route::get('add_to_workflow_modal',['as'=> 'add_to_workflow_modal', 'before'=> 'csrf', 'uses'=> 'WorkflowController@add_to_workflow_modal']);
Route::get('search_object_to_workflow_modal',['as'=> 'search_object_to_workflow_modal', 'before'=> 'csrf', 'uses'=> 'WorkflowController@search_object_to_workflow_modal']);
Route::get('search_object_data',['as'=> 'search_object_data', 'before'=> 'csrf', 'uses'=> 'WorkflowController@search_object_data']);
Route::post('load_activity_form',['as'=> 'load_activity_form', 'before'=> 'csrf', 'uses'=> 'WorkflowController@load_activity_form']);
Route::post('save_action_workflow',['as'=> 'save_action_workflow', 'before'=> 'csrf', 'uses'=> 'WorkflowController@save_action_workflow']);
Route::get('workflow_exit',['as'=> 'workflow_exit', 'before'=> 'csrf', 'uses'=> 'WorkflowController@workflow_exit']);

//workflow by faisal END
	
// Activity module
Route::get('activities/{id?}', ['as' => 'activity', 'before'=> 'csrf', 'uses' => 'ActivityController@index']); 
Route::get('activityList', ['as' => 'activityList', 'before'=> 'csrf', 'uses' => 'ActivityController@activityList']); 
Route::post('activitySave', ['as' => 'activitySave', 'before'=> 'csrf', 'uses' => 'ActivityController@activitySave']); 
Route::get('deleteActivity', ['as' => 'deleteActivity', 'before'=> 'csrf', 'uses' => 'ActivityController@deleteActivity']);

// Save workflow history when change stage
Route::get('saveWorkflowHistory', ['as' => 'saveWorkflowHistory', 'before'=> 'csrf', 'uses' => 'WorkflowController@saveWorkflowHistory']); 
// Show document
Route::get('showWorkflowHistory/{name}/{id}',['as' => 'showWorkflowHistory', 'before'=> 'csrf', 'uses' => 'WorkflowController@showWorkflowHistory']);

// Image annotation
Route::get('saveImgAnnotation', ['as' => 'saveImgAnnotation', 'before'=> 'csrf', 'uses' => 'DocumentsController@saveImgAnnotation']); 
Route::get('getImgAnnotations', ['as' => 'getImgAnnotations', 'before'=> 'csrf', 'uses' => 'DocumentsController@getImgAnnotations']); 
Route::post('deleteImgAnnotation', ['as' => 'deleteImgAnnotation', 'before'=> 'csrf', 'uses' => 'DocumentsController@deleteImgAnnotation']); 

// Save and update image rotations
Route::get('saveImgRotations', ['as' => 'saveImgRotations', 'before'=> 'csrf', 'uses' => 'DocumentsController@saveImgRotations']); 

//forms
Route::get('my_forms', ['as' => 'my_forms', 'before'=> 'csrf', 'uses' => 'FormController@my_forms']);
Route::post('my_forms_filter', ['as' => 'my_forms_filter', 'before'=> 'csrf', 'uses' => 'FormController@my_forms_filter']);
Route::get('forms', ['as' => 'forms', 'before'=> 'csrf', 'uses' => 'FormController@forms']);
Route::post('viewform',['as'=> 'viewform', 'before'=> 'csrf', 'uses'=> 'FormController@view_form']); 
Route::get('getform',['as'=> 'getform', 'before'=> 'csrf', 'uses'=> 'FormController@load']);
Route::get('form/{form_id?}',['as'=> 'form', 'before'=> 'csrf', 'uses'=> 'FormController@form']);
Route::get('load_form',['as'=> 'load_form', 'before'=> 'csrf', 'uses'=> 'FormController@load_form']);
Route::post('save_dynamic_form',['as'=> 'save_dynamic_form', 'before'=> 'csrf', 'uses'=> 'FormController@save_dynamic_form']);
//save form values
Route::post('saveFormValues',['as'=> 'saveFormValues', 'before'=> 'csrf', 'uses'=> 'FormController@saveFormValues']);

Route::post('saveFormValuesAdd',['as'=> 'saveFormValuesAdd', 'before'=> 'csrf', 'uses'=> 'FormController@saveFormValuesAdd']);
//edit form values
Route::post('editFormValues',['as'=> 'editFormValues', 'before'=> 'csrf', 'uses'=> 'FormController@editFormValues']);
Route::get('form_details/{form_id}',['as'=> 'form_details', 'before'=> 'csrf', 'uses'=> 'FormController@form_details']);
Route::get('formMoreDetails',['as'=> 'formMoreDetails', 'before'=> 'csrf', 'uses'=> 'FormController@formMoreDetails']); 
Route::post('deleteform',['as'=> 'deleteform', 'before'=> 'csrf', 'uses'=> 'FormController@deleteform']);
Route::post('single_forms_filter_ajax',['as'=> 'single_forms_filter_ajax', 'before'=> 'csrf', 'uses'=> 'FormController@single_forms_filter_ajax']); 
Route::get('form_permission/{form_id}', ['as' => 'form_permission', 'before'=> 'csrf', 'uses' => 'FormController@form_permission']);
Route::post('form_permissionSave/{form_id}',['as'=> 'form_permissionSave', 'before'=> 'csrf', 'uses'=> 'FormController@form_permissionSave']);
//single submitted form delete
Route::post('deleteSingleSubmittedform',['as'=> 'deleteSingleSubmittedform', 'before'=> 'csrf', 'uses'=> 'FormController@deleteSingleSubmittedform']);
Route::post('save_action_form',['as'=> 'save_action_form', 'before'=> 'csrf', 'uses'=> 'FormController@save_action_form']);
Route::post('formAttachments', 'FormController@formAttachments');
Route::post('deleteAttachments', 'FormController@deleteAttachments');
Route::post('getattachDetails', 'FormController@getattachDetails');
Route::post('deleteAttached',['as'=> 'deleteAttached', 'before'=> 'csrf', 'uses'=> 'FormController@deleteAttached']);

//Read Notification
Route::get('load_notification',['as'=> 'load_notification', 'before'=> 'csrf', 'uses'=> 'NotificationController@load_notification']);
Route::get('read_notification',['as'=> 'read_notification', 'before'=> 'csrf', 'uses'=> 'NotificationController@read_notification']);
Route::get('notifications_list',['as'=> 'notifications_list', 'before'=> 'csrf', 'uses'=> 'NotificationController@index']);
Route::post('notificationfilter',['as'=> 'notificationfilter', 'before'=> 'csrf', 'uses'=> 'NotificationController@notificationfilter']);

// Test
Route::get('test',['as'=> 'test', 'before'=> 'csrf', 'uses'=> 'TestController@test']);
// FTP Test Connectivity
Route::post('test_ftp_connectivity',['as'=> 'test_ftp_connectivity', 'before'=> 'csrf', 'uses'=> 'SettingsController@test_ftp_connectivity']);
Route::post('changeSkin', 'HomeController@changeSkin');
Route::post('getSkin', 'HomeController@getSkin');

Route::post('form_adv_search',['as'=> 'viewform', 'before'=> 'csrf', 'uses'=> 'FormController@form_adv_search']);

//Closed Workflow
Route::post('wrkflowDelete', ['as' => 'wrkflowDelete', 'before'=> 'csrf', 'uses' => 'WorkflowsController@delete']);
Route::get('closed_workflow/{id?}',['as'=> 'closed_workflow', 'before'=> 'csrf', 'uses'=> 'WorkflowsController@closed_workflow']);

Route::post('save_closed_workflow',['as'=> 'save_closed_workflow', 'before'=> 'csrf', 'uses'=> 'WorkflowsController@save_closed_workflow']);

Route::get('load_Workflow_json/{id?}',['as'=> 'load_Workflow_json', 'before'=> 'csrf', 'uses'=> 'WorkflowsController@load_Workflow_json']);

Route::get('load_Workflow_json_data/{id?}',['as'=> 'load_Workflow_json_data', 'before'=> 'csrf', 'uses'=> 'WorkflowsController@load_Workflow_json_data']);

Route::get('load_workflow_objects',['as'=> 'load_workflow_objects', 'before'=> 'csrf', 'uses'=> 'WorkflowsController@load_workflow_objects']);

Route::get('allworkflow',['as'=> 'allworkflow', 'before'=> 'csrf', 'uses'=> 'WorkflowsController@allworkflow']);

Route::post('ajax_workflow_list',['as'=> 'ajax_workflow_list', 'before'=> 'csrf', 'uses'=> 'WorkflowsController@ajax_workflow_list']);

Route::get('view_workflow/{work_flow_id}',['as'=> 'view_workflow', 'before'=> 'csrf', 'uses'=> 'WorkflowsController@view_workflow']);

Route::post('workflows_stages',['as'=> 'workflows_stages', 'before'=> 'csrf', 'uses'=> 'WorkflowsController@workflows_stages']);

Route::post('wfProcessDelete',['as'=> 'wfProcessDelete', 'before'=> 'csrf', 'uses'=> 'WorkflowsController@wfProcessDelete']);

Route::get('add_to_workflows_modal',['as'=> 'add_to_workflows_modal', 'before'=> 'csrf', 'uses'=> 'WorkflowsController@add_to_workflows_modal']);

Route::post('start_workflow_process',['as'=> 'start_workflow_process', 'before'=> 'csrf', 'uses'=> 'WorkflowsController@start_workflow_process']);

Route::get('view_wf_process/{work_flow_id?}',['as'=> 'view_wf_process', 'before'=> 'csrf', 'uses'=> 'WorkflowsController@view_wf_process']);

Route::get('view_wf_process_old/{work_flow_id?}',['as'=> 'view_wf_process_old', 'before'=> 'csrf', 'uses'=> 'WorkflowsController@view_wf_process_old']);

Route::post('transition_click',['as'=> 'transition_click', 'before'=> 'csrf', 'uses'=> 'WorkflowsController@transition_click']);

Route::get('workflow_new_activity/{activity_id?}',['as'=> 'workflow_new_activity', 'before'=> 'csrf', 'uses'=> 'WorkflowsController@workflow_new_activity']);

Route::post('workflows_activity_save',['as'=> 'workflows_activity_save', 'before'=> 'csrf', 'uses'=> 'WorkflowsController@workflows_activity_save']);
Route::get('delegateUser',['as'=> 'delegateUser', 'before'=> 'csrf', 'uses'=> 'WorkflowsController@delegateUser']);
Route::get('delegate_user_save',['as'=> 'delegate_user_save', 'before'=> 'csrf', 'uses'=> 'WorkflowsController@delegate_user_save']);
Route::post('ajax_activity_list',['as'=> 'ajax_activity_list', 'before'=> 'csrf', 'uses'=> 'WorkflowsController@ajax_activity_list']);
Route::post('workflow_delete_activity',['as'=> 'workflow_delete_activity', 'before'=> 'csrf', 'uses'=> 'WorkflowsController@workflow_delete_activity']);

Route::post('load_department_users',['as'=> 'load_department_users', 'before'=> 'csrf', 'uses'=> 'DepartmentsController@load_department_users']);

Route::post('load_department_users',['as'=> 'load_department_users', 'before'=> 'csrf', 'uses'=> 'DepartmentsController@load_department_users']);
Route::post('load_rule_components',['as'=> 'load_rule_components', 'before'=> 'csrf', 'uses'=> 'WorkflowsController@load_rule_components']);

Route::post('get_wf_details',['as'=> 'get_wf_details', 'before'=> 'csrf', 'uses'=> 'WorkflowsController@get_wf_details']);

Route::get('cronjob_test',['as'=> 'cronjob_test', 'before'=> 'csrf', 'uses'=> 'Croncontroller@crontest']);

//cronjob url
Route::get('cronjob_test',['as'=> 'cronjob_test', 'before'=> 'csrf', 'uses'=> 'Croncontroller@crontest']);
//export and imort new
Route::post('RecordscountExport',['as'=> 'RecordscountExport', 'before'=> 'csrf', 'uses'=> 'ImportController@RecordscountExport']);

Route::post('RecordscountExportData',['as'=> 'RecordscountExportData', 'before'=> 'csrf', 'uses'=> 'ImportController@RecordscountExportData']);

Route::post('NumberZip',['as'=> 'NumberZip', 'before'=> 'csrf', 'uses'=> 'ImportController@NumberZip']);

Route::post('RecordscountImport',['as'=> 'RecordscountImport', 'before'=> 'csrf', 'uses'=> 'ImportController@RecordscountImport']);

Route::post('chunkExport',['as'=> 'chunkExport', 'before'=> 'csrf', 'uses'=> 'ImportController@exportadvncdoc']);

Route::post('chunkImport',['as'=> 'chunkImport', 'before'=> 'csrf', 'uses'=> 'ImportController@importadvncdoc']);

Route::post('downloadDataZip',['as'=> 'downloadDataZip', 'before'=> 'csrf', 'uses'=> 'ImportController@downloadDataZip']);

Route::post('getTotalRecords',['as'=> 'getTotalRecords', 'before'=> 'csrf', 'uses'=> 'ImportController@getTotalRecords']);
//content search
Route::get('contentSearch', ['as' => 'contentSearch', 'before'=> 'csrf', 'uses' => 'DocumentsController@contentSearchview']);

Route::post('RecordsCountContentSearch',['as'=> 'RecordsCountContentSearch', 'before'=> 'csrf', 'uses'=> 'DocumentsController@RecordsCountContentSearch']);

Route::post('contentAdvSearch',['as'=> 'contentAdvSearch', 'before'=> 'csrf', 'uses'=> 'DocumentsController@contentSearch']);
//default view set
Route::post('setview', 'DocumentsController@setDefaultview');
//document type reorder
Route::post('rowReorder',['as'=> 'rowReorder', 'before'=> 'csrf', 'uses'=> 'DocumentTypesController@rowReorder']);
//dept reorder
Route::post('rowReorderDept',['as'=> 'rowReorderDept', 'before'=> 'csrf', 'uses'=> 'DepartmentsController@rowReorderDept']);
//stack reorder
Route::post('rowReorderStack',['as'=> 'rowReorderDept', 'before'=> 'csrf', 'uses'=> 'StacksController@rowReorderStack']);
//encrypt
Route::post('documentEncrypt',['as'=> 'documentEncrypt', 'before'=> 'csrf', 'uses'=> 'DocumentsController@encrypt']);
//decrypt
Route::post('documentDecrypt',['as'=> 'documentDecrypt', 'before'=> 'csrf', 'uses'=> 'DocumentsController@decrypt']);
//encrypt all
Route::post('bulkEncrypt', ['as' => 'bulkEncrypt', 'before'=> 'csrf', 'uses' => 'DocumentsController@bulkEncrypt']);

Route::get('file/{foldername}/{filename}', ['as' => 'getFile', 'before'=> 'csrf', 'uses' => 'FileController@getFile']);

Route::get('ExportZip/{filename}', ['as' => 'getZip', 'before'=> 'csrf', 'uses' => 'FileController@getZip']);
Route::get('logo/{filename}', ['as' => 'logo', 'before'=> 'csrf', 'uses' => 'FileController@logo']);
//advsearch result export
Route::get('ExportSearch/{filename}', ['as' => 'ExportSearch', 'before'=> 'csrf', 'uses' => 'FileController@ExportSearch']);

Route::post('DeleteZip', ['as' => 'DeleteZip', 'before'=> 'csrf', 'uses' => 'FileController@DeleteZip']);

Route::get('oldExportView',['as'=> 'oldExportView', 'before'=> 'csrf', 'uses'=> 'FileController@oldExportView']);
Route::get('dupdelete',['as'=> 'dupdelete', 'before'=> 'csrf', 'uses'=> 'Croncontroller@dup_delete']);

//Modules
Route::get('load_apps',['as'=> 'load_apps', 'before'=> 'csrf', 'uses'=> 'AppsController@load_apps']);
Route::get('apps', ['as' => 'apps', 'before'=> 'csrf', 'uses' => 'AppsController@index']);
Route::get('appslistview/{id}/{app?}', ['as' => 'appslistview', 'before'=> 'csrf', 'uses' => 'AppsController@list_apps']);
Route::get('appsEdit/{id}', ['as' => 'appsEdit', 'before'=> 'csrf', 'uses' => 'AppsController@edit_apps']);
Route::get('appsLink/{id}', ['as' => 'appsLink', 'before'=> 'csrf', 'uses' => 'AppsController@edit_link']);
Route::get('appsView/{id}', ['as' => 'appsEdit', 'before'=> 'csrf', 'uses' => 'AppsController@view_apps']);
Route::post('save_apps', ['as' => 'save_apps', 'before'=> 'csrf', 'uses' => 'AppsController@save_apps']);
Route::get('document_column_suggession', ['as' => 'document_column_suggession', 'before'=> 'csrf', 'uses' => 'AppsController@document_column_suggession']);
Route::post('saveAppsWidgetPostion',['as'=>'saveAppsWidgetPostion','before'=>'csrf','uses'=>'AppsController@saveAppsWidgetPostion']);
Route::post('my_apps_filter', ['as' => 'my_apps_filter', 'before'=> 'csrf', 'uses' => 'AppsController@my_apps_filter']);
Route::post('deleteSubmittedIndexvalue',['as'=> 'deleteSubmittedIndexvalue', 'before'=> 'csrf', 'uses'=> 'AppsController@deleteSubmittedIndexvalue']);
Route::get('load_app_form', ['as' => 'load_app_form', 'before'=> 'csrf', 'uses' => 'AppsController@load_app_form']);
Route::get('addapps/{id}/{app}', ['as' => 'addapps', 'before'=> 'csrf', 'uses' => 'AppsController@addapps']);
Route::get('viewapp',['as'=> 'viewapp', 'before'=> 'csrf', 'uses'=> 'AppsController@view_app']); 
Route::get('load_app',['as'=> 'load_app', 'before'=> 'csrf', 'uses'=> 'AppsController@load_app']);
Route::post('saveAppValues',['as'=> 'saveAppValues', 'before'=> 'csrf', 'uses'=> 'AppsController@saveAppValues']);
Route::post('deleteAppAttached',['as'=> 'deleteAppAttached', 'before'=> 'csrf', 'uses'=> 'AppsController@deleteAppAttached']);
Route::post('save_app_links', ['as' => 'save_app_links', 'before'=> 'csrf', 'uses' => 'AppsController@save_app_links']);
Route::get('checkhasRecords', ['as' => 'checkhasRecords', 'before'=> 'csrf', 'uses' => 'AppsController@checkhasRecords']);
Route::post('appDelete',['as'=> 'appDelete', 'before'=> 'csrf', 'uses'=> 'AppsController@appDelete']);
Route::get('viewappdata/{app_id}/{doc_id}', ['as' => 'viewappdata', 'before'=> 'csrf', 'uses' => 'AppsController@viewappdata']);
Route::get('related_app_doc/{app_id}/{doc_id}', ['as' => 'viewappdata', 'before'=> 'csrf', 'uses' => 'AppsController@related_app_doc']);
Route::get('load_app_data',['as'=> 'load_app_data', 'before'=> 'csrf', 'uses'=> 'AppsController@load_app_data']);
Route::get('importRecords',['as'=> 'importRecords', 'before'=> 'csrf', 'uses'=> 'AppsController@importrecords']);
Route::post('appSample',['as'=> 'appSample', 'before'=> 'csrf', 'uses'=> 'AppsController@appSample']);
Route::get('auto_complete_document_column', ['as' => 'auto_complete_document_column', 'before'=> 'csrf', 'uses' => 'AppsController@auto_complete_document_column']);
Route::get('importRecords',['as'=> 'importRecords', 'before'=> 'csrf', 'uses'=> 'AppsController@importrecords']);
Route::post('appSample',['as'=> 'appSample', 'before'=> 'csrf', 'uses'=> 'AppsController@appSample']);
Route::post('app_import_parse', ['as' => 'app_import_parse', 'before'=> 'csrf', 'uses' => 'AppsController@appParseImport']);
Route::post('app_importExcel', 'AppsController@app_importExcel');
Route::post('multipleAppDelete', ['as' => 'multipleAppDelete', 'before'=> 'csrf', 'uses' => 'AppsController@deleteAll']);
Route::get('appMoreDetails',['as'=> 'appMoreDetails', 'before'=> 'csrf', 'uses'=> 'AppsController@appMoreDetails']);

});
?>
