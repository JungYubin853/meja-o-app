<?php

namespace App\Http\Controllers;

use App\Models\Table;
use App\Models\Waitlist;
use App\Models\VisitorLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // <-- Add this import
use Carbon\Carbon;

class TableController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $outletId = $user ? $user->outlet_id : null;
        $outlets = \App\Models\Outlet::all();

        $selectedOutletId = $outletId;
        if ($user && $user->isSuperAdmin()) {
            $selectedOutletId = request('outlet_id') ?: ($outlets->first()->id ?? null);
        }

        $tablesQuery = Table::query();
        $waitlistQuery = Waitlist::where('status', 'waiting');
        
        $gridWidthQuery = DB::table('settings')->where('key', 'grid_width');
        $gridHeightQuery = DB::table('settings')->where('key', 'grid_height');
        $gridSectionsQuery = DB::table('settings')->where('key', 'grid_sections');

        if ($selectedOutletId) {
            $tablesQuery->where('outlet_id', $selectedOutletId);
            $waitlistQuery->where('outlet_id', $selectedOutletId);
            $gridWidthQuery->where('outlet_id', $selectedOutletId);
            $gridHeightQuery->where('outlet_id', $selectedOutletId);
            $gridSectionsQuery->where('outlet_id', $selectedOutletId);
        }

        $tables = $tablesQuery->get();
        $hasAvailable = $tables->contains('status', 'available');
        $waitlist = $waitlistQuery->get();

        $gridWidth  = $gridWidthQuery->value('value') ?? 0;
        $gridHeight = $gridHeightQuery->value('value') ?? 0;
        
        $gridSectionsStr = $gridSectionsQuery->value('value') ?? '[]';
        $gridSections = json_decode($gridSectionsStr, true) ?? [];
        
        $sectionTemplatesQuery = DB::table('settings')->where('key', 'section_templates');
        if ($selectedOutletId) {
            $sectionTemplatesQuery->where('outlet_id', $selectedOutletId);
        }
        $sectionTemplatesStr = $sectionTemplatesQuery->value('value') ?? '[]';

        return view('dashboard', compact('tables', 'hasAvailable', 'waitlist', 'gridWidth', 'gridHeight', 'gridSections', 'gridSectionsStr', 'sectionTemplatesStr', 'outlets', 'selectedOutletId'));
    }

    public function showWaitlist()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $outletId = $user ? $user->outlet_id : null;
        
        $selectedOutletId = $outletId;
        if ($user && $user->isSuperAdmin()) {
            $selectedOutletId = request('outlet_id') ?: (\App\Models\Outlet::first()->id ?? null);
        }

        $query = Waitlist::where('status', 'waiting');
        if ($selectedOutletId) {
            $query->where('outlet_id', $selectedOutletId);
        }
        
        $waitlist = $query->get();
        $outlets = \App\Models\Outlet::all();
        return view('waitlist', compact('waitlist', 'outlets', 'selectedOutletId'));
    }

    public function getLiveStatus(Request $request)
    {
        $query = Table::query();
        if ($request->has('outlet_id') && $request->outlet_id !== '') {
            $query->where('outlet_id', $request->outlet_id);
        }
        return response()->json($query->get(['id', 'status', 'pax', 'table_number', 'capacity']));
    }

    public function seatCustomer(Request $request, $id)
    {
        $table = Table::findOrFail($id);

        $request->validate([
            'pax' => 'required|integer|min:1|max:' . $table->capacity,
        ], [
            'pax.max' => 'The number of guests exceeds this table maximum capacity (' . $table->capacity . ' persons).'
        ]);

        $paxCount = (int) $request->input('pax');

        $table->update([
            'status'      => 'occupied',
            'pax'         => $paxCount,
            'seated_time' => now(),
            'updated_by'  => Auth::check() ? Auth::user()->name : 'System',
        ]);

        VisitorLog::create([
            'outlet_id'     => $table->outlet_id,
            'customer_name' => 'Walk-in Guest',
            'phone'         => '-',
            'pax'           => $paxCount,
            'started_at'    => now(),
            'created_by'    => Auth::check() ? Auth::user()->name : 'System',
        ]);

        return redirect()->back();
    }

    public function storeWaitlist(Request $request)
    {
        $request->validate([
            'customer_name' => 'nullable|string|max:255',
            'pax'           => 'required|integer|min:1',
            'phone'         => 'nullable|string|max:50',
            'outlet_id'     => 'nullable|exists:outlets,id',
        ]);

        $customerName = $request->input('customer_name') ?: 'Walk-in Guest';
        $outletId = $request->input('outlet_id') ?: (Auth::check() ? Auth::user()->outlet_id : null);
        
        // Fallback for Super Admins if no outlet_id is passed and their own is null
        if (!$outletId && Auth::check() && Auth::user()->isSuperAdmin()) {
            $outletId = \App\Models\Outlet::first()->id ?? null;
        }

        Waitlist::create([
            'outlet_id'     => $outletId,
            'customer_name' => $customerName,
            'phone'         => $request->input('phone'),
            'pax'           => $request->input('pax'),
            'status'        => 'waiting',
            'created_by'    => Auth::check() ? Auth::user()->name : 'System',
        ]);

        return redirect()->back()->with('success_waitlist', 'Successfully Added to Waitlist');
    }

    public function finishMeal($id)
    {
        $table = Table::findOrFail($id);

        $activeLog = VisitorLog::whereNull('ended_at')
            ->latest('started_at')
            ->first();

        if ($activeLog) {
            $end   = now();
            $start = Carbon::parse($activeLog->started_at);

            $diffInMinutes = $start->diffInMinutes($end);
            $hours   = intdiv($diffInMinutes, 60);
            $minutes = $diffInMinutes % 60;
            $elapsedStr = ($hours > 0 ? "{$hours}h " : "") . "{$minutes}m";

            $activeLog->update([
                'ended_at'     => $end,
                'time_elapsed' => $elapsedStr,
            ]);

            if ($activeLog->customer_name && $activeLog->customer_name !== 'Walk-in Guest') {
                Waitlist::where('customer_name', $activeLog->customer_name)
                    ->where('status', 'seated')
                    ->update(['status' => 'Finished']);
            }
        }

        // Updated to immediately reset back to available (Green)
        $table->update([
            'status'       => 'available',
            'seated_time'  => null,
            'cleared_time' => null,
            'pax'          => null,
            'updated_by'   => Auth::check() ? Auth::user()->name : 'System',
        ]);

        return redirect()->back();
    }

    public function seatWaitlistCustomer(Request $request, $waitlistId)
    {
        $request->validate([
            'table_id' => 'required|exists:tables,id',
        ]);

        $table = Table::where('id', $request->input('table_id'))
            ->where('status', 'available')
            ->firstOrFail();

        $waitlist = Waitlist::findOrFail($waitlistId);

        if ($waitlist->pax > $table->capacity) {
            return redirect()->back()->withErrors([
                'error' => 'Party size (' . $waitlist->pax . ') exceeds table capacity (' . $table->capacity . ').'
            ]);
        }

        $paxCount = (int) $waitlist->pax;

        $table->update([
            'status'      => 'occupied',
            'pax'         => $paxCount,
            'seated_time' => now(),
            'updated_by'  => Auth::check() ? Auth::user()->name : 'System',
        ]);

        $waitlist->update(['status' => 'seated']);

        VisitorLog::create([
            'customer_name' => $waitlist->customer_name,
            'phone'         => $waitlist->phone,
            'pax'           => $paxCount,
            'started_at'    => now(),
            'created_by'    => $waitlist->created_by,
        ]);

        return redirect()->back();
    }

    public function cancelWaitlist($id)
    {
        Waitlist::findOrFail($id)->update(['status' => 'cancelled']);

        return redirect()->back();
    }

    public function storeCustomTable(Request $request)
    {
        $request->validate([
            'table_number' => 'nullable|string|max:50',
            'capacity'     => 'nullable|integer|min:0',
            'shape'        => 'required|in:square,circle,triangle',
            'width'        => 'required|integer|min:1|max:4',
            'height'       => 'required|integer|min:1|max:4',
            'outlet_id'    => 'nullable|exists:outlets,id',
        ]);

        $tableNum = $request->input('table_number');
        $tableNum = !empty(trim($tableNum ?? '')) ? trim($tableNum) : null;

        $targetOutletId = $request->input('outlet_id') ?: Auth::user()->outlet_id;

        // Only check for duplicates if a REAL name is actually typed in
        if ($tableNum !== null && Table::where('table_number', $tableNum)->where('outlet_id', $targetOutletId)->exists()) {
            return redirect()->back()
                ->withErrors(['table_number' => 'Error: A table with this number or name already exists. Please choose a different name.'])
                ->withInput();
        }

        Table::create([
            'outlet_id'    => $targetOutletId,
            'table_number' => $tableNum, // Stores NULL when empty
            'capacity'     => $request->input('capacity') ? (int)$request->input('capacity') : 0,
            'shape'        => $request->input('shape'),
            'grid_x'       => 0, // Stays in inventory tray
            'grid_y'       => 0,
            'width'        => $request->input('width'),
            'height'       => $request->input('height'),
            'status'       => 'available',
            'created_by'   => Auth::user()->name ?? 'System',
        ]);

        return redirect()->back()->with('success', 'Table successfully created.');
    }

    public function cloneTable(Request $request, $id)
    {
        $original = Table::findOrFail($id);

        Table::create([
            'outlet_id'    => $original->outlet_id,
            'table_number' => null, // Stays null so it can be named later
            'capacity'     => $original->capacity ?? 0,
            'shape'        => $original->shape,
            'grid_x'       => $request->input('grid_x', 0),
            'grid_y'       => $request->input('grid_y', 0),
            'width'        => $original->width,
            'height'       => $original->height,
            'status'       => 'available',
            'created_by'   => Auth::user()->name ?? 'System',
        ]);

        return response()->json(['success' => true]);
    }

    public function updateTableCoordinates(Request $request, $id)
    {
        $table = Table::findOrFail($id);

        $table->update([
            'table_number' => $request->input('table_number', $table->table_number),
            'capacity'     => $request->input('capacity', $table->capacity),
            'shape'        => $request->input('shape', $table->shape),
            'grid_x'       => $request->input('grid_x', $table->grid_x),
            'grid_y'       => $request->input('grid_y', $table->grid_y),
            'width'        => $request->input('width', $table->width),
            'height'       => $request->input('height', $table->height),
        ]);

        return response()->json(['success' => true]);
    }

    public function updateGridSize(Request $request)
    {
        $request->validate([
            'grid_width'  => 'required|integer|min:0|max:40',
            'grid_height' => 'required|integer|min:0|max:40',
            'grid_sections' => 'nullable|string',
            'outlet_id'   => 'nullable|exists:outlets,id',
        ]);

        $outletId = $request->input('outlet_id') ?: Auth::user()->outlet_id;

        if ($outletId) {
            DB::table('settings')->updateOrInsert(
                ['key' => 'grid_width', 'outlet_id' => $outletId],
                ['value' => $request->input('grid_width'), 'updated_at' => now()]
            );

            DB::table('settings')->updateOrInsert(
                ['key' => 'grid_height', 'outlet_id' => $outletId],
                ['value' => $request->input('grid_height'), 'updated_at' => now()]
            );
            
            if ($request->has('grid_sections')) {
                DB::table('settings')->updateOrInsert(
                    ['key' => 'grid_sections', 'outlet_id' => $outletId],
                    ['value' => $request->input('grid_sections'), 'updated_at' => now()]
                );
            }
            if ($request->has('section_templates')) {
                DB::table('settings')->updateOrInsert(
                    ['key' => 'section_templates', 'outlet_id' => $outletId],
                    ['value' => $request->input('section_templates'), 'updated_at' => now()]
                );
            }
        }

        return redirect()->back()->with('success', 'Grid canvas dimensions updated successfully.');
    }

    public function destroy($id)
    {
        $table = Table::findOrFail($id);
        $table->delete();

        return redirect()->back()->with('success', 'Table deleted successfully.');
    }

    public function seatTable(Request $request, $id)
    {
        $table = Table::findOrFail($id);

        $request->validate([
            'pax' => 'required|integer|min:1',
        ]);

        $pax = $request->input('pax');

        $table->update([
            'status'      => 'occupied',
            'seated_time' => now(),
            'pax'         => $pax,
            'updated_by'  => Auth::check() ? Auth::user()->name : 'System',
        ]);

        VisitorLog::create([
            'table_id'      => $table->id,
            'customer_name' => $request->input('customer_name', 'Walk-in Guest'),
            'pax'           => $pax,
            'started_at'    => now(),
        ]);

        return redirect()->back();
    }
}
