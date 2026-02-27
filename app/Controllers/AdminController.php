<?php

namespace App\Controllers;

use App\Core\AdminGuard;
use App\Core\Auth;
use App\Core\Csrf;
use App\Core\View;

final class AdminController
{
    public function dashboard(): void
    {
        AdminGuard::requireLogin();

        $projectsCount = (new \App\Models\Project())->countByUser(Auth::userId() ?? 0);
        $categoriesCount = (new \App\Models\Category())->countByUser(Auth::userId() ?? 0);
        $tagsCount = (new \App\Models\Tag())->countByUser(Auth::userId() ?? 0);

        View::render('admin/dashboard', [
            'csrf' => Csrf::token(),
            'projectsCount' => $projectsCount,
            'categoriesCount' => $categoriesCount,
            'tagsCount' => $tagsCount
        ]);
    }
}