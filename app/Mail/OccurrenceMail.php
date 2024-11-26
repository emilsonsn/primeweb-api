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
    public $subject;


    /**
     * Create a new message instance.
     */
    public function __construct($phone, $clientName, $url, $date, $time, $subject = "Agendamento de Reunião - Prime Web" )
    {
        $this->phone = $phone;
        $this->clientName = $clientName;
        $this->url = $url;
        $this->date = $date;
        $this->time = $time;
        $this->subject = $subject;
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
                    ->subject($this->subject);
    }

}
