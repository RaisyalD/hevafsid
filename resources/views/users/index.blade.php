@extends('layouts.app')
@section('title', 'Manajemen User')
@section('page-title', 'Manajemen User')
@section('breadcrumb', 'Administrasi → User')

@section('content')
<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

    <div class="lg:col-span-2">
        <form method="GET" class="flex gap-2 mb-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / email…" class="input flex-1">
            <select name="role_id" class="input w-44">
                <option value="">Semua Role</option>
                @foreach($roles as $role)
                <option value="{{ $role->id }}" {{ request('role_id') == $role->id ? 'selected' : '' }}>{{ $role->display_name }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn-primary">Cari</button>
        </form>

        <div class="bg-white rounded-2xl border border-pink-100 shadow-sm overflow-hidden">
            <table class="table w-full">
                <thead class="bg-pink-50/50">
                    <tr>
                        <th>User</th>
                        <th>Role</th>
                        <th>Email</th>
                        <th>Telepon</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-pink-50">
                    @forelse($users as $user)
                    <tr>
                        <td>
                            <div class="flex items-center gap-3">
                                <img src="{{ $user->avatar_url }}" alt="" class="h-8 w-8 rounded-full object-cover">
                                <span class="font-medium text-gray-900">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td>
                            @php
                            $roleClass = match($user->role?->name) {
                                'super_admin'    => 'badge-red',
                                'owner'          => 'badge-pink',
                                'admin_gudang'   => 'badge-blue',
                                'admin_keuangan' => 'badge-yellow',
                                default          => 'badge-blue',
                            };
                            @endphp
                            <span class="{{ $roleClass }}">{{ $user->role?->display_name }}</span>
                        </td>
                        <td class="text-sm text-gray-600">{{ $user->email }}</td>
                        <td class="text-sm text-gray-500">{{ $user->phone ?? '—' }}</td>
                        <td>
                            <span class="{{ $user->is_active ? 'badge-green' : 'badge-red' }}">
                                {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td>
                            @if($user->id !== auth()->id())
                            <form id="del-usr-{{ $user->id }}" method="POST" action="{{ route('users.destroy', $user) }}">
                                @csrf @method('DELETE')
                                <button type="button"
                                        onclick="openConfirm('Nonaktifkan User', 'User ini akan dinonaktifkan dan tidak dapat login ke sistem.', 'Ya, Nonaktifkan', 'del-usr-{{ $user->id }}', false)"
                                        class="rounded-lg p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 transition text-xs">
                                    Nonaktifkan
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="py-10 text-center text-sm text-gray-400">Belum ada user</td></tr>
                    @endforelse
                </tbody>
            </table>
            @if($users->hasPages())
            <div class="border-t border-pink-50 px-6 py-4">{{ $users->links() }}</div>
            @endif
        </div>
    </div>

    {{-- Add User --}}
    <div class="bg-white rounded-2xl border border-pink-100 shadow-sm p-5">
        <h3 class="text-sm font-semibold text-gray-900 mb-4">Tambah User Baru</h3>
        <form method="POST" action="{{ route('users.store') }}" class="space-y-3">
            @csrf
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Nama <span class="text-rose-500">*</span></label>
                <input type="text" name="name" required class="input text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Email <span class="text-rose-500">*</span></label>
                <input type="email" name="email" required class="input text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Telepon</label>
                <input type="text" name="phone" class="input text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Role <span class="text-rose-500">*</span></label>
                <select name="role_id" required class="input text-sm">
                    @foreach($roles as $role)
                    <option value="{{ $role->id }}">{{ $role->display_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Password <span class="text-rose-500">*</span></label>
                <input type="password" name="password" required minlength="8" class="input text-sm">
            </div>
            <button type="submit" class="btn-primary w-full text-sm">Tambah User</button>
        </form>
    </div>
</div>
@endsection
