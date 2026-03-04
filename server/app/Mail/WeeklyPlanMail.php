<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WeeklyPlanMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $rows;
    public $shoppingList;
    public $selectedWeek;
    private string $pdfBinary;

    public function __construct($user, $rows, $shoppingList, string $pdfBinary, int $selectedWeek = 0)
    {
        $this->user = $user;
        $this->rows = $rows;
        $this->shoppingList = $shoppingList;
        $this->pdfBinary = $pdfBinary;
        $this->selectedWeek = $selectedWeek;
    }

    public function build()
    {
        $subject = $this->selectedWeek > 0
            ? "Etrend osszefoglalo - {$this->selectedWeek}. het"
            : "Etrend osszefoglalo";

        return $this->subject($subject)
            ->view('emails.weekly-plan')
            ->attachData(
                $this->pdfBinary,
                'heti-etrend.pdf',
                ['mime' => 'application/pdf']
            );
    }
}
