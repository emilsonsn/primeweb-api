<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OccurrenceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $phone;
    public $clientName;
    public $url;
    public $date;
    public $time;


    /**
     * Create a new message instance.
     */
    public function __construct($phone, $clientName, $url, $date, $time)
    {
        $this->phone = $phone;
        $this->clientName = $clientName;
        $this->url = $url;
        $this->date = $date;
        $this->time = $time;
    }

    /**
     * Get the message envelope.
     */
    
    public function build()
    {
        return $this->view('emails.occurrence')
                    ->with([
                        'phone' => $this->phone,
                        'clientName' => $this->clientName,
                        'url' => $this->url,
                        'date' => $this->date,
                        'time' => $this->time,
                    ])
                    ->subject('Prime Web - Ocorrência');
    }

}
