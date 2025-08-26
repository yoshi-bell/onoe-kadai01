<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ContactRequest;
use App\Models\Contact;
use App\Models\Category;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::all();

        return view('index', compact('categories'));
    }

    public function confirm(ContactRequest $request)
    {
        $contact = $request->only([
            'first_name',
            'last_name',
            'gender',
            'email',
            'tel1',
            'tel2',
            'tel3',
            'address',
            'building',
            'category_id',
            'detail'
        ]);

        $category = Category::find($contact['category_id']);
        $contact['category_content'] = $category->content;

        return view('confirm', compact('contact'));
    }

    public function store(Request $request)
    {
        // JSONデータをデコード
        $contact = json_decode($request->input('contact_data'), true);

        // バリデーションルール
        $rules = [
            'first_name' => 'required',
            'last_name' => 'required',
            'gender' => 'required',
            'email' => 'required|email',
            'tel1' => 'required|numeric',
            'tel2' => 'required|numeric',
            'tel3' => 'required|numeric',
            'address' => 'required',
            'category_id' => 'required',
            'detail' => 'required'
        ];

        // 手動でバリデーションを実行
        $validator = validator($contact, $rules);

        if ($validator->fails()) {
            return redirect('/confirm')->withErrors($validator)->withInput($contact);
        }

        // 電話番号を結合
        $contact['tel'] = $contact['tel1'] . $contact['tel2'] . $contact['tel3'];
        unset($contact['tel1'], $contact['tel2'], $contact['tel3']);

        // データベースに保存
        Contact::create($contact);

        return view('thanks');
    }

    // 新しいbackメソッドを追加
    public function back(Request $request)
    {
        $contact = json_decode($request->input('contact_data'), true);
        return redirect('/')->withInput($contact);
    }
}
