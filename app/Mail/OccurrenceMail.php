<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

class OccurrenceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $phone;
    public $clientName;
    public $address;
    public $url;
    public $date;
    public $time;
    public $subject;
    public $colaboratorName;
    public $colaboratorPhone;


    /**
     * Create a new message instance.
     */
    public function __construct($phone, $clientName, $url, $address, $date, $time, $subject = "Agendamento de Reunião - Prime Web" )
    {
        $this->phone = $phone;
        $this->clientName = $clientName;
        $this->url = $url;
        $this->address = $address;
        $this->date = $date;
        $this->time = $time;
        $this->subject = $subject;
        $this->colaboratorName = Auth::user()->name ?? null;
        $this->colaboratorPhone = Auth::user()->phone ?? null;
    }

    /**
     * Get the message envelope.
     */
    
     public function build()
     {
         $this->colaboratorPhone = $this->formatPhone($this->colaboratorPhone);
     
         return $this->view('emails.occurrence')
                     ->with([
                         'phone' => $this->phone,
                         'clientName' => $this->clientName,
                         'url' => $this->url,
                         'address' => $this->address,
                         'date' => $this->date,
                         'time' => $this->time,
                         'colaboratorName' => $this->colaboratorName,
                         'colaboratorPhone' => $this->colaboratorPhone
                     ])
                     ->subject($this->subject);
     }
     
     private function formatPhone($phone)
     {
         $phone = preg_replace('/\D/', '', $phone);
         if (strlen($phone) === 11) {
             return '(' . substr($phone, 0, 2) . ') ' . substr($phone, 2, 5) . '-' . substr($phone, 7);
         }
         if (strlen($phone) === 10) {
             return '(' . substr($phone, 0, 2) . ') ' . substr($phone, 2, 4) . '-' . substr($phone, 6);
         }
         return $phone;
     }
    
}
