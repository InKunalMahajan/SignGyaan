@php($isEdit = isset($managedUser))

<div class="grid gap-6 lg:grid-cols-2">
    <div>
        <label for="name" class="text-sm font-black text-slate-700">Full name</label>
        <input id="name" name="name" value="{{ old('name', $managedUser->name ?? '') }}" required class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-cyan-100">
        @error('name')<p class="mt-2 text-xs font-bold text-rose-700">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="email" class="text-sm font-black text-slate-700">Email address</label>
        <input id="email" name="email" type="email" value="{{ old('email', $managedUser->email ?? '') }}" required class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-cyan-100">
        @error('email')<p class="mt-2 text-xs font-bold text-rose-700">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="role" class="text-sm font-black text-slate-700">Role</label>
        <select id="role" name="role" required class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-cyan-100">
            @foreach ($roles as $value => $label)
                <option value="{{ $value }}" @selected(old('role', $managedUser->role ?? 'learner') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('role')<p class="mt-2 text-xs font-bold text-rose-700">{{ $message }}</p>@enderror
    </div>

    <div class="flex items-end">
        <label class="flex w-full items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $managedUser->is_active ?? true)) class="size-4 rounded border-slate-300 text-blue-700 focus:ring-cyan-300">
            <span>
                <span class="block text-sm font-black text-slate-800">Active account</span>
                <span class="block text-xs text-slate-500">Inactive users cannot sign in.</span>
            </span>
        </label>
        @error('is_active')<p class="mt-2 text-xs font-bold text-rose-700">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="password" class="text-sm font-black text-slate-700">Password {{ $isEdit ? '(optional)' : '' }}</label>
        <input id="password" name="password" type="password" @required(! $isEdit) autocomplete="new-password" class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-cyan-100">
        <p class="mt-2 text-xs text-slate-500">Minimum 8 characters. {{ $isEdit ? 'Leave blank to keep the current password.' : '' }}</p>
        @error('password')<p class="mt-2 text-xs font-bold text-rose-700">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="password_confirmation" class="text-sm font-black text-slate-700">Confirm password</label>
        <input id="password_confirmation" name="password_confirmation" type="password" @required(! $isEdit) autocomplete="new-password" class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-cyan-100">
    </div>
</div>
