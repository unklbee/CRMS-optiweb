<?php
// app/Controllers/Frontend/ContactController.php
namespace App\Controllers\Frontend;

use App\Controllers\BaseController;
use CodeIgniter\Email\Email;

class ContactController extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Contact Us',
            'meta_description' => 'Get in touch with our computer repair experts',
            'contact_info' => [
                'email' => get_site_setting('contact_email', 'info@repairshop.com'),
                'phone' => get_site_setting('contact_phone', '+62-21-1234567'),
                'address' => get_site_setting('address', 'Jl. Teknologi No. 123, Jakarta'),
                'business_hours' => get_site_setting('business_hours', [])
            ],
            'breadcrumb' => [
                ['title' => 'Home', 'url' => '/'],
                ['title' => 'Contact', 'url' => '']
            ]
        ];

        return view('frontend/contact', $data);
    }

    public function send()
    {
        $rules = [
            'name' => 'required|min_length[2]|max_length[100]',
            'email' => 'required|valid_email',
            'subject' => 'required|min_length[5]|max_length[200]',
            'message' => 'required|min_length[10]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $email = \Config\Services::email();

        $email->setTo(get_site_setting('contact_email', 'info@repairshop.com'));
        $email->setFrom($this->request->getPost('email'), $this->request->getPost('name'));
        $email->setSubject('Contact Form: ' . $this->request->getPost('subject'));

        $message = "New message from contact form:\n\n";
        $message .= "Name: " . $this->request->getPost('name') . "\n";
        $message .= "Email: " . $this->request->getPost('email') . "\n";
        $message .= "Subject: " . $this->request->getPost('subject') . "\n\n";
        $message .= "Message:\n" . $this->request->getPost('message');

        $email->setMessage($message);

        if ($email->send()) {
            return redirect()->to('/contact')->with('success', 'Thank you for your message. We will get back to you soon!');
        } else {
            return redirect()->back()->with('error', 'Failed to send message. Please try again.');
        }
    }
}