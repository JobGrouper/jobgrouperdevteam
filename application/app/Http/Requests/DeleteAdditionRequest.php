<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use App\Addition;

class DeleteAdditionRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $addition = Addition::find($this->route()->parameter('id'));
        return $this->user()->id == $addition->user_id; //todo uncomment for production
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
        ];
    }
}
