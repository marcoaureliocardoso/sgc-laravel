<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use App\CustomClasses\SgcLogger;

class NotificationController extends Controller
{
    /**
     * Mark notification as read.
     *
     * @param  \lluminate\Notifications\DatabaseNotification  $notification
     * @return \Illuminate\Http\Response
     */
    public function dismiss(DatabaseNotification $notification)
    {
        SgcLogger::writeLog($notification, 'dismiss');

        try {
            $notification->markAsRead();
        } catch (\Exception $e) {
            return back()->withErrors(['noDismiss' => 'Não foi possível dispensar a notificação: ' . $e->getMessage()]);
        }

        return redirect()->route('home')->with('success', 'Notificação dispensada.');
    }
}
