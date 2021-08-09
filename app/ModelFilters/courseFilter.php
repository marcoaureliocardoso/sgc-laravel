<?php

namespace App\ModelFilters;

use Illuminate\Database\Eloquent\Builder;
use App\CustomClasses\ModelFilterHelpers;

trait courseFilter
{
    public function name_contains(Builder $builder, $value)
    {
        $values = ModelFilterHelpers::inputToArray($value);
        $builder = ModelFilterHelpers::contains($builder, 'name', $values);
        return $builder;
    }

    public function description_contains(Builder $builder, $value)
    {
        $values = ModelFilterHelpers::inputToArray($value);
        $builder = ModelFilterHelpers::contains($builder, 'name', $values);
        return $builder;
    }

    public function courseType_name_contains(Builder $builder, $value)
    {
        $values = ModelFilterHelpers::inputToArray($value);
        $builder = ModelFilterHelpers::relation_contains($builder, 'courseType', 'name', $values);
        return $builder;
    }

    public function begin_exactly(Builder $builder, $value)
    {
        //^\[([^\[\]]+)\]\[([^\[\]]+)\]$ regex para pegar [asdsad][sadasdads]
        $values = ModelFilterHelpers::inputToArray($value);
        $values = ModelFilterHelpers::convertDateFormat($values);
        $builder = ModelFilterHelpers::simple_operation($builder, 'begin', '=', $values);
        return $builder;
    }

    public function begin_BigOrEqu(Builder $builder, $value)
    {
        $values = ModelFilterHelpers::inputToArray($value);
        $values = ModelFilterHelpers::convertDateFormat($values);
        $builder = ModelFilterHelpers::simple_operation($builder, 'begin', '>=', $values);
        return $builder;
    }

    public function begin_LowOrEqu(Builder $builder, $value)
    {
        $values = ModelFilterHelpers::inputToArray($value);
        $values = ModelFilterHelpers::convertDateFormat($values);
        $builder = ModelFilterHelpers::simple_operation($builder, 'begin', '<=', $values);
        return $builder;
    }

    public function end_exactly(Builder $builder, $value)
    {
        //^\[([^\[\]]+)\]\[([^\[\]]+)\]$ regex para pegar [asdsad][sadasdads]
        $values = ModelFilterHelpers::inputToArray($value);
        $values = ModelFilterHelpers::convertDateFormat($values);
        $builder = ModelFilterHelpers::simple_operation($builder, 'end', '=', $values);
        return $builder;
    }

    public function end_BigOrEqu(Builder $builder, $value)
    {
        $values = ModelFilterHelpers::inputToArray($value);
        $values = ModelFilterHelpers::convertDateFormat($values);
        $builder = ModelFilterHelpers::simple_operation($builder, 'end', '>=', $values);
        return $builder;
    }

    public function end_LowOrEqu(Builder $builder, $value)
    {
        $values = ModelFilterHelpers::inputToArray($value);
        $values = ModelFilterHelpers::convertDateFormat($values);
        $builder = ModelFilterHelpers::simple_operation($builder, 'end', '<=', $values);
        return $builder;
    }
}
