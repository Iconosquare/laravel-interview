<?php

namespace App\Http\Requests;

use App\Models\PostStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PostStoreRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        $method = $this->method();

        $requiredOnCreate = ['title', 'slug', 'content', 'author'];
        $common = [
            'title' => 'min:3|max:255',
            'slug' => 'unique:posts,slug|max:100|regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/i',
            'author' => 'min:3|max:100',
            'content' => 'min:10',
            'status' => Rule::in(PostStatus::all())
        ];

        if ($method === 'PATCH') {

            return $common;
        }

        // Add the "required" rule to all required fields.
        return array_map(function ($v) use ($requiredOnCreate) {

            if (in_array($v, $requiredOnCreate)) {

                return 'required' . (strlen($v) > 0 ? '|' : '') . $v;
            }

            return $v;
        }, $common);
    }
}
