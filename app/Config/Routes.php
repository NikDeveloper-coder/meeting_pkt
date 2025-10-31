<?php
namespace Config;

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Public Routes (Calendar - No login required)
$routes->get('/', 'PublicController::calendar');
$routes->get('/public/calendar', 'PublicController::calendar');
$routes->get('/public/getBookedDates', 'PublicController::getBookedDates');

// Auth Routes
$routes->get('/login', 'Auth::login');
$routes->post('/login', 'Auth::attemptLogin');
$routes->get('/register', 'Auth::register');
$routes->post('/register', 'Auth::attemptRegister');
$routes->get('/logout', 'Auth::logout');

// Dashboard Routes
$routes->get('/dashboard', 'Dashboard::index');
$routes->get('/dashboard/mailbox', 'Dashboard::mailbox');
$routes->get('/dashboard/profile', 'Dashboard::profile');
$routes->post('/dashboard/updateProfile', 'Dashboard::updateProfile');

// Booking Routes
$routes->get('/booking', 'Booking::index');
$routes->post('/booking', 'Booking::create');

// Booking Management Routes
$routes->get('/booking/update/(:num)', 'BookingController::update/$1');
$routes->post('/booking/update/(:num)', 'BookingController::update/$1');
$routes->get('/booking/delete/(:num)', 'BookingController::delete/$1');

// Admin Routes
$routes->get('/admin', 'Admin::index');
$routes->get('/admin/dashboard', 'Admin::dashboard');
$routes->get('/admin/users', 'Admin::users');
$routes->get('/admin/mailbox', 'Admin::mailbox');
$routes->get('/admin/action/(:any)/(:num)', 'Admin::action/$1/$2');

// Admin User Management Routes
$routes->get('/admin/users/new', 'Admin::usersCreate');
$routes->post('/admin/users/store', 'Admin::usersStore');
$routes->get('/admin/users/edit', 'Admin::users'); // Fallback route
$routes->get('/admin/users/edit/(:num)', 'Admin::usersEdit/$1');
$routes->post('/admin/users/update/(:num)', 'Admin::usersUpdate/$1');
$routes->get('/admin/users/delete/(:num)', 'Admin::usersDelete/$1');

// Admin Reports Routes
$routes->get('admin/reports', 'Reports::index');
$routes->get('admin/printBooking/(:any)', 'Reports::printBooking/$1');
$routes->get('admin/printBooking', 'Reports::printBooking');
$routes->get('admin/exportBookings', 'Reports::exportBookings');

// Social Auth Routes
$routes->get('auth/google', 'Auth::google');
$routes->get('auth/facebook', 'Auth::facebook');
$routes->get('auth/apple', 'Auth::apple');
$routes->get('auth/googleCallback', 'Auth::google');
$routes->get('auth/facebookCallback', 'Auth::facebook');
$routes->get('auth/appleCallback', 'Auth::apple');