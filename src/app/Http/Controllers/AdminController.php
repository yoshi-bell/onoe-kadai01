<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;
use App\Models\Category;

class AdminController extends Controller
{
    /**
     * Display the admin page with all contacts.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // CategoryリレーションをEager Loadingする
        $contacts = Contact::with('category')->paginate(7);
        $categories = Category::all();

        return view('admin', compact('contacts', 'categories'));
    }

    /**
     * Search contacts based on various criteria.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function search(Request $request)
    {
        $name_or_email = $request->input('name_or_email');
        $gender = $request->input('gender');
        $contact_type = $request->input('contact_type');
        $date = $request->input('date');

        $query = Contact::with('category'); // ここでもCategoryリレーションをEager Loading

        if ($name_or_email) {
            $query->where(function ($q) use ($name_or_email) {
                $q->where('last_name', 'like', "%{$name_or_email}%")
                    ->orWhere('first_name', 'like', "%{$name_or_email}%")
                    ->orWhere('email', 'like', "%{$name_or_email}%");
            });
        }

        if ($gender && $gender !== 'all') {
            $genderValue = 0;
            if ($gender === '男性') {
                $genderValue = 1;
            } elseif ($gender === '女性') {
                $genderValue = 2;
            } elseif ($gender === 'その他') {
                $genderValue = 3;
            }
            $query->where('gender', $genderValue);
        }

        if ($contact_type) {
            $query->whereHas('category', function ($q) use ($contact_type) {
                $q->where('content', $contact_type);
            });
        }

        if ($date) {
            $query->whereDate('created_at', $date);
        }

        $contacts = $query->paginate(7)->appends($request->query());
        $categories = Category::all();

        return view('admin', compact('contacts', 'categories'));
    }
}
