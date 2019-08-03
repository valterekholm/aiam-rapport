<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
//$route['default_controller'] = 'pages/view';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
//$route['(:any)'] = "pages/view/$1";
$route['staff/create'] = 'staff/create';
$route['staff/edit'] = 'staff/edit';
$route['staff/update'] = 'staff/update';//?
$route['staff/send_mail'] = 'staff/send_mail';
$route['staff/send_mail_3'] = 'staff/send_mail_3';
$route['staff/send_mail_company'] = 'staff/send_mail_company';
$route['staff/set_password'] = 'staff/set_password';
$route['staff/set_password_post'] = 'staff/set_password_post';
$route['staff/calendar'] = 'staff/calendar';
$route['staff/null_company'] = 'staff/null_company';
$route['staff'] = 'staff';
$route['staff/(:any)'] = 'staff/index';//förut bara staff

$route['pages/error'] = 'pages/error';//?

//exempel
//$route['product/:num'] = 'catalog/product_lookup';
//:num en siffra

$route['customers/view1'] = 'customers/view1';
$route['customers/create'] = 'customers/create';
$route['customers/create_ajax'] = 'customers/create_ajax';
$route['customers/edit'] = 'customers/edit';
$route['customers/update'] = 'customers/update';
$route['customers/add_workplace'] = 'customers/add_workplace';
$route['customers/add_company'] = 'customers/add_company';
$route['customers/get_bl_customers'] = 'customers/get_bl_customers';
$route['customers/connect_any_company'] = 'customers/connect_any_company';
$route['customers/get_form_create'] = 'customers/get_form_create';
$route['customers'] = 'customers';
$route['customers/(:any)'] = 'customers/index';

//$route['workplaces/(:any)'] = 'workplaces/index';
$route['workplaces/create'] = 'workplaces/create';
$route['workplaces/edit'] = 'workplaces/edit';
$route['workplaces'] = 'workplaces';


$route['jobs/create'] = 'jobs/create';
$route['jobs/edit'] = 'jobs/edit';
$route['jobs/view'] = 'jobs/view';
$route['jobs/update'] = 'jobs/update';//behövs?
$route['jobs/view1'] = 'jobs/view1';
$route['jobs/link_list'] = 'jobs/link_list';
$route['jobs/link_list_nullcheck'] = 'jobs/link_list_nullcheck';
$route['jobs/view1_personal'] = 'jobs/view1_personal';
$route['jobs/calendar'] = 'jobs/calendar';
$route['jobs/calendar_day'] = 'jobs/calendar_day';//onödig?
$route['jobs/calendar_week'] = 'jobs/calendar_week';//onödig?
$route['jobs/calendar_month'] = 'jobs/calendar_month';//onödig?
$route['jobs/delete'] = 'jobs/delete';
$route['jobs/get_staff_json'] = 'jobs/get_staff_json';
$route['jobs/add_schema'] = 'jobs/add_schema';
$route['jobs'] = 'jobs';
$route['jobs/add_staff'] = 'jobs/add_staff';
$route['jobs/(:any)'] = 'jobs/index';


$route['reports/create'] = 'reports/create';
$route['reports/edit'] = 'reports/edit';
$route['reports/update'] = 'reports/update';
$route['reports/check'] = 'reports/check';
$route['reports/check_no_coords'] = 'reports/check_no_coords';
$route['reports/make_excel'] = 'reports/make_excel';
$route['reports/make_excel_post'] = 'reports/make_excel_post';
$route['reports/make_pdf_post'] = 'reports/make_pdf_post';
//$route['reports/download_excel'] = 'reports/download_excel';//?varför ha
$route['reports/choose_staff'] = 'reports/choose_staff';
$route['reports/request_break_info'] = 'reports/request_break_info';
$route['reports/add_break_info'] = 'reports/add_break_info';
$route['reports'] = 'reports';
$route['reports/(:any)'] = 'reports/index';

$route['company'] = 'company';
$route['company/update'] = 'company/update';
$route['company/create'] = 'company/create';
$route['company/superadmin'] = 'company/superadmin';
$route['company/(:any)'] = 'company/index';

$route['users/login'] = 'users/login';
$route['users/logout'] = 'users/logout';
$route['users/(:any)'] = 'pages';

$route['apikeys'] = 'apikeys';
$route['apikeys/index'] = 'apikeys/index';
$route['apikeys/make_my_key'] = 'apikeys/make_my_key';
$route['apikeys/(:any)'] = 'apikeys/index';

$route['jobschema'] = 'jobschema';
$route['jobschema/index'] ='jobschema/index';
$route['jobschema/create'] = 'jobschema/create';
$route['jobschema/edit'] = 'jobschema/edit';
$route['jobschema/update'] = 'jobschema/update';
$route['jobschema/listing'] = 'jobschema/listing';
$route['jobschema/add_to_job'] = 'jobschema/add_to_job';
$route['jobschema/(:any)'] = 'jobschema/index';

$route['miniblog'] = 'miniblog';
$route['miniblog/index'] ='miniblog/index';
$route['miniblog/create'] = 'miniblog/create';
$route['miniblog/edit'] = 'miniblog/edit';
$route['miniblog/update'] = 'miniblog/update';
$route['miniblog/(:any)'] = 'miniblog/index';

$route['test'] = 'test';//for Test class...


$route['(:any)'] = 'pages/view/$1';
$route['default_controller'] = 'pages/view';
