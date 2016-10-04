<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use App\Experience;

class DeleteExperienceRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $experience = Experience::find($this->route()->parameter('id'));
        return $this->user()->id == $experience->user_id; //todo uncomment for production
        //return true;
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
