<?php
/*
------------------
Language: English
------------------
*/
$max_upload_size		    =	ini_get('upload_max_filesize');
//Gb=> G / Mb => M
$format = substr($max_upload_size, -1);
//echo $format;
$int_val = substr($max_upload_size, 0, -1);
//echo $int_val;
//if gb convert to mb
if($format == 'G')
{
    $max_upload_size = ($int_val*1024).'MB';
}
if($format == 'M')
{
    $max_upload_size = ($int_val).'MB';
}
$language = array();

//Login
$language['toptech']					=	'Toptech Informatics';
$language['username']					=	'User Name';
$language['password']					=	'Password';
$language['curpassword']				=	'Current Password';
$language['newpassword']				=	'New Password';
$language['confpassword']				=	'Confirm Password';
$language['email_address'] 				=   'E-Mail Address';
$language['sign in']					=	'Sign In';
$language['log in']						=	'Log In';
$language['home']						=	'Home';
$language['admin']						=	'Admin';
$language['dms']						=	'DMS';
$language['forgot_pwd']					=	'I forgot my password';
$language['remember me']				=	'Remember Me';
$language['reset_paswd']				=	'Reset Password';
$language['no_notif']					=	'No notifications';
$language['pswd_contain']				=	'Password must contain the following:';
$language['active_title']				=	'Currently Active. Click here to change';
$language['inactive_title']				=	'Currently Inactive. Click here to change';
$language['currently_active_title']		=	'Currently Active';
$language['currently_inactive_title']	=	'Currently Inactive';
$language['cur_ver']					=	'Current Version';
$language['checked_version']			= 	'Automatically increment the version when checking in';
$language['comments']					=	'Check Out Comments';
$language['help']						=	'Help';
$language['faq']						=	'FAQ';
$language['faqs']						=	'FAQs';
$language['title']						=	'Title';
$language['no_notification']			=	'No notification found';
$language['checkinout'] 				= 	'Check In/Out';
$language['add_user'] 					= 	'Add User';
$language['add_form']					=	'Add Form';
$language['inactive']					=	'Inactive';
$language['active']						=	'Active';
$language['locl_title']					=	'This account is locked out. Click to unlock';
$language['last_paswd_change']			=	'Last Password Change';
$language['signout_title']				=	'Currently Signed In. Click here to force a Sign Out';
$language['notification']				=	'Notifications';
$language['edit_user']					=	'Edit User';
$language['parse_csv']					=	'Parse CSV';
$language['caps_and_small']				=	'Capital and Small letter Combination';
$language['spl_chars']					=	'Special Characters';
$language['alphabets']					=	'Alphabets';
$language['nums']						=	'Numbers';
$language['splchar']					=	'!@#$^&*()';
$language['department_name']			=	'Department Name';
$language['user_status']				=	'User Status';
$language['active']						=	'Active';
$language['inactive']					=	'Inactive';

$language['rows_per_page']				=	'Rows per Page';
$language['rows_per_desc']				=	'Set the number of limit for pagination';
$language['passwd_expiry']				=	'Password Expiry';
$language['document_expiry']			=	'Document Expiry Notification';
$language['passwd_expired']				=	'Password has Expired';
$language['login_attempts']				=	'Login Attempts';
$language['acc_lock_sett']				=	'Security Settings';
$language['pswd_cmplx_desc']			=	'Define how complex a password must be';
$language['login_attempts_desc']		=	'Set the no. of unsuccesful Login attepts after which the account will be locked out. Only an Administrator can unlock a locked account. Unlimited attempts will be allowed if set to 0';
$language['passwd_expiry_desc']			=	'Set the number of days after which the user will be forced to change the password. Passwords will never expire if set to 0';
$language['document_expiry_desc']		=	'Set the number of days to get the document expiry notification';

$language['pswd_length_desc']			=	'Set the minimum and maximum length of the password';
$language['reset_passwd_message']		=	'Your password has expired, please choose a new password';

$language['passwd_complexity']			=	'Password Complexity';
$language['password_length']			=	'Password Length';
$language['general_settings']			=	'General Settings';
$language['company_settings']			=	'Company Settings';
//Register
$language['reset_pwd_msg']				=	'Please enter your email address for reset password';
$language['email_id']					=	'Email Id';
$language['pwd_reset']					=	'Send Password Reset Link';
$language['pwd_reset_validate']			=	'Please enter your email address for reset password';
$language['pwd_reset_success']			=	'We have e-mailed your password reset link!';
$language['pwd_reset_title']			=	'Reset Password';

$language['form_note']					=	'Choose department(s) and/or user(s) to set specific permissions. Choose one or more department(s) if all users under that department(s) must be given that permission. If only specific user(s) need to be given permission choose only user(s)';

