<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>admin</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inika:wght@400;700&display=swap" rel="stylesheet">
</head>

<body>
    <header class="header">
        <div class="header__inner">
            <div class="header-utilities">
                <a class="header__logo" href="/">
                    FashionablyLate
                </a>
                <div class="logout-button">
                    <form action="/logout" method="post">
                        @csrf
                        <button class="logout-button__link" type="submit">logout</button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <main>
        <div class="admin-form__content">
            <div class="admin-form__heading">
                <h2>Admin</h2>
            </div>
            <div class="search-form">
                <form class="search-form__items" action="/admin/search" method="get">
                    @csrf
                    <div class="search-form__item">
                        <input type="text" name="name_or_email" placeholder="名前やメールアドレスを入力してください" value="{{ old('name_or_email') ?? $name_or_email ?? '' }}">
                    </div>
                    <div class="search-form__item">
                        <select name="gender">
                            <option value="" @if(empty(old('gender') ?? $gender ?? '' )) selected @endif>性別</option>
                            <option value="all" @if((old('gender') ?? $gender ?? '' )=='all' ) selected @endif>全て</option>
                            <option value="男性" @if((old('gender') ?? $gender ?? '' )=='男性' ) selected @endif>男性</option>
                            <option value="女性" @if((old('gender') ?? $gender ?? '' )=='女性' ) selected @endif>女性</option>
                            <option value="その他" @if((old('gender') ?? $gender ?? '' )=='その他' ) selected @endif>その他</option>
                        </select>
                    </div>
                    <div class="search-form__item">
                        <select name="contact_type">
                            <option value="" @if(empty(old('contact_type') ?? $contact_type ?? '' )) selected @endif>お問い合わせの種類</option>
                            @foreach ($categories as $category)
                            <option value="{{ $category->content }}" @if((old('contact_type') ?? $contact_type ?? '' )==$category->content) selected @endif>
                                {{ $category->content }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="search-form__item">
                        <input type="date" name="date" value="{{ old('date') ?? $date ?? '' }}">
                    </div>
                    <div class="search-form__button-group">
                        <button class="search-form__button--search" type="submit">検索</button>
                        <a href="{{ route('admin') }}" class="search-form__button--reset">リセット</a>
                    </div>
                </form>
            </div>

            <div class="layout-container">
                <div class="export-button">
                    <button>エクスポート</button>
                </div>
                <div class="pagination">
                    {{ $contacts->links('vendor.pagination.admin_pagination') }}
                </div>
            </div>

            <div class="result-table">
                <table>
                    <thead>
                        <tr>
                            <th>お名前</th>
                            <th>性別</th>
                            <th>メールアドレス</th>
                            <th>お問い合わせの種類</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($contacts->isEmpty())
                        <tr>
                            <td colspan="5">検索結果が見つかりませんでした。</td>
                        </tr>
                        @else
                        @foreach($contacts as $contact)
                        <tr>
                            <td>{{ $contact->last_name }} {{ $contact->first_name }}</td>
                            <td>
                                @if($contact->gender === 1)
                                男性
                                @elseif($contact->gender === 2)
                                女性
                                @else
                                その他
                                @endif
                            </td>
                            <td>{{ $contact->email }}</td>
                            <td>
                                @if($contact->category)
                                {{ $contact->category->content }}
                                @else
                                -
                                @endif
                            </td>
                            <td><button>詳細</button></td>
                        </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>

</html>