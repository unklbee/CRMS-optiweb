<?php
// app/Controllers/Frontend/PageController.php
namespace App\Controllers\Frontend;

use App\Controllers\BaseController;
use App\Models\CmsPageModel;

class PageController extends BaseController
{
    protected CmsPageModel $pageModel;

    public function __construct()
    {
        $this->pageModel = new CmsPageModel();
    }

    public function show($slug)
    {
        $page = $this->pageModel->getPageBySlug($slug);

        if (!$page) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Page not found');
        }

        $data = [
            'title' => $page['meta_title'] ?: $page['title'],
            'meta_description' => $page['meta_description'],
            'page' => $page,
            'breadcrumb' => [
                ['title' => 'Home', 'url' => '/'],
                ['title' => $page['title'], 'url' => '']
            ]
        ];

        // Use custom template if specified
        $template = $page['template'] ?: 'default';
        $viewFile = "frontend/pages/{$template}";

        // Fallback to default template if custom template doesn't exist
        if (!view_exists($viewFile)) {
            $viewFile = 'frontend/pages/default';
        }

        return view($viewFile, $data);
    }
}