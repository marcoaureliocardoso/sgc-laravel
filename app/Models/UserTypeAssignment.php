<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;
use eloquentFilter\QueryFilter\ModelFilters\Filterable;
use App\ModelFilters\userTypeAssignmentFilter;

class UserTypeAssignment extends Model
{
    use HasFactory;
    use Sortable;
    use userTypeAssignmentFilter, Filterable;

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
        'user_email_contains',
        'usertype_name_contains',
        'course_name_contains',
        'begin_exactly',
        'begin_BigOrEqu',
        'begin_LowOrEqu',
        'end_exactly',
        'end_BigOrEqu',
        'end_LowOrEqu'
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
}