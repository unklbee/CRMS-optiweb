<?php
// app/Controllers/Frontend/HomeController.php
namespace App\Controllers\Frontend;

use App\Controllers\BaseController;
use App\Models\ServiceCategoryModel;
use App\Models\ServiceModel;
use App\Models\CmsPageModel;
use App\Models\CmsSettingModel;

class HomeController extends BaseController
{
    public function index()
    {
        $serviceCategoryModel = new ServiceCategoryModel();
        $serviceModel = new ServiceModel();
        $pageModel = new CmsPageModel();

        $data = [
            'title' => get_site_setting('site_name', 'Computer Repair Shop'),
            'meta_description' => get_site_setting('site_description', 'Professional computer repair services'),
            'service_categories' => $serviceCategoryModel->getActiveCategories(),
            'featured_services' => $serviceModel->getServicesWithCategory(),
            'hero_content' => $this->getHeroContent(),
            'testimonials' => $this->getTestimonials()
        ];

        return view('frontend/home', $data);
    }

    private function getHeroContent()
    {
        return [
            'title' => get_site_setting('hero_title', 'Professional Computer Repair Services'),
            'subtitle' => get_site_setting('hero_subtitle', 'Fast, reliable, and affordable repair solutions for all your devices'),
            'features' => [
                'Free Diagnosis',
                'Expert Technicians',
                'Quick Turnaround',
                'Warranty Included'
            ]
        ];
    }

    private function getTestimonials()
    {
        return [
            [
                'name' => 'John Doe',
                'rating' => 5,
                'comment' => 'Excellent service! Fixed my laptop screen quickly and professionally.',
                'device' => 'Laptop Repair'
            ],
            [
                'name' => 'Sarah Smith',
                'rating' => 5,
                'comment' => 'Very satisfied with the virus removal service. Computer runs like new!',
                'device' => 'Virus Removal'
            ],
            [
                'name' => 'Mike Johnson',
                'rating' => 5,
                'comment' => 'Professional staff and fair pricing. Highly recommended!',
                'device' => 'Hardware Repair'
            ]
        ];
    }
}