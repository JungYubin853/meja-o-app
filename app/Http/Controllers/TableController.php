<?php

namespace App\Http\Controllers;

use App\Models\Table;
use App\Models\Waitlist;
use App\Models\VisitorLog;
use App\Models\Floor;
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
        if ($user && !$user->hasPermission('nav_dashboard')) {
            if ($user->hasPermission('nav_waitlist')) return redirect('/waitlist');
            if ($user->hasPermission('nav_reports')) return redirect('/database');
            if ($user->hasPermission('nav_tutorial')) return redirect('/tutorial');
            if ($user->hasPermission('nav_calendar')) return redirect('/calendar');
            if ($user->hasPermission('nav_profile')) return redirect('/profile');
            abort(403);
        }

        $outletId = $user ? $user->outlet_id : null;
        $outlets = \App\Models\Outlet::all();

        $selectedOutletId = $outletId;
        if ($user && $user->isSuperAdmin()) {
            $selectedOutletId = request('outlet_id') ?: ($outlets->first()->id ?? null);
        }

        // Retrieve floors for this outlet (auto-create default Lantai 1 if none exists)
        $floors = Floor::where('outlet_id', $selectedOutletId)->orderBy('floor_number')->get();
        if ($floors->isEmpty() && $selectedOutletId) {
            $defaultFloor = Floor::create([
                'outlet_id'         => $selectedOutletId,
                'name'              => 'Lantai 1',
                'floor_number'      => 1,
                'grid_width'        => 20,
                'grid_height'       => 20,
                'grid_sections'     => '[]',
                'section_templates' => '[]',
            ]);
            $floors = collect([$defaultFloor]);
            Table::where('outlet_id', $selectedOutletId)->whereNull('floor_id')->update(['floor_id' => $defaultFloor->id]);
        }

        // Active floor selection (URL param -> Session -> First floor)
        $requestedFloorId = request('floor_id') ?: session('selected_floor_id_' . $selectedOutletId);
        $activeFloor = $floors->firstWhere('id', $requestedFloorId) ?: $floors->first();
        if ($activeFloor) {
            session(['selected_floor_id_' . $selectedOutletId => $activeFloor->id]);
        }

        $tablesQuery = Table::query();
        $waitlistQuery = Waitlist::where('status', 'waiting');

        if ($selectedOutletId) {
            $tablesQuery->where('outlet_id', $selectedOutletId);
            $waitlistQuery->where('outlet_id', $selectedOutletId);
        }

        // Fetch tables: placed tables on active floor + all unplaced tables in this outlet
        $tables = $tablesQuery->where(function ($q) use ($activeFloor) {
            $q->where(function ($sub) use ($activeFloor) {
                if ($activeFloor) {
                    $sub->where('floor_id', $activeFloor->id)->where('grid_x', '>', 0);
                }
            })->orWhere(function ($sub) {
                $sub->where('grid_x', '<=', 0)->orWhereNull('grid_x');
            });
        })->get();

        $hasAvailable = $tables->contains('status', 'available');
        $waitlist = $waitlistQuery->get();

        $gridWidth  = $activeFloor ? (int) $activeFloor->grid_width : 20;
        $gridHeight = $activeFloor ? (int) $activeFloor->grid_height : 20;
        
        $gridSectionsStr = $activeFloor ? ($activeFloor->grid_sections ?: '[]') : '[]';
        $gridSections = json_decode($gridSectionsStr, true) ?? [];
        
        $sectionTemplatesStr = $activeFloor ? ($activeFloor->section_templates ?: '[]') : '[]';

        return view('dashboard', compact(
            'tables',
            'hasAvailable',
            'waitlist',
            'gridWidth',
            'gridHeight',
            'gridSections',
            'gridSectionsStr',
            'sectionTemplatesStr',
            'outlets',
            'selectedOutletId',
            'floors',
            'activeFloor'
        ));
    }

    public function showWaitlist()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($user && !$user->hasPermission('nav_waitlist')) {
            abort(403);
        }

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
        if ($request->has('floor_id') && $request->floor_id !== '') {
            $query->where('floor_id', $request->floor_id);
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
            'floor_id'     => 'nullable|exists:floors,id',
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
            'floor_id'     => $request->input('floor_id', null),
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
            'floor_id'     => $request->input('floor_id', $original->floor_id),
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

        $updateData = [
            'table_number' => $request->input('table_number', $table->table_number),
            'capacity'     => $request->input('capacity', $table->capacity),
            'shape'        => $request->input('shape', $table->shape),
            'grid_x'       => $request->input('grid_x', $table->grid_x),
            'grid_y'       => $request->input('grid_y', $table->grid_y),
            'width'        => $request->input('width', $table->width),
            'height'       => $request->input('height', $table->height),
        ];

        if ($request->has('floor_id')) {
            $updateData['floor_id'] = $request->input('floor_id');
        }

        $table->update($updateData);

        return response()->json(['success' => true]);
    }

    public function updateGridSize(Request $request)
    {
        $request->validate([
            'grid_width'        => 'required|integer|min:0|max:40',
            'grid_height'       => 'required|integer|min:0|max:40',
            'grid_sections'     => 'nullable|string',
            'section_templates' => 'nullable|string',
            'outlet_id'         => 'nullable|exists:outlets,id',
            'floor_id'          => 'nullable|exists:floors,id',
        ]);

        $outletId = $request->input('outlet_id') ?: (Auth::check() ? Auth::user()->outlet_id : null);
        $floorId  = $request->input('floor_id');

        if ($floorId) {
            $floor = Floor::find($floorId);
            if ($floor) {
                $floorUpdate = [
                    'grid_width'  => $request->input('grid_width'),
                    'grid_height' => $request->input('grid_height'),
                ];
                if ($request->has('grid_sections')) {
                    $floorUpdate['grid_sections'] = $request->input('grid_sections');
                }
                if ($request->has('section_templates')) {
                    $floorUpdate['section_templates'] = $request->input('section_templates');
                }
                $floor->update($floorUpdate);
            }
        }

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

        if ($request->expectsJson() || $request->isJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Grid canvas dimensions updated successfully.');
    }

    public function storeFloor(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user || (!$user->isAdmin() && !$user->isSuperAdmin())) {
            abort(403);
        }

        $request->validate([
            'name'         => 'required|string|max:100',
            'floor_number' => 'nullable|integer',
            'outlet_id'    => 'nullable|exists:outlets,id',
            'grid_width'   => 'nullable|integer|min:5|max:40',
            'grid_height'  => 'nullable|integer|min:5|max:40',
        ]);

        $outletId = $request->input('outlet_id') ?: $user->outlet_id;
        if (!$outletId && $user->isSuperAdmin()) {
            $outletId = \App\Models\Outlet::first()->id ?? null;
        }

        if (!$outletId) {
            return redirect()->back()->withErrors(['error' => 'Outlet is required to create a floor.']);
        }

        $floorNumber = $request->input('floor_number');
        if ($floorNumber === null || $floorNumber === '') {
            $maxFloor = Floor::where('outlet_id', $outletId)->max('floor_number') ?? 0;
            $floorNumber = $maxFloor + 1;
        }

        $floor = Floor::create([
            'outlet_id'         => $outletId,
            'name'              => $request->input('name'),
            'floor_number'      => (int) $floorNumber,
            'grid_width'        => $request->input('grid_width', 20),
            'grid_height'       => $request->input('grid_height', 20),
            'grid_sections'     => '[]',
            'section_templates' => '[]',
        ]);

        session(['selected_floor_id_' . $outletId => $floor->id]);

        return redirect()->back()->with('success', __('Floor created successfully.'));
    }

    public function updateFloor(Request $request, $id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user || (!$user->isAdmin() && !$user->isSuperAdmin())) {
            abort(403);
        }

        $floor = Floor::findOrFail($id);

        $request->validate([
            'name'         => 'required|string|max:100',
            'floor_number' => 'required|integer',
            'grid_width'   => 'nullable|integer|min:5|max:40',
            'grid_height'  => 'nullable|integer|min:5|max:40',
        ]);

        $data = [
            'name'         => $request->input('name'),
            'floor_number' => (int) $request->input('floor_number'),
        ];
        if ($request->filled('grid_width')) {
            $data['grid_width'] = (int) $request->input('grid_width');
        }
        if ($request->filled('grid_height')) {
            $data['grid_height'] = (int) $request->input('grid_height');
        }

        $floor->update($data);

        return redirect()->back()->with('success', __('Floor updated successfully.'));
    }

    public function destroyFloor($id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user || (!$user->isAdmin() && !$user->isSuperAdmin())) {
            abort(403);
        }

        $floor = Floor::findOrFail($id);
        $outletId = $floor->outlet_id;

        $totalFloors = Floor::where('outlet_id', $outletId)->count();
        if ($totalFloors <= 1) {
            return redirect()->back()->withErrors([
                'floor_error' => __('Cannot delete the only floor in this outlet.')
            ]);
        }

        // Safely move placed tables on this floor back to unplaced inventory
        Table::where('floor_id', $floor->id)->update([
            'grid_x'   => 0,
            'grid_y'   => 0,
            'floor_id' => null,
        ]);

        // Switch active floor in session if this was selected
        $remainingFloor = Floor::where('outlet_id', $outletId)->where('id', '!=', $floor->id)->orderBy('floor_number')->first();
        if ($remainingFloor) {
            session(['selected_floor_id_' . $outletId => $remainingFloor->id]);
        }

        $floor->delete();

        return redirect()->back()->with('success', __('Floor deleted successfully. Placed tables moved to unplaced inventory.'));
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
