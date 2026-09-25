<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SyncOutlets extends Command
{
    protected $signature = 'app:sync-outlets';
    protected $description = 'Sync outlets from external API';

    public function handle()
    {
        $this->info("Fetching outlets from API...");
        $outlets = [];
        $page = 1;
        do {
            $response = Http::get("https://appdashboard.mykopiogroup.com/api/v1/openapi/branches?page=$page");
            if (!$response->successful()) break;
            $data = $response->json();
            
            foreach ($data['data']['data'] as $item) {
                $name = $item['nama_outlet'];
                if (!$name) {
                    $name = $item['brand']['nama_brand'] . ' (ID: ' . $item['id'] . ')';
                }
                $outlets[] = [
                    'id' => $item['id'],
                    'name' => $name,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            
            $lastPage = $data['data']['last_page'] ?? 1;
            $page++;
        } while ($page <= $lastPage);

        $this->info("Found " . count($outlets) . " outlets. Updating database...");

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        DB::table('outlets')->truncate();
        DB::table('tables')->truncate();
        
        try { DB::table('waitlists')->truncate(); } catch (\Exception $e) {}
        try { DB::table('settings')->truncate(); } catch (\Exception $e) {}
        try { DB::table('guests')->truncate(); } catch (\Exception $e) {}

        foreach ($outlets as $o) {
            DB::table('outlets')->insert($o);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->info("Sync complete!");
    }
}
