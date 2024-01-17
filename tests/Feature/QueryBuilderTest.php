<?php

namespace Tests\Feature;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
//use PhpParser\Builder;
use Tests\TestCase;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertEquals;

class QueryBuilderTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        DB::delete('delete from products');
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

    public function insertProducts()
    {
        $this->insertCategories();

        DB::table('products')->insert([
            'id' => 1,
            'name' => 'Iphone 14',
            'category_id' => 'SMARTPHONE',
            'price' => 2000000
        ]);

        DB::table('products')->insert([
            'id' => 2,
            'name' => 'Samsung Galaxy',
            'category_id' => 'SMARTPHONE',
            'price' => 1800000
        ]);
    }
    public function testJoin()
    {
        $this->insertProducts();

        $collection = DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('products.id', 'products.name', 'products.price', 'categories.name as category_name')
            ->get();

        assertCount(2, $collection);
        $collection->each(function ($item){
            Log::info(json_encode($item));
        });
    }

    public function testOrdering()
    {
        $this->insertProducts();

        $collection = DB::table('products')
            ->orderBy('price','desc')
            ->orderBy('name', 'asc')
            ->get();

        assertCount(2, $collection);
        $collection->each(function ($item){
            Log::info(json_encode($item));
        });
    }

    public function testPaging()
    {
        $this->insertProducts();

        $collection = DB::table('categories')
            ->skip(2)
            ->take(2)
            ->get();

        assertCount(2,$collection);
        $collection->each(function ($item){
            Log::info(json_encode($item));
        });
    }

    public function insertManyCategories()
    {
        for ($i = 0; $i < 100; $i++) {
            DB::table('categories')->insert([
                'id' => "CATEGORY-$i",
                'name' => "Category $i",
                'created_at' => '2020-10-10 10:10:10'
            ]);
        }
    }

    public function testChunk()
    {
        $this->insertManyCategories();

        DB::table('categories')
            ->orderBy('id')
            ->chunk(10, function ($categories) {
                self::assertNotNull($categories);
                foreach ($categories as $category) {
                    Log::info(json_encode($category));
                }
            });
    }

    public function testLazy()
    {
        $this->insertManyCategories();

        $collection = DB::table('categories')->orderBy('id')->lazy(10)->take(3);
        self::assertNotNull($collection);

        $collection->each(function ($item) {
            Log::info(json_encode($item));
        });
    }

    public function testCursor()
    {
        $this->insertManyCategories();

        $collection = DB::table('categories')->orderBy('id')->cursor(10);
        self::assertNotNull($collection);

        $collection->each(function ($item) {
            Log::info(json_encode($item));
        });
    }

    public function testQueryBuilderRaw()
    {
        $this->insertProducts();

        $collection = DB::table('products')
            ->select(
                DB::raw('count(id) as total_product'),
                DB::raw('min(price) as min_price'),
                DB::raw('max(price) as max_price')
            )->get();

        assertEquals(2, $collection[0]->total_product);
        self::assertEquals(1800000, $collection[0]->min_price);
        assertEquals(2000000, $collection[0]->max_price);
    }
}
