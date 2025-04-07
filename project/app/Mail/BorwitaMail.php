<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BorwitaMail extends Mailable
{
    use Queueable, SerializesModels;
    public $param;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($param)
    {
        $this->param = $param;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if(@$this->param['files']){
            foreach ($this->param['files'] as $i => $item_file) {
                $this->attach($item_file);
            }
        }
        return $this->markdown($this->param['view_file'])
                   ->from($this->param['mail_from'], $this->param['mail_alias'])
                   ->subject($this->param['mail_subject']);
    }
}
