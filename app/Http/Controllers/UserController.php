<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Admin\UserController as AdminUserController;

/**
 * Controller Manajemen Pengguna & Hak Akses (ICONIX Robustness: UserController)
 * 
 * Merepresentasikan Control Object 'UserController' pada Robustness Diagram.
 * Terhubung dengan boundary 'profile', 'registrasi', 'login' dan entity 'User'.
 */
class UserController extends AdminUserController
{
    // Mewarisi manajemen index, store, update, destroy, toggleStatus dari Admin\UserController
}
