<?php

namespace App\Http\Requests\Products;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {

       if(Auth::check()){
        return true;
       }
       return false;
    }

    public function all($keys = null){
        $data = Parent::all($keys);
        $data['id'] = (int) $this->route('product');
        return $data;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id' => 'integer|required|gt:0|exists:'.Product::class.',id',
            'description' => 'required',
            'title'=>'required',
            'sku' =>'required'
        ];
    }
}
