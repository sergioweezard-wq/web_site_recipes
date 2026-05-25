@extends('layouts.app')

@section('content')
    <div class="container mt-3">
    <h2 class="fw-bold mb-3">Адмін-панель</h2>

    <table class="table table-striped table-hover">
        <thead class="table-light">
        <tr>
            <th>Email</th>
            <th>Admin</th>
            <th>Redactor</th>
            <th class="admin-action-col">Дія</th>
        </tr>
        </thead>
        <tbody>
        @foreach($users as $user)
            <tr>
                <td>{{ $user->email }}</td>
                <td>
                    @if($user->role === 'Admin')
                        <span class="badge text-bg-success">Так</span>
                    @else
                        <span class="badge text-bg-secondary">Ні</span>
                    @endif
                </td>
                <td>
                    @if($user->role === 'Redactor')
                        <span class="badge text-bg-warning">Так</span>
                    @else
                        <span class="badge text-bg-secondary">Ні</span>
                    @endif
                </td>
                <td>
                    @if($user->role === 'Admin')
                        <span class="text-muted">Адміністратор</span>
                    @elseif($user->role === 'Redactor')
                        <form method="post" action="{{ route('admin.revoke-redactor') }}" class="d-inline">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ $user->id }}">
                            <button class="btn btn-outline-danger btn-sm">Зняти Redactor</button>
                        </form>
                    @else
                        <form method="post" action="{{ route('admin.grant-redactor') }}" class="d-inline">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ $user->id }}">
                            <button class="btn btn-outline-primary btn-sm">Дати Redactor</button>
                        </form>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    </div>
@endsection
