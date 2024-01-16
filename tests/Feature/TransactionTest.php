<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        DB::delete('DELETE FROM categories');
    }

    public function testTransaction()
    {
        DB::transaction( function () {
            DB::insert('insert into categories(id, name, description, created_at) values (?,?,?,?)', [
                'GADGET', 'Gadget', 'Gadget Category', '2020-01-01 00:00:00'
            ]);

            DB::insert('insert into categories(id, name, description, created_at) values (?,?,?,?)', [
                'FOOD', 'Food', 'Food Category', '2020-01-01 00:00:00'
            ]);
        });

        $result = DB::select('select * from categories');
        self::assertEquals(2, count($result));
    }
}
