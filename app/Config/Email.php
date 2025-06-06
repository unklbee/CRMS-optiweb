<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Email extends BaseConfig
{
    public string $fromEmail = '';
    public string $fromName = '';
    public string $recipients = '';

    /**
     * The "user agent"
     */
    public string $userAgent = 'CodeIgniter';

    /**
     * The mail sending protocol: mail, sendmail, smtp
     */
    public string $protocol = 'smtp';

    /**
     * The server path to Sendmail.
     */
    public string $mailPath = '/usr/sbin/sendmail';

    /**
     * SMTP Server Address
     */
    public string $SMTPHost = 'smtp.hostinger.com';

    /**
     * SMTP Username
     */
    public string $SMTPUser = 'cs@optiontech.id';

    /**
     * SMTP Password
     */
    public string $SMTPPass = 'S$l=gy:ml6*Y';

    /**
     * SMTP Port
     */
    public int $SMTPPort = 587;

    /**
     * SMTP Timeout (in seconds)
     */
    public int $SMTPTimeout = 60; // Increased timeout

    /**
     * Enable persistent SMTP connections
     */
    public bool $SMTPKeepAlive = false;

    /**
     * SMTP Encryption.
     * @var string '', 'tls' or 'ssl'
     */
    public string $SMTPCrypto = 'tls';

    /**
     * Enable word-wrap
     */
    public bool $wordWrap = true;

    /**
     * Character count to wrap at
     */
    public int $wrapChars = 76;

    /**
     * Type of mail, either 'text' or 'html'
     */
    public string $mailType = 'html';

    /**
     * Character set (utf-8, iso-8859-1, etc.)
     */
    public string $charset = 'UTF-8';

    /**
     * Whether to validate the email address
     */
    public bool $validate = false;

    /**
     * Email Priority. 1 = highest. 5 = lowest. 3 = normal
     */
    public int $priority = 3;

    /**
     * Newline character. (Use "\r\n" to comply with RFC 822)
     */
    public string $CRLF = "\r\n";

    /**
     * Newline character. (Use "\r\n" to comply with RFC 822)
     */
    public string $newline = "\r\n";

    /**
     * Enable BCC Batch Mode.
     */
    public bool $BCCBatchMode = false;

    /**
     * Number of emails in each BCC batch
     */
    public int $BCCBatchSize = 200;

    /**
     * Enable notify message from server
     */
    public bool $DSN = false;

    public function __construct()
    {
        parent::__construct();

        // Override with environment variables if available
        if (env('email.SMTPHost')) {
            $this->SMTPHost = env('email.SMTPHost');
        }
        if (env('email.SMTPUser')) {
            $this->SMTPUser = env('email.SMTPUser');
        }
        if (env('email.SMTPPass')) {
            $this->SMTPPass = env('email.SMTPPass');
        }
        if (env('email.SMTPPort')) {
            $this->SMTPPort = (int) env('email.SMTPPort');
        }
        if (env('email.SMTPCrypto')) {
            $this->SMTPCrypto = env('email.SMTPCrypto');
        }
        if (env('email.protocol')) {
            $this->protocol = env('email.protocol');
        }
        if (env('email.mailType')) {
            $this->mailType = env('email.mailType');
        }

        // Set from email and name from environment or defaults
        $this->fromEmail = env('email.SMTPUser') ?: 'cs@optiontech.id';
        $this->fromName = env('app.siteName') ?: 'Computer Repair Shop';
    }
}