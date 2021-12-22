<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BoxRequest extends FormRequest
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
        return [
            'name' => 'required|string|min:3|max:255|unique:boxes' . ($this->getMethod() != "PUT" ?  '':',name,' . $this->box->id),
            'content_list' => "array|min:1|max:20",
            'content_list.*' => 'file|mimes:jpeg,png,bmp,svg,webp,doc,docx,pdf,csv,xls,xlsx,gif,ico,mp3,mp4,mov,ogg,qt,weba,webp,mpeg|max:50000',
        ];
    }
}
