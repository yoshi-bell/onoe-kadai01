<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Contact;
use Carbon\Carbon;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Build the base search query based on request parameters.
     *
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function buildSearchQuery(Request $request)
    {
        $query = Contact::with('category');

        if ($request->filled('name_or_email')) {
            $nameOrEmail = $request->input('name_or_email');
            $query->where(function ($q) use ($nameOrEmail) {
                $q->where('last_name', 'like', "%{$nameOrEmail}%")
                    ->orWhere('first_name', 'like', "%{$nameOrEmail}%")
                    ->orWhere('email', 'like', "%{$nameOrEmail}%");
            });
        }

        if ($request->filled('gender') && $request->input('gender') !== 'all') {
            $gender = $request->input('gender');
            $genderValue = 0;
            switch ($gender) {
                case '男性':
                    $genderValue = 1;
                    break;
                case '女性':
                    $genderValue = 2;
                    break;
                case 'その他':
                    $genderValue = 3;
                    break;
            }
            $query->where('gender', $genderValue);
        }

        if ($request->filled('contact_type')) {
            $contactType = $request->input('contact_type');
            $query->whereHas('category', function ($q) use ($contactType) {
                $q->where('content', $contactType);
            });
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->input('date'));
        }

        return $query;
    }

    /**
     * Display the admin page with all contacts.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
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
        $query = $this->buildSearchQuery($request);
        $contacts = $query->paginate(7)->appends($request->query());
        $categories = Category::all();

        return view('admin', compact('contacts', 'categories'));
    }

    /**
     * Delete a contact.
     *
     * @param int $contact_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($contact_id)
    {
        try {
            $contact = Contact::find($contact_id);
            if ($contact) {
                $contact->delete();
                return response()->json(['success' => true]);
            } else {
                return response()->json(['success' => false, 'message' => 'Record not found'], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Export contacts as a CSV file.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function exportCsv(Request $request)
    {
        $query = $this->buildSearchQuery($request);
        $contacts = $query->get();

        // CSVファイル生成
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="contacts_' . Carbon::now()->format('Ymd_His') . '.csv"',
        ];

        $callback = function () use ($contacts) {
            $stream = fopen('php://output', 'w');
            // Shift-JISに変換
            stream_filter_prepend($stream, 'convert.iconv.utf-8/cp932//TRANSLIT');

            // ヘッダー行
            fputcsv($stream, [
                'ID',
                '姓',
                '名',
                '性別',
                'メールアドレス',
                '電話番号',
                '住所',
                '建物名',
                'お問い合わせの種類',
                'お問い合わせ内容',
            ]);

            // データ行
            foreach ($contacts as $contact) {
                $genderName = '';
                if ($contact->gender === 1) {
                    $genderName = '男性';
                } elseif ($contact->gender === 2) {
                    $genderName = '女性';
                } else {
                    $genderName = 'その他';
                }

                fputcsv($stream, [
                    $contact->id,
                    $contact->last_name,
                    $contact->first_name,
                    $genderName,
                    $contact->email,
                    $contact->tel,
                    $contact->address,
                    $contact->building,
                    $contact->category ? $contact->category->content : '-',
                    $contact->detail,
                ]);
            }

            fclose($stream);
        };

        return Response::stream($callback, 200, $headers);
    }
}
