<?php

namespace App\Listeners;

use App\Events\BondCreated;
use App\Events\BondImpeded;
use App\Events\BondLiberated;
use App\Events\BondReviewRequested;
use App\Events\EmployeeDesignated;
use App\Events\InstitutionalLoginConfirmationRequired as InstitutionalLoginConfirmationRequiredEvent;
use App\Events\InstitutionalLoginConfirmed;
use App\Events\RightsDocumentArchived;
use App\Logging\LoggerInterface;
use App\Models\Course;
use App\Models\Employee;
use App\Models\User;
use App\Models\UserType;
use App\Notifications\BondCreated as BondCreatedNotification;
use App\Notifications\BondImpeded as BondImpededNotification;
use App\Notifications\BondReviewRequested as BondReviewRequestedNotification;
use App\Notifications\InstitutionalLoginConfirmationRequired as InstitutionalLoginConfirmationRequiredNotification;
use App\Notifications\RightsDocumentArchived as RightsDocumentArchivedNotification;
use App\Services\MailService;
use Illuminate\Support\Facades\Notification;

class DomainEventSubscriber
{
    private int $secUtId;

    private int $coordUtId;

    private int $assUtId;

    private int $ldiUtId;

    public function __construct(private LoggerInterface $logger, private MailService $mailService)
    {
        $this->secUtId = UserType::getIdByAcronym('sec');
        $this->coordUtId = UserType::getIdByAcronym('coord');
        $this->assUtId = UserType::getIdByAcronym('ass');
        $this->ldiUtId = UserType::getIdByAcronym('ldi');
    }

    /**
     * Handle employee designated events.
     *
     * @param  EmployeeDesignated  $event
     *
     * @return void
     */
    public function handleEmployeeDesignated(EmployeeDesignated $event)
    {
        $this->logger->logDomainEvent(
            'employee_designated',
            $event->employee
        );
    }

    /**
     * Handle bond liberated events.
     *
     * @param  BondLiberated  $event
     *
     * @return void
     */
    public function handleBondLiberated(BondLiberated $event)
    {
        $this->logger->logDomainEvent(
            'bond_liberated',
            $event->bond
        );
    }

    /**
     * Handle bond review requested events.
     *
     * @param  BondReviewRequested  $event
     *
     * @return void
     */
    public function handleBondReviewRequested(BondReviewRequested $event)
    {
        $secUsers = User::active()->ofActiveType($this->secUtId)->get();

        /**
         * @var Course $course
         */
        $course = $event->bond->course;
        $courseId = $course->id;
        $coordUsers = User::active()->ofActiveType($this->coordUtId)->ofCourse($courseId)->get();

        $assUsers = User::active()->ofActiveType($this->assUtId)->get();

        $users = $secUsers->merge($coordUsers)->merge($assUsers);

        Notification::send($users, new BondReviewRequestedNotification($event->bond));
        $this->logger->logDomainEvent(
            'bond_review_requested',
            $event->bond
        );
    }

    /**
     * Handle bond impeded events.
     *
     * @param  BondImpeded  $event
     *
     * @return void
     */
    public function handleBondImpeded(BondImpeded $event)
    {
        $secUsers = User::active()->ofActiveType($this->secUtId)->get();

        /**
         * @var Course $course
         */
        $course = $event->bond->course;
        $courseId = $course->id;
        $coordUsers = User::active()->ofActiveType($this->coordUtId)->ofCourse($courseId)->get();

        $users = $secUsers->merge($coordUsers);

        Notification::send($users, new BondImpededNotification($event->bond));
        $this->logger->logDomainEvent(
            'bond_impeded',
            $event->bond
        );
    }

    /**
     * Handle bond created events.
     *
     * @param  BondCreated  $event
     *
     * @return void
     */
    public function handleBondCreated(BondCreated $event)
    {
        //Notify grantor assistants
        $coordOrAssistants = User::active()->ofActiveType($this->assUtId)->get();

        Notification::send($coordOrAssistants, new BondCreatedNotification($event->bond));

        $this->logger->logDomainEvent(
            'bond_created',
            $event->bond
        );
    }

    /**
     * Handle rights document archived events.
     *
     * @param  RightsDocumentArchived  $event
     *
     * @return void
     */
    public function handleRightsDocumentArchived(RightsDocumentArchived $event)
    {
        $ldiUsers = User::active()->ofActiveType($this->ldiUtId)->get();

        Notification::send($ldiUsers, new RightsDocumentArchivedNotification($event->bond));

        $this->logger->logDomainEvent(
            'rights_document_archived',
            $event->bond
        );
    }

    /**
     * Handle institutional login confirmed events.
     *
     * @param  InstitutionalLoginConfirmed  $event
     *
     * @return void
     */
    public function handleInstitutionalLoginConfirmed(InstitutionalLoginConfirmed $event)
    {
        $this->logger->logDomainEvent(
            'institutional_login_confirmed',
            $event->bond
        );

        $this->mailService->sendNewEmployeeEmails($event->bond);
    }

    /**
     * Handle institutional login confirmation required events.
     *
     * @param  InstitutionalLoginConfirmationRequiredEvent  $event
     *
     * @return void
     */
    public function handleInstitutionalLoginConfirmationRequired(InstitutionalLoginConfirmationRequiredEvent $event): void
    {
        /**
         * @var User $user
         */
        $user = $event->user;
        /**
         * @var Employee $employee
         */
        $employee = $event->employee;

        $this->logger->logDomainEvent(
            'institutional_login_confirmation_required',
            $employee
        );

        Notification::send($user, new InstitutionalLoginConfirmationRequiredNotification($employee));
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  \Illuminate\Events\Dispatcher  $events
     *
     * @return array<string, string>
     */
    public function subscribe($events): array
    {
        return [
            EmployeeDesignated::class => 'handleEmployeeDesignated',
            BondCreated::class => 'handleBondCreated',
            BondLiberated::class => 'handleBondLiberated',
            BondImpeded::class => 'handleBondImpeded',
            BondReviewRequested::class => 'handleBondReviewRequested',
            RightsDocumentArchived::class => 'handleRightsDocumentArchived',
            InstitutionalLoginConfirmed::class => 'handleInstitutionalLoginConfirmed',
            InstitutionalLoginConfirmationRequiredEvent::class => 'handleInstitutionalLoginConfirmationRequired',
        ];
    }
}