//Dashboard
$language['recent documents']			=	'Recent Documents';
$language['view all documents']			=	'View All Documents';
$language['version']					=	'Version 1.0.0';

// Work Flow
$language['work_flow']					=	'Work Flow';
$language['workflow_stage']				=	'Work Flow Stage';
$language['create_and_list_stage']		=	'Create And List Stages';
$language['stage_name']					=	'Stage Name';

//Dashboard side bar items
$language['dashboard'] 					= 	'Dashboard';
$language['all documents']				=	'All Documents';
$language['document types']				=	'Document Types';
$language['all document types']			=	'All Document Types';
$language['stacks']						=	'Stacks';
$language['all stacks']					=	'All Stacks';
$language['departments']				=	'Departments';
$language['all departments']			=	'All Departments';
$language['tag words'] 					= 	'Tag Words';
$language['audits']						=	'Audits';
$language['users']						=	'Users';
$language['settings']					=	'Settings';
$language['sign out']					=	'Sign Out';
$language['documents']					=	'Documents';
$language['document']					=	'Document';
$language['stack']						=	'Stack';

// User Module
$language['view'] 					    = 	'View';
$language['checkout'] 					= 	'Check Out';

//Top icons
$language['add new']					=	'Add New';
$language['check out']					=	'Check Out';
$language['checkin']					=	'Check In';
$language['delete']						=	'Delete';
$language['refresh']					=	'Refresh';
$language['listview']					=	'List View';
$language['import list']				=	'Import List';
$language['import_data']				=	'Import Data';
$language['chkout list']				=	'Check Out List';
$language['new doc']					=	'Add Document';
$language['new doc type']				=	'Add Document Type';
$language['new stack']					=	'Add Stack';
$language['new dept']					=	'New Department';
$language['grid']						=	'Grid View';
$language['upld_files']					=	'Upld Files';

//Document details
$language['doc no']						=	'Doc No';
$language['document no']				=	'Document No';
$language['doc name']					=	'Doc Name';
$language['document name']				=	'Document Name';
$language['ownership']					=	'Ownership';
$language['version no']					=	'Version No';
$language['status']						=	'Status';
$language['last updated']				=	'Updated';
$language['check out']					=	'Check Out';
$language['draft']						=	'Draft';
$language['published']					=	'Published';
$language['document type']				=	'Document Type';
$language['document path']				=	'Document Path';
$language['file Name']					=	'File Name';
$language['department']					=	'Department';
$language['last check out date']		=	'Last Check Out Date';
$language['last check in date']			=	'Last Check In Date';
$language['created by']					=	'Created By';
$language['created on']					=	'Created On';
$language['created at']					=	'Created At';
$language['last modified by']			=	'Last Modified By';
$language['last updated at']			=	'Last Updated At';
$language['check out date']				=	'Check Out Date';
$language['check out by']				=	'Check Out By';
$language['last updated at']			=	'Last Updated At';
$language['created date - from']		=	'Created Date - From';
$language['created date - to']			=	'Created Date - To';
$language['last modified - from']		=	'Last Modified - From';
$language['last modified - to']			=	'Last Modified - To';

$language['search_criteria']			=	'Previously Saved Searches';
$language['update_criteria']			=	'Update Current Search Criteria';
$language['save_criteria_name']			=	'Save Current Search Criteria';
$language['criteria_name']			    =	'Criteria Name';
$language['change_criteria']			=	'Change Criteria';

$language['search_option']				=	'Search Option';

//Workspace
$language['current path']				=	'Current Path';
$language['DMS ROOT']					=	'DMS ROOT';
$language['new folder']					=	'New Folder';
$language['wrkspacemsg']				=       'Right click on the folder name for Options';
$language['toggle-title']				=	'Hide/Show Workspace';
$language['num_docs']					=	' documents found';

