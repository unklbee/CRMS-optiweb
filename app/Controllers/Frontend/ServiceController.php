<?php
// app/Controllers/Frontend/ServiceController.php
namespace App\Controllers\Frontend;

use App\Controllers\BaseController;
use App\Models\ServiceCategoryModel;
use App\Models\ServiceModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class ServiceController extends BaseController
{
    protected ServiceCategoryModel $serviceCategoryModel;
    protected ServiceModel $serviceModel;

    public function __construct()
    {
        $this->serviceCategoryModel = new ServiceCategoryModel();
        $this->serviceModel = new ServiceModel();
    }

    public function index(): string
    {
        $categoryId = $this->request->getGet('category');

        $data = [
            'title' => 'Our Services',
            'meta_description' => 'Professional computer repair services including hardware repair, software installation, data recovery, and more.',
            'categories' => $this->serviceCategoryModel->getActiveCategories(),
            'services' => $categoryId ?
                $this->serviceModel->getServicesByCategory($categoryId) :
                $this->serviceModel->getServicesWithCategory(),
            'selected_category' => $categoryId,
            'breadcrumb' => [
                ['title' => 'Home', 'url' => '/'],
                ['title' => 'Services', 'url' => '/services']
            ]
        ];

        return view('frontend/services/index', $data);
    }

    public function show($categorySlug): string
    {
        // Find category by slug (you might need to add slug field to categories)
        $category = $this->serviceCategoryModel->where('name', str_replace('-', ' ', $categorySlug))->first();

        if (!$category) {
            throw new PageNotFoundException('Service category not found');
        }

        $data = [
            'title' => $category['name'] . ' Services',
            'meta_description' => $category['description'],
            'category' => $category,
            'services' => $this->serviceModel->getServicesByCategory($category['id']),
            'all_categories' => $this->serviceCategoryModel->getActiveCategories(),
            'breadcrumb' => [
                ['title' => 'Home', 'url' => '/'],
                ['title' => 'Services', 'url' => '/services'],
                ['title' => $category['name'], 'url' => '']
            ]
        ];

        return view('frontend/services/category', $data);
    }
}
