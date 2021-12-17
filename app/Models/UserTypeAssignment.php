<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;
use eloquentFilter\QueryFilter\ModelFilters\Filterable;
use App\ModelFilters\UserTypeAssignmentFilter;

class UserTypeAssignment extends Model
{
    use HasFactory;
    use Sortable;
    use UserTypeAssignmentFilter, Filterable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'user_type_id',
        'course_id',
        'begin',
        'end',
    ];

    protected $observables = [
        'listed',
        'fetched',
    ];

    public $sortable = [
        'id',
        'user.email',
        'userType.name',
        'begin',
        'end',
        'created_at',
        'updated_at'
    ];

    private static $whiteListFilter = ['*'];
    public static $accepted_filters = [
        'userEmailContains',
        'usertypeNameContains',
        'courseNameContains',
        'beginExactly',
        'beginBigOrEqu',
        'beginLowOrEqu',
        'endExactly',
        'endBigOrEqu',
        'endLowOrEqu',
        'userId'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function userType()
    {
        return $this->belongsTo(UserType::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function logListed()
    {
        $this->fireModelEvent('listed', false);
    }

    public function logFetched()
    {
        $this->fireModelEvent('fetched', false);
    }
}
