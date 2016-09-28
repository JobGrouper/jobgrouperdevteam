<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use App\Skill;

class DeleteSkillRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $skill = Skill::find($this->route()->parameter('id'));
        return $this->user()->id == $skill->user_id; //todo uncomment for production
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
