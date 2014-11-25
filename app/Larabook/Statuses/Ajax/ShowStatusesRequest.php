<?php  namespace Larabook\Statuses\Ajax; 

use Laracasts\Validation\FormValidator;

class ShowStatusesRequest extends FormValidator {

    /**
     *
     * Validation rules for the publish status form
     * @var array
     */
    protected $rules = [
        'page' => 'integer'
    ];
}