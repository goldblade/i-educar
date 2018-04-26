<?php

require_once 'vendor/autoload.php';

class Portabilis_Mailer {

    public $config = [];

    protected $transport;
    protected $mailer;
    protected $logger;

    public function __construct()
    {
        /* Configurações podem ser alteradas em tempo de execução, ex:
        $mailerInstance->configs->smtp->username = 'new_username'; */

        $this->config = $GLOBALS['coreExt']['Config']->app->mailer;
    }

    public function sendMail($to, $subject, $message, $options = [])
    {
        $this->transport = (new Swift_SmtpTransport($this->config->smtp->host, $this->config->smtp->port))
            ->setUsername($this->config->smtp->username)
            ->setPassword($this->config->smtp->password);

        $this->mailer = new Swift_Mailer($this->transport);

        if ((bool)$this->config->debug || (bool)$options['debug']) {
            $this->logger = new Swift_Plugins_Loggers_ArrayLogger();
            $this->mailer->registerPlugin(new Swift_Plugins_LoggerPlugin($this->logger));
        }

        $from = !empty($options['from'])
            ? $options['from']
            : [$this->config->smtp->from_email => $this->config->smtp->from_name];

        $message = (new Swift_Message($subject))
            ->setFrom($from)
            ->setTo($to)
            ->setBody($message);

        $allowedDomains = !empty($this->configs->allowed_domains)
            ? $this->configs->allowed_domains
            : ['*'];

        $result = false;

        foreach ($allowedDomains as $domain) {
            if ($domain === '*' || strpos($to, "@$domain") !== false) {
                $result = $this->mailer->send($message);
                break;
            }
        }

        return $result;
    }

    public function debug()
    {
        if ((bool)$this->config->debug || (bool)$options['debug']) {
            return $this->logger->dump();
        }
    }

    protected function host()
    {
        return (!empty($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : false);
    }
}
