<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ResumeEditRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $resume = $this->route()->parameter('resume');
        return [
            'name' => 'required|string',
            'email' => 'required|email',
            'website'=> 'nullable|url',
            'picture'=> 'nullable|image',
            'about' => 'nullable|string',
            'title' => Rule::unique('resumes')
                ->where( fn($query) =>  $query->where('user_id', $resume->user->id) )
                ->ignore($resume->id)
        ];
    }
}
