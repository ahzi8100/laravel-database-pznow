<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpParser\Builder;
use Tests\TestCase;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertEquals;

class QueryBuilderTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        DB::delete('DELETE FROM categories');
    }

    public function testInsert()
    {
        DB::table('categories')->insert([
            'id' => 'GADGET',
            'name' => 'Gadget'
        ]);

        DB::table('categories')->insert([
            'id' => 'FOOD',
            'name' => 'Food'
        ]);

        $result = DB::select('select count(id) as total from categories');
        assertEquals(2,$result[0]->total);
    }

    public function testSelect()
    {
        $this->testInsert();

        $collection = DB::table('categories')->select(['id','name'])->get();
        self::assertNotNull($collection);

        $collection->each(function ($item) {
            Log::info(json_encode($item));
        });
    }

    public function insertCategories()
    {
        DB::table('categories')
            ->insert(['id'=>'SMARTPHONE', 'name'=>'Smartphone','created_at'=>'2020-01-01 00:00:00']);
        DB::table('categories')
            ->insert(['id'=>'FOOD', 'name'=>'Food','created_at'=>'2020-01-01 00:00:00']);
        DB::table('categories')
            ->insert(['id'=>'LAPTOP', 'name'=>'Laptop','created_at'=>'2020-01-01 00:00:00']);
        DB::table('categories')
            ->insert(['id'=>'FASHION', 'name'=>'Fashion','created_at'=>'2020-01-01 00:00:00']);
    }

    public function testWhere()
    {
        $this->insertCategories();

        $collection = DB::table('categories')->Where(function (\Illuminate\Database\Query\Builder $builder) {
            $builder->where('id', '=', 'SMARTPHONE');
            $builder->orWhere('id', '=', 'LAPTOP');
        })->get();

        self::assertCount(2, $collection);
        $collection->each(function ($item) {
            Log::info(json_encode($item));
        });
    }

    public function testWhereBetween()
    {
        $this->insertCategories();

        $collection = DB::table('categories')
            ->whereBetween('created_at', ['2020-01-01 00:00:00', '2020-01-02 00:00:00'])
            ->get();

        self::assertCount(4, $collection);
        $collection->each(function ($item){
            Log::info(json_encode($item));
        });
    }

    public function testWhereInMethod()
    {
        $this->insertCategories();

        $collection = DB::table('categories')->whereIn('id', ['SMARTPHONE', 'LAPTOP'])->get();
        self::assertCount(2, $collection);
        $collection->each(function ($item) {
            Log::info(json_encode($item));
        });
    }

    public function testWhereNull()
    {
        $this->insertCategories();

        $collection = DB::table('categories')->whereNull('description')->get();
        assertCount(4,$collection);
        $collection->each(function ($item){
            Log::info(json_encode($item));
        });
    }

    public function testWhareDate()
    {
        $this->insertCategories();

        $collection = DB::table('categories')->whereDate('created_at', '2020-01-01')->get();
        assertCount(4, $collection);
        $collection->each(function ($item) {
            Log::info(json_encode($item));
        });
    }

    public function testUpdate()
    {
        $this->insertCategories();

        DB::table('categories')->where('id', '=', 'SMARTPHONE')->update([
            'name' => 'Handphone'
        ]);

        $collection = DB::table('categories')->where('name','=', 'Handphone')->get();
        assertCount(1, $collection);
        $collection->each(function ($item) {
            Log::info(json_encode($item));
        });
    }

    public function testUpdateOrInsert()
    {
        DB::table('categories')->updateOrInsert([
            'id' => 'VOUCHER'
        ], [
            'name' => 'Voucher',
            'description' => 'Ticket and Voucher',
            'created_at' => '2020-01-01 00:00:00'
        ]);

        $collection = DB::table('categories')->where('id','=','VOUCHER')->get();
        assertCount(1, $collection);
        $collection->each(function ($item){
            Log::info(json_encode($item));
        });
    }

    public function testDelete()
    {
        $this->insertCategories();

        DB::table('categories')->where('id','=','SMARTPHONE')->delete();
        $collection = DB::table('categories')->where('id', '=','SMARTPHONE')->get();
        assertCount(0, $collection);
    }
}
