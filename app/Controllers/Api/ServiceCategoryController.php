<?php
// app/Controllers/Api/ServiceCategoryController.php
namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\ServiceCategoryModel;
use CodeIgniter\HTTP\ResponseInterface;

class ServiceCategoryController extends BaseController
{
    protected ServiceCategoryModel $serviceCategoryModel;

    public function __construct()
    {
        $this->serviceCategoryModel = new ServiceCategoryModel();
    }

    public function index(): ResponseInterface
    {
        $categories = $this->serviceCategoryModel->getActiveCategories();

        return $this->response->setJSON([
            'success' => true,
            'categories' => $categories
        ]);
    }
}