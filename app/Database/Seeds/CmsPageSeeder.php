<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CmsPageSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'title' => 'About Us',
                'slug' => 'about-us',
                'content' => '<h2>About TechFix Computer Repair</h2>
                <p>With over 10 years of experience in computer repair and IT services, TechFix has been serving the community with professional, reliable, and affordable repair solutions.</p>
                
                <h3>Our Mission</h3>
                <p>To provide exceptional computer repair services that exceed customer expectations while maintaining the highest standards of quality and professionalism.</p>
                
                <h3>Why Choose Us?</h3>
                <ul>
                <li><strong>Expert Technicians:</strong> Certified professionals with years of experience</li>
                <li><strong>Quality Parts:</strong> We use only genuine and high-quality replacement parts</li>
                <li><strong>Quick Turnaround:</strong> Most repairs completed within 24-48 hours</li>
                <li><strong>Warranty:</strong> All repairs come with comprehensive warranty</li>
                <li><strong>Competitive Pricing:</strong> Fair and transparent pricing with no hidden fees</li>
                </ul>',
                'meta_title' => 'About Us - TechFix Computer Repair',
                'meta_description' => 'Learn about TechFix Computer Repair - professional repair services with expert technicians and quality parts.',
                'status' => 'published',
                'template' => 'default',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'title' => 'Privacy Policy',
                'slug' => 'privacy-policy',
                'content' => '<h2>Privacy Policy</h2>
                <p>This Privacy Policy describes how TechFix Computer Repair collects, uses, and protects your personal information.</p>
                
                <h3>Information We Collect</h3>
                <p>We collect information you provide directly to us, such as when you create an account, request repair services, or contact us.</p>
                
                <h3>How We Use Your Information</h3>
                <p>We use the information we collect to provide, maintain, and improve our services, process transactions, and communicate with you.</p>
                
                <h3>Information Security</h3>
                <p>We take reasonable measures to protect your personal information from loss, theft, misuse, and unauthorized access.</p>',
                'meta_title' => 'Privacy Policy - TechFix Computer Repair',
                'meta_description' => 'Privacy Policy for TechFix Computer Repair services.',
                'status' => 'published',
                'template' => 'default',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        $this->db->table('cms_pages')->insertBatch($data);
    }
}