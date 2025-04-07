<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendMail extends Mailable
{
    use Queueable, SerializesModels;
    public $details;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($details)
    {
        $this->details = $details;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if (!empty($this->details['type_send'])) {
            return $this->markdown($this->details['view_file'])
            ->subject($this->details['mail_subject']);
        }else{
            if ($this->details['code_surat'] == 'SKK') {
                $view = 'eletter.employment_certificate.send-mail';
            }elseif ($this->details['code_surat'] == 'IM') {
                $view = 'eletter.internal_memo.send-mail';
            }elseif ($this->details['code_surat'] == 'SKI') {
                $view = 'eletter.internship_certificate.send-mail';
            }elseif ($this->details['code_surat'] == 'SUPA') {
                $view = 'eletter.invitation_letter.send-mail';
            }elseif ($this->details['code_surat'] == 'Other Letter') {
                $view = 'eletter.other_letter.send-mail';
            }elseif ($this->details['code_surat'] == 'SK') {
                $view = 'eletter.statement_letter.send-mail';
            }elseif ($this->details['code_surat'] == 'SP3') {
                $view = 'eletter.stern_warning_letter.send-mail';
            }elseif ($this->details['code_surat'] == 'SPB') {
                $view = 'eletter.notif_letter.send-mail';
            }else{
                $view = 'eletter.warning_letter.send-mail';
            }
            return $this->subject($this->details['subject'])->view($view);
        }
    }
}
