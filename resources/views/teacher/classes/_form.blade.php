<div class="grid gap-5 sm:grid-cols-2">
    <label class="block sm:col-span-2">
        <span class="text-sm font-black text-slate-700">Class name</span>
        <input name="name" value="{{ old('name', $class->name ?? '') }}" required maxlength="120" placeholder="Example: FYJC Information Technology" class="mt-2 w-full rounded-xl border-slate-300 focus:border-cyan-500 focus:ring-cyan-500">
    </label>

    <label class="block">
        <span class="text-sm font-black text-slate-700">Subject</span>
        <input name="subject" value="{{ old('subject', $class->subject ?? '') }}" maxlength="120" placeholder="Information Technology" class="mt-2 w-full rounded-xl border-slate-300 focus:border-cyan-500 focus:ring-cyan-500">
    </label>

    <label class="block">
        <span class="text-sm font-black text-slate-700">Level / Class</span>
        <input name="level" value="{{ old('level', $class->level ?? '') }}" maxlength="100" placeholder="FYJC / SYJC / Beginner" class="mt-2 w-full rounded-xl border-slate-300 focus:border-cyan-500 focus:ring-cyan-500">
    </label>

    <label class="block sm:col-span-2">
        <span class="text-sm font-black text-slate-700">Academic year</span>
        <input name="academic_year" value="{{ old('academic_year', $class->academic_year ?? '') }}" maxlength="30" placeholder="2026–27" class="mt-2 w-full rounded-xl border-slate-300 focus:border-cyan-500 focus:ring-cyan-500">
    </label>

    <label class="block sm:col-span-2">
        <span class="text-sm font-black text-slate-700">Description</span>
        <textarea name="description" rows="5" maxlength="1000" placeholder="Short class description, learning goals, or notes." class="mt-2 w-full rounded-xl border-slate-300 focus:border-cyan-500 focus:ring-cyan-500">{{ old('description', $class->description ?? '') }}</textarea>
    </label>
</div>
