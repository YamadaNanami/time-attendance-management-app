<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttendanceCorrectionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'clockIn' => ['required','date_format:H:i','before:clockOut'],
            'clockOut' => ['required','date_format:H:i','after:clockIn'],
            'breakIn.*' => ['nullable','date_format:H:i','after_or_equal:clockIn','before_or_equal:clockOut'],
            'breakOut.*' => ['nullable','date_format:H:i','after_or_equal:clockIn','before_or_equal:clockOut'],
            'comment' => ['required']
        ];
    }

    public function messages(){
        return [
            //clockIn.beforeでエラーになれば、clockOut.afterもエラーになるためメッセージはclockIn.beforeで表示させる
            'clockIn.before' => '出勤時間もしくは退勤時間が不適切な値です',
            'breakIn.*.after_or_equal' => '休憩時間が勤務時間外です',
            'breakIn.*.before_or_equal' => '休憩時間が勤務時間外です',
            'breakOut.*.after_or_equal' => '休憩時間が勤務時間外です',
            'breakOut.*.before_or_equal' => '休憩時間が勤務時間外です',
            'comment.required' => '備考を記入してください',

            //仕様書にメッセージの指定がないため、メッセージを表示させないバリデーションルール
            'clockIn.required' => '',
            'clockIn.date_format' => '',
            'clockOut.required' => '',
            'clockOut.date_format' => '',
            'breakIn.*.date_format' => '',
        ];
    }
}
