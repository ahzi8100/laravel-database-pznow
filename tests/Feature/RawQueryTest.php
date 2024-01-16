<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use function PHPUnit\Framework\assertEquals;

class RawQueryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        DB::delete('DELETE FROM categories');
    }

//    public function testCrud()
//    {
//        DB::insert('INSERT INTO categories(id, name, description, created_at) VALUES (?, ?, ?, ?)', [
//            'GADGET', 'Gadget', 'Gadget Category', '2020-01-01 00:00:00'
//        ]);
//
//        $result = DB::select('SELECT * FROM categories WHERE id = ?', ['GADGET']);
//
//        assertEquals(1, count($result));
//        self::assertEquals('GADGET', $result[0]->id);
//        self::assertEquals('Gadget', $result[0]->name);
//        self::assertEquals('Gadget Category', $result[0]->description);
//        self::assertEquals('2020-01-01 00:00:00', $result[0]->created_at);
//    }

    public function testNamedBinging()
    {
        DB::insert('insert into categories(id, name, description, created_at) values (:id, :name, :description, :created_at)',[
            'id'=>'GADGET',
            'name' => 'Gadget',
            'description' => 'Gadget Category',
            'created_at' => '2020-01-01 00:00:00'
        ]);

        $result = DB::select('select * from categories where id = :id', ['id' => 'GADGET']);

        assertEquals(1, count($result));
        assertEquals('GADGET', $result[0]->id);
        self::assertEquals('Gadget', $result[0]->name);
        self::assertEquals('Gadget Category', $result[0]->description);
        self::assertEquals('2020-01-01 00:00:00', $result[0]->created_at);
    }
}
