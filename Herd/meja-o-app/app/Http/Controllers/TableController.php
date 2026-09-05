<?php

namespace App\Http\Controllers;

use App\Models\Table;
use App\Models\Waitlist;
use App\Models\VisitorLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Add this at the top

class TableController extends Controller
{
    public function index()
    {
        $tables = Table::all();
        $hasAvailable = $tables->contains('status', 'available');
        // Fetch only active queue for current view, but history remains safely in DB
        $waitlist = Waitlist::where('status', 'waiting')->get();

        return view('dashboard', compact('tables', 'hasAvailable', 'waitlist'));
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
            'status' => 'occupied',
            'pax' => $paxCount,
            'seated_time' => now(),
        ]);

        // Log session start
        VisitorLog::create([
            'customer_name' => 'Walk-in Guest',
            'phone' => '-',
            'pax' => $paxCount,
            'started_at' => now(),
        ]);

        return redirect()->back();
    }

    public function storeWaitlist(Request $request)
    {
        $request->validate([
            'customer_name' => 'nullable|string|max:255',
            'pax' => 'required|integer|min:1',
            'phone' => 'nullable|string|max:50',
        ]);

        $customerName = $request->input('customer_name');
        if (empty($customerName)) {
            $customerName = 'Walk-in Guest';
        }

        Waitlist::create([
            'customer_name' => $customerName,
            'phone' => $request->input('phone'),
            'pax' => $request->input('pax'),
            'status' => 'waiting',
            'created_by' => Auth::check() ? Auth::user()->name : 'System', // Captures the exact user's name
        ]);

        return redirect()->back()->with('success_waitlist', 'Successfully Added to Waitlist');
    }

    public function finishMeal($id)
    {
        $table = Table::findOrFail($id);

        // Find the active log entry or waitlist connection if needed
        $activeLog = VisitorLog::whereNull('ended_at')
            ->orderBy('started_at', 'desc')
            ->first();

        if ($activeLog) {
            $end = now();
            $start = \Carbon\Carbon::parse($activeLog->started_at);

            $diffInMinutes = $start->diffInMinutes($end);
            $hours = intdiv($diffInMinutes, 60);
            $minutes = $diffInMinutes % 60;

            $elapsedStr = ($hours > 0 ? "{$hours}h " : "") . "{$minutes}m";

            $activeLog->update([
                'ended_at' => $end,
                'time_elapsed' => $elapsedStr,
            ]);

            // If this active log matched a waitlist customer by name/phone, update their waitlist status to Finished
            if ($activeLog->customer_name && $activeLog->customer_name !== 'Walk-in Guest') {
                Waitlist::where('customer_name', $activeLog->customer_name)
                    ->where('status', 'seated')
                    ->update(['status' => 'Finished']);
            }
        }

        // Update Database: Set Table Status to Dirty & Save Cleared Time
        $table->update([
            'status' => 'dirty',
            'cleared_time' => now(),
            'pax' => null, // Clear table pax if appropriate for your flow
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

        // Properly fetch the waitlist record
        $waitlist = Waitlist::findOrFail($waitlistId);

        if ($waitlist->pax > $table->capacity) {
            return redirect()->back()->withErrors(['error' => 'Party size (' . $waitlist->pax . ') exceeds table capacity (' . $table->capacity . ').']);
        }

        $paxCount = (int) $waitlist->pax;

        $table->update([
            'status' => 'occupied',
            'pax' => $paxCount,
            'seated_time' => now(),
        ]);

        $waitlist->update([
            'status' => 'seated'
        ]);

        // Log session start with waitlist info & creator name
        VisitorLog::create([
            'customer_name' => $waitlist->customer_name,
            'phone' => $waitlist->phone,
            'pax' => $paxCount,
            'started_at' => now(),
            'created_by' => $waitlist->created_by, // Passes Bob or Yubin correctly
        ]);

        return redirect()->back();
    }

    public function updateCapacities(Request $request)
    {
        $capacities = $request->input('capacities', []);

        foreach ($capacities as $tableId => $cap) {
            Table::where('id', $tableId)->update(['capacity' => max(1, (int) $cap)]);
        }

        return redirect()->back();
    }

    public function cancelWaitlist($id)
    {
        $waitlist = Waitlist::findOrFail($id);
        $waitlist->update([
            'status' => 'cancelled'
        ]);

        return redirect()->back();
    }

    public function generateTables(Request $request)
    {
        $capacities = $request->input('capacities', []);
        $quantities = $request->input('quantities', []);

        Table::truncate();

        $counter = 1;

        foreach ($capacities as $index => $capacity) {
            $qty = (int) ($quantities[$index] ?? 0);
            $cap = (int) $capacity;

            if ($qty > 0 && $cap > 0) {
                for ($i = 1; $i <= $qty; $i++) {
                    Table::create([
                        'table_number' => 'T-' . str_pad($counter++, 2, '0', STR_PAD_LEFT),
                        'capacity' => $cap,
                        'status' => 'available',
                    ]);
                }
            }
        }

        return redirect()->back();
    }
}