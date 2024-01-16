<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpParser\Builder;
use Tests\TestCase;
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
}
