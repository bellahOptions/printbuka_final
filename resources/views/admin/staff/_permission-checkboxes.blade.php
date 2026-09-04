@php($suffix = $suffix ?? 'new')
<label class="mb-3 flex items-center gap-2 rounded-lg border border-red-200 bg-red-50 px-3 py-2.5 text-sm font-medium text-red-800 cursor-pointer">
    <input type="checkbox" name="permissions[]" value="*" id="perm-wildcard-{{ $suffix }}"
           class="h-4 w-4 rounded border-red-300 text-red-600 focus:ring-red-500"
           @checked(in_array('*', $selected, true))>
    Full access (all permissions, including future ones) — grant with care
</label>

<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
    @foreach($permissionGroups as $group => $permissions)
        <fieldset class="rounded-lg border border-slate-200 p-3">
            <legend class="px-1 text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $group }}</legend>
            <div class="space-y-1.5 mt-1">
                @foreach($permissions as $value => $description)
                    <label class="flex items-start gap-2 text-xs text-slate-700 cursor-pointer">
                        <input type="checkbox" name="permissions[]" value="{{ $value }}" id="perm-{{ $value }}-{{ $suffix }}"
                               class="mt-0.5 h-3.5 w-3.5 rounded border-slate-300 text-violet-600 focus:ring-violet-500"
                               @checked(in_array($value, $selected, true))>
                        <span>{{ $description }}<span class="block text-slate-400">{{ $value }}</span></span>
                    </label>
                @endforeach
            </div>
        </fieldset>
    @endforeach
</div>