//List elements, labels, placeholders
$language['options']					=	'Options';
$language['description']				=	'Description';
$language['actions']					=	'Actions';
$language['action']						=	'Action';
$language['created date']				=	'Created';
$language['updated date']				=	'Updated';
$language['index field']				=	'Index Field';
$language['field type']					=	'Field Type';
$language['mandatory']					=	'Mandatory';
$language['Action']						=	'Action';
$language['name']						=	'Name';
$language['date']						=	'Date';
$language['items']						=	'Item';
$language['integer']					=	'Integer';
$language['string']						=	'String';
$language['yes/no']						=	'Yes/No';
$language['piclist']					=	'Pick List';
$language['alphanumeric']				=	'Alphanumeric';
$language['number']						=	'Number';
$language['no']							=	'No';
$language['name']						=	'Name';
$language['field type']					=	'Field Type';
$language['mandatory']					=	'Mandatory';
$language['path']						=	'Path';
$language['category']					=	'Category';
$language['choose category']			=	'Choose Category';
$language['category name']				=	'Category Name';
$language['user type']					=	'User Type';
$language['company name']				=	'Company Name';
$language['address']					=	'Address';
$language['email']						=	'Email';
$language['logo']						=	'Logo';
$language['choose']						=	'Choose Option';
$language['upload document']			=	'Upload Document';
$language['document types']				=	'Document Types';
$language['pls_select']					=	'Please select tag category';
$language['upload folder']				=	'Folder Path';
$language['tag category']				=	'Tag Category';
$language['uploaded file']				=	'File Name';
$language['information']				=	'Information';
$language['notes']						=	'Notes';
$language['previous version']			=	'Previous Version';
$language['event log']					=	'Event Log';
$language['history']					=	'History';
$language['item']						=	'Item';
$language['sign in name']				=	'Sign In Name';
$language['date_from']					=	'Date - From';
$language['date_to']					=	'Date - To';
$language['department name']			=	'Department Name';
$language['index field']				=	'Index Field';
$language['field type']					=	'Field Type';
$language['mandatory']					=	'Mandatory';
$language['full name']					=	'Full Name';
$language['privileges']					=	'Document Access Privileges';
$language['form_privileges']			=	'Form Template Privileges';
$language['workflow_privileges']		=	'Workflow Template Privileges';
$language['expiry_date']				=	'Sign In Expiry Date';
$language['expir_date']				    =	'Expire';
$language['no expiry']					=	'No Expiry';
$language['last sign in']				=	'Last Sign In';
$language['super admin']				=	'Super Admin';
$language['group admin']				=	'Department Admin';
$language['regular user']				=	'Regular User';
$language['private_user']				=	'Private User';
$language['user']						=	'User';
$language['select category']			=	'Select Category';
$language['add category']				=	'Add Category and Tags';
$language['no.of docs']					=	'No. & Size of Docs by ';
$language['documents extension']		=	'Documents Extension';
$language['edit_document']				=	'Edit Document';
$language['missing']					=	'Missing';
$language['delete_all']					=	'Delete All';
$language['content_search']				=	'Content Search';
$language['place_holder_content_search']=	'Please input the text you want to search inside the documents';
$language['keyword_search']				=	'Index Field Search';
$language['content_search_desc']		= 	'The text you want to search inside the documents';
$language['place_holder_keywrd_search']=	'Please input the text you want to search';
$language['OR']							=	'OR';
$language['AND']						=	'AND';
$language['EXACT']						=	'EXACT';
$language['hint']						=	'Hint';
$language['Accuracy']					=	'Accuracy';
$language['search_term']				=	'Search Term';
$language['Exactly']					=	'Exactly';
$language['Partially']					=	'Partially';
$language['Complementary']				=	'Complementary';
$language['Show Only Expired Docs']		=	'Show Only Expired Documents';
$language['lnclude Expired Docs']		=	'Show All Documents';
$language['Exclude Expired Docs']		=	'Exclude Expired Documents';
$language['Expired Soon']				=	'Show Documents that will Expire Soon';
$language['Assigned docs']				=	'Show Documents Assigned for me';
$language['Assigned docs to']			=	'Show Documents Assigned by me';
$language['radio_all']					=	'all';
$language['radio_expired']				=	'expired';
$language['radio_exclude']				=	'exclude';
$language['radio_expire_soon']			=	'expire_soon';
$language['radio_assigned']				=	'assigned';
$language['radio_assigned_to']			=	'assigned_to';
$language['radio_rejected']				=	'rejected';
$language['radio_accepted']				=	'accepted';
$language['view_all_data']				=	'View All Data';
$language['plse_entr_ur_pswd']			=	'Please enter your password';
$language['filter']						=	'Filter';
$language['set_as_default']             =   'Set as default view';
$language['set_as_default_title']       =   'Set this view as default view';

