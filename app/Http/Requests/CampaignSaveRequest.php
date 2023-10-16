<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CampaignSaveRequest extends FormRequest
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
            'advertiser_id' => ['required', 'numeric','min:1'],
            'target_url' => ['required', 'string'],
            'campaign_name' => ['required', 'string'],
//            'query_string' => ['required', 'string'],
            'link_type' => ['required', 'string', 'in:typein,n2s'],
            'target_count' => ['required', 'numeric','min:1']
        ];
    }
}
