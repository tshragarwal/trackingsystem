<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CampaignDetailUpdateRequest extends FormRequest
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
            'id' => ['required', 'numeric'],
            'target_url' => ['required', 'string'],
//            'query_string' => ['required', 'string'],
            'target_count' => ['required', 'numeric'],
            'status' => ['required', 'numeric', 'in:1,2,3'],
            'campaign_name' => ['required', 'string'],
            'link_type' => ['required', 'string', 'in:typein,n2s'],
        ];
    }
}
