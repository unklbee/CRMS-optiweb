<?php
// app/Controllers/Frontend/OrderController.php
namespace App\Controllers\Frontend;

use App\Controllers\BaseController;
use App\Models\RepairOrderModel;
use App\Models\CustomerModel;
use App\Models\DeviceTypeModel;
use App\Models\ServiceCategoryModel;
use App\Libraries\NotificationService;

class OrderController extends BaseController
{
    protected RepairOrderModel $orderModel;
    protected CustomerModel $customerModel;
    protected DeviceTypeModel $deviceTypeModel;
    protected ServiceCategoryModel $serviceCategoryModel;

    public function __construct()
    {
        $this->orderModel = new RepairOrderModel();
        $this->customerModel = new CustomerModel();
        $this->deviceTypeModel = new DeviceTypeModel();
        $this->serviceCategoryModel = new ServiceCategoryModel();
    }

    public function track()
    {
        $orderNumber = $this->request->getGet('order');
        $order = null;

        if ($orderNumber) {
            $order = $this->orderModel->getOrderByNumber($orderNumber);
        }

        $data = [
            'title' => 'Track Your Order',
            'meta_description' => 'Track the status of your computer repair order',
            'order' => $order,
            'order_number' => $orderNumber,
            'breadcrumb' => [
                ['title' => 'Home', 'url' => '/'],
                ['title' => 'Track Order', 'url' => '']
            ]
        ];

        return view('frontend/orders/track', $data);
    }

    public function checkStatus()
    {
        $orderNumber = $this->request->getPost('order_number');

        if (!$orderNumber) {
            return redirect()->to('/track-order')->with('error', 'Please enter an order number');
        }

        return redirect()->to('/track-order?order=' . urlencode($orderNumber));
    }

    public function create()
    {
        $data = [
            'title' => 'Book Repair Service',
            'meta_description' => 'Book a repair service for your computer or device',
            'device_types' => $this->deviceTypeModel->getActiveTypes(),
            'service_categories' => $this->serviceCategoryModel->getActiveCategories(),
            'breadcrumb' => [
                ['title' => 'Home', 'url' => '/'],
                ['title' => 'Book Service', 'url' => '']
            ]
        ];

        return view('frontend/orders/create', $data);
    }

    public function store()
    {
        $rules = [
            'customer_name' => 'required|min_length[2]|max_length[100]',
            'customer_phone' => 'required|valid_phone',
            'customer_email' => 'permit_empty|valid_email',
            'device_type_id' => 'required|integer',
            'device_brand' => 'required|min_length[2]',
            'device_model' => 'required|min_length[2]',
            'problem_description' => 'required|min_length[10]',
            'service_category_id' => 'required|integer'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Create or find customer
        $customerData = [
            'full_name' => $this->request->getPost('customer_name'),
            'phone' => $this->request->getPost('customer_phone'),
            'email' => $this->request->getPost('customer_email'),
            'address' => $this->request->getPost('customer_address')
        ];

        // Check if customer already exists
        $existingCustomer = $this->customerModel->where('phone', $customerData['phone'])->first();

        if ($existingCustomer) {
            $customerId = $existingCustomer['id'];
            // Update customer info if provided
            $this->customerModel->update($customerId, $customerData);
        } else {
            $customerId = $this->customerModel->insert($customerData);
        }

        // Create order
        $orderData = [
            'customer_id' => $customerId,
            'device_type_id' => $this->request->getPost('device_type_id'),
            'device_brand' => $this->request->getPost('device_brand'),
            'device_model' => $this->request->getPost('device_model'),
            'device_serial' => $this->request->getPost('device_serial'),
            'problem_description' => $this->request->getPost('problem_description'),
            'accessories' => $this->request->getPost('accessories'),
            'priority' => $this->request->getPost('priority', 'normal'),
            'status' => 'received'
        ];

        $orderId = $this->orderModel->insert($orderData);

        if ($orderId) {
            $order = $this->orderModel->getOrderByNumber(
                $this->orderModel->find($orderId)['order_number']
            );

            // Send confirmation email
            if ($order['customer_email']) {
                $notificationService = new NotificationService();
                $notificationService->sendOrderConfirmation($order);
            }

            return redirect()->to('/track-order?order=' . $order['order_number'])
                ->with('success', 'Your repair order has been submitted successfully! Order number: ' . $order['order_number']);
        }

        return redirect()->back()->with('error', 'Failed to submit order. Please try again.');
    }
}