<?php namespace Larabook\Forms;

use Laracasts\Validation\FormValidator;

class SendMessageForm extends FormValidator {

    /**
     * Validation rules for send a message form
     *
     * @var array
     */
    protected $rules = [
        'sendTo' => 'required',
        'message' => 'required'
    ];
}