//Buttons
$language['add new']					=	'Add New';
$language['search']						=	'Search';
$language['search by']					=	'Search by';
$language['update']						=	'Update';
$language['reset']						=	'Reset';
$language['select']						=	'Select';
$language['cancel']						=	'Cancel';
$language['close']						=	'Close';
$language['edit']						=	'Edit';
$language['list']						=	'List';
$language['delete']						=	'Delete';
$language['add']						=	'Add';
$language['back']						=	'Back';
$language['show document']				=	'Show Document';
$language['show users']					=	'Show Users';
$language['add document']				=	'Add Document';
$language['check in and publish']		=	'Check In and Publish';
$language['check in as draft']			=	'Check In as Draft';
$language['save']						=	'Save';
$language['save_and_close']				=	'Save And Close';
$language['add new index field']		=	'Add New Index Field';
$language['import data']				=	'Import CSV Data';
$language['import_csv']					=	'Import CSV Data';
$language['bulk_import_doc']		 	=	'Import Documents in Bulk';
$language['discard check out']			=	'Discard Check Out';
$language['open document']				=	'Open Document';
$language['view document']				=	'View Document';
$language['download']					=	'Download';
$language['history']					=	'History';
$language['more details']				=	'More Details';
$language['advance search']				=	'Advance Search';
$language['related documents']			=	'Related Documents';
$language['advsearch']					=	'Adv. Search';
$language['download_data']				=	'Download the Master Data';
$language['download_sample']			=	'Download a Sample File';
$language['import']						=	'Import';
$language['show all']					=	'Show All';
$language['add index']					=	'Add New Index Field';
$language['doc type col']				=	'Document type column';
$language['doc col name']				=	'Document column name';
$language['add notes']					=	'Add Notes';
$language['export']						=	'Export';
$language['export data']				=	'Export Data';
$language['export_csv_only']			=	'Export CSV Only';
$language['export_datafiles']			=	'Export Data Files';
$language['upload_all']					=	'Upload All';
$language['Perform']					=	'Perform';
$language['seperate_word_search']		=	'Seperate word search';
$language['Diacritics']					=	'Diacritics';
$language['search_results']				=	'Search Results';

// Label
$language['import_file']				=	'Import File';

//Side titles
$language['tempallview']				=	'These are the documents previously Imported and not yet Published';
$language['upload files']				=	'Click here to import documents';
$language['view upload documents']		=	'View Previously Imported Documents';
$language['import_mismatch']			=	'Download the list of mismatched data';
$language['recent search']				=	'Recently Searched List';
$language['uploaded docs']				=	'Imported Documents';
$language['input_search']				=	'Please input the search parameters below';
$language['related title']				=	'Choose the index field from the list below with which the documents are linked:';
$language['OR_content_note']			=	'Search for documents that contain at least one of the entered texts';
$language['AND_content_note']			=	'Search for documents that contain all of the entered texts';
$language['EXACT_content_note']			=	'Search for documents that contain exact similar to the entered texts';
$language['AND_search_option']			=	'Search documents that meets all the conditions(default)';
$language['OR_search_option']			=	'Search documents that meet any one of the conditions';

//messages and alerts
$language['no_file_msg']				=	"No new file has been uploaded. If there is no file to upload please choose 'Discard Check Out' button";
$language['err_ftp']				=	"Error in ftp connection. Make sure the ftp credentials is correct or try changing it to http in the settings.";
$language['no_file']					=	"No new file has been uploaded";
$language['no entry msg']				=	'Atleast one field must be required';
$language['no entry create_date_to']	=	'Please enter created date - To';
$language['no entry create_date_from']	=	'Please enter created date - From';
$language['no entry modify_date_to']	=	'Please enter last modified date - To';
$language['no entry modify_date_from']	=	'Please enter last modified date - From';
$language['import_msg']					=	"To import data in bulk, use a CSV file";
$language['no_doc__msg']				=	'There are no documents in this folder';
$language['no_permission']				=	'Sorry! You do not have the permission';
$language['no_permission_add']			=	'You have no permission for add';
$language['no_permission_download']		=	'You have no permission for download this document';
$language['no_permission_delete']		=	'You have no permission for delete this document';
$language['err_upload']					=	'Error occurs in uploading files';
$language['folder_check']				=	'Cannot delete folder, folder is not empty';
$language['temp_folder_check']			=	'Cannot delete folder, folder contains the temporary documents';
$language['root_delete']				=	'Cannot delete root folder';
$language['new_password_not_match']	    =	'New password must be different from the old password';
$language['parse_msg']					=	'The table below shows the first few lines of the data to be imported. Please map the fields correctly by selecting corresponding fields from the dropdown boxes at the bottom of each column';
//confirmation
$language['confirm_delete']				=	'Do you want to delete ?';
$language['confirm_delete_single']		=	'Do you want to delete ';
$language['confirm_logout']				=	'Are you sure you want to Sign Out ';
$language['confirm_unlock']				=	'This account is locked out. Do you want to unlock ';
$language['confirm_discard_multiple']	=	'Do you want to discard the Check Out of documents?';
$language['confirm_delete_multiple']	=	'Do you want to delete the documents?';
$language['confirm_discard_single']		=	'Do you want to discard the Check Out of ';
$language['confirm_checkin_multiple']	=	'Do you want to Check In ';
$language['confirm_checkin_single']		=	'Do you want to Check In ';
$language['confirm_draft_single']		=	'Do you want to Draft ';
$language['confirm_checkout_single']	=	'Do you want to Check Out ';
$language['confirm_import']				=	'Do you want to import data using the uploaded .csv file? This will overwrite any existing data';
$language['no_internet']				=	'Unable to load the file. Google Docs Viewer requires an active internet connection to view MS Office documents';

