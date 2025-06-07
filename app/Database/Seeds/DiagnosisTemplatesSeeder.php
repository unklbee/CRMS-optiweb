<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DiagnosisTemplatesSeeder extends Seeder
{
    public function run()
    {
        // Sample diagnosis templates
        $templates = [
            [
                'device_type_id' => 1, // Assuming Laptop
                'title' => 'Laptop Standard Diagnosis',
                'common_issues' => json_encode([
                    'Screen not working',
                    'Keyboard malfunction',
                    'Battery not charging',
                    'Overheating issues',
                    'Performance degradation',
                    'Hard drive failure',
                    'RAM issues',
                    'WiFi connectivity problems',
                    'Audio not working',
                    'Charging port damaged'
                ]),
                'recommended_actions' => 'Perform comprehensive hardware test including:
- Visual inspection for physical damage
- Power system check (adapter, battery, charging port)
- Display functionality test
- Keyboard and touchpad functionality
- RAM and storage diagnostic
- Temperature monitoring
- Network connectivity test
- Audio system check
- Port functionality verification',
                'estimated_hours' => 2.0,
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'device_type_id' => 2, // Assuming Desktop
                'title' => 'Desktop Standard Diagnosis',
                'common_issues' => json_encode([
                    'Not booting',
                    'Blue screen errors',
                    'Performance issues',
                    'Hardware component failure',
                    'Power supply issues',
                    'Graphics card problems',
                    'Memory errors',
                    'Storage device failure',
                    'Overheating',
                    'Network connectivity issues'
                ]),
                'recommended_actions' => 'Comprehensive desktop diagnosis:
- Power supply voltage test
- RAM diagnostic using MemTest86
- Storage health check (SMART status)
- Graphics card stress test
- CPU temperature monitoring
- Motherboard component inspection
- Boot sequence analysis
- System error log review
- Driver compatibility check',
                'estimated_hours' => 1.5,
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'device_type_id' => 3, // Assuming Smartphone
                'title' => 'Smartphone Standard Diagnosis',
                'common_issues' => json_encode([
                    'Screen cracked/not working',
                    'Battery draining fast',
                    'Not charging',
                    'Camera not working',
                    'Water damage',
                    'Software issues/crashes',
                    'Speaker/microphone problems',
                    'Touch sensitivity issues',
                    'WiFi/Bluetooth connectivity',
                    'Overheating'
                ]),
                'recommended_actions' => 'Mobile device diagnosis checklist:
- Visual inspection for physical damage
- Screen functionality and touch response test
- Battery health and charging system check
- Camera and flash functionality
- Audio system test (speakers, microphone)
- Connectivity test (WiFi, Bluetooth, cellular)
- Software diagnostic and crash log analysis
- Water damage indicator check
- Port and button functionality
- Sensor calibration check',
                'estimated_hours' => 1.0,
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'device_type_id' => 4, // Assuming Tablet
                'title' => 'Tablet Standard Diagnosis',
                'common_issues' => json_encode([
                    'Touch screen not responsive',
                    'Battery issues',
                    'Charging problems',
                    'WiFi connectivity issues',
                    'Apps crashing',
                    'Slow performance',
                    'Screen damage',
                    'Audio problems',
                    'Camera malfunction',
                    'Storage issues'
                ]),
                'recommended_actions' => 'Tablet diagnostic procedure:
- Touch screen calibration and responsiveness test
- Battery capacity and charging rate analysis
- Display quality and dead pixel check
- WiFi and Bluetooth connectivity verification
- App performance and memory usage analysis
- Storage capacity and file system check
- Camera and audio functionality test
- Sensor functionality verification
- Physical port and button inspection',
                'estimated_hours' => 1.0,
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'device_type_id' => 1, // Laptop - Gaming specific
                'title' => 'Gaming Laptop Diagnosis',
                'common_issues' => json_encode([
                    'Graphics performance issues',
                    'Overheating during gaming',
                    'FPS drops',
                    'Display artifacts',
                    'Thermal throttling',
                    'Fan noise excessive',
                    'Game crashes',
                    'GPU driver issues',
                    'Memory bottlenecks',
                    'Storage speed issues'
                ]),
                'recommended_actions' => 'Gaming laptop specialized diagnosis:
- GPU stress test and temperature monitoring
- Gaming performance benchmarking
- Thermal paste condition check
- Fan system cleaning and speed test
- RAM speed and timing verification
- Storage read/write speed test
- Display refresh rate and color accuracy
- Driver version compatibility check
- Power delivery system analysis
- Gaming-specific software compatibility',
                'estimated_hours' => 3.0,
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'device_type_id' => 2, // Desktop - Workstation
                'title' => 'Workstation Diagnosis',
                'common_issues' => json_encode([
                    'CAD software crashes',
                    'Rendering performance issues',
                    'Multi-monitor problems',
                    'Professional software compatibility',
                    'Memory intensive task failures',
                    'Storage bottlenecks',
                    'Network performance issues',
                    'Color accuracy problems',
                    'System stability under load',
                    'Backup system failures'
                ]),
                'recommended_actions' => 'Professional workstation diagnosis:
- Professional software compatibility test
- Multi-core CPU stress testing
- ECC memory diagnostic (if applicable)
- Professional graphics card testing
- Multi-monitor configuration verification
- Color calibration accuracy check
- Network throughput analysis
- RAID system health check
- Backup system functionality test
- Certified driver verification',
                'estimated_hours' => 2.5,
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]
        ];

        // Insert the data
        $this->db->table('diagnosis_templates')->insertBatch($templates);

        // Display success message
        echo "Diagnosis templates seeded successfully!\n";
        echo "Templates created: " . count($templates) . "\n";
    }
}