<?php

namespace App\Notifications;

use App\Models\Bond;
use App\Models\BondDocument;
use App\Models\DocumentType;
use Illuminate\Bus\Queueable;
use App\CustomClasses\SgcLogger;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NewRightsNotification extends Notification/* implements ShouldQueue */ //Queueing disabled while in development
{
    use Queueable;

    protected $bond;
    protected $document;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($bond)
    {
        $this->bond = Bond::with(['course', 'employee', 'role', 'bondDocuments'])->find($bond->id);

        $type = DocumentType::where('name', 'Ficha de Inscrição - Termos e Licença')->first();
        $this->document = BondDocument::where('document_type_id', $type->id)->where('bond_id', $this->bond->id)->first();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'/* , 'mail' */];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    /* public function toMail($notifiable)
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    } */

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        SgcLogger::writeLog(target: 'NewRightsNotification', model: $this->bond);

        return [
            'bond_id' => $this->bond->id,
            'course_name' => $this->bond->course->name,
            'employee_name' => $this->bond->employee->name,
            'role_name' => $this->bond->role->name,
            'document_id' => $this->document->id,
            'document_name' => $this->document->original_name,
        ];
    }
}