//success
$language['success_delete']				=	'deleted successfully';
$language['success_del_folder']			=	'Folder deleted successfully';
$language['success_update']				=	'updated successfully';
$language['success_discard']			=	'Document Check Out discarded';
$language['success_checkin']			=	'Checked In successfully';
$language['success_remove_document']	=	'Document removed successfully';
$language['success_draft']				=	'published as Draft successfully';
$language['success_add']				=	'\' added successfully';
$language['success_edit']				=	'edited successfully';
$language['success_publish']			=	'published successfully';
$language['no_document_found']			=	'No document found';
$language['deleted_selected_iteams']	=	'Successfully deleted selected iteams';

//error
$language['dont_hav_permission']	    =	"Sorry! You don't have the permission";
$language['index_change']	    		=	"This index field cannot be changed. Data exists";
$language['value_exists_err_msg']	    =	"is already in our database";

//others
$language['note']						=	'Note';
$language['date']						=	'Date';
$language['by']					        =	'By';
$language['hint_message']				=	'Use advance search for all fields other than Doc No and Doc Name';

$language['not select']					=	'Please select at least one document';
$language['no_permission']				=	'Sorry! You do not have the permission to view documents';
$language['upload error']				=	'You have tried to upload an invalid file extension';
$language['doc_already_exist']			=	'Document already exist. Please rename the document and upload';
$language['no notes']					=	'No notes available';
$language['no previous']				=	'No previous versions available';
$language['no event']					=	'No event log available';
$language['edit_upload']				=	'Edit Uploaded Files';
$language['upload_sample']				=	'Please upload file in correct format as shown in sample file';
$language['row_effected']				=	'rows have been affected';

$language['dept_not_add']				=	'Sorry you cannot add department';
$language['doc_type_col_not_add']		=	'Sorry you cannot add document type column';
$language['doc_type_not_add']			=	'Sorry you cannot add document type'; 
$language['doc_not_add']				=	'Sorry you cannot add document';
$language['dept_fill_correct']			=	'Please fill the departments data correctely';
$language['doc_type_col_fill_correct']	=	'Please fill the document type column data correctely';
$language['doc_type_fill_correct']		=	'Please fill the document type data correctely';
$language['already_db']					=	' is already in our database';
$language['contact_admin']				=	'Some issues in log file,contact admin';
$language['dept_not_edit']				=	'You cannot edit department data';
$language['doc_type_not_edit']			=	'You cannot edit document type data';
$language['cancel_checkout']			=	'Check out canceled';
$language['fill_correct_doc']			=	'Please fill the document correctly';
$language['no_doc_edit']				=	'Upload file error, no documents for edit';
$language['not_empty_tag']				=	'Please make sure that tag is not empty';
$language['information_miss']			=	'Some required information is missing. Please correct your entries and try again';
$language['not_note_save']				=	'Sorry,something went wrong.The note not saved';
$language['fill_doc_type_col']			=	'Cannot publish documents, Please fill document type columns';
$language['list_miss_match']			=	'List of mismatched entries';

$language['error_tagword']				=	'Sorry! cannot delete, This tagword is availabe in child table';
$language['doc_type_drag_msg']			=	'*You can add index fields for document types below and change the order of them by dragging';
$language['one_index_field']			=	'Failed!! You must have atleast one index field';
$language['img_size_msg']				=	'The image must contains width below 301px and height below 121px';
$language['upld_proper']				=	'Document file is missing, please upload the proper file';
$language['list_master_data']			=	'Given below is the list of master data for Department, Stack and Document Type';
$language['use_name_import']			=	'Please use these names in the csv files for data import';

$language['error_document_types']		=	'Sorry! This row cannot be deleted! the data exists in the related table';

// validation

$language['length_username']			=	' length must be minimum 6 and maximum 15';
$language['length_password']			=	"'Password' length must be minimum 6 and maximum 15";
$language['length_description']			=	"'Description' length must be less than 160 characters";
$language['length_others']				=	' length must be less than 50 characters';
$language['max_length']					=	'50';
$language['min_length_username']		=	'6';
$language['min_length_password']		=	'6';
$language['max_length_username']		=	'15';
$language['max_length_description']		=	'160';
$language['mail_tooltip']				=	"'Email Id' length must be less than 30 characters and valid";
$language['length_address']				=	"'Address' length must be less than 160 characters";
$language['no multiple doctype']		=	"Multiple document type selection not available";
$language['allowed_file_extensions']	=	'Allowed file extensions are';
$language['file_extension_csv']	        =	'Allowed file extension is .csv only';

