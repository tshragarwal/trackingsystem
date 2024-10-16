<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignPublisherJob extends FormRequest
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
            'advertiser_campaign_id' => ['required', 'numeric'],
            'publisher_id' => ['required', 'numeric'],
            'target_count' => ['numeric'],
            'fallback_url' => ['nullable','url:https,http']
        ];
    }
}
