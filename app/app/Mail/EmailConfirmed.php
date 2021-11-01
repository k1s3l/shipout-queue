<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailConfirmed extends Mailable
{
    use Queueable, SerializesModels;

//    private $code;
//
//    /**
//     * Create a new message instance.
//     *
//     * @return void
//     */
//    public function __construct(string|callable $code)
//    {
//        $this->code = $code;
//    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('confirm')->with(['code' => mt_rand(100000, 999999)]);
    }
}
