file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\account-management.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

old_action_block = """                                    <td class="p-3 text-right">
                                        @if ($u->id !== $user->id)
                                            <form action="/users/{{ $u->id }}" method="POST"
                                                onsubmit="return confirm('Are you sure you want to delete user {{ $u->name }}?');" class="inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="text-[10px] font-bold uppercase tracking-wider text-rose-600 hover:text-rose-800 bg-rose-50 hover:bg-rose-100 px-3 py-1.5 rounded-lg border border-rose-200 transition">
                                                    Delete
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Current User</span>
                                        @endif
                                    </td>"""

new_action_block = """                                    <td class="p-3 text-right">
                                        @if ($u->id !== $user->id)
                                            <div class="flex items-center justify-end gap-2">
                                                <a href="/role-permission?email={{ urlencode($u->email) }}" title="Manage Permissions"
                                                   class="flex items-center justify-center w-8 h-8 rounded-lg bg-slate-50 hover:bg-slate-100 border border-slate-200 text-slate-500 hover:text-slate-800 transition">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                                </a>
                                                
                                                <form action="/users/{{ $u->id }}" method="POST"
                                                    onsubmit="return confirm('Are you sure you want to delete user {{ $u->name }}?');" class="inline-block m-0">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" title="Delete User"
                                                        class="flex items-center justify-center w-8 h-8 rounded-lg bg-rose-50 hover:bg-rose-100 border border-rose-200 text-rose-500 hover:text-rose-700 transition">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Current User</span>
                                        @endif
                                    </td>"""

content = content.replace(old_action_block, new_action_block)

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)
print("Updated Action buttons.")
