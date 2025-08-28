@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
@endsection

@section('header-button')
<div class="logout-button">
    <form action="/logout" method="post">
        @csrf
        <button class="logout-button__link" type="submit">logout</button>
    </form>
</div>
@endsection

@section('content')
<div class="admin-form__content">
    <div class="admin-form__heading">
        <h2>Admin</h2>
    </div>
    <div class="search-form">
        <form class="search-form__items" id="search-form" action="/admin/search" method="get">
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
            <button id="export-button">エクスポート</button>
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
                    <td><button class="detail-button"
                            data-id="{{ $contact->id }}"
                            data-last_name="{{ $contact->last_name }}"
                            data-first_name="{{ $contact->first_name }}"
                            data-gender="@if($contact->gender === 1)男性@elseif($contact->gender === 2)女性@elseその他@endif"
                            data-email="{{ $contact->email }}"
                            data-tel="{{ $contact->tel }}"
                            data-address="{{ $contact->address }}"
                            data-building="{{ $contact->building }}"
                            data-contact_type="{{ $contact->category ? $contact->category->content : '-' }}"
                            data-detail="{{ $contact->detail }}">詳細</button></td>
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const exportButton = document.getElementById('export-button');

        if (exportButton) {
            exportButton.addEventListener('click', function(event) {
                // 現在のURLからクエリ文字列を取得
                const queryString = window.location.search;
                const exportUrl = '/admin/export-csv' + queryString;
                window.location.href = exportUrl;
            });
        }
    });
</script>
@endsection

@section('modal')
<div class="modal" id="modal">
    <div class="modal__content">
        <span class="modal__close-btn">&times;</span>
        <table class="modal-table">
            <tr>
                <th>お名前</th>
                <td id="modal-name"></td>
            </tr>
            <tr>
                <th>性別</th>
                <td id="modal-gender"></td>
            </tr>
            <tr>
                <th>メールアドレス</th>
                <td id="modal-email"></td>
            </tr>
            <tr>
                <th>電話番号</th>
                <td id="modal-tel"></td>
            </tr>
            <tr>
                <th>住所</th>
                <td id="modal-address"></td>
            </tr>
            <tr>
                <th>建物名</th>
                <td id="modal-building"></td>
            </tr>
            <tr>
                <th>お問い合わせの種類</th>
                <td id="modal-contact-type"></td>
            </tr>
            <tr>
                <th>お問い合わせ内容</th>
                <td id="modal-detail"></td>
            </tr>
        </table>
        <div class="delete-button-container">
            <button type="button" class="delete-button" data-id="">削除</button>
        </div>
    </div>
</div>

<script src="{{ asset('js/admin.js') }}"></script>
@endsection