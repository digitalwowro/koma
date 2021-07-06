<?php

use App\Category;
use App\EncryptedStore;
use App\User;
use Illuminate\Database\Seeder;

class CategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = User::whereEmail('tester@manole.ro')->first();

        if (!$admin) {
            return;
        }

        $vps = Category::create([
            'owner_id' => $admin->id,
        ]);

        EncryptedStore::upsert($vps, [
            'title' => 'VPS',
            'icon' => 'cloud',
            'fields' => '[{"key":"DdPeRFz9LG5nF1xoeYS3c50U","name":"ID","type":"ID","options":{"prefix":"VPS-"}}]',
        ]);

        $do = Category::create([
            'owner_id' => $admin->id,
            'parent_id' => $vps->id,
        ]);

        EncryptedStore::upsert($do, [
            'title' => 'Digital Ocean',
        ]);

        $linode = Category::create([
            'owner_id' => $admin->id,
            'parent_id' => $vps->id,
        ]);

        EncryptedStore::upsert($linode, [
            'title' => 'Linode',
        ]);
    }
}