// Required message
$language['is_required']	            =	'is required';  
$language['description_is_required']	=	'Description is required';
$language['email_id_required']	        =	'Email id required';  
$language['full_name_is_required']	    =	'Full Name is required'; 
$language['department_is_required']	    =	'Department is required';
$language['sign_in_name_is_required']	=	'Sign In Name is required';
$language['password_is_required']		=	'Password is required'; 
$language['current_pswd_is_required']	=	'Current password is required'; 
$language['confirm_psw_is_required']	=	'Confirm password is required';
$language['user_type_is_required']		=	'User Type is required'; 
$language['user_access_privileges_is_required']	=	'Document Access Privileges is required';
$language['form_access_privileges_is_required']	=	'Form Access Privileges is required'; 
$language['wf_access_privileges_is_required']	=	'Workflow Access Privileges is required';   
$language['sign_in_expiry_date_is_required']	=	'Sign in expiry date is required';
$language['No results found']			=	'No results found';
$language['No documents found']			=	'No documents found';
$language['Not accessed documents']		=	'Documents Not Accessed Last Days';
$language['count_of_days']				=	'Enter count of days';
$language['days_not_accessed_value']	=	'30';
$language['Fill Check Out Comments']	=	'Please fill Check Out Comments';
// swal msg
$language['Swal_are_you_sure']			=	'Are you sure?';
$language['Swal_deleted']				=	'Deleted!';
$language['Swal_row_cant_be_deleted']   =   'Sorry! This row cannot be deleted! the data exists in the related table';
$language['Swal_not_revert']			=	'Do you want to continue!';
$language['Swal_not_revert_custom']			=	'Any data that exists in this field will be lost. Do you want to continue?';
$language['Swal_ok']					=	'OK';
$language['Swal_warning']				=	'warning';
$language['Swal_success']				=	'Success';
$language['Swal_confirm_btn_text']		=	'Yes';

$language['required_info_missing']		=	'Some required information is missing. Please correct your entries and try again';
$language['Documents_published']		=	'Documents published successfully';
$language['Cannot_publish_docs']		=	'Cannot publish documents, Please fill document type columns';
$language['Documents_published_draft']	=	'Documents published as draft successfully';
$language['Documents_not_published_draft']	=	'Cannot publish documents as draft, Please fill document type columns';
$language['search_content']				=	'Search content on results';
$language['There is no data found']		=	'There is no data found';
$language['Documents_published_draft']	=	'Documents published as draft successfully';
$language['Documents_published_draft']	=	'Documents published as draft successfully';
$language['Documents_published_draft']	=	'Documents published as draft successfully';
$language['clear_all_changes']			=	'Clear all changes';
$language['clear_all_btn']				=	'Clear All';
$language['add_new_note']				=	'Add new note';
$language['note_required']				=	'Note is required';
$language['not_able_save']				=	'You will not be able to save the changes!';
$language['sure_navigate']				=	'Are you sure you want to navigate away from this page?';
$language['storagepath_history']		=	'http://www.toptechinfo.net/dms/storage/documents/documents_backup/';
$language['storagepath_docs']			=	'http://www.toptechinfo.net/dms/storage/documents/';
$language['delete_from_audits']			=	'Delete from audits';
$language['audit_data']					=	'Audit Data';
$language['clear_audit_data']			=	'Clear Audit Data';
$language['label_from']			        =	'From';
$language['label_to']				    =	'To';
$language['select_the_actions']			=	'Select The Actions';
$language['select_one']					=	'Please select atleast one';

// Recently updated.
$language['are_u_sure']					=	'Are you sure?';
$language['for_security_reson_msg']     =   'For security reasons, one more Super Admin must approve this action. A message will be sent to another Super Admin to approve your action and the records will be deleted after the approval is granted';
$language['wrong_password']				=	'Wrong password'; 
$language['approved_deleted_success']	=	'Successfully approved and deleted audits records';
$language['already_approved']			=	'Sorry! Already approved';
$language['deleted_audits']				=	'Successfully deleted audits records';
$language['success']				    =	'Success';
$language['date_before_no_months']		=	'Date before 6 months';
$language['old_date_from_audits']		=	'Old date from the audits';
$language['data_tbl_search_placeholder']=	' Search any records';
//view united
$language['import_view']				=	'import';
$language['checkout_view']				=	'checkout';
$language['list_view']					=	'list';
$language['stack_view']					=	'stack';
$language['document_type_view']			=	'documentType';
$language['department_view']			=	'department';

