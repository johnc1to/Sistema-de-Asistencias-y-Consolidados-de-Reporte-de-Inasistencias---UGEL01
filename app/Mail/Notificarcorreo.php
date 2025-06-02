<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Notificarcorreo extends Mailable
{
    use Queueable, SerializesModels;

    public $subject="Informacion";
    public $body="Cuerpo";
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('notificacion/marco_institucional',array('body'=>$this->body));
    }
}
