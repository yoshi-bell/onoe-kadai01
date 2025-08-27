<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ContactRequest;
use App\Models\Contact;
use App\Models\Category;

class ContactController extends Controller
{
    /**
     * Display the contact form.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $categories = Category::all();
        return view('index', compact('categories'));
    }

    /**
     * Validate the contact form and show the confirmation page.
     *
     * @param ContactRequest $request
     * @return \Illuminate\View\View
     */
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
        // Convert gender code to display text
        if ($contact['gender'] === '1') {
            $contact['gender_text'] = '男性';
        } elseif ($contact['gender'] === '2') {
            $contact['gender_text'] = '女性';
        } else {
            $contact['gender_text'] = 'その他';
        }
        return view('confirm', compact('contact'));
    }

    /**
     * Store the contact data in the database and show the thanks page.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function store(Request $request)
    {
        // Decode the JSON data from the hidden input field
        $contact = json_decode($request->input('contact_data'), true);

        // Convert gender to a numerical value for the database
        $genderValue = null;
        if ($contact['gender'] === '男性') {
            $genderValue = 1;
        } elseif ($contact['gender'] === '女性') {
            $genderValue = 2;
        } else {
            $genderValue = 3;
        }

        // Prepare data for database insertion
        $dataToSave = [
            'first_name' => $contact['first_name'],
            'last_name' => $contact['last_name'],
            'gender' => $genderValue,
            'email' => $contact['email'],
            'address' => $contact['address'],
            'building' => $contact['building'],
            'category_id' => $contact['category_id'],
            'detail' => $contact['detail'],
            'tel' => $contact['tel1'] . $contact['tel2'] . $contact['tel3'],
        ];

        // Store data in the database
        Contact::create($dataToSave);

        return view('thanks');
    }

    /**
     * Redirect back to the input form with previous data.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function back(Request $request)
    {
        $contact = json_decode($request->input('contact_data'), true);
        return redirect('/')->withInput($contact);
    }
}