$language['no_records_to_delete']		=	'Sorry!There are no records to delete';
$language['already_proceed_this_action']=	'Already proceed this action';
$language['red_color_expired']			=	'- Documents shown in red are expired';
$language['label_Purge_audit_records_untill'] =	'Purge Audit Records Untill';
$language['option_length_must_be']      =	'Option length must be less than 50 characters';
$language['please_fill_the_text']       =	'Please fill the text correctly';
$language['select_box_is_empty']        =	'Select box is empty';
$language['check_out_discarded_success']=	'Document Check Out discarded successfully';
$language['toggle_columns']				=	'Show/Hide Column:';
$language['select_index_to_link']		=	'Select an index field to link';
$language['msg_not_send']		        =	'Sorry. The message could not be sent. The date that you entered does not match with required condition';
$language['token_error']		        =	'For security reason the session was disabled';
$language['sign_in_again']		        =	'Please sign in again';
$language['sort_reorder_column']		=	'Click to sort, Click and drag to reorder the column';
$language['reorder_column']		        =	'Click and drag to reorder the column';
$language['doc_assigned_to']		    =	'Assign To';
$language['assigned_by']		    	=	'Assigned By';
$language['under_review']		    	=	'Sorry! You do not have the permission, document under review';
$language['assign_placeholder']		    =	'State the reason to accept or reject the document';
$language['assign_add_note']		    =	'Add Note';
$language['timezone']		            =	'Time Zone';
$language['workflows']		            =	'Workflows';
$language['workflow']		            =	'Workflow';
$language['stage']		            	=	'Stage';
$language['stages']		            	=	'Workflow Stages';
$language['add stage']		            =	'Add New Stage';
$language['one_stage']					=	'Failed!! You must have atleast one Stage';
$language['order']		            	=	'Order';
$language['workflow name']		        =	'Workflow Name';
$language['all workflows']		        =	'All Workflows';
$language['add/view workflow']		    =	'Add/View Workflow';
$language['edit workflow']		    	=	'Edit Workflow';
$language['no workflow']		    	=	'No Workflows Found';
$language['no:docs']		    		=	'No. Docs';
$language['workflow_default_color']		=	'#781a22';
$language['Swal_stage_cant_be_deleted'] =   'Sorry! This stage cannot be deleted! the data exists in the related table';
$language['color']		        		=	'Color';
$language['max_upload_size']		    =	$max_upload_size;
$language['max upload msg']		    	=	'Maximum file upload size:';
$language['workfow_history']		    =	'Workflow History';
$language['show']		    		    =	'Show';
$language['no_data_available']		    =	'No data available in table';
$language['no_note_found']		        =	'No note found';
// Workfow

$language['activities']		            =	'Activities';
$language['list_workflows']		        =	'List of Workflows of Document';
$language['existing_workflow']		    =	' to Existing Workflow';
$language['no_stages']		            =	'No. of Stages';
$language['no']		                    =	'No.';

$language['responsible_user']		    =	'Assigned to';
$language['activity_date']		        =	'Date';
$language['activity_by_user']		    =	'Assigned by';
$language['activity_due_date']		    =	'Due Date';
$language['no_history']		            =	'Sorry!There is no history to list';
$language['activities']		            =	'Activities';
$language['select_workflow'] 			= 	'Select a Workflow'; 
$language['add_new_obj'] 				=	'Add an object to Workflow';
$language['select_document'] 			= 	'Select a Document'; 
$language['select_activity'] 			= 	'Select an Activity';
$language['select_user'] 				= 	'Select a User';
$language['select_workflow_stage'] 		= 	'Select Workflow Stage';
$language['workflow_stage'] 			= 	'Workflow Stage';
$language['workflow_type'] 				= 	'Type';
$language['activity'] 					= 	'Activity';
$language['activity_name'] 				= 	'Activity Name';
$language['activity_note'] 				= 	'Activity Note';
$language['activity_name_required'] 	= 	'Activity name is required';
$language['workflow_stage_required'] 	= 	'Workflow Stage is required';
$language['workflow_stage_change'] 		= 	'Change Stage';
$language['workflow_complete_note'] 	= 	'Are you sure you want to complete this workflow?';
$language['activity_delete_note'] 		= 	'Are you sure you want to delete this activity?';
$language['search_document'] 			= 	'Search Document';
$language['search_form'] 				= 	'Search Form';
$language['form_response'] 				= 	'Form Response';
$language['activity_response'] 			= 	'Activity Response';
$language['action_response'] 			= 	'Action Response';
$language['action_response_help_text'] 	= 	'Check this if you would like this activity to be added as one of the responses when a Form is submitted or an Activity is assigned to a user';
$language['last_activity_help_text'] 	= 	'When an activity marked as the last activity is selected in a stage of a workflow, that stage will be automaticaclly moved to the next stage. When selected at the last stage of a workflow, the workflow will be regarded as complete and the object will be moved out of the workflow';
$language['last_activity_wf_exit'] 		= 	'The workflow for this object is complete and it will be moved out of the workflow';
$language['last_activity_wf_next_stage']= 	'The activity you assigned is the last activity of this stage and this object will be moved to the next stage';
//forms
$language['forms'] 						= 	'Forms';
$language['form'] 						= 	'Form';
$language['form_name'] 					= 	'Form Name';
$language['Assigned_To']		    	=	'Assigned To';
$language['show_all_forms']		    	=	'All forms';
$language['form_submitted_by_me']		=	'Forms submitted by me';
$language['form_submitted_to_me']		=	'Forms submitted to me';
$language['form_help1']		    		=	'Click on the type of the field you want to add to this form from the Form Component section on the left side';
$language['form_help2']		    		=	'Select a user from this list if you would like this form to be submited to that user whenever it is used';
$language['form_help3']		    		=	'Select a workflow from this list if you would like this form to be added to that workflow automatically whenever it is used. You can also add a submitted form to a workflow at a later time';
// File viewer 
$language['add_view_annotation'] 		= 	'Add/View Annotation';
$language['reset_message'] 		        = 	'Changes that you made will reset it!';

