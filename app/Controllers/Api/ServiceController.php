<?php
// app/Controllers/Api/ServiceController.php
namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\ServiceModel;
use App\Models\ServiceCategoryModel;
use CodeIgniter\HTTP\ResponseInterface;

class ServiceController extends BaseController
{
    protected ServiceModel $serviceModel;
    protected ServiceCategoryModel $serviceCategoryModel;

    public function __construct()
    {
        $this->serviceModel = new ServiceModel();
        $this->serviceCategoryModel = new ServiceCategoryModel();
    }

    public function index(): ResponseInterface
    {
        $categoryId = $this->request->getGet('category_id');

        if ($categoryId) {
            $services = $this->serviceModel->getServicesByCategory($categoryId);
        } else {
            $services = $this->serviceModel->getServicesWithCategory();
        }

        return $this->response->setJSON([
            'success' => true,
            'services' => $services
        ]);
    }
}