<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class AsteriskService
{
    private $host;
    private $port;
    private $username;
    private $secret;
    private $socket;

    public function __construct()
    {
        $this->host = config('ami.host');
        $this->port = config('ami.port');
        $this->username = config('ami.username');
        $this->secret = config('ami.secret');
    }

    private function connect()
    {
        $this->socket = fsockopen($this->host, $this->port, $errno, $errstr, 10);
        if (!$this->socket) {
            Log::error("AMI Connection Error: $errstr ($errno)");
            return false;
        }
        stream_set_timeout($this->socket, 5);
        fgets($this->socket);
        return true;
    }

    private function login()
    {
        $loginCmd = "Action: Login\r\n";
        $loginCmd .= "Username: {$this->username}\r\n";
        $loginCmd .= "Secret: {$this->secret}\r\n\r\n";
        
        fwrite($this->socket, $loginCmd);
        usleep(200000);
        $response = fread($this->socket, 4096);

        if (strpos($response, 'Success') !== false) {
            return true;
        }
        
        Log::error("AMI Login Failed: ", ['response' => $response]);
        return false;
    }

    /**
     * Memulai panggilan antara Agen dan Pelanggan (Click-to-Call).
     * @param string $agentExtension Ekstensi internal agen (misal: "101").
     * @param string $targetPhoneNumber Nomor telepon pelanggan yang akan dihubungi.
     * @return bool
     */
    public function originateCall(string $agentExtension, string $targetPhoneNumber)
    {
        if (!$this->connect() || !$this->login()) {
            Log::error("Gagal connect atau login ke AMI.");
            $this->logoff();
            return false;
        }

        $originateCmd = "Action: Originate\r\n";
        // 1. Channel yang ditelepon pertama kali adalah AGEN
        $originateCmd .= "Channel: PJSIP/{$agentExtension}\r\n"; 
        
        // 2. Jika agen mengangkat, jalankan konteks 'from-laravel-crm'
        $originateCmd .= "Context: from-laravel-crm\r\n";
        $originateCmd .= "Exten: s\r\n";
        $originateCmd .= "Priority: 1\r\n";
        
        // 3. Atur CallerID dan kirim nomor tujuan sebagai variabel
        $originateCmd .= "CallerID: CRM Call <{$agentExtension}>\r\n";
        $originateCmd .= "Variable: CRM_TARGET_NUMBER={$targetPhoneNumber}\r\n";
        $originateCmd .= "Async: true\r\n\r\n";
        
        fwrite($this->socket, $originateCmd);
        usleep(200000);
        $response = fread($this->socket, 4096);
        
        $this->logoff();

        if (strpos($response, 'Success') !== false) {
            Log::info("AMI Originate Success: Perintah diterima oleh Asterisk.");
            return true;
        }
        
        Log::error("AMI Originate Failed: Perintah ditolak atau terjadi error.", ['response' => $response]);
        return false;
    }

    private function logoff()
    {
        if ($this->socket) {
            fwrite($this->socket, "Action: Logoff\r\n\r\n");
            fclose($this->socket);
            $this->socket = null;
        }
    }
}