$language['user_expiry_note']			=	'If you set an expiry date, the user will not be able to sign in after that date. This is useful when you want to give temporary access to users';

$language['wf_permissions'] 			= 	'Workflow Permissions';
$language['wf_permsn_info'] 			= 	'Choose department(s) and/or user(s) to set specific permissions. Choose one or more department(s) if all users under that department(s) must be given that permission. If only specific user(s) need to be given permission choose only user(s)';


$language['edit_index_firelds'] 		= 	'Edit Index Fields';
$language['soon_tobe_expired'] 			= 	'Expires in';
$language['expired_docs'] 				= 	'Expired';
$language['no_action_taken'] 			= 	'Due date has expired';
$language['color_legend'] 				= 	'Color legend:';
$language['all_other_docs'] 			= 	'All others';
//Notifications
$language['notifications'] 				= 	'Notifications';
$language['notification_type'] 			= 	'Notification Type';
$language['notification_details'] 		= 	'';
$language['from'] 						= 	'From';
$language['to'] 						= 	'To';
$language['type'] 						= 	'Type';
$language['search_in'] 					= 	'Search In';
$language['notification_all']			=	'Show All Notifications';
$language['notification_read']			=	'Show Notifications read';
$language['notification_not_read']		=	'Unread Notifications';
$language['notification_assigned_by_me']=	'Show Notifications Assigned by me';
$language['notification_assigned_to_me']=	'My Notifications';
$language['all notification types']		=	'All Notifications';
$language['doc_notifications'] 			= 	'Document Notifications';
$language['audit_notifications'] 		= 	'Audit Notifications';
$language['pwd_notifications'] 			= 	'Password Notifications';
$language['workflow_notifications'] 	= 	'Workflow Notifications';
$language['form_notifications'] 		= 	'Form Notifications';
$language['activity_notifications'] 	= 	'Activity Notifications';
$language['general_notifications'] 		= 	'General Notifications';
$language['button_missing_checkout'] 	= 	'Annotation and Workflow buttons are disbaled for Checked Out documents';
$language['users_with_roles']           =   '[SA] - Super Admin, [DA] - Department Admin, [RU] - Regular User, [PU] - Private User';
//encryption
$language['encrypt']		        	=	'Encrypt';
$language['decrypt']		        	=	'Decrypt';
$language['File Encryption Key']		=	'File Encryption Key';
$language['encrypt_help_text']			=	'Set the encryption key for encrypt and decrypt the documents';
$language['encrypt_settings']			=	'File Encryption Settings';
$language['doc_encrypted_at']			=	'Encrypted At';
$language['doc_encrypted_by']			=	'Last Encrypted By';
$language['doc_decrypted_at']			=	'Decrypted At';
$language['doc_decrypted_by']			=	'Last Decrypted By';
$language['decrypt_please']				=	'To decrypt the file please ';
$language['decrypt_no_permission']		=	'You have no permission for decrypt the file';
$language['confirm_encrypt_single']		=	'Do you want to encrypt ';
$language['confirm_decrypt_single']		=	'Note that files once decrypted will remain decrypted until it is encrypted again. A decrypted file can be viewed by anyone who has a Edit or View permission. Do you want to decrypt ';
$language['success_encrypt']			=	'encrypted successfully';
$language['success_decrypt']			=	'decrypted successfully';
$language['encrypted_docs']				=	'Encrypted';
$language['checkout_docs']				=	'Check Out';
$language['encrypt_key_length']			=	'Encryption key must have 6 characters or more';
$language['encrypt_export_confirm']		=	'There are some encrypted files. Encrypted files could not export. Do you want to continue?';