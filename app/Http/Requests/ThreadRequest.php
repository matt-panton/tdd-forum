<?php

namespace App\Http\Requests;

use App\Rules\SpamFree;
use App\Rules\Recaptcha;
use Illuminate\Foundation\Http\FormRequest;

class ThreadRequest extends FormRequest
{
    protected $recaptcha;

    /**
     * Inject the recaptcha class via constructor so that it can be mocked
     * when testing to avoid unnecessary API calls.
     * 
     * @param App\Rules\Recaptcha  $recaptcha
     */
    public function __construct(Recaptcha $recaptcha)
    {
        $this->recaptcha = $recaptcha;
    }

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
        $rules = [
            'title' => ['required', new SpamFree],
            'body'  => ['required', new SpamFree],
        ];

        if ($this->method() === 'POST') {
            $rules['channel_id'] = ['required', 'exists:channels,id'];
            $rules['g-recaptcha-response'] = ['required', $this->recaptcha];
        }

        return $rules;
    }
}